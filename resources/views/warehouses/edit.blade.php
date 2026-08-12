@extends('layouts.app', ['activePage' => 'warehouse', 'activeButton' => 'warehouse', 'title' => 'Zemef ERP', 'navName' => 'Raktár szerkesztése' ])

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
                            <a href="{{ route('warehouse.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    <form id="warehouse_form" method="post" action="{{ route('warehouse.update', $warehouse->warehouse_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Raktár alap adatai</h6>
                                            
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                                Megnevezés</label>
                                                            <input required type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $warehouse->name) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('is_central_connected') ? ' has-danger' : '' }}">
                                                                Kategória
                                                            </label>
                                                            <select required name="is_central_connected" class="form-control{{ $errors->has('is_central_connected') ? ' is-invalid' : '' }}">
                                                                <option value="" {{ is_null(old('is_central_connected', $warehouse->is_central_connected)) ? 'selected' : '' }}>Egyéb raktár</option>
                                                                <option value="1" {{ old('is_central_connected', $warehouse->is_central_connected) == 1 ? 'selected' : '' }}>Központhoz rendelt</option>
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'is_central_connected'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            



                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->status == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                            <span class="form-check-sign"></span>
                                                            Aktív
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_central == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_central">
                                                            <span class="form-check-sign"></span>
                                                            Központi raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_store == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_store">
                                                            <span class="form-check-sign"></span>
                                                            Bolti raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_receiving == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_receiving">
                                                            <span class="form-check-sign"></span>
                                                            Vételijegy raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_consignment == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_consignment">
                                                            <span class="form-check-sign"></span>
                                                            Bizományos raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_goldsmith == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_goldsmith">
                                                            <span class="form-check-sign"></span>
                                                            Ötvös raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_repair == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_repair">
                                                            <span class="form-check-sign"></span>
                                                            Javítási raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_hallmark == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_hallmark">
                                                            <span class="form-check-sign"></span>
                                                            NEHITI raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_broken_gold == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_broken_gold">
                                                            <span class="form-check-sign"></span>
                                                            Tört arany raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_broken_silver == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_broken_silver">
                                                            <span class="form-check-sign"></span>
                                                            Tört ezüst raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($warehouse->is_fake == 1) checked @endif @if($exist) disabled @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_fake">
                                                            <span class="form-check-sign"></span>
                                                            Selejt raktár
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>

                                <button type="button" class="btn btn-success save_warehouse">Mentés</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>
      


        $(document).ready(function () {
            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );
        

            $('.save_warehouse').click(function(){
                $('#warehouse_form').submit();
            });

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