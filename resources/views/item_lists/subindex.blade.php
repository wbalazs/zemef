@extends('layouts.app', ['activePage' => 'item-list', 'activeButton' => 'item-list', 'title' => 'Zemef ERP', 'navName' => 'Kényszerlista ' ])
<style>
    .compact-form .form-group {
            margin-bottom: 12px;
        }
        .compact-form .form-label {
            font-weight: 500;
            margin-bottom: 4px;
            font-size: 0.9rem;
            color: #444;
        }
        .compact-form .form-control {
            height: 38px;
            font-size: 0.95rem;
        }
        .compact-form textarea.form-control {
            height: 60px;
            resize: vertical;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            margin-bottom: 12px;
            cursor: pointer;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        .upload-area:hover {
            background-color: #e9ecef;
            border-color: #4b6cb7;
        }
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        .preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-item .remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .modal-content {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .modal-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            padding: 12px 20px;
        }
        .submit-btn {
            background: linear-gradient(to right, #4b6cb7, #182848);
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            background: linear-gradient(to right, #3a5998, #15223d);
            transform: translateY(-2px);
        }
        .form-check {
            margin-top: 8px;
            margin-bottom: 12px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #4b6cb7;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #dee2e6;
        }
        #uploadText {
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
        }
        #modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 12px 20px;
        }
</style>
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            
                            <div class="row align-items-center">
                                <div class="col-2">
                                    <a href="{{ route('item_list.index') }}" class="btn btn-secondary btn-block mt-2"><i class="fa fa-arrow-left"></i> Vissza a listához</a>
                                </div>

                                <div class="col-8">
                                    <h3 class="mb-0"><th>Kényszerlista tételek</th></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="col-12 mt-2">
                            <div class="row align-items-end">
                                <div class="col-md-4 form-group mb-2">
                                    <label for="filter_zalogjegy" class="form-label">Zálogjegy</label>
                                    <input type="text" id="filter_zalogjegy" class="form-control form-control-sm" placeholder="Szűrés zálogjegy alapján...">
                                </div>
                                <div class="col-md-4 form-group mb-2">
                                    <label for="filter_status" class="form-label">Státusz</label>
                                    <select id="filter_status" class="form-control form-control-sm">
                                        <option value="">Mind</option>
                                        @foreach (\App\Enums\ItemListStatus::cases() as $status)
                                            <option value="{{ $status->label() }}">{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="stock_entries_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Azonosító</th>
                                        <th>Zálogjegy</th>
                                        <th>Fennálló hitel</th>
                                        <th>Kamat</th>
                                        <th>Késedelmi kamat</th>
                                        <th>Egyéb</th>
                                        <th>Esedékes közv. díjak</th>
                                        <th>Átvételi ár</th>
                                        <th>Becsérték</th>
                                        <th>Nettó súly</th>
                                        <th>Bruttó súly</th>
                                        <th>Karát</th>
                                        <th>Tárgyleírás</th>
                                        <th>Ügyfél</th>
                                        <th>Státusz</th>
                                        @can('manage-purchase_tickets', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Azonosító</th>
                                        <th>Zálogjegy</th>
                                        <th>Fennálló hitel</th>
                                        <th>Kamat</th>
                                        <th>Késedelmi kamat</th>
                                        <th>Egyéb</th>
                                        <th>Esedékes közv. díjak</th>
                                        <th>Átvételi ár</th>
                                        <th>Becsérték</th>
                                        <th>Nettó súly</th>
                                        <th>Bruttó súly</th>
                                        <th>Karát</th>
                                        <th>Tárgyleírás</th>
                                        <th>Ügyfél</th>
                                        <th>Státusz</th>
                                        @can('manage-purchase_tickets', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @php
                                        $rows = $itemList->json['tetelek'] ?? [];

                                        // sárga: átadás napja + türelmi idő még nem járt le
                                        $atadasNapja = $itemList->json['header']['atadas_napja'] ?? null;
                                        $isPastDeadline = false;
                                        if ($atadasNapja) {
                                            try {
                                                $deadline = \Carbon\Carbon::createFromFormat('Y.m.d', $atadasNapja)
                                                    ->addDays((int) config('zemef.kenyszerlista_sarga'));
                                                $isPastDeadline = now()->startOfDay()->greaterThanOrEqualTo($deadline);
                                            } catch (\Exception $e) {
                                                $isPastDeadline = false;
                                            }
                                        }
                                    @endphp
                                    @foreach ($rows as $item)
                                        @php
                                            $rowStatus = (int) ($item['status'] ?? 0);
                                            $customer = '';
                                            if (isset($item['ugyfel']) && is_array($item['ugyfel'])) {
                                                $customer = collect([
                                                    $item['ugyfel']['nev'] ?? null,
                                                    $item['ugyfel']['iranyitoszam'] ?? null,
                                                    $item['ugyfel']['telepules'] ?? null,
                                                    $item['ugyfel']['cim'] ?? null,
                                                ])->filter()->implode(', ');
                                            }

                                            // zöld: visszavásárolva vagy termékké alakítva, felülírja a sárgát
                                            $rowClass = $isPastDeadline ? 'table-warning' : '';
                                            if ($rowStatus === \App\Enums\ItemListStatus::BOUGHT_BACK->value || $rowStatus === \App\Enums\ItemListStatus::PRODUCT->value) {
                                                $rowClass = 'table-success';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $item['sor'] ?? '-' }}</td>
                                            <td>{{ $item['zalogjegy_szama'] ?? '-' }}</td>
                                            <td>{{ $item['fennallo_hitel'] ?? '-' }}</td>
                                            <td>{{ $item['kamat'] ?? '-' }}</td>
                                            <td>{{ $item['kesedelmi_kamat'] ?? '-' }}</td>
                                            <td>{{ $item['egyeb'] ?? '-' }}</td>
                                            <td>{{ $item['esedekes_kozv_dijak'] ?? '-' }}</td>
                                            <td>{{ $item['atveteli_ar'] ?? '-' }}</td>
                                            <td>{{ $item['becsertek'] ?? '-' }}</td>
                                            <td>{{ $item['netto_suly'] ?? '-' }}</td>
                                            <td>{{ $item['brutto_suly'] ?? '-' }}</td>
                                            <td>{{ $item['karat'] ?? ($item['karat_finomsag'] ?? '-') }}</td>
                                            <td>{{ $item['targyleiras'] ?? '-' }}</td>
                                            <td>{{ $customer !== '' ? $customer : '-' }}</td>
                                            <td>{{ \App\Enums\ItemListStatus::tryFrom($rowStatus)?->label() ?? '-' }}</td>
                                            
                                            @can('manage-purchase_tickets', App\User::class)
                                            <td>
                                                <a href="{{ route('item_list.row.show', ['itemList' => $itemList->id, 'sor' => $item['sor'] ?? 0]) }}"
                                                   class="btn btn-link text-info p-0 mr-2"
                                                   title="Megtekintés">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @if($itemList->status < 2)
                                                    @if((int)$rowStatus === \App\Enums\ItemListStatus::BOUGHT_BACK->value)
                                                        <span class="text-muted">Visszavásárolva</span>
                                                    @else
                                                        <a href="{{ route('item_list.row.edit', ['itemList' => $itemList->id, 'sor' => $item['sor'] ?? 0]) }}"
                                                           class="btn btn-link btn-warning p-0 mr-2"
                                                           title="Szerkesztés">
                                                            <i class="fa fa-edit"></i>
                                                        </a>

                                                        <a href="#"
                                                               onclick="closeItemList('{{ $item['sor'] ?? '' }}', '{{ $item['zalogjegy_szama'] ?? '' }}'); return false;"
                                                               class="fa fa-check text-info mr-2"
                                                               title="Lezárás"></a>

                                                        <a href="#"
                                                           onclick="confirmBoughtBack('{{ $item['sor'] ?? '' }}', '{{ $item['zalogjegy_szama'] ?? '' }}'); return false;"
                                                           class="fa fa-undo text-secondary"
                                                           title="Visszavásárlás"></a>

                                                           <a href="{{ route('item_list.row.transfer', ['itemList' => $itemList->id, 'sor' => $item['sor'] ?? 0]) }}"
                                                           class="btn btn-link text-primary p-0 ml-2"
                                                           title="Átemelés a rendszerbe (ékszer vásárlás rögzítése)"
                                                           onclick="return confirm('Biztosan átemeli ezt a tételt az ékszer vásárlás rendszerbe?');">
                                                            <i class="fa fa-file-import"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    Lezárva
                                                    @if((int)$rowStatus === \App\Enums\ItemListStatus::PRODUCT->value)
                                                        <span class="text-success ml-2" title="Átemelve a rendszerbe"><i class="fa fa-check-circle"></i></span>
                                                    @elseif((int)$rowStatus !== \App\Enums\ItemListStatus::BOUGHT_BACK->value)
                                                        
                                                    @endif
                                                @endif
                                            </td>
                                            @endcan
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
@endsection

@push('modals')
    {{-- multi route modal --}}
    @include('item_lists.modals.item_list_status')
    @include('item_lists.modals.item_list_import')
@endpush


@push('js')
    <script type="text/javascript">
        const BOUGHT_BACK_STATUS = {{ \App\Enums\ItemListStatus::BOUGHT_BACK->value }};

        function onlyNumbers(str) {
            return /^\d+$/.test(str);
        }
        $(document).ready(function() {

            $('select[name=business_id]').change(function(){
                let business_id = $(this).val();

                if(onlyNumbers(business_id)){
                    //Raktárak lekérése üzlet azonosító alapján a munkavállalóhoz
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_storages_by_business_id_employee_consignment_receipt",
                        data: { business_id: business_id }
                    })
                    .done(function( resp ) {
                        $('select[name=storage_id]').empty();
                        $.each(resp, function(index, warehouse) {
                            $('select[name=storage_id]').append($('<option>', {
                                value: warehouse.warehouse_id,
                                text: warehouse.name
                            }));
                        });    
                    });

                }else{
                    let textdef = '<option value="">- válasszon -</option>';
                    $('select[name=storage_id]').empty();
                    $('select[name=storage_id]').append(textdef);
                }
            });

            if ($.fn.dataTable.isDataTable('#datatables')) {
                $('#datatables').DataTable().destroy();
            }

            var table = $('#datatables').DataTable({
                dom: 'lrtip',
                initComplete: function() {
                    let api = this.api();

                    let zalogjegyColIdx = 1;
                    $(api.column(zalogjegyColIdx).footer()).empty();

                    let statusColIdx = 14;
                    $(api.column(statusColIdx).footer()).empty();
                }
            });

            $('#filter_zalogjegy').on('keyup change clear', function() {
                table.column(1).search(this.value).draw();
            });

            $('#filter_status').on('change', function() {
                let val = this.value;
                table.column(14).search(val ? '^' + val + '$' : '', true, false).draw();
            });

            $('body').on('click', '.applyBtn', function(e) {
                var hlpr = $('input[placeholder=Sorszám]').val();
                $('input[placeholder=Sorszám]').val(Math.random());
                $('input[placeholder=Sorszám]').trigger('change');
                $('input[placeholder=Sorszám]').val(hlpr);
                $('input[placeholder=Sorszám]').trigger('change');
            });

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
            
            $('#itemlistStatusModalYes').click(function() {
                
                // Close the modal
                $('#itemlistStatusModal').modal('hide');
                const item_row1 = $('#modal_itemlist_id').val();
                const item_sor = $('#modal_item_sor').val();
                const status = parseInt($('#modal_new_status').val(), 10) || 1;

                $.ajax({
                    url: "{{ route('item_list.substatus') }}", // Use the dynamically generated URL
                    method: "POST",
                    delay: 250,
                    data: {
                        item_list_id: {{$itemList->id}},
                        item_row1: item_row1,
                        item_sor: item_sor,
                        status: status,
                    }
                })
                .done(function( msg ) {
                    location.reload();
                })
            });
            $('#importModalYes').click(function() {
                //check
                if(! ($('#sei_description').val().length &&
                    $('#sei_name').val().length &&
                    $('#sei_factory_number').val().length &&
                    $('#sei_weight').val().length &&
                    $('#sei_quantity').val().length &&
                    $('#sei_oneprice').val().length &&
                    $('#sei_price').val().length)
                ) {
                     $('#sei_error').show();
                     return 1;
                } else {
                    $('#sei_error').hide();
                }

                // Close the modal
                $('#itemlistStatusModal').modal('hide');

                $.ajax({
                    url: "{{ route('item-list-items.store') }}", // Use the dynamically generated URL
                    method: "POST",
                    delay: 250,
                    data: {
                        item_list_id: {{$itemList->id}},
                        name: $('#sei_name').val(),
                        //is_unique: $('#sei_is_unique').is(':checked'),
                        is_unique: $('#sei_is_unique').is(':checked') ? 1 : 0,
                        description: $('#sei_name').val(),
                        factory_number: $('#sei_factory_number').val(),
                        weight: $('#sei_weight').val(),
                        quantity: $('#sei_quantity').val(),
                        oneprice: $('#sei_oneprice').val(),
                        price: parseInt($('#sei_price').val()),
                        storage_id: $('#storage_id').val(),
                        business_id: $('#business_id').val(),
                        imageInput: $('#imageInput').val(),
                    }
                })
                .done(function( msg ) {
                    $.notify({
                            icon: "done",
                            message: "A mentés sikeresen megtörtént."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 2000);

                })
            });

            //

            $('.sei_quantity, #sei_oneprice').on('input', calculateTotal);
            
            // Handle image upload preview
            $('#uploadText').on('click', function(e) {
                    // Prevent event from bubbling up and causing recursion
                    e.stopPropagation();
                    $('#imageInput').trigger('click');
                });
            
            $('#imageInput').on('change', function() {
                const files = this.files;
                const previewContainer = $('#previewContainer');
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.match('image.*')) continue;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = $(
                            '<div class="preview-item">' +
                                '<img src="' + e.target.result + '" alt="Preview">' +
                                '<div class="remove-btn"><i class="fas fa-times text-danger"></i></div>' +
                            '</div>'
                        );
                        
                        previewContainer.append(previewItem);
                        
                        previewItem.find('.remove-btn').on('click', function() {
                            previewItem.remove();
                        });
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Allow drag and drop for images
            $('#uploadArea').on('dragover', function(e) {
                e.preventDefault();
                $(this).css('background-color', '#e9ecef');
                $(this).css('border-color', '#4b6cb7');
            });
            
            $('#uploadArea').on('dragleave', function(e) {
                e.preventDefault();
                $(this).css('background-color', '#f8f9fa');
                $(this).css('border-color', '#dee2e6');
            });
            
            $('#uploadArea').on('drop', function(e) {
                e.preventDefault();
                $(this).css('background-color', '#f8f9fa');
                $(this).css('border-color', '#dee2e6');
                
                const files = e.originalEvent.dataTransfer.files;
                $('#imageInput').prop('files', files);
                $('#imageInput').trigger('change');
            });
            
            // Form submission
            $('#import_stock_entry_form').on('submit', function(e) {
                e.preventDefault();
                
                const requiredFields = $('#sei_name, .sei_quantity, #sei_oneprice');
                let isValid = true;
                
                requiredFields.each(function() {
                    if (!$(this).val().trim()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    $('#sei_error').show();
                    return;
                }
                
                $('#sei_error').hide();
                
                // Simulate form submission
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...').prop('disabled', true);
                
                setTimeout(function() {
                    
                    $('#stockModal').modal('hide');
                    $('#submitBtn').html('<i class="fas fa-paper-plane me-2"></i> Submit Stock Entry').prop('disabled', false);
                }, 1500);
            });
            
            // Initialize total price calculation
            calculateTotal();

            //
        });
        
        // Calculate total price
        function calculateTotal() {
            const quantity = $('.sei_quantity').val() || 0;
            const unitPrice = $('#sei_oneprice').val() || 0;
            const total = (quantity * unitPrice).toFixed(2);
            $('#sei_price').val(total);
        }


        function closeItemList(itemSor, ticketNumber){
            $('#itemlistStatusModalText').text('Biztosan le akarja zárni?');
            $('#modal_new_status').val('1');
            $('#modal_item_sor').val(itemSor);
            $('#itemlistStatusModal').modal('show');
            $('#modal_itemlist_id').val(ticketNumber);
        }

        function confirmBoughtBack(itemSor, ticketNumber){
            $('#itemlistStatusModalText').text('Biztosan vissza akarja vásárolni ezt a tételt?');
            $('#modal_new_status').val(BOUGHT_BACK_STATUS);
            $('#modal_item_sor').val(itemSor);
            $('#modal_itemlist_id').val(ticketNumber);
            $('#itemlistStatusModal').modal('show');
        }
        
        function closeItemImport(sei_name,sei_description,sei_factory_number,sei_weight){
            $('#sei_name').val(sei_name);
            $('#sei_description').val(sei_description);
            $('#sei_factory_number').val(sei_factory_number);
            $('#sei_weight').val(sei_weight);

            $('#importModal').modal('show');
        }


    </script>
@endpush