@extends('layouts.app', ['activePage' => 'property', 'activeButton' => 'property', 'title' => 'Zemef ERP', 'navName' => 'Ingatlan létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Ingatlan létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_property">Mentés</button>
                            <a href="{{ route('property.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="property_form" method="post" action="{{ route('property.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Ingatlan alap adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Megnevezés</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name') }}" placeholder="" required autofocus>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('zip') ? ' has-danger' : '' }}">
                                                        Irányítószám</label>
                                                    <input type="text" class="form-control{{ $errors->has('zip') ? ' is-invalid' : '' }}" name="zip" maxlength="100" value="{{ old('zip') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'zip'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('city') ? ' has-danger' : '' }}">
                                                        Város</label>
                                                    <input type="text" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" maxlength="100" value="{{ old('city') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'city'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-5">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('address') ? ' has-danger' : '' }}">
                                                        Cím</label>
                                                    <input type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" maxlength="100" value="{{ old('address') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'address'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('floor') ? ' has-danger' : '' }}">
                                                        Emelet</label>
                                                    <input type="text" class="form-control{{ $errors->has('floor') ? ' is-invalid' : '' }}" name="floor" maxlength="100" value="{{ old('floor') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'floor'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('door') ? ' has-danger' : '' }}">
                                                        Ajtó</label>
                                                    <input type="text" class="form-control{{ $errors->has('door') ? ' is-invalid' : '' }}" name="door" maxlength="100" value="{{ old('door') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'door'])
                                                </div>
                                            </fieldset>
                                        </div>

                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-group">
                                                    
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input value="1" class="form-check-input" type="checkbox" value="" data-gtm-form-interact-field-id="1" name="is_business">
                                                        <span class="form-check-sign"></span>
                                                        Üzlet
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                    <input checked class="form-check-input" type="radio" name="is_own" id="is_own0" value="0" data-gtm-form-interact-field-id="0">
                                                    <span class="form-check-sign"></span>
                                                    Saját
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="is_own" id="is_own1" value="1" data-gtm-form-interact-field-id="0">
                                                    <span class="form-check-sign"></span>
                                                    Bérelt
                                                    </label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                    <input value="1" class="form-check-input" type="checkbox" value="" data-gtm-form-interact-field-id="1" name="is_franchise">
                                                    <span class="form-check-sign"></span>
                                                    Franchise
                                                    </label>
                                                </div>
                                            </fieldset>
                                        </div>

                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('stock_value') ? ' has-danger' : '' }}">
                                                        Készlet érték</label>
                                                    <input type="number" class="form-control{{ $errors->has('stock_value') ? ' is-invalid' : '' }}" name="stock_value" value="{{ old('stock_value') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'stock_value'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('pledge_value') ? ' has-danger' : '' }}">
                                                        Zálogérték</label>
                                                    <input type="number" class="form-control{{ $errors->has('pledge_value') ? ' is-invalid' : '' }}" name="pledge_value" value="{{ old('pledge_value') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'pledge_value'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('email') ? ' has-danger' : '' }}">
                                                        Email</label>
                                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'email'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                                        Telefonszám</label>
                                                    <input type="number" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'phone'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('facbook') ? ' has-danger' : '' }}">
                                                        Facebook</label>
                                                    <input type="text" class="form-control{{ $errors->has('facbook') ? ' is-invalid' : '' }}" name="facbook" value="{{ old('facbook') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'facbook'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('instagram') ? ' has-danger' : '' }}">
                                                        Instagram</label>
                                                    <input type="text" class="form-control{{ $errors->has('instagram') ? ' is-invalid' : '' }}" name="instagram" value="{{ old('instagram') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'instagram'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('tiktok') ? ' has-danger' : '' }}">
                                                        Tiktok</label>
                                                    <input type="text" class="form-control{{ $errors->has('tiktok') ? ' is-invalid' : '' }}" name="tiktok" value="{{ old('tiktok') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'tiktok'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input checked class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktív
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4"></h6>
                                    <div class="row">
                                        
                                    </div>
                                    
                                </div>
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
        
    });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.save_property').click(function(){
                $('#property_form').submit();
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