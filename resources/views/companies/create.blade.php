@extends('layouts.app', ['activePage' => 'company', 'activeButton' => 'company', 'title' => 'Zemef ERP', 'navName' => 'Cég létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Cég létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_company">Mentés</button>
                            <a href="{{ route('company.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="company_form" method="post" action="{{ route('company.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Cég alap adatai</h6>
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
                                                    <label class="form-control-label{{ $errors->has('web') ? ' has-danger' : '' }}">
                                                        Web cím</label>
                                                    <input type="text" class="form-control{{ $errors->has('web') ? ' is-invalid' : '' }}" name="web" maxlength="100" value="{{ old('web') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'web'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('company_registration_number') ? ' has-danger' : '' }}">
                                                        Cégjegyzékszám</label>
                                                    <input type="text" class="form-control{{ $errors->has('company_registration_number') ? ' is-invalid' : '' }}" name="company_registration_number" maxlength="100" value="{{ old('company_registration_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'company_registration_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('tax_number') ? ' has-danger' : '' }}">
                                                        Adószám</label>
                                                    <input type="text" class="form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="100" value="{{ old('tax_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'tax_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('registration_number') ? ' has-danger' : '' }}">
                                                        Nyilvántartási szám</label>
                                                    <input type="text" class="form-control{{ $errors->has('registration_number') ? ' is-invalid' : '' }}" name="registration_number" maxlength="100" value="{{ old('registration_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'registration_number'])
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
            
            $('.save_company').click(function(){
                $('#company_form').submit();
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