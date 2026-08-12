@extends('layouts.app', ['activePage' => 'waited_list', 'activeButton' => 'waited_list', 'title' => 'Zemef ERP', 'navName' => 'Átadásra váró tételek' ])

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
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Vételi jegy sorszám</th>
                                        <th>Megnevezés</th>
                                        <th>Ügyfél</th>
                                        <th>Címkék</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Vételi jegy sorszám</th>
                                        <th>Megnevezés</th>
                                        <th>Ügyfél</th>
                                        <th>Címkék</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($waited_list as $item)
                                        <tr data-ticket-number="{{ $item->purchase_ticket->ticket_number }}" data-business-id="{{ $item->purchase_ticket->business_id }}">
                                            <td>
                                                <input type="checkbox" class="ticket-checkbox" data-business-id="{{ $item->purchase_ticket->business_id }}" data-ticket-number="{{ $item->purchase_ticket->purchase_ticket_id }}">
                                            </td>
                                            <td>{{ $item->purchase_ticket->ticket_number }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->purchase_ticket->customer->name }}</td>
                                            <td>@foreach($item->tags as $tag)<span class="badge badge-default" style="background-color:{{ $tag->color }}">{{ $tag->name }}</span>@endforeach</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

                    <!-- Más dolgozó részletei -->
                    <div id="otherWorkerDetails" class="d-none">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="alert alert-danger d-none" id="loginErrorAlert">
                                    <span id="loginErrorAlertText"></span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="owemail">Email:</label>
                                <input type="text" class="form-control mb-3" name="uniqueEmailFieldName" id="owemail" autocomplete="off">
                            </div>
                            <div class="col-md-5">
                                <label for="owpassword">Jelszó:</label>
                                <input type="text" class="form-control mb-3" name="uniquePasswordFieldName" id="owpassword" autocomplete="off" oninput="handlePasswordInput(this)">
                            </div>
                            <div class="col-md-2 align-self-end" style="margin-bottom:12px;">
                                <button class="btn btn-primary loginbtn">Belépés</button>
                            </div>
                        </div>
                    </div>

                    <div id="selectedotherWorkerDetails" class="d-none">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="other_employee_id">Dolgozó:</label>
                                <select class="form-control" id="other_employee_id">
                                    <option value="0">-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Futárszolgálat részletei -->
                <div id="courierDetails" class="mt-3 d-none">
                    <label for="trackingNumber">Fuvarcímke szám:</label>
                    <input type="text" class="form-control" id="trackingNumber" required>

                    <label for="shippingDate">Feladás dátuma:</label>
                    <input type="date" class="form-control" id="shippingDate" required>

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



@endsection

@push('js')
    <script type="text/javascript">
        function handlePasswordInput(inputElement) {
            inputElement.type = 'password';
        }
        $(document).ready(function() {
            let selectedBusinessId = null;
            let currentFiles = [];

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
                        let contentDiv = $('<div>'); // Létrehozunk egy új div-et a tartalomnak

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

            var table2 = $('#datatables').DataTable(drawCallback: function() {
                    $('.ticket-checkbox').off('change');
                    $('.ticket-checkbox').on('change', handleCheckboxChange);
                });

            $('.ticket-checkbox').on('change', handleCheckboxChange);

            // Elrejtjük a megerősítés gombot kezdetben
            $('#confirmTransfer').hide();

            function validateInputs() {
                isEmployeeValid = false;
                // Ellenőrizzük, hogy a fuvarcímke száma megfelelő-e
                let isValidCourier = $('#courierTransfer').is(':checked') && $('#trackingNumber').val().length >= 3;

                // Ellenőrizzük, hogy a "Más dolgozó" részletei megfelelőek-e
                let isValidOtherWorker = $('#personalSelect').val() === 'other' &&
                             $('#owemail').val() && $('#owpassword').val() && isEmployeeValid;

                // Ellenőrizzük, hogy a személyes átvétel megfelelő-e
                let isValidPersonal = $('#personalTransfer').is(':checked') && (
                    $('#personalSelect').val() === 'self' || isValidOtherWorker
                );

                // Ha bármelyik feltétel teljesül, megjelenítjük a megerősítés gombot
                if (isValidCourier || isValidPersonal) {
                    $('#confirmTransfer').show();
                } else {
                    $('#confirmTransfer').hide();
                }
            }

            $(document).on('click', '.selectable-card', function() {
                $('.selectable-card').removeClass('selected');
                $(this).addClass('selected');
                let targetRadio = $(this).data('target');
                $('input[type=radio]').prop('checked', false);
                $(targetRadio).prop('checked', true);

                // A beviteli mezők kiürítése kártya váltáskor
                $('#trackingNumber').val('');
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

            $(document).on('input', '#trackingNumber', function() {
                // Ellenőrzés minden bevitelkor
                validateInputs();
            });

            $(document).on('change', '#personalSelect', function() {
                if ($(this).val() === 'other') {
                    $('#otherWorkerDetails').removeClass('d-none');
                } else {
                    $('#selectedotherWorkerDetails').addClass('d-none');
                    $('#other_employee_id').empty().append('<option value="0">-</option>').trigger('change');
                    $('#otherWorkerDetails').addClass('d-none');
                    $('#owemail').val('');
                    $('#owpassword').val('');
                }

                // Ellenőrzés minden váltáskor
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
                $('#transferModal').modal('show');
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

                let formData = new FormData();
                formData.append('type', 'waited');
                formData.append('transferMethod', transferMethod);
                formData.append('courierTrackingNumber', courierTrackingNumber);
                formData.append('courierSignCode', courierSignCode);
                formData.append('transferSignCode', transferSignCode);
                formData.append('shippingDate', shippingDate);
                formData.append('otherWorkerId', otherWorkerId);
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

                        if (transferMethod === 'personal') {
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
                            location.reload();
                        }
                    } else {
                        $.notify({ 
                            icon: "error", 
                            message: resp.data.message 
                        }, {
                            type: 'error',
                            timer: 3000, 
                            placement: { from: 'top', align: 'right' }
                        });
                        $('#confirmTransfer').prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.loginbtn', function() {
                $('#loginErrorAlert').addClass('d-none');
                let email = $('#owemail').val();
                let password = $('#owpassword').val();
                if (email && password) {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/check_login",
                        data: { email: email, password: password }
                    }).done(function(resp) {
                        if (resp.success) {
                            $('#otherWorkerDetails').addClass('d-none');
                            $('#selectedotherWorkerDetails').removeClass('d-none');
                            $('#other_employee_id').empty();
                            $('#other_employee_id')
                                .append('<option value="'+resp.employee.employee_id+'">'+resp.employee.lastname+' '+resp.employee.firstname+'</option>')
                                .trigger('change');
                            $('#confirmTransfer').show();
                        }else{
                            $('#loginErrorAlertText').text('Sikertelen belépés!');
                            $('#loginErrorAlert').removeClass('d-none');
                        }
                    });
                }
            });

        });
    </script>
@endpush