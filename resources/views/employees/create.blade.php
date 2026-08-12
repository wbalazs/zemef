@extends('layouts.app', ['activePage' => 'employee', 'activeButton' => 'employee', 'title' => 'Zemef ERP', 'navName' => 'Munkavállaló szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="education_data-tab" data-toggle="pill" href="#education_data" role="tab" aria-controls="education_data" aria-selected="true">Végzettség / szakmai tapasztalat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="holiday_data-tab" data-toggle="pill" href="#holiday_data" role="tab" aria-controls="holiday_data" aria-selected="true">Szabadság</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="docs_data-tab" data-toggle="pill" href="#docs_data" role="tab" aria-controls="docs_data" aria-selected="true">Dokumentumok</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <button type="button" class="btn btn-success save_employee">Mentés</button>
                            <a href="{{ route('employee.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    <form method="post" id="employee_form" action="{{ route('employee.store') }}" enctype="multipart/form-data">
                        @csrf
                        @method('post')

                        <div class="tab-content" id="custom-tabs-two-tabContent">


                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Munkavállaló alap adatai</h6>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('lastname') ? ' has-danger' : '' }}">
                                                                Vezetéknév</label>
                                                            <input type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" maxlength="100" value="{{ old('lastname') }}" placeholder="" required autofocus>
                                                            @include('alerts.feedback', ['field' => 'lastname'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                                                                Keresztnév</label>
                                                            <input type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" maxlength="100" value="{{ old('firstname') }}" placeholder="" required>
                                                            @include('alerts.feedback', ['field' => 'firstname'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('maiden_name') ? ' has-danger' : '' }}">
                                                        Leánykori név</label>
                                                    <input type="text" class="form-control{{ $errors->has('maiden_name') ? ' is-invalid' : '' }}" name="maiden_name" maxlength="100" value="{{ old('maiden_name') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'maiden_name'])
                                                </div>
                                            </fieldset>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_place') ? ' has-danger' : '' }}">
                                                                Születési hely</label>
                                                            <input type="text" class="form-control{{ $errors->has('birth_place') ? ' is-invalid' : '' }}" name="birth_place" maxlength="100" value="{{ old('birth_place') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'birth_place'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_date') ? ' has-danger' : '' }}">
                                                                Születési idő</label>
                                                            <input type="date" class="form-control{{ $errors->has('birth_date') ? ' is-invalid' : '' }}" name="birth_date" maxlength="100" value="{{ old('birth_date') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'birth_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tax_number') ? ' has-danger' : '' }}">
                                                                Adószám</label>
                                                            <input type="text" class="form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="100" value="{{ old('tax_number') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'tax_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('social_number') ? ' has-danger' : '' }}">
                                                                TAJ szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('social_number') ? ' is-invalid' : '' }}" name="social_number" maxlength="100" value="{{ old('social_number') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'social_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('address_card_number') ? ' has-danger' : '' }}">
                                                                Lakcímkártya szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('address_card_number') ? ' is-invalid' : '' }}" name="address_card_number" maxlength="100" value="{{ old('address_card_number') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'address_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('identity_card_number') ? ' has-danger' : '' }}">
                                                                Személyi igazolvány szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('identity_card_number') ? ' is-invalid' : '' }}" name="identity_card_number" maxlength="100" value="{{ old('identity_card_number') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'identity_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('private_phone') ? ' has-danger' : '' }}">
                                                                Magán telefonszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('private_phone') ? ' is-invalid' : '' }}" name="private_phone" maxlength="100" value="{{ old('private_phone') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'private_phone'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('company_phone') ? ' has-danger' : '' }}">
                                                                Céges telefonszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('company_phone') ? ' is-invalid' : '' }}" name="company_phone" maxlength="100" value="{{ old('company_phone') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'company_phone'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('private_email') ? ' has-danger' : '' }}">
                                                                Magán e-mail cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('private_email') ? ' is-invalid' : '' }}" name="private_email" maxlength="100" value="{{ old('private_email') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'private_email'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">

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
                                                                <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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
                                            <h6 class="heading-small text-muted mb-4">Lakcím</h6>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_zip') ? ' has-danger' : '' }}">
                                                                Irányítószám</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_zip') ? ' is-invalid' : '' }}" name="home_zip" maxlength="100" value="{{ old('home_zip') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-9">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_city') ? ' has-danger' : '' }}">
                                                                Település</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_city') ? ' is-invalid' : '' }}" name="home_city" maxlength="100" value="{{ old('home_city') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_city'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_address') ? ' has-danger' : '' }}">
                                                                Cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_address') ? ' is-invalid' : '' }}" name="home_address" maxlength="100" value="{{ old('home_address') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_floor') ? ' has-danger' : '' }}">
                                                                Emelet</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_floor') ? ' is-invalid' : '' }}" name="home_floor" maxlength="100" value="{{ old('home_floor') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_door') ? ' is-invalid' : '' }}" name="home_door" maxlength="100" value="{{ old('home_door') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_door'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>


                                            <h6 class="heading-small text-muted mb-4">Levelezési cím</h6>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_zip') ? ' has-danger' : '' }}">
                                                                Irányítószám</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_zip') ? ' is-invalid' : '' }}" name="mailing_zip" maxlength="100" value="{{ old('mailing_zip') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-9">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_city') ? ' has-danger' : '' }}">
                                                                Település</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_city') ? ' is-invalid' : '' }}" name="mailing_city" maxlength="100" value="{{ old('mailing_city') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_city'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_address') ? ' has-danger' : '' }}">
                                                                Cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_address') ? ' is-invalid' : '' }}" name="mailing_address" maxlength="100" value="{{ old('mailing_address') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_floor') ? ' has-danger' : '' }}">
                                                                Emelet</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_floor') ? ' is-invalid' : '' }}" name="mailing_floor" maxlength="100" value="{{ old('mailing_floor') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_door') ? ' is-invalid' : '' }}" name="mailing_door" maxlength="100" value="{{ old('mailing_door') }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_door'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>


                                            <h6 class="heading-small text-muted mb-4">Banki adatok</h6>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="bank_contact">
                                                            <span class="form-check-sign"></span>
                                                            Banki kapcsolat
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bank_accounts') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Bankszámlák</label>
                                                        <select title="- válasszon bankszámlákat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="bank_accounts[]" id="input-role" class="selectpicker form-control{{ $errors->has('bank_accounts') ? ' is-invalid' : '' }}" placeholder="">
                                                            @foreach($bank_accounts as $bank_account)
                                                                <option value="{{ $bank_account->bank_account_id }}" >{{ $bank_account->bank_account_number . ' ('.$bank_account->bank_account_number.')' }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'bank_accounts'])
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>

                            <div class="tab-pane fade" id="education_data" role="tabpanel" aria-labelledby="education_data-tab">
                                
                            </div>
                            <div class="tab-pane fade" id="holiday_data" role="tabpanel" aria-labelledby="holiday_data-tab">
                                
                            </div>
                            <div class="tab-pane fade" id="docs_data" role="tabpanel" aria-labelledby="docs_data-tab">
                                Kérem, előbb mentse az alap adatokat!
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

            $('.save_employee').click(function(){
                $('#employee_form').submit();
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