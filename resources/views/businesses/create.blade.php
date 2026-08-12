@extends('layouts.app', ['activePage' => 'business', 'activeButton' => 'business', 'title' => 'Zemef ERP', 'navName' => 'Üzlet létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Üzlet létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_business">Mentés</button>
                            <a href="{{ route('business.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="business_form" method="post" action="{{ route('business.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Üzlet alap adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Név</label>
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

                                    </div>

                                    <hr/>
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

                                    </div>


                                    <div class="row">
                                        <div class="col-lg-2">
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
                                        <div class="col-lg-5">
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
                                        <div class="col-lg-3">
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
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4"></h6>
                            

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

            $('.save_business').click(function(){
                $('#business_form').submit();
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