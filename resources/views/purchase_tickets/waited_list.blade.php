@extends('layouts.app', ['activePage' => 'waited_list', 'activeButton' => 'waited_list', 'title' => 'Zemef ERP', 'navName' => 'Átadéásra váró tételek' ])

@section('content')
<style>
    .selectable-card.selected {
        padding:15px;
        border: 2px solid #007bff;
        box-shadow: 0px 0px 4px blue;
    }
    #personalImagePreviews {
        display: flex;
        flex-wrap: wrap;
    }
    #courierImagePreviews {
        display: flex;
        flex-wrap: wrap;
    }
    .imageContainer {
        flex: 0 0 33.33%;
        position: relative;
        padding: 5px;
    }
    .imageContainer img {
        max-width: 100%;
        height: auto;
    }
    .imageContainer button {
        position: absolute;
        top: 5px;
        right: 5px;
    }
</style>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-12" style="display:grid;">
                                    <h3 class="mb-0"><th>Átadásra váró tételek</th></h3>
                                    <a style="display: none;" class="btn btn-sm btn-success transfer_selected"><i class="fa fa-transfer"></i> Kijelöltek átadása</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="col-12 mt-2 mb-2">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label for="filter_customer_id">Ügyfél</label>
                                    <select id="filter_customer_id" class="form-control">
                                        <option value="">Mindegyik</option>
                                        @foreach($customerOptions as $customerOption)
                                            <option value="{{ $customerOption['customer_id'] }}">{{ $customerOption['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="filter_business_id">Üzlet</label>
                                    <select id="filter_business_id" class="form-control">
                                        <option value="">Mindegyik</option>
                                        @foreach($businessOptions as $businessOption)
                                            <option value="{{ $businessOption->business_id }}">{{ $businessOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="filter_storage_id">Célraktár</label>
                                    <select id="filter_storage_id" class="form-control">
                                        <option value="">Mindegyik</option>
                                        @foreach($storageOptions as $storageOption)
                                            <option value="{{ $storageOption->warehouse_id }}">{{ $storageOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="reset_waited_filters">Szűrők törlése</button>
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="waited_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Vételi jegy sorszám</th>
                                        <th>Ügyfél</th>
                                        <th>Célraktár</th>
                                        <th>Ékszerek száma</th>
                                        
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Vételi jegy sorszám</th>
                                        <th>Ügyfél</th>
                                        <th>Célraktár</th>
                                        <th>Ékszerek száma</th>
                                        
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($waited_list as $item)
                                        
                                            <tr data-ticket-number="{{ $item->ticket_number }}" data-business-id="{{ $item->business_id }}" data-customer-id="{{ $item->customer_id }}" data-storage-id="{{ $item->storage_id }}">
                                                <td>
                                                    <input type="checkbox" class="ticket-checkbox" data-business-id="{{ $item->business_id }}" data-ticket-number="{{ $item->purchase_ticket_id }}">
                                                </td>
                                                <td>{{ $item->ticket_number }}</td>
                                                <td>{{ $item->customer->name }}</td>
                                                <td class="target-warehouse-name" data-ticket-id="{{ $item->purchase_ticket_id }}">{{ $item->warehouse->name }}</td>
                                                <td>
                                                    @if($item->inventoryItems)
                                                        {{ $item->inventoryItems->count() }}
                                                    @endif
                                                </td>
                                                
                                            </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="ticket-detail-templates" class="d-none">
                            @foreach ($waited_list as $item)
                                <div id="ticket-detail-template-{{ $item->purchase_ticket_id }}">
                                    <div class="mb-3">
                                        <div><strong>Vételi jegy:</strong> {{ $item->ticket_number }}</div>
                                        <div><strong>Ügyfél:</strong> {{ $item->customer->name ?? '-' }}</div>
                                        <div><strong>Célraktár:</strong> {{ $item->warehouse->name ?? '-' }}</div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Kategória</th>
                                                    <th>Megnevezés</th>
                                                    <th>Karát</th>
                                                    <th>Db</th>
                                                    <th>Súly</th>
                                                    <th>Vételár</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($item->inventoryItems as $inventoryItem)
                                                    @php
                                                        $itemData = $inventoryItem->item_data;
                                                        if (is_string($itemData)) {
                                                            $itemData = json_decode($itemData, true);
                                                        }
                                                        $itemData = is_array($itemData) ? $itemData : [];
                                                        $categoryName = !empty($itemData['category_id']) ? ($categoryMap[$itemData['category_id']] ?? '-') : '-';
                                                        $karatName = !empty($itemData['karat_id']) ? ($karatMap[$itemData['karat_id']] ?? '-') : '-';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $categoryName }}</td>
                                                        <td>{{ $inventoryItem->name ?? '-' }}</td>
                                                        <td>{{ $karatName }}</td>
                                                        <td>{{ $inventoryItem->quantity ?? 0 }}</td>
                                                        <td>{{ $inventoryItem->weight ?? 0 }}</td>
                                                        <td>{{ number_format((float) ($inventoryItem->price ?? 0), 0, ',', ' ') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">Nincs tétel.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Átadás módja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Hogyan szeretné átadni a kijelölt elemeket?</p>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="transfer_business_id">Üzlet</label>
                        <select id="transfer_business_id" class="form-control">
                            <option value="">- válasszon -</option>
                            @foreach($businessOptions as $businessOption)
                                <option value="{{ $businessOption->business_id }}">{{ $businessOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="transfer_storage_id">Célraktár</label>
                        <select id="transfer_storage_id" class="form-control">
                            <option value="">- válasszon -</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 selectable-card" data-target="#personalTransfer">
                        <div class="card">
                            <div class="card-header">
                                Személyesen
                            </div>
                            <div class="card-body text-center">
                                <i class="fa fa-user fa-3x"></i>
                                <div class="form-check mt-2 d-none">
                                    <input class="form-check-input" type="radio" name="transferMethod" id="personalTransfer" value="personal">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 selectable-card" data-target="#courierTransfer">
                        <div class="card">
                            <div class="card-header">
                                Futárszolgálattal
                            </div>
                            <div class="card-body text-center">
                                <i class="fa fa-truck fa-3x"></i>
                                <div class="form-check mt-2 d-none">
                                    <input class="form-check-input" type="radio" name="transferMethod" id="courierTransfer" value="courier">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- A személyes átadás részletei -->
                <div id="personalDetails" class="mt-3 d-none">
                    <input type="hidden" value="{{ Auth::User()->employee_id }}" id="own_employee_id"/>

                    <label for="porsonal_images">Fájlok feltöltése:</label>
                    <input type="file" multiple class="form-control" id="personal_images">

                    <!-- Fájlok előnézete -->
                    <div id="personalImagePreviews" class="mt-2">
                    </div>

                    <label for="personalSelect">Ki viszi el?</label>
                    <select class="form-control mb-3" id="personalSelect">
                        <option value="self">Saját magam</option>
                        <option value="other">Más dolgozó</option>
                    </select>

                    <label>Saját aláíró kód:</label>
                    <input type="text" class="form-control" autocomplete="one-time-code" oninput="handlePasswordInput(this)" id="ownSignCode">


                    <!-- Más dolgozó részletei -->
                    <div id="otherWorkerDetails" class="d-none">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label>Szállító aláíró kódja:</label>
                                <input type="text" class="form-control" autocomplete="one-time-code" oninput="handlePasswordInput(this)"  id="transferSignCode">
                            </div>
                        </div>
                    </div>

                    
                </div>

                <!-- Futárszolgálat részletei -->
                <div id="courierDetails" class="mt-3 d-none">
                    <label for="trackingNumber">Fuvarcímke szám/Név:</label>
                    <input type="text" class="form-control" id="trackingNumber" required>

                    <label for="shippingDate">Feladás dátuma:</label>
                    <input type="date" class="form-control" id="shippingDate" required>

                    <label for="shippingDate">Aláíró kód:</label>
                    <input type="text" autocomplete="one-time-code" class="form-control"  oninput="handlePasswordInput(this)" id="courierSignCode" required>

                    <label for="courier_images">Fájlok feltöltése:</label>
                    <input type="file" multiple class="form-control" id="courier_images">

                    <!-- Képek előnézete -->
                    <div id="courierImagePreviews" class="mt-2">
                        <!-- Az ide feltöltött képek előnézetei itt jelennek meg -->
                    </div>

                </div>
            </div>
            <div class="modal-footer mt-7">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bezár</button>
                <button type="button" class="btn btn-primary" id="confirmTransfer">Megerősít</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketDetailsModalLabel">Vételi jegy adatlap</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="ticketDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezár</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStorageModal" tabindex="-1" aria-labelledby="editStorageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStorageModalLabel">Célraktár módosítása</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editStorageTicketId">
                <input type="hidden" id="editStorageBusinessId">
                <input type="hidden" id="editStorageCurrentId">
                <div class="form-group">
                    <label for="editStorageSelect">Hova adja át</label>
                    <select id="editStorageSelect" class="form-control"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Mégsem</button>
                <button type="button" class="btn btn-success" id="saveStorageBtn">Mentés</button>
            </div>
        </div>
    </div>
</div>


@include('receipts.print_modal')
@endsection

@push('js')
    <script type="text/javascript">
        function handlePasswordInput(inputElement) {
            inputElement.type = 'password';
        }
        $(document).ready(function() {
            let selectedBusinessId = null;
            let currentFiles = [];
            let editingTicketId = null;

            function loadTransferStorages(businessId, preselectStorageId = null) {
                if (!businessId) {
                    $('#transfer_storage_id').html('<option value="">- válasszon -</option>');
                    return;
                }

                $('#transfer_storage_id').html('<option value="">Betöltés...</option>');

                $.ajax({
                    method: 'POST',
                    url: '/ajax/get_storages_by_business_id_employee',
                    data: { business_id: businessId }
                }).done(function(resp) {
                    let options = '<option value="">- válasszon -</option>';
                    if (resp && resp.length) {
                        resp.forEach(function(storage) {
                            let selected = preselectStorageId && String(storage.warehouse_id) === String(preselectStorageId) ? ' selected' : '';
                            options += '<option value="' + storage.warehouse_id + '"' + selected + '>' + storage.name + '</option>';
                        });
                    }
                    $('#transfer_storage_id').html(options);
                    validateInputs();
                }).fail(function() {
                    $('#transfer_storage_id').html('<option value="">Nincs elérhető raktár</option>');
                    validateInputs();
                });
            }

            handleImageUpload('personal_images', 'personalImagePreviews');
            handleImageUpload('courier_images', 'courierImagePreviews');

            //Képfeltöltés futár
            function handleImageUpload(inputId, previewContainerId) {

            $(`#${inputId}`).on('change', function() {
                let newFiles = Array.from(this.files);
                currentFiles = [...currentFiles, ...newFiles];
                updateFilePreviews(previewContainerId);
                updateFileInputLabel(inputId);
            });

            function updateFilePreviews(containerId) {
                $(`#${containerId}`).empty();

                currentFiles.forEach((file, index) => {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        let contentDiv = $('<div>');

                        if (file.type.startsWith("image/")) {
                            let img = $('<img>').attr('src', e.target.result).attr('style', 'max-width: 150px; margin-right: 10px;');
                            contentDiv.append(img);
                        } else {
                            let fileName = $('<span>').text(file.name).attr('style', 'font-size: 12px; display: block; text-align: left;');
                            let fileIcon = $('<i>').addClass('fa fa-file').attr('style', 'font-size: 100px; display: block; margin-right: 10px;');
                            contentDiv.append(fileIcon, fileName);
                        }

                        let removeButton = $('<button>').text('Törlés').addClass('btn btn-sm btn-danger').click(function() {
                            currentFiles.splice(index, 1);
                            updateFilePreviews(containerId);
                            updateFileInputLabel(inputId);
                            refreshFileInput(inputId);
                        });

                        let div = $('<div>').addClass('imageContainer').append(contentDiv, removeButton);
                        $(`#${containerId}`).append(div);
                    };

                    if (file.type.startsWith("image/")) {
                        reader.readAsDataURL(file);
                    } else {
                        reader.onload();
                    }
                });
            }

            function updateFileInputLabel(inputId) {
                let fileCount = currentFiles.length;
                let label = fileCount > 0 ? fileCount + ' fájl' : 'Válassz fájlt';
                $(`#${inputId}`).next('.custom-file-label').html(label);
            }

            function refreshFileInput(inputId) {
                let input = $(`#${inputId}`);
                input.replaceWith(input.val('').clone(true));
                $.each(currentFiles, function(index, file) {
                    $(`#${inputId}`)[0].files = addToFileList($(`#${inputId}`)[0].files, file);
                });
            }

            function addToFileList(fileList, file) {
                let newFileList = new ClipboardEvent('').clipboardData || new DataTransfer();
                for (let i = 0; i < fileList.length; i++) {
                    newFileList.items.add(fileList[i]);
                }
                newFileList.items.add(file);
                return newFileList.files;
            }
        }
            

            let table2 = $('#datatables').DataTable();

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'datatables') {
                    return true;
                }

                let selectedCustomerId = $('#filter_customer_id').val();
                let selectedBusinessId = $('#filter_business_id').val();
                let selectedStorageId = $('#filter_storage_id').val();

                let rowNode = table2.row(dataIndex).node();
                if (!rowNode) {
                    return true;
                }

                let rowCustomerId = String($(rowNode).data('customer-id') || '');
                let rowBusinessId = String($(rowNode).data('business-id') || '');
                let rowStorageId = String($(rowNode).data('storage-id') || '');

                if (selectedCustomerId && rowCustomerId !== String(selectedCustomerId)) {
                    return false;
                }

                if (selectedBusinessId && rowBusinessId !== String(selectedBusinessId)) {
                    return false;
                }

                if (selectedStorageId && rowStorageId !== String(selectedStorageId)) {
                    return false;
                }

                return true;
            });

            $(document).on('change', '#filter_customer_id, #filter_business_id, #filter_storage_id', function() {
                table2.draw();
            });

            $(document).on('click', '#reset_waited_filters', function() {
                $('#filter_customer_id').val('');
                $('#filter_business_id').val('');
                $('#filter_storage_id').val('');
                table2.draw();
            });

            $('#datatables tbody').on('change', '.ticket-checkbox', function() {
                handleCheckboxChange();
            });





            function handleCheckboxChange() {
                let currentBusinessId = $(this).data('business-id');
                let ticketNumber = $(this).closest('tr').data('ticket-number');
                let isChecked = $(this).prop('checked');

                if (isChecked) {
                    if (selectedBusinessId === null) {
                        selectedBusinessId = currentBusinessId;
                    } else if (selectedBusinessId !== currentBusinessId) {
                        $(this).prop('checked', false);
                        $.notify({ icon: "error", message: "Egy bizonylaton csak azonos üzletek vételi jegyei adhatóak át!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        return;
                    }
                } else if ($('.ticket-checkbox:checked').length === 0) {
                    selectedBusinessId = null;
                }

                // Frissítjük az azonos jegyszámú checkboxokat
                table2.$('tr[data-ticket-number="' + ticketNumber + '"]').find('.ticket-checkbox').prop('checked', isChecked);
                updateTransferButtonVisibility();
            }

            $('.ticket-checkbox').on('change', handleCheckboxChange);

            // Elrejtjük a megerősítés gombot kezdetben
            $('#confirmTransfer').hide();

            function validateInputs() {
                isEmployeeValid = false;
                let isValidStorage = $('#transfer_storage_id').val() && String($('#transfer_storage_id').val()).trim() !== '';
                // Ellenőrizzük, hogy a fuvarcímke száma megfelelő-e
                let isValidCourier = $('#courierTransfer').is(':checked') && $('#trackingNumber').val().length >= 3;
                let isvalidcourierSignCode = $('#courierSignCode').val().length >= 6;

                // Ellenőrizzük, hogy a "Más dolgozó" részletei megfelelőek-e
                let isValidOtherWorker = $('#personalSelect').val() === 'other' &&
                    $('#ownSignCode').val().length >= 6 && $('#transferSignCode').val().length >= 6;

                // Ellenőrizzük, hogy a személyes átvétel megfelelő-e
                let isValidPersonal = $('#personalTransfer').is(':checked') && (
                    $('#personalSelect').val() === 'self' && $('#ownSignCode').val().length >= 6 || isValidOtherWorker
                );

                // Ha bármelyik feltétel teljesül, megjelenítjük a megerősítés gombot
                if (isValidStorage && ((isValidCourier && isvalidcourierSignCode) || isValidPersonal)) {
                    $('#confirmTransfer').show();
                } else {
                    $('#confirmTransfer').hide();
                }
            }

            $(document).on('change', '#transfer_business_id', function() {
                loadTransferStorages($(this).val());
            });

            $(document).on('change', '#transfer_storage_id', function() {
                validateInputs();
            });

            $(document).on('click', '.selectable-card', function() {
                $('.selectable-card').removeClass('selected');
                $(this).addClass('selected');
                let targetRadio = $(this).data('target');
                $('input[type=radio]').prop('checked', false);
                $(targetRadio).prop('checked', true);

                // A beviteli mezők kiürítése kártya váltáskor
                $('#trackingNumber').val('');
                $('#courierSignCode').val('');
                $('#transferSignCode').val('');
                $('#ownSignCode').val('');
                $('#shippingDate').val('');
                $('#owemail').val('');
                $('#owpassword').val('');
                $('#personalSelect').val('self');
                $('#otherWorkerDetails').addClass('d-none');
                $('#loginErrorAlert').addClass('d-none');
                $('#selectedotherWorkerDetails').addClass('d-none');
                $('#other_employee_id').empty().append('<option value="0">-</option>').trigger('change');
                // További mezők kiürítése szükség esetén...

                // A kiválasztott opció alapján a releváns részletek megjelenítése/elrejtése
                if (targetRadio === '#personalTransfer') {
                    $('#personalDetails').removeClass('d-none');
                    $('#courierDetails').addClass('d-none');
                    $('#courierImagePreviews').empty();
                    // Courier inputok ürítése
                    $('#courierDetails input[type="text"], #courierDetails input[type="date"], #courierDetails input[type="file"]').val('');
                } else if (targetRadio === '#courierTransfer') {
                    $('#courierDetails').removeClass('d-none');
                    $('#personalDetails').addClass('d-none');
                    $('#personalImagePreviews').empty();
                     // Personal inputok ürítése
                    $('#personalDetails input[type="text"], #personalDetails input[type="date"], #personalDetails input[type="file"]').val('');
                }

                // Ellenőrzés
                validateInputs();
            });

            $(document).on('input', '#trackingNumber,#courierSignCode', function() {
                // Ellenőrzés minden bevitelkor
                validateInputs();
            });

            $(document).on('change', '#personalSelect', function() {
                if ($(this).val() === 'other') {
                    $('#otherWorkerDetails').removeClass('d-none');
                } else {
                    $('#selectedotherWorkerDetails').addClass('d-none');
                    $('#otherWorkerDetails').addClass('d-none');
                }
                $('#ownSignCode').val('');
                $('#transferSignCode').val('');
                // Ellenőrzés minden váltáskor
                validateInputs();
            });

            $(document).on('input', '#ownSignCode,#transferSignCode', function() {
                validateInputs();
            });

            function updateTransferButtonVisibility() {
                let hasCheckedCheckbox = $('.ticket-checkbox:checked').length > 0;
                if (hasCheckedCheckbox) {
                    $('.transfer_selected').show();
                } else {
                    $('.transfer_selected').hide();
                }
            }

            $(document).on('click', '.transfer_selected', function() {
                let firstCheckedRow = $('.ticket-checkbox:checked').first().closest('tr');
                let defaultBusinessId = String(selectedBusinessId || firstCheckedRow.data('business-id') || '');
                let defaultStorageId = String(firstCheckedRow.data('storage-id') || '');

                if (defaultBusinessId) {
                    $('#transfer_business_id').val(defaultBusinessId);
                    loadTransferStorages(defaultBusinessId, defaultStorageId);
                } else {
                    $('#transfer_business_id').val('');
                    $('#transfer_storage_id').html('<option value="">- válasszon -</option>');
                }

                $('#transferModal').modal('show');
            });

            $(document).on('click', '.open-ticket-details', function() {
                let ticketId = $(this).data('ticket-id');
                let template = $('#ticket-detail-template-' + ticketId).html() || '<div>Nincs megjeleníthető adat.</div>';
                $('#ticketDetailsContent').html(template);
                $('#ticketDetailsModal').modal('show');
            });

            $(document).on('click', '.open-storage-edit', function() {
                let ticketId = $(this).data('ticket-id');
                let businessId = $(this).data('business-id');
                let currentStorageId = $(this).data('storage-id');
                let ticketNumber = $(this).data('ticket-number');

                editingTicketId = ticketId;
                $('#editStorageTicketId').val(ticketId);
                $('#editStorageBusinessId').val(businessId);
                $('#editStorageCurrentId').val(currentStorageId);
                $('#editStorageModalLabel').text('Célraktár módosítása - ' + ticketNumber);
                $('#editStorageSelect').html('<option value="">Betöltés...</option>');
                $('#editStorageModal').modal('show');

                $.ajax({
                    method: 'POST',
                    url: '/ajax/get_storages_by_business_id_employee',
                    data: { business_id: businessId }
                }).done(function(resp) {
                    let options = '';
                    if (resp && resp.length) {
                        resp.forEach(function(storage) {
                            let selected = String(storage.warehouse_id) === String(currentStorageId) ? ' selected' : '';
                            options += '<option value="' + storage.warehouse_id + '"' + selected + '>' + storage.name + '</option>';
                        });
                    } else {
                        options = '<option value="">Nincs elérhető raktár</option>';
                    }
                    $('#editStorageSelect').html(options);
                });
            });

            $(document).on('click', '#saveStorageBtn', function() {
                let ticketId = $('#editStorageTicketId').val();
                let storageId = $('#editStorageSelect').val();

                if (!ticketId || !storageId) {
                    $.notify({ icon: 'error', message: 'Válassz célraktárat.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                    return;
                }

                $('#saveStorageBtn').prop('disabled', true);

                $.ajax({
                    method: 'POST',
                    url: '/ajax/update_waited_ticket_storage',
                    data: {
                        purchase_ticket_id: ticketId,
                        storage_id: storageId
                    }
                }).done(function(resp) {
                    if (resp.data && resp.data.success === 'success') {
                        $('td.target-warehouse-name[data-ticket-id="' + ticketId + '"]').text(resp.data.warehouse_name);
                        $('.open-storage-edit[data-ticket-id="' + ticketId + '"]').data('storage-id', storageId);
                        $('#editStorageModal').modal('hide');
                        $.notify({ icon: 'done', message: resp.data.message }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
                    } else {
                        $.notify({ icon: 'error', message: (resp.data && resp.data.message) ? resp.data.message : 'A mentés nem sikerült.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                    }
                }).fail(function(xhr) {
                    let message = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                        ? xhr.responseJSON.data.message
                        : 'Hiba történt a mentés közben.';
                    $.notify({ icon: 'error', message: message }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                }).always(function() {
                    $('#saveStorageBtn').prop('disabled', false);
                });
            });

            $(document).on('click', '#confirmTransfer', function() {
                $(this).prop('disabled', true);
                let ticketNumbers = [];
                
                $('.ticket-checkbox:checked').each(function() {
                    let ticketNumber = $(this).data('ticket-number');
                    if (!ticketNumbers.includes(ticketNumber)) {
                        ticketNumbers.push(ticketNumber);
                    }
                });

                let transferMethod = $('input[name=transferMethod]:checked').val();
                let courierTrackingNumber = $('#trackingNumber').val();
                let shippingDate = $('#shippingDate').val();
                let otherWorkerId = $('#other_employee_id').val();
                let courierSignCode = $('#courierSignCode').val();
                let transferSignCode = $('#transferSignCode').val();
                let ownSignCode = $('#ownSignCode').val();
                let personalSelect = $('#personalSelect').val();
                let transferBusinessId = $('#transfer_business_id').val();
                let transferStorageId = $('#transfer_storage_id').val();

                if (!transferStorageId) {
                    $.notify({ icon: 'error', message: 'Válassz célraktárat az átadáshoz.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                    $(this).prop('disabled', false);
                    return;
                }

                let formData = new FormData();
                formData.append('type', 'waited');
                formData.append('transferMethod', transferMethod);
                formData.append('courierTrackingNumber', courierTrackingNumber);
                formData.append('courierSignCode', courierSignCode);
                formData.append('transferSignCode', transferSignCode);
                formData.append('ownSignCode', ownSignCode);
                formData.append('shippingDate', shippingDate);
                formData.append('otherWorkerId', otherWorkerId);
                formData.append('personalSelect', personalSelect);
                formData.append('business_id', transferBusinessId);
                formData.append('warehouse_to', transferStorageId);
                ticketNumbers.forEach(ticket => {
                    formData.append('ticketNumbers[]', ticket);
                });
                currentFiles.forEach((file, index) => {
                    formData.append('images[]', file);
                });

                $.ajax({
                    method: "POST",
                    url: "/ajax/transfer_items",
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function( resp ) {
                    if (resp.data.success === 'success') {
                        $.notify({ 
                            icon: "done", 
                            message: resp.data.message 
                        }, {
                            type: 'success',
                            timer: 3000, 
                            placement: { from: 'top', align: 'right' }
                        });
                        
                        $('#transferModal').modal('hide');
                        $('#confirmTransfer').hide();
                        $('.transfer_selected').hide();
                        
                        // Táblázat frissítése
                        $('.ticket-checkbox:checked').each(function() {
                            table2.row($(this).closest('tr')).remove().draw();
                        });

                        // nyomtatás
                        $.ajax({
                            method: "POST",
                            url: "/ajax/pdf/delivery_note",
                            data: { delivery_note_id: resp.data.delivery_note_id }
                        }).done(function( msg ) {
                            var file_path = "/pdf/delivery_notes/" + msg;
                            printJS(file_path);
                        });
                    } else {
                        // Ha a válasz 'error' típusú
                        $.notify({ 
                            icon: "error", 
                            message: resp.data.message 
                        }, {
                            type: 'danger',
                            timer: 3000, 
                            z_index: 1060,
                            placement: { from: 'top', align: 'center' }
                        });
                        $('#confirmTransfer').prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endpush