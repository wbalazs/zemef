@extends('layouts.app', ['activePage' => 'actual_stock', 'activeButton' => 'actual_stock', 'title' => 'Zemef ERP', 'navName' => 'Aktuális készlet' ])

@section('content')

<style>
    #warehouseImagePreviews {
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
    #items-table .table-tools-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    #items-table .toolbar {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #items-table .table-tools-row .dt-search,
    #items-table .table-tools-row .dataTables_filter {
        margin-left: auto;
    }
</style>



    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <hr>
                        <!-- Szűrők gomb -->
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary mb-3" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                    Szűrők ⌄
                                </button>
                            </div>
                        </div>

                        <!-- Szűrőblokk -->
                        <div class="collapse" id="filterCollapse">
                            <div class="card card-body border shadow-sm">
                                <form method="GET" action="{{ route('actual_stock') }}">
                                    <input type="hidden" name="filter" value="1">
                                    <div class="row">
                                        {{-- Beszállítók --}}
                                        <div class="col-md-2 mb-3">
                                            <label>Beszállító(k)</label>
                                            <select name="supplier_ids[]" class="form-control select2" multiple>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->customer_id }}" {{ in_array($supplier->customer_id, request('supplier_ids', [])) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Üzlet(ek) --}}
                                        <div class="col-md-3 mb-3">
                                            <label>Üzlet(ek)</label>
                                            <select name="business_ids[]" class="form-control select2" multiple>
                                                @foreach ($businesses as $business)
                                                    <option value="{{ $business->business_id }}" {{ in_array($business->business_id, request('business_ids', [])) ? 'selected' : '' }}>{{ $business->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Raktár(ak) --}}
                                        <div class="col-md-3 mb-3">
                                            <label>Raktár(ak)</label>
                                            <select name="warehouse_ids[]" class="form-control select2" multiple>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->warehouse_id }}" {{ in_array($warehouse->warehouse_id, request('warehouse_ids', [])) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Beszerzés dátum -tól / -ig --}}
                                        <div class="col-md-2 mb-3">
                                            <label>Beszerzés -tól</label>
                                            <input type="date" name="purchase_from" class="form-control" value="{{ request('purchase_from') }}">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label>Beszerzés -ig</label>
                                            <input type="date" name="purchase_to" class="form-control" value="{{ request('purchase_to') }}">
                                        </div>
                                    </div>

                                    
                                    <div class="row">

                                        {{-- Súly -tól / -ig --}}
                                        <div class="col-md-2 mb-3">
                                            <label>Súly (g) -tól</label>
                                            <input type="number" step="0.1" name="weight_min" class="form-control" value="{{ request('weight_min') }}">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label>Súly (g) -ig</label>
                                            <input type="number" step="0.1" name="weight_max" class="form-control" value="{{ request('weight_max') }}">
                                        </div>

                                        {{-- Besz. grammár -tól / -ig --}}
                                        <div class="col-md-2 mb-3">
                                            <label>Besz. grammár -tól</label>
                                            <input type="number" step="1" name="price_per_gram_min" class="form-control" value="{{ request('price_per_gram_min') }}">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label>Besz. grammár -ig</label>
                                            <input type="number" step="1" name="price_per_gram_max" class="form-control" value="{{ request('price_per_gram_max') }}">
                                        </div>

                                        {{-- Státusz(ok) --}}
                                        <div class="col-md-2 mb-3">
                                            <label>Státusz(ok)</label>
                                            <select name="status_ids[]" class="form-control select2" multiple>
                                                @foreach(\App\Http\Controllers\StockController::STATUSES as $key => $value)
                                                    <option value="{{ $key }}" {{ in_array($key, request('status_ids', [])) ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Keresés gomb --}}
                                        <div class="col-md-2 align-self-end">
                                            <button style="margin-bottom: 16px;" type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Keresés</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="stock-karat-filter">Karát</label>
                                            <select id="stock-karat-filter" name="karat_ids[]" class="form-control select2" multiple>
                                                @foreach ($karats as $karat)
                                                    <option value="{{ $karat->karat_id }}" {{ in_array($karat->karat_id, (array) request('karat_ids', [])) ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                                    

                        <hr>
                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        <div class="table-responsive py-4" id="items-table">
                            <div class="toolbar">
                                @if(Auth::user()->hasPermission('create', App\Models\WarehouseTransfer::class))
                                    <a class="btn btn-sm btn-primary warehouse_transfer"><i class="fa fa-plus"></i> Raktárközi átadás</a>
                                @endif
                            </div>
                            <table id="actual_stock_table" data-name="actual_stock_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>+-</th>
                                        <th>Cikkszám</th>
                                        <th>Fémjel</th>
                                        <th>Megnevezés</th>
                                        <th>Karát</th>
                                        <th>Súly</th>
                                        <th>Méret</th>
                                        <th>Üzlet</th>
                                        <th>Eladási ár</th>
                                        <th>Beszerzési ár</th>
                                        <th>Szállító</th>
                                        <th>Állapot</th>
                                        <th>Beszerzés dátuma</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Összesen</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Quick image modal -->
<div class="modal fade" id="inventoryQuickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tétel képek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="quick_inventory_gallery" class="row"></div>
            </div>
        </div>
    </div>
</div>



<!-- Warehouse_modal -->
<div class="modal fade" id="warehouse_transfer_modal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Raktárközi átadás</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Átadó raktár</label>
                        <select class="form-control select2" id="warehouse_transfer_from">
                            <option value="">-- Válasszon --</option>
                            @foreach ($available_warehouses as $warehouse)
                                <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Átvevő raktár</label>
                        <select class="form-control select2" id="warehouse_transfer_to">
                            <option value="">-- Válasszon --</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label>Termék megjegyzés (Pl: ötvösnek)</label>
                        <textarea class="form-control" id="transfer_items_note"></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8">
                        <label>Termékek</label>
                        <select class="form-control select2" id="warehouse_transfer_items">
                            <option value="">-- Válasszon --</option>
                        </select>
                    </div>
                    <div class="col-8 col-md-2">
                        <label>Menny.</label>
                        <input type="number" class="form-control" id="warehouse_transfer_quantity" value="1">
                    </div>
                    <div class="col-4 col-md-2" style="align-self:end;margin-bottom:-5px !important;">
                        <label>&nbsp;</label>
                        <button type="button" disabled class="btn btn-success" id="warehouse_transfer_add_item"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-4">
                        <label for="sign_code">Aláíró kód:</label>
                        <input type="text" class="form-control" oninput="handlePasswordInput(this)" id="sign_code" autocomplete="one-time-code">
                    </div>
                    <div class="col-lg-8">
                        <label for="warehouse_images">Fájlok feltöltése:</label>
                        <input type="file" multiple class="form-control" id="warehouse_images">

                        <!-- Képek előnézete -->
                        <div id="warehouseImagePreviews" class="mt-2">
                            <!-- Az ide feltöltött képek előnézetei itt jelennek meg -->
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-danger d-none" id="ErrorAlert">
                            <span id="ErrorAlertText"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="table-responsive py-4" id="items-table-2">
                            <table id="datatables2" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Leírás</th>
                                        <th>Mennyiség</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody id="items-table-2-rows">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-7">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i> Kilépés</button>
                <button type="button" hidden class="btn btn-primary" id="confirmTransfer"><i class="fa fa-save"></i> Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- pricing_modal -->
<div class="modal fade" id="pricing_modal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Csoportos árazás</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                       <table class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100% ">
                            <thead>
                                <tr>
                                    <th>Termék</th>
                                    <th>Jelenlegi ár</th>
                                    <th>Új ár</th>
                                </tr>
                            </thead>
                            <tbody id="pricing_table_rows">
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer mt-7">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i> Kilépés</button>
                <button type="button" hidden class="btn btn-primary" id="confirmPricing"><i class="fa fa-save"></i> Mentés</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script type="text/javascript">
     // Segédfüggvény HTML escaping-hez
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        function handlePasswordInput(inputElement) {
            inputElement.type = 'password';
        }

        function formatInventoryItem(item) {
             if (item.loading) return item.text;
            var markup = "<div><b>" + escapeHtml(item.prodname) + "</b>";
            if (item.description) markup += "<br/><small>" + escapeHtml(item.description) + "</small>";
            markup += "<br/><span class='text-muted'>Készlet: " + item.stock + " db, Súly: " + item.weight + " g</span></div>";
            return markup;
        }

        function formatInventoryItemSelection(item) {
            return item.prodname || item.text;
        }
        $(document).ready(function() {
/*
            let table = $('#datatables').DataTable();

            table.on('draw', function() {
                setupDrawCallback();
            });
*/
            // Initialize DataTable with server-side processing
            let dataTableConfig = {
                processing: true,
                serverSide: true,
                 language: {
                    info: "Megjelenítve _START_ - _END_ összesen: _MAX_ találat",
                    infoFiltered: "",
                    // vagy ha teljesen el akarod hagyni:
                    // infoFiltered: ""
                },
                ajax: {
                    url: "{{ route('stocks.ajax-data') }}",
                    type: 'GET',
                    data: function(d) {
                        // Get all filter values
                        d.filter_params = 1; // Indicate we want filtered data
                        d.supplier_ids = $('select[name="supplier_ids[]"]').val();
                        d.business_ids = $('select[name="business_ids[]"]').val();
                        d.warehouse_ids = $('select[name="warehouse_ids[]"]').val();
                        d.purchase_from = $('input[name="purchase_from"]').val();
                        d.purchase_to = $('input[name="purchase_to"]').val();
                        d.weight_min = $('input[name="weight_min"]').val();
                        d.weight_max = $('input[name="weight_max"]').val();
                        d.price_per_gram_min = $('input[name="price_per_gram_min"]').val();
                        d.price_per_gram_max = $('input[name="price_per_gram_max"]').val();
                        d.status_ids = $('select[name="status_ids[]"]').val();
                        d.karat_ids = $('select[name="karat_ids[]"]').val();
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                    { data: 'inventory_id_text', name: 'inventory_id' },
                    { data: 'inventory_hallmark', name: 'inventory_hallmark' },
                    { data: 'inventory_termek_tipus', name: 'inventory_termek_tipus' },
                    { data: 'inventory_karat', name: 'inventory_karat' },
                    { data: 'product_weight', name: 'product_weight' },
                    { data: 'inventory_size', name: 'inventory_size' },
                    { data: 'business_name', name: 'business_name' },
                    { data: 'inventory_eladasi_ar', name: 'inventory_eladasi_ar' },
                    { data: 'inventory_beszerzesi_ar', name: 'inventory_beszerzesi_ar' },
                    { data: 'inventory_vzj', name: 'inventory_vzj' },
                    { data: 'inventory_stock_status', name: 'inventory_stock_status' },
                    { data: 'inventory_created_at', name: 'inventory_created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
                pageLength: 25,

                footerCallback: function() {
                    let api = this.api();
                    let response = api.ajax.json();
                    let totals = response && !response.error ? response.totals : null;
                    let weightFormat = new Intl.NumberFormat('hu-HU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    let priceFormat = new Intl.NumberFormat('hu-HU', { maximumFractionDigits: 0 });

                    $(api.column(5).footer()).text(totals ? weightFormat.format(totals.weight) + ' g' : '');
                    $(api.column(8).footer()).text(totals ? priceFormat.format(totals.selling_price) + ' Ft' : '');
                    $(api.column(9).footer()).text(totals ? priceFormat.format(totals.purchase_price) + ' Ft' : '');
                },

                drawCallback: function(settings) {
                    setupDrawCallback();
                }
            };

            // If DataTables Buttons extension is available, enable column visibility selector.
            if ($.fn.dataTable && $.fn.dataTable.Buttons) {
                dataTableConfig.dom = 'Bfrtip';
                dataTableConfig.buttons = [
                    {
                        extend: 'colvis',
                        text: 'Oszlopok',
                        columns: ':not(:first-child):not(:last-child)'
                    }
                ];
            }

            let table = $('#actual_stock_table').DataTable(dataTableConfig);

            function placeTableControlsInline() {
                let $itemsTable = $('#items-table');
                let $toolbar = $itemsTable.find('.toolbar');
                let $search = $itemsTable.find('.dt-search').first();

                if (!$search.length) {
                    $search = $('#actual_stock_table_filter').first();
                }

                let $toolsRow = $itemsTable.find('.table-tools-row');
                if (!$toolsRow.length) {
                    $toolsRow = $('<div class="table-tools-row"></div>');
                    $toolbar.before($toolsRow);
                }

                if ($toolbar.length && !$toolbar.parent().is($toolsRow)) {
                    $toolsRow.append($toolbar);
                }

                if ($search.length && !$search.parent().is($toolsRow)) {
                    $toolsRow.append($search);
                }
            }

            placeTableControlsInline();
            table.on('draw', function() {
                placeTableControlsInline();
            });

            if ($.fn.dataTable && $.fn.dataTable.Buttons && table.buttons) {
                table.buttons().container().appendTo('#items-table .toolbar');
                placeTableControlsInline();
            }

    
            
            // Refresh table after successful actions
            function refreshTable() {
                table.ajax.reload(null, false); // false means don't reset paging
            }
            setupDrawCallback();
            

            function setupDrawCallback() {
                $('.note-checkbox').off('change');
                $('.note-checkbox').on('change', function() {
                    let warehouse_id = $(this).data('warehouse_id');
                    let item_id = $(this).data('item_id');
                    let business_id = $(this).data('business_id');
                    let isChecked = $(this).prop('checked');
                    updateTransferButtonVisibility();
                });
            }


            function updateTransferButtonVisibility() {
                let hasCheckedCheckbox = $('.note-checkbox:checked').length > 0;
                if (hasCheckedCheckbox) {
                    $('.pricing').show();
                } else {
                    $('.pricing').hide();
                }
            }

            $(document).on('click', '.sell', function() {
                let product_id = $(this).data('id');
                let warehouse_id = $(this).data('warehouse_id');

                swal({
                    title: 'Termék kivezetése',
                    type: 'warning',
                    html: "Biztosan ki szeretné vezetni az alábbi terméket?<br><div class='row'><div class='col-4'><label class='form-label'>Dátum</label><input type='date' id='prod_out_date' value='" + new Date().toISOString().slice(0,10) + "' class='form-control'/></div><div class='col-3'><label class='form-label'>Ár</label><input type='number' id='prod_out_price' value='0' class='form-control'/></div><div class='col-5'><label class='form-label'>Ok</label><input type='text' id='prod_out_reason' class='form-control'/></div></div>",
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem'
                }, function(isConfirm) {
                    if (isConfirm) {
                        let prod_out_date = $('#prod_out_date').val();
                        let prod_out_reason = $('#prod_out_reason').val();
                        let prod_out_price = $('#prod_out_price').val();
                        $.ajax({
                            method: "POST",
                            url: "/ajax/prod_sell_out",
                            data: { 
                                product_id: product_id, 
                                warehouse_id: warehouse_id,
                                prod_out_date: prod_out_date,
                                prod_out_reason: prod_out_reason,
                                prod_out_price: prod_out_price
                            },
                            success: function(resp) {
                                if (!resp.success) {
                                    $.notify({ message: resp.message }, { type: 'danger', timer: 3000 });
                                } else {
                                    $.notify({ message: "A termék kivezetése sikeresen megtörtént!" }, { type: 'success', timer: 3000 });
                                    setTimeout(function() { location.reload(); }, 2000);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.quick-view-images', function() {
                let inventoryId = $(this).data('inventory-id');
                if (!inventoryId) {
                    return;
                }

                $('#quick_inventory_gallery').html('<div class="col-12 text-center text-muted">Betöltés...</div>');
                $('#inventoryQuickViewModal').modal('show');

                $.post('/load_inventory_gallery', {
                    inventory_id: inventoryId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(html) {
                    $('#quick_inventory_gallery').html(html);
                    $('#quick_inventory_gallery .delete_inventory_image').remove();
                }).fail(function() {
                    $('#quick_inventory_gallery').html('<div class="col-12 text-danger">Hiba a képek betöltése közben.</div>');
                });
            });

            $(document).on('click', '#pricing_btn', function() {
                let itemsData = [];

                $('.note-checkbox:checked').each(function() {
                    let item = {
                         warehouse_id: $(this).data('warehouse_id'),
                        item_id: $(this).data('item_id'),
                        business_id: $(this).data('business_id')
                    };

                    itemsData.push(item);
                });

                //AJAX
                $.ajax({
                    method: "POST",
                    url: "/ajax/group_pricing",
                    data: { items: itemsData }
                }).done(function(resp) {
                    if(!resp.success){
                        $.notify({ icon: "error", message: resp.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        return;
                    }else{
                        $('#pricing_table_rows').empty().html(resp.tr);
                        $('#confirmPricing').prop('disabled', true);
                        $('#pricing_modal').modal('show');
                    }
                });
            });

            // Raktárközi átadás modal esemény
            $(document).on('click', '.warehouse_transfer', function() {
                $('#warehouse_transfer_modal').modal('show');
                $('#transfer_items_note').val('');
                // Töröljük a régi select2-t, ha van
                if ($('#warehouse_transfer_items').data('select2')) {
                    $('#warehouse_transfer_items').select2('destroy');
                }
                $('#warehouse_transfer_items').empty().append('<option value="">-- Válasszon --</option>');
                // Alaphelyzetbe állítjuk a raktár választókat
                $('#warehouse_transfer_from').val(null).trigger('change');
                $('#warehouse_transfer_to').empty().html('<option value="">-- Válasszon --</option>');
                $('#items-table-2-rows').empty();
                $('#warehouse_transfer_add_item').prop('disabled', true);
                $('#confirmTransfer').attr('hidden', true);
                $('#ErrorAlert').addClass('d-none');
            });
           
            $(document).on('change', '#warehouse_transfer_from', function() {
                $('#ErrorAlert').addClass('d-none');
                let warehouse_id = $(this).val();
                if (warehouse_id != '') {
                    // Célraktár lista frissítése
                    $.ajax({
                        url: "/ajax/get_warehouses_transfer_from_exclude_this",
                        method: 'POST',
                        data: { warehouse_id: warehouse_id },
                        success: function(data) {
                            $('#warehouse_transfer_to').empty().html(data.html);
                        }
                    });

                    // Megszüntetjük a korábbi select2-t, ha van
                    if ($('#warehouse_transfer_items').data('select2')) {
                        $('#warehouse_transfer_items').select2('destroy');
                    }
                    $('#warehouse_transfer_items').empty().append('<option value="">-- Válasszon --</option>');

                    // Inicializáljuk a select2-t AJAX módban, a modalhoz kötve
                    $('#warehouse_transfer_items').select2({
                        theme: 'bootstrap4',
                        placeholder: '-- Válasszon vagy keressen --',
                        allowClear: true,
                        dropdownParent: $('#warehouse_transfer_modal'), // KULCS: modalhoz kötés
                        escapeMarkup: function(m) { return m; },  // Allow HTML
                        ajax: {
                            url: '/ajax/get_inventory_items_by_warehouse',
                            dataType: 'json',
                            method: 'POST',
                            delay: 300,
                            data: function(params) {
                                return {
                                    warehouse_id: warehouse_id,
                                    search: params.term || '',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                };
                            },
                            processResults: function(data) {
                                return { results: data.results };
                            },
                            cache: true
                        },
                        minimumInputLength: 1,
                        templateResult: formatInventoryItem,
                        templateSelection: formatInventoryItemSelection
                    });

                    // Ha már volt érték, töröljük
                    $('#warehouse_transfer_items').val(null).trigger('change');
                    $('#warehouse_transfer_add_item').prop('disabled', true);
                } else {
                    $('#warehouse_transfer_to').empty().html('<option value="">-- Válasszon --</option>');
                    if ($('#warehouse_transfer_items').data('select2')) {
                        $('#warehouse_transfer_items').select2('destroy');
                    }
                    $('#warehouse_transfer_items').empty().html('<option value="">-- Válasszon --</option>');
                    $('#warehouse_transfer_add_item').prop('disabled', true);
                    $('#items-table-2-rows').empty();
                }
            });

        // Mennyiség változás ellenőrzése (a select2 kiválasztás után)
        $(document).on('change input', '#warehouse_transfer_quantity', function() {
            let selected = $('#warehouse_transfer_items').select2('data')[0];
            let maxStock = selected ? selected.stock : 0;
            let quantity = parseInt($(this).val(), 10);
            let from = $('#warehouse_transfer_from').val();
            let to = $('#warehouse_transfer_to').val();

            if (selected && from && to && quantity > 0 && quantity <= maxStock) {
                $('#warehouse_transfer_add_item').prop('disabled', false);
                $('#ErrorAlert').addClass('d-none');
            } else {
                $('#warehouse_transfer_add_item').prop('disabled', true);
                if (quantity > maxStock && selected) {
                    $('#ErrorAlertText').html('A kért mennyiség meghaladja a készletet!');
                    $('#ErrorAlert').removeClass('d-none');
                }
            }
        });

        // Termék hozzáadása a listához
        $(document).on('click', '#warehouse_transfer_add_item', function() {
            $('#ErrorAlert').addClass('d-none');
            let selected = $('#warehouse_transfer_items').select2('data')[0];
            if (!selected) return;

            let inventoryId = selected.id;
            let prodname = selected.prodname;
            let description = selected.description;
            let maxStock = selected.stock;
            let from = $('#warehouse_transfer_from').val();
            let to = $('#warehouse_transfer_to').val();
            let transfer_items_note = $('#transfer_items_note').val();
            let quantity = parseInt($('#warehouse_transfer_quantity').val(), 10);

            // Ellenőrizzük, hogy a tétel már szerepel-e a listában
            let check = [];
            $('#items-table-2-rows tr').each(function() {
                let existingId = $(this).find('td').data('row-inventory-id');
                if (existingId) check.push(existingId);
            });

            if (inventoryId && from && to && quantity > 0 && quantity <= maxStock && !check.includes(inventoryId)) {
                let html = '<tr>';
                html += '<td data-transfer_items_note="' + escapeHtml(transfer_items_note) + '" data-row-inventory-id="' + inventoryId + '" data-row-quantity="' + quantity + '">' + escapeHtml(prodname) + '</td>';
                html += '<td>' + escapeHtml(description) + '</td>';
                html += '<td>' + quantity + ' db</td>';
                html += '<td><button class="btn btn-sm btn-danger row_delete"><i class="fa fa-trash"></i></button></td>';
                html += '</tr>';

                $('#items-table-2-rows').append(html);
                checkConfirmButtonVisibility();
                $('#warehouse_transfer_items').val(null).trigger('change');
                $('#warehouse_transfer_quantity').val('1');
                $('#warehouse_transfer_add_item').prop('disabled', true);
            } else if (check.includes(inventoryId)) {
                $('#ErrorAlertText').html('A termék már szerepel a listában!');
                $('#ErrorAlert').removeClass('d-none');
            } else if (quantity > maxStock) {
                $('#ErrorAlertText').html('A kért mennyiség meghaladja a készletet!');
                $('#ErrorAlert').removeClass('d-none');
            }
        });

        // ConfirmTransfer esemény
        $(document).on('click', '#confirmTransfer', function() {
            $('#ErrorAlert').addClass('d-none');
            let from = $('#warehouse_transfer_from').val();
            let to = $('#warehouse_transfer_to').val();
            let sign_code = $('#sign_code').val();
            let items = [];
            $('#items-table-2-rows tr').each(function() {
                let td = $(this).find('td:first');
                let inventoryId = td.data('row-inventory-id');
                let quantity = td.data('row-quantity');
                let transfer_items_note = td.data('transfer_items_note');
                if (inventoryId) {
                    items.push({
                        inventory_id: inventoryId,
                        quantity: quantity,
                        transfer_items_note: transfer_items_note
                    });
                }
            });

            if (from && to && items.length > 0) {
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('from', from);
                formData.append('to', to);
                formData.append('sign_code', sign_code);
                items.forEach((item, index) => {
                    formData.append(`items[${index}][inventory_id]`, item.inventory_id);
                    formData.append(`items[${index}][quantity]`, item.quantity);
                    formData.append(`items[${index}][transfer_items_note]`, item.transfer_items_note);
                });
                $.each(currentFiles, function(index, file) {
                    formData.append('images[]', file);
                });

                $.ajax({
                    url: "/ajax/warehouse_transfer",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.success) {
                            $('#confirmTransfer').prop('disabled', true);
                            $('#warehouse_transfer_modal').modal('hide');
                            $.notify({
                                icon: "done",
                                message: "A raktárközi átadás sikeresen megtörtént!"
                            }, {
                                type: 'success',
                                timer: 3000,
                                placement: { from: 'top', align: 'right' }
                            });
                            //setTimeout(function() { location.reload(); }, 2000);
                        } else {
                            $('#ErrorAlertText').html(data.message);
                            $('#ErrorAlert').removeClass('d-none');
                        }
                    }
                });
            }
        });
         $(document).on('click', '#items-table-2-rows .row_delete', function() {
            $('#ErrorAlert').addClass('d-none');
            $(this).closest('tr').remove();
            checkConfirmButtonVisibility();
        });
        

            $('.search_select2').on('change', function() {
                let product_name = $('select[name="product_id_search"] option:selected').text();
                let business_name = $('select[name="business_id_search"] option:selected').text();
                let warehouse_name = $('select[name="warehouse_id_search"] option:selected').text();

                // Szűrés a DataTables táblázatban a szöveges értékek alapján
                table.column(1).search(product_name !== '-- Válasszon --' ? product_name : '', true, false);
                table.column(4).search(business_name !== '-- Válasszon --' ? business_name : '', true, false);
                table.column(5).search(warehouse_name !== '-- Válasszon --' ? warehouse_name : '', true, false);
                table.draw();
            });

            //Képfeltöltés
            let currentFiles = [];

            handleImageUpload('warehouse_images', 'warehouseImagePreviews');

            function handleImageUpload(inputId, previewContainerId) 
            {

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
                
            $(document).on('change input', '#warehouse_transfer_items, #warehouse_transfer_quantity', function() {
                let selected = $('#warehouse_transfer_items').select2('data')[0];
                let maxStock = selected ? selected.stock : 0;
                let quantity = parseInt($('#warehouse_transfer_quantity').val(), 10);
                let from = $('#warehouse_transfer_from').val();
                let to = $('#warehouse_transfer_to').val();

                if (selected && from && to && quantity > 0 && quantity <= maxStock) {
                    $('#warehouse_transfer_add_item').prop('disabled', false);
                    $('#ErrorAlert').addClass('d-none');
                } else {
                    $('#warehouse_transfer_add_item').prop('disabled', true);
                    if (quantity > maxStock && selected) {
                        $('#ErrorAlertText').html('A kért mennyiség meghaladja a készletet!');
                        $('#ErrorAlert').removeClass('d-none');
                    }
                }
            });

                

               

                $(document).on('click', '#items-table-2-rows .row_delete', function() {
                    $('#ErrorAlert').addClass('d-none');
                    $(this).closest('tr').remove();
                    checkConfirmButtonVisibility();
                });

                // A confirmTransfer eseményben javítsd ki a inventory_id használatát (a sorokban a data-row-inventory-id)
                
                $('body').on('click', '.print', function(e) {
                    let delivery_note_id = $(this).data('id');
                    $.ajax({
                        type: "POST",
                        url: '/ajax/print_delivery_note',
                        data: {
                            "delivery_note_id": delivery_note_id
                        },
                        success: function(response) {
                            let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                            $('#printmodal .modal-body').html(iframeHTML);
                            $('#printmodalLabel').empty().html('Szállítólevél nyomtatási kép');
                            $('#printmodal').modal('show');
                        }
                    });
                });
            }

            function get_items_by_warehouse_id(warehouse_id)
            {
                $.ajax({
                    url: "/ajax/get_items_by_warehouse_id",
                    method: 'POST',
                    data: {
                        warehouse_id: warehouse_id
                    },
                    success: function(data) {
                        $('#warehouse_transfer_items').empty().html(data.html);
                        $('#warehouse_transfer_add_item').prop('disabled', true);
                    }
                });
            }

             function checkConfirmButtonVisibility() {
                $('#transfer_items_note').val('');
                if ($('#items-table-2-rows tr').length > 0) {
                    $('#confirmTransfer').removeAttr('hidden');
                    $('#warehouse_transfer_from').prop('disabled', true);
                    $('#warehouse_transfer_to').prop('disabled', true);
                } else {
                    $('#confirmTransfer').attr('hidden', true);
                    $('#warehouse_transfer_from').removeAttr('disabled');
                    $('#warehouse_transfer_to').removeAttr('disabled');
                }
            }
            
        });
    </script>
@endpush