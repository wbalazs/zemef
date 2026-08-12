@extends('layouts.app', ['activePage' => 'inventory_control', 'activeButton' => 'inventory_control', 'title' => 'Zemef ERP', 'navName' => 'Leltárív szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                    </ul>




                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="{{ route('inventory_control.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>



                <div class="card-body ">
                    

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body ">

                                            <form id="inventory_control_form" method="post" action="{{ route('inventory_control.update', $inventory_control->inventory_control_id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('patch')

                                            <h6 class="heading-small text-muted mb-4">Leltárív adatai</h6>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                                                                Üzlet</label>
                                                            <select required name="business_id" disabled class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                                @foreach($businesses as $business)
                                                                    <option @if($inventory_control->business_id == $business->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('warehouse_id') ? ' has-danger' : '' }}">
                                                                Raktár</label>
                                                            <select required name="warehouse_id" disabled class="select2 form-control{{ $errors->has('warehouse_id') ? ' is-invalid' : '' }}">
                                                                @foreach($warehouses as $warehouse)
                                                                    <option @if($inventory_control->warehouse_id == $warehouse->warehouse_id) selected @endif value="{{ $warehouse->warehouse_id }}" >{{ $warehouse->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('inventory_control_date') ? ' has-danger' : '' }}">
                                                                Leltár dátum</label>
                                                            <input type="date" disabled class="form-control{{ $errors->has('inventory_control_date') ? ' is-invalid' : '' }}" required name="inventory_control_date" value="{{ $inventory_control->inventory_control_date }}"/>
                                                            @include('alerts.feedback', ['field' => 'inventory_control_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-control-label">Felelős kezelő</label>
                                                    <input disabled type="text" class="form-control" value="{{ $inventory_control->responsible_user->name }}" name="responsible_user_id" id="responsible_user_id"/>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-control-label">Ellenőrző személy</label>
                                                    <input disabled type="text" class="form-control" value="{{ $inventory_control->inspector_user->name }}" name="inspector_user_id" id="inspector_user_id"/>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-control-label">Termékazonosító beolvasása</label>
                                                    <input type="number" class="form-control" id="scan_product_id"/>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="table-responsive py-4" id="items-table">
                                                        <table id="datatables" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                                            <thead>
                                                                <tr>
                                                                    <th>Azonosító</th>
                                                                    <th>Megnevezés</th>
                                                                    <th>Márka</th>
                                                                    <th>Leírás</th>
                                                                    <th>Létr.dátum</th>
                                                                    <th>Könyv menny.</th>
                                                                    <th>Leltár menny.</th>
                                                                    <th>Súly/db</th>
                                                                    <th>Egys.Kat</th>
                                                                    <th>Kalkulált ár</th>
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Azonosító</th>
                                                                    <th>Megnevezés</th>
                                                                    <th>Márka</th>
                                                                    <th>Leírás</th>
                                                                    <th>Létr.dátum</th>
                                                                    <th>Könyv menny.</th>
                                                                    <th>Leltár menny.</th>
                                                                    <th>Súly/db</th>
                                                                    <th>Egys.Kat</th>
                                                                    <th>Kalkulált ár</th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody id="items-table-tbody">
                            
                                                                @foreach ($items as $item)
                                                                @php
                                                                    $row_color = $item->book_quantity == $item->inventory_quantity ? 'background-color: rgba(0, 128, 0, 0.5);' : 'background-color: rgba(255, 0, 0, 0.5);';
                                                                @endphp
                                                                    <tr style="{{ $row_color }}">
                                                                        <td># {{ $item['product_id'] }}</td>
                                                                        <td>{{ $item['name'] }}</td>
                                                                        <td>{{ $item['brand'] }}</td>
                                                                        <td>{{ $item['description'] }}</td>
                                                                        <td>{{ $item['prod_ins_date'] }}</td>
                                                                        <td>{{ $item['book_quantity'] }} db</td>
                                                                        <td>{{ $item['inventory_quantity'] }} db</td>
                                                                        <td>{{ $item['book_weight'] }} g</td>
                                                                        <td>{{ $item['unit_price_type'] }}</td>
                                                                        <td>{{ number_format($item['book_price'], 0, ',', ' ') }} Ft</td>
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

                                <button type="button" id="close_inventory_control" class="btn btn-success"><i class="fa fa-power-off"></i> Leltár zárás</button>
                            </div>

                           
                </div>


            </div>
        </div>
    </div>


<!-- Product_find_model -->
<div class="modal fade" id="product_find_modal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Termék találat(ok)</h5>
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
                                    <th>Azonosító</th>
                                    <th>Termék</th>
                                    <th>Üzlet</th>
                                    <th>Raktár</th>
                                    <th>Mennyiség</th>
                                </tr>
                            </thead>
                            <tbody id="product_find_rows">
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer mt-7">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i> Kilépés</button>
            </div>
        </div>
    </div>
</div>
<!-- Product_find_model -->

@endsection
@push('js')

    <script>

        function onlyNumbers(str) {
            return /^\d+$/.test(str);
        }


        $(document).ready(function () {


            $(document).on('click', '#close_inventory_control', function() {
                let inventory_control_id = {{ $inventory_control->inventory_control_id }}
                swal({
                    title: 'Leltárív lezárása',
                    html: 'Biztosan zárható a letárív?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        $('#inventory_control_form').submit();
                    }
                });
                
            });

            $(document).on('input', '#scan_product_id', function() {
                let inventory_control_id = {{ $inventory_control->inventory_control_id }}
                let product_id = $(this).val();
                if(product_id.length >= 3){
                    $.ajax({
                        url: '/ajax/update_inventory_control',
                        type: 'POST',
                        data: {inventory_control_id : inventory_control_id, product_id : product_id},
                        success: function(response) {
                            let items = JSON.parse(response);
                            if(items.success){
                                if ($.fn.DataTable.isDataTable('#datatables')) {
                                $('#datatables').DataTable().destroy();
                            }
                                $('#items-table-tbody').empty().append(items.html_table);
                                $('#datatables').DataTable({
                                    "pagingType": "full_numbers",
                                    "lengthMenu": [
                                        [10, 25, 50, -1],
                                        [10, 25, 50, "All"]
                                    ],
                                    "sorting": [],
                                    "responsive": true,
                                    "fixedHeader": true,
                                    "dom": 'lrtip',
                                    "searchDelay": 1000,
                                    "columnSearchDelay": 1000,
                                    "columnDefs":[ 
                                    ],
                                    "serverSide": false,
                                    "processing": true,
                                    "columnSearch": true,
                                    "searchDelay": 1000,
                                    "columnSearchDelay": 1500,
                                    "language": {
                                        searchPlaceholder: "Keresés...",
                                        infoEmpty: "0 találat",
                                        lengthMenu: "Mutass _MENU_ elemet oldalanként",
                                        search: "Keresés:",
                                        zeroRecords: "Nincs adat",
                                        info: "_START_ - _END_ (Össz.: _TOTAL_)",
                                        thousands: " ",
                                        processing: "Kérés feldolgozása folyamatban...",
                                        infoFiltered: "(szűrve _MAX_ találat közül)",
                                        paginate: {
                                            first:      "Első",
                                            last:       "Utolsó",
                                            next:       "Következő",
                                            previous:   "Előző"
                                        },
                                    },

                                });
                            }else{
                                if(items.prod_find){
                                    show_modal_with_item_data(items.prod_now);
                                }else{
                                    $.notify({ icon: "error", message: 'Nem megfelelő termékazonosító!' }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                    return;
                                }
                            }
                            $('#scan_product_id').val('').focus();
                        }
                    });
                }
                
            });


            var table = $('#datatables').DataTable();

            // Init Sliders
            $('.datepicker').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-screenshot',
                    clear: 'fa fa-trash',
                    close: 'fa fa-remove'
                }
            });


        });

        function show_modal_with_item_data(prod_now)
        {
            $('#product_find_rows').empty().append(prod_now);
            $('#product_find_modal').modal('show');
        }

    </script>
@endpush