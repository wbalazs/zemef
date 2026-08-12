@extends('layouts.app', ['activePage' => 'inventory_control', 'activeButton' => 'inventory_control', 'title' => 'Zemef ERP', 'navName' => 'Új leltárív rögzítése' ])

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

                                            <form id="inventory_control_form" method="post" action="{{ route('inventory_control.store') }}" enctype="multipart/form-data">
                                            @csrf

                                            <h6 class="heading-small text-muted mb-4">Leltárív adatai</h6>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                                                                Üzlet</label>
                                                            <select required name="business_id" id="business_id" class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($businesses as $business)
                                                                    <option value="{{ $business->business_id }}" >{{ $business->name }}</option>
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
                                                            <select required name="warehouse_id" id="warehouse_id" class="select2 form-control{{ $errors->has('warehouse_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('inventory_control_date') ? ' has-danger' : '' }}">
                                                                Leltár dátum</label>
                                                            <input type="date" class="form-control{{ $errors->has('inventory_control_date') ? ' is-invalid' : '' }}" required name="inventory_control_date" value=""/>
                                                            @include('alerts.feedback', ['field' => 'inventory_control_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('responsible_user_id') ? ' has-danger' : '' }}">
                                                                Felelős Kezelő</label>
                                                            <select required name="responsible_user_id" id="responsible_user_id" class="select2 form-control{{ $errors->has('responsible_user_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('inspector_user_id') ? ' has-danger' : '' }}">
                                                                Ellenőrző Személy</label>
                                                            <select required name="inspector_user_id" id="inspector_user_id" class="select2 form-control{{ $errors->has('inspector_user_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>

                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Mentés</button>
                            </div>

                           
                </div>


            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>

        Dropzone.autoDiscover = false;

        function onlyNumbers(str) {
            return /^\d+$/.test(str);
        }

        $('#business_id').change(function(){
        let business_id = $(this).val();

            if(onlyNumbers(business_id)){
                //Raktárak lekérése üzlet azonosító alapján a munkavállalóhoz
                $.ajax({
                    method: "POST",
                    url: "/ajax/get_storages_by_business_id_employee_inventory_control",
                    data: { business_id: business_id }
                })
                .done(function( resp ) {
                    $('#warehouse_id').empty();
                    $.each(resp, function(index, warehouse) {
                        $('#warehouse_id').append($('<option>', {
                            value: warehouse.warehouse_id,
                            text: warehouse.name
                        }));
                    });    
                });

            }else{
                let textdef = '<option value="">- válasszon -</option>';
                $('#warehouse_id').empty().append(textdef);
            }

        });


        $(document).ready(function () {

            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );

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
    </script>
@endpush