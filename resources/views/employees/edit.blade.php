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
                            <a href="{{ route('employee.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    <form method="post" id="employee_form" action="{{ route('employee.update', $employee->employee_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

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
                                                            <input type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" maxlength="100" value="{{ old('lastname', $employee->lastname) }}" placeholder="" required autofocus>
                                                            @include('alerts.feedback', ['field' => 'lastname'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                                                                Keresztnév</label>
                                                            <input type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" maxlength="100" value="{{ old('firstname', $employee->firstname) }}" placeholder="" required>
                                                            @include('alerts.feedback', ['field' => 'firstname'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('maiden_name') ? ' has-danger' : '' }}">
                                                                Leánykori név</label>
                                                            <input type="text" class="form-control{{ $errors->has('maiden_name') ? ' is-invalid' : '' }}" name="maiden_name" maxlength="100" value="{{ old('maiden_name', $employee->maiden_name) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'maiden_name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mothers_name') ? ' has-danger' : '' }}">
                                                                Anyja neve</label>
                                                            <input type="text" class="form-control{{ $errors->has('mothers_name') ? ' is-invalid' : '' }}" name="mothers_name" maxlength="100" value="{{ old('mothers_name', $employee->mothers_name) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mothers_name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_place') ? ' has-danger' : '' }}">
                                                                Születési hely</label>
                                                            <input type="text" class="form-control{{ $errors->has('birth_place') ? ' is-invalid' : '' }}" name="birth_place" maxlength="100" value="{{ old('birth_place', $employee->birth_place) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'birth_place'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_date') ? ' has-danger' : '' }}">
                                                                Születési idő</label>
                                                            <input type="date" class="form-control{{ $errors->has('birth_date') ? ' is-invalid' : '' }}" name="birth_date" maxlength="100" value="{{ old('birth_date', $employee->birth_date) }}" placeholder="">
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
                                                                Adóazonosító jel</label>
                                                            <input type="text" class="form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="100" value="{{ old('tax_number', $employee->tax_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'tax_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('social_number') ? ' has-danger' : '' }}">
                                                                TAJ szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('social_number') ? ' is-invalid' : '' }}" name="social_number" maxlength="100" value="{{ old('social_number', $employee->social_number) }}" placeholder="">
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
                                                            <input type="text" class="form-control{{ $errors->has('address_card_number') ? ' is-invalid' : '' }}" name="address_card_number" maxlength="100" value="{{ old('address_card_number', $employee->address_card_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'address_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('identity_card_number') ? ' has-danger' : '' }}">
                                                                Személyi igazolvány szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('identity_card_number') ? ' is-invalid' : '' }}" name="identity_card_number" maxlength="100" value="{{ old('identity_card_number', $employee->identity_card_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'identity_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('identity_card_number_expire_date') ? ' has-danger' : '' }}">
                                                                Lejárati dátum</label>
                                                            <input type="date" class="form-control{{ $errors->has('identity_card_number_expire_date') ? ' is-invalid' : '' }}" name="identity_card_number_expire_date" maxlength="100" value="{{ old('identity_card_number_expire_date', $employee->identity_card_number_expire_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'identity_card_number_expire_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('nice_card_number') ? ' has-danger' : '' }}">
                                                                Szépkártya száma</label>
                                                            <input type="text" class="form-control{{ $errors->has('nice_card_number') ? ' is-invalid' : '' }}" name="nice_card_number" maxlength="100" value="{{ old('nice_card_number', $employee->nice_card_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'nice_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('nice_card_expire_date') ? ' has-danger' : '' }}">
                                                                Lejárati dátum</label>
                                                            <input type="date" class="form-control{{ $errors->has('nice_card_expire_date') ? ' is-invalid' : '' }}" name="nice_card_expire_date" maxlength="100" value="{{ old('nice_card_expire_date', $employee->nice_card_expire_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'nice_card_expire_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-5">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('nice_card_bank_number') ? ' has-danger' : '' }}">
                                                                Szépkártya bankszámla</label>
                                                            <input type="text" class="form-control{{ $errors->has('nice_card_bank_number') ? ' is-invalid' : '' }}" name="nice_card_bank_number" maxlength="100" value="{{ old('nice_card_bank_number', $employee->nice_card_bank_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'nice_card_bank_number'])
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
                                                            <input type="text" class="form-control{{ $errors->has('private_phone') ? ' is-invalid' : '' }}" name="private_phone" maxlength="100" value="{{ old('private_phone', $employee->private_phone) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'private_phone'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('company_phone') ? ' has-danger' : '' }}">
                                                                Céges telefonszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('company_phone') ? ' is-invalid' : '' }}" name="company_phone" maxlength="100" value="{{ old('company_phone', $employee->company_phone) }}" placeholder="">
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
                                                            <input type="text" class="form-control{{ $errors->has('private_email') ? ' is-invalid' : '' }}" name="private_email" maxlength="100" value="{{ old('private_email', $employee->private_email) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'private_email'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('private_bank_account') ? ' has-danger' : '' }}">
                                                                Magán bankszámlaszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('private_bank_account') ? ' is-invalid' : '' }}" name="private_bank_account" maxlength="100" value="{{ old('private_bank_account', $employee->private_bank_account) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'private_bank_account'])
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
                                                                <input @if($employee->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                                <span class="form-check-sign"></span>
                                                                Aktív
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('note') ? ' has-danger' : '' }}">
                                                            Megjegyzés</label>
                                                        <input type="text" @if($employee->status) disabled @endif class="form-control{{ $errors->has('note') ? ' is-invalid' : '' }}" name="note" maxlength="100" value="{{ old('note', $employee->note) }}" placeholder="">
                                                        @include('alerts.feedback', ['field' => 'note'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('note_date') ? ' has-danger' : '' }}">
                                                            Dátum</label>
                                                        <input type="date" @if($employee->status) disabled @endif class="form-control{{ $errors->has('note_date') ? ' is-invalid' : '' }}" name="note_date" maxlength="100" value="{{ old('note_date', $employee->note_date) }}" placeholder="">
                                                        @include('alerts.feedback', ['field' => 'note_date'])
                                                    </div>
                                                </fieldset>
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
                                                                Irányítószám
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('home_zip') ? ' is-invalid' : '' }}" id="home_zip" name="home_zip_id">
                                                                @if($employee->home_zip)
                                                                    <option value="{{ $employee->home_zip_id }}">{{ $employee->home_zip }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'home_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-9">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_city') ? ' has-danger' : '' }}">
                                                                Település
                                                            </label>

                                                            <select class="form-control select2{{ $errors->has('home_city') ? ' is-invalid' : '' }}" id="home_city" name="home_city_id">
                                                                @if($employee->home_city)
                                                                    <option value="{{ $employee->home_city_id }}">{{ $employee->home_city }}</option>
                                                                @endif
                                                            </select>
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
                                                            <input type="text" class="form-control{{ $errors->has('home_address') ? ' is-invalid' : '' }}" id="home_address" name="home_address" maxlength="100" value="{{ old('home_address', $employee->home_address) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_floor') ? ' has-danger' : '' }}">
                                                                Emelet</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_floor') ? ' is-invalid' : '' }}" id="home_floor" name="home_floor" maxlength="100" value="{{ old('home_floor', $employee->home_floor) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('home_door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('home_door') ? ' is-invalid' : '' }}" id="home_door" name="home_door" maxlength="100" value="{{ old('home_door', $employee->home_door) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'home_door'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <h6 class="heading-small text-muted mb-4">Tartózkodási hely</h6>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($employee->has_temporary) checked @endif id="has_temporary" class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="has_temporary">
                                                            <span class="form-check-sign"></span>
                                                            Megyegyezik a lakcímmel
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('temporary_zip') ? ' has-danger' : '' }}">
                                                                Irányítószám
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('temporary_zip') ? ' is-invalid' : '' }}" id="temporary_zip" name="temporary_zip_id">
                                                                @if($employee->temporary_zip)
                                                                    <option value="{{ $employee->temporary_zip_id }}">{{ $employee->temporary_zip }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'home_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-9">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('temporary_city') ? ' has-danger' : '' }}">
                                                                Település
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('temporary_city') ? ' is-invalid' : '' }}" id="temporary_city" name="temporary_city_id">
                                                                @if($employee->temporary_city)
                                                                    <option value="{{ $employee->temporary_city_id }}">{{ $employee->temporary_city }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'temporary_city'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('temporary_address') ? ' has-danger' : '' }}">
                                                                Cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('temporary_address') ? ' is-invalid' : '' }}" id="temporary_address" name="temporary_address" maxlength="100" value="{{ old('temporary_address', $employee->temporary_address) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'temporary_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('temporary_floor') ? ' has-danger' : '' }}">
                                                                Emelet</label>
                                                            <input type="text" class="form-control{{ $errors->has('temporary_floor') ? ' is-invalid' : '' }}" id="temporary_floor" name="temporary_floor" maxlength="100" value="{{ old('temporary_floor', $employee->temporary_floor) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'temporary_floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('temporary_door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('temporary_door') ? ' is-invalid' : '' }}" id="temporary_door" name="temporary_door" maxlength="100" value="{{ old('temporary_door', $employee->temporary_door) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'temporary_door'])
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
                                                                Irányítószám
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('mailing_zip') ? ' is-invalid' : '' }}" id="mailing_zip" name="mailing_zip_id">
                                                                @if($employee->mailing_zip)
                                                                    <option value="{{ $employee->mailing_zip_id }}">{{ $employee->mailing_zip }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'mailing_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-9">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_city') ? ' has-danger' : '' }}">
                                                                Település
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('mailing_city') ? ' is-invalid' : '' }}" id="mailing_city" name="mailing_city_id">
                                                                @if($employee->mailing_city)
                                                                    <option value="{{ $employee->mailing_city_id }}">{{ $employee->mailing_city }}</option>
                                                                @endif
                                                            </select>
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
                                                            <input type="text" class="form-control{{ $errors->has('mailing_address') ? ' is-invalid' : '' }}" name="mailing_address" maxlength="100" value="{{ old('mailing_address', $employee->mailing_address) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_floor') ? ' has-danger' : '' }}">
                                                                Emelet</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_floor') ? ' is-invalid' : '' }}" name="mailing_floor" maxlength="100" value="{{ old('mailing_floor', $employee->mailing_floor) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mailing_door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('mailing_door') ? ' is-invalid' : '' }}" name="mailing_door" maxlength="100" value="{{ old('mailing_door', $employee->mailing_door) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mailing_door'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <h6 class="heading-small text-muted mb-4">Üzletek, cégek</h6>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('businesses') ? ' has-danger' : '' }}">
                                                            <label class="form-control-label" for="input-role">Üzletek</label>
                                                            <select title="- válasszon üzleteket -" multiple data-style="btn-info btn-fill btn-block" data-actions-box="true" name="businesses[]" id="input-role" data-menu-style="dropdown-blue"  class="selectpicker business_select form-control{{ $errors->has('businesses') ? ' is-invalid' : '' }}" placeholder="">
                                                                @foreach($businesses as $business)
                                                                    <option @if(in_array($business->business_id, $employee->businesses->pluck('business_id')->toArray())) selected @endif value="{{ $business->business_id }}">{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                
                                                            @include('alerts.feedback', ['field' => 'businesses'])
                                                        </div>
                                                    </div>
                                                </div>                                            
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('warehouses') ? ' has-danger' : '' }}">
                                                            <label class="form-control-label" for="input-role">Raktárak</label>
                                                            <select title="- válasszon raktárakat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="warehouses[]" id="warehouse_select" class="selectpicker form-control{{ $errors->has('warehouses') ? ' is-invalid' : '' }}" placeholder="">
                                                                @foreach($warehouses as $warehouse)
                                                                    <option @if(in_array($warehouse->warehouse_id, $employee->warehouses->pluck('warehouse_id')->toArray())) selected @endif value="{{ $warehouse->warehouse_id }}" value="{{ $warehouse->warehouse_id }}" >{{ $warehouse->name }}</option>
                                                                @endforeach
                                                            </select>

                                                            @include('alerts.feedback', ['field' => 'warehouses'])
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('companies') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Cégek</label>
                                                        <select title="- válasszon üzleteket -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="companies[]" id="input-role" class="selectpicker form-control{{ $errors->has('companies') ? ' is-invalid' : '' }}" placeholder="" required>
                                                            @foreach($companies as $company)
                                                                <option @if(in_array($company->company_id, $employee->companies->pluck('company_id')->toArray())) selected @endif value="{{ $company->company_id }}" value="{{ $company->company_id }}" >{{ $company->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'companies'])
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="heading-small text-muted mb-4">Aktív Üzlet, cég</h6>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('businesses') ? ' has-danger' : '' }}">
                                                            <label class="form-control-label" for="input-role">Aktív Üzlet</label>
                                                            <select title="- válasszon üzletet -" data-style="btn-info btn-fill btn-block" data-actions-box="true" name="active_business_id" id="active_business_id" data-menu-style="dropdown-blue"  class="selectpicker business_select form-control{{ $errors->has('businesses') ? ' is-invalid' : '' }}" placeholder="" required>
                                                                @foreach($businesses as $business)
                                                                    <option @if($employee->active_business_id == $business->business_id) selected @endif value="{{ $business->business_id }}">{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                
                                                            @include('alerts.feedback', ['field' => 'businesses'])
                                                        </div>
                                                    </div>
                                                </div>    
                                                 <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('companies') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Aktív Cég</label>
                                                        <select title="- válasszon üzletet -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="active_company_id" id="active_company_id" class="selectpicker form-control{{ $errors->has('companies') ? ' is-invalid' : '' }}" placeholder="" required>
                                                            @foreach($companies as $company)
                                                                <option @if($company->company_id == $employee->active_company_id) selected @endif value="{{ $company->company_id }}">{{ $company->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'companies'])
                                                    </div>
                                                </div>                                        

                                            <h6 class="heading-small text-muted mb-4">Banki adatok</h6>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($employee->bank_contact) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="bank_contact">
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
                                                                <option @if(in_array($bank_account->bank_account_id, $employee->bank_accounts->pluck('bank_account_id')->toArray())) selected @endif value="{{ $bank_account->bank_account_id }}" value="{{ $bank_account->bank_account_id }}" >{{ $bank_account->bank_account_number . ' ('.$bank_account->bank->name.')' }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'bank_accounts'])
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($employee->card) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="card">
                                                            <span class="form-check-sign"></span>
                                                            Bankkártya
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('card_expiration_year') ? ' has-danger' : '' }}">
                                                                Bankkártya lejárat</label>

                                                            <div class="row">
                                                                <div class="col-lg-3">
                                                                    <input @if(!$employee->card) disabled @endif type="number" max="99" class="input-sm form-control{{ $errors->has('card_expiration_year') ? ' is-invalid' : '' }}" name="card_expiration_year" value="{{ old('card_expiration_year', $employee->card_expiration_year) }}" placeholder="ÉÉ">
                                                                    @include('alerts.feedback', ['field' => 'card_expiration_year'])
                                                                </div>
                                                                <div class="col-lg-1">
                                                                    <span class="slash">/</span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <input @if(!$employee->card) disabled @endif type="number" min="1" max="12" class="input-sm form-control{{ $errors->has('card_expiration_month') ? ' is-invalid' : '' }}" name="card_expiration_month" value="{{ old('card_expiration_month', $employee->card_expiration_month) }}" placeholder="HH">
                                                                    @include('alerts.feedback', ['field' => 'card_expiration_month'])
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="form-control-label{{ $errors->has('card_limit') ? ' has-danger' : '' }}">
                                                                Készpénzfelvételi limit</label>
                                                            <input @if(!$employee->card) disabled @endif type="number" class="input-sm form-control{{ $errors->has('card_limit') ? ' is-invalid' : '' }}" name="card_limit" value="{{ old('card_limit', $employee->card_limit) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'card_limit'])
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="form-control-label{{ $errors->has('internet_limit') ? ' has-danger' : '' }}">
                                                                Internetes limit</label>
                                                            <input @if(!$employee->card) disabled @endif type="number" class="input-sm form-control{{ $errors->has('internet_limit') ? ' is-invalid' : '' }}" name="internet_limit" value="{{ old('internet_limit', $employee->internet_limit) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'internet_limit'])
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="form-control-label{{ $errors->has('buy_limit') ? ' has-danger' : '' }}">
                                                                Vásárlási limit</label>
                                                            <input @if(!$employee->card) disabled @endif type="number" class="input-sm form-control{{ $errors->has('buy_limit') ? ' is-invalid' : '' }}" name="buy_limit" value="{{ old('buy_limit', $employee->buy_limit) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'buy_limit'])
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                    
                                    
                                </div>

                                <button type="button" class="btn btn-success save_employee">Mentés</button>
                            </div>

                            <div class="tab-pane fade" id="education_data" role="tabpanel" aria-labelledby="education_data-tab">
                                <fieldset>
                                    <input type="hidden" name="education_id" value="">
                                    <div class="row">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="education_name">Végzettség megnevezése</label>
                                                <input type="text" name="education_name" id="education_name" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="school">Intézmény megnevezése</label>
                                                <input type="text" name="school" id="school" class="form-control">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="education_start">Képzési időszak kezdete</label>
                                                <input type="date" name="education_start" id="education_start" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="education_end">Képzési időszak vége</label>
                                                <input type="date" name="education_end" id="education_end" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_education">Képzettség mentése</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 education_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Képzettség megnevezése</th>
                                                        <th>Intézmény megnevezése</th>
                                                        <th>Képzési időszak kezdete</th>
                                                        <th>Képzési időszak vége</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employee->educations as $education)
                                                        <tr class='grey_background education_row_{{$education->education_id}}'>
                                                            <td class="education_education_name">{{ $education->education_name }}</td>
                                                            <td class="education_school">{{ $education->school }}</td>
                                                            <td class="education_education_start">{{ $education->education_start }}</td>
                                                            <td class="education_education_end">{{ $education->education_end }}</td>
                                                            <td>
                                                                <div data-education_id="{{ $education->education_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-success edit_education" data-education_id="{{ $education->education_id }}">
                                                                        Szerkesztés
                                                                    </button>
                                                                </div>
                                                                <div data-education_id="{{ $education->education_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_education" data-education_id="{{ $education->education_id }}">
                                                                        Törlés
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </fieldset>    
                            </div>

                            <div class="tab-pane fade" id="holiday_data" role="tabpanel" aria-labelledby="holiday_data-tab">
                                <fieldset>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 holiday_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Év</th>
                                                        <th>Aktuális évi szabadság</th>
                                                        <th>Fennmaradó</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employee->holidays as $holiday)
                                                        <tr class='grey_background holiday_row'>
                                                            <td class="holiday_year">{{ $holiday->holiday_year }}</td>
                                                            <td class="holiday_days"><input class="form-control" type="number" name="holiday_days" value="{{ $holiday->holiday_days }}"></td>
                                                            <td class="holiday_remainings"><input class="form-control" type="number" name="holiday_remainings" value="{{ $holiday->holiday_remainings }}"></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_holiday">Szabadság mentése</button>
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 all_holiday_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Dátum-tól</th>
                                                        <th>Dátum-ig</th>
                                                        <th>Munkanap</th>
                                                        <th>Státusz</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employee->all_holiday as $holiday_all)
                                                        <tr>
                                                            <td>{{ $holiday_all->start_date }}</td>
                                                            <td>{{ $holiday_all->end_date }}</td>
                                                            <td>{{ $holiday_all->days_taken }} nap</td>
                                                            <td>{{ $holiday_all->status->status_name }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </fieldset>
                            </div>



                            <div class="tab-pane fade" id="docs_data" role="tabpanel" aria-labelledby="docs_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0" >
                                                <tr class='grey_background'>
                                                    <td>
                                                        <form action="" class="employee_doc_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-employee_doc_comment">Megjegyzés</label>
                                                                <input type="text" name="employee_doc_comment" id="input-employee_doc_comment" class="form-control">
                                                                <input type="hidden" name="employee_doc_input" value="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-doc_type_id">Dokumentum típus</label>
                                                                <select required name="doc_type_id" id="input-doc_type_id" class="form-control">
                                                                    <option value="">-</option>
                                                                    @foreach ($doc_types as $doc_type)
                                                                        <option value="{{ $doc_type->doc_type_id }}">{{ $doc_type->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-employee_doc_issue_date">Kiállítás dátuma</label>
                                                                <input type="date" name="employee_doc_issue_date" id="input-employee_doc_issue_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-employee_doc_expire_date">Lejárat dátuma</label>
                                                                <input type="date" name="employee_doc_expire_date" id="input-employee_doc_expire_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-employee_doc_reminder_days">Emlékeztető határideje (nap)</label>
                                                                <input type="number" name="employee_doc_reminder_days" id="input-employee_doc_reminder_days" class="form-control">
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr class='grey_background'>
                                                    <td>
                                                        <div class="dropzone_area" >
                                                            <form action="/ajax/upload_employee_doc" id="employee_doc_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                <div class="dz-message needsclick">
                                                                    <span class="">Húzza ide a fájlt vagy kattintson a feltöltéshez! Egyszerre maximum 1 fájlt tud feltölteni.</span>
                                                                    <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                                                                    @csrf
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success mt-4 upload_employee_doc">Feltöltés</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 employee_doc_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Dokumentum</th>
                                                        <th>Megjegyzés</th>
                                                        <th>Típus</th>
                                                        <th>Kiállítás dátuma</th>
                                                        <th>Lejárat dátuma</th>
                                                        <th>Emlékeztető határideje (nap)</th>
                                                        <th>Feltöltés időpontja</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employee->documents as $doc)
                                                        <tr class='grey_background'>
                                                            <td><a href="{{ getenv('APP_URL') .'/document_storage/'. $doc->document_path }}" target="_blank">{{ $doc->document_path }}</a></td>
                                                            <td>{{ $doc->document_comment }}</td>
                                                            <td>@if($doc->document_type_id){{ $doc->doc_type->name }}@endif</td>
                                                            <td>{{ $doc->doc_issue_date }}</td>
                                                            <td>{{ $doc->doc_expire_date }}</td>
                                                            <td>{{ $doc->doc_reminder_days }}</td>
                                                            <td>{{ date('Y. m. d. H:i', strtotime($doc->created_at)) }}</td>
                                                            <td>
                                                                <div data-doc_id="{{ $doc->document_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_doc" data-document_id="{{ $doc->document_id }}">
                                                                        <i class="nc-icon nc-simple-remove"></i> Törlés
                                                                    </button>
                                                                    <button type="button" class="btn btn-warning edit_doc" data-document_id="{{ $doc->document_id }}">
                                                                        <i class="nc-icon nc-simple-remove"></i> Szerkesztés
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </fieldset>
                                    
                            </div>

                                
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('modals')
{{-- Edit Document Modal --}}
<div class="modal fade" id="editDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentum szerkesztése</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDocumentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="edit_document_id">
                    
                    <div class="form-group">
                        <label>Dokumentum típus</label>
                        <select class="form-control" name="document_type_id" id="edit_document_type_id" required>
                            <option value="">Válasszon típust</option>
                            @foreach($documentTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Megjegyzés</label>
                        <textarea class="form-control" name="document_comment" id="edit_document_comment" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Kiadás dátuma</label>
                        <input type="date" class="form-control " name="doc_issue_date" id="edit_doc_issue_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Lejárat dátuma</label>
                        <input type="date" class="form-control " name="doc_expire_date" id="edit_doc_expire_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Emlékeztető napok</label>
                        <input type="number" class="form-control" name="doc_reminder_days" id="edit_doc_reminder_days" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Your existing calculate modal --}}
<div class="modal fade" id="calculateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- ... your existing calculate modal content ... -->
</div>
@endpush
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
    

        Dropzone.autoDiscover = false;


        $("#employee_doc_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'employee_doc',
            uploadMultiple: false,
            addRemoveLinks: true,
            maxFiles: 1,
            acceptedFiles: '.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf,.txt,.csv',
            dictRemoveFile: 'Törlés',
            dictCancelUpload: 'mégse',
            dictDefaultMessage: 'Kattintson vagy húzza ide a fájlt! <br /> (max 50MB)',
            renameFilename: cleanImageNameFn,
            success: function(file, response) {
                //add the file name as hidden input
                $('input[name=employee_doc_input]').val(response);
            },
            removedfile: function(file) {
                $('input[name=employee_doc_input]').val('');

                var class_name = file.name;
                class_name = class_name.replace(/[. ]/g,'');
                jQuery('.' + class_name).remove();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
        });


        $(document).ready(function() {

            $('.save_employee').click(function(){
                $('#employee_form').submit();
            });


            $('.business_select').change(function(e){
                e.stopPropagation();
                let business_ids = $(this).val();
                    $.ajax({
                            method: "POST",
                            url: "/ajax/employee_business_change",
                            data: { business_ids: business_ids,employee_id: {{ $employee->employee_id }} }
                    })
                    .done(function( msg ) {
                        let $warehouseSelect = $('#warehouse_select');
                        $warehouseSelect.empty().append(msg);
                        $warehouseSelect.selectpicker('destroy').selectpicker();
                    });
                
            });

            //Írányítószám részek
            $('#home_zip').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-zips',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(zip) {
                                return {
                                    id: zip.id,
                                    text: zip.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Válasszon irányítószámot..'
            });


            $('#home_zip').on('select2:select', function(e) {
                let selectedZip = e.params.data.id; // Az irányítószám ID-ja.

                $.ajax({
                    url: '/ajax/get-cities/' + selectedZip,
                    type: 'GET',
                    success: function(data) {
                        let citySelector = $('#home_city');
                        citySelector.empty();
                        $.each(data, function(key, value) {
                            citySelector.append(new Option(value.name, value.city_id));
                        });
                    }
                });
            });


            $('#mailing_zip').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-zips',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(zip) {
                                return {
                                    id: zip.id,
                                    text: zip.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Válasszon irányítószámot..'
            });

            $('#temporary_zip').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-zips',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(zip) {
                                return {
                                    id: zip.id,
                                    text: zip.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Válasszon irányítószámot..'
            });

            //Írányítószám részek
            $('#home_city').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-city/',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(city) {
                                return {
                                    id: city.id,
                                    text: city.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Város'
            });
            $('#mailing_city').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-city/',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(city) {
                                return {
                                    id: city.id,
                                    text: city.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Város'
            });
            $('#temporary_city').select2({
                theme: 'bootstrap4',
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get-city/',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(city) {
                                return {
                                    id: city.id,
                                    text: city.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Város'
            });
            
            $('#mailing_zip').on('select2:select', function(e) {
                let selectedZip = e.params.data.id; // Az irányítószám ID-ja.

                $.ajax({
                    url: '/ajax/get-cities/' + selectedZip,
                    type: 'GET',
                    success: function(data) {
                        let citySelector = $('#mailing_city');
                        citySelector.empty();
                        $.each(data, function(key, value) {
                            citySelector.append(new Option(value.name, value.city_id));
                        });
                    }
                });
            });

            $('#temporary_zip').on('select2:select', function(e) {
                let selectedZip = e.params.data.id;

                $.ajax({
                    url: '/ajax/get-cities/' + selectedZip,
                    type: 'GET',
                    success: function(data) {
                        let citySelector = $('#temporary_city');
                        citySelector.empty();
                        $.each(data, function(key, value) {
                            citySelector.append(new Option(value.name, value.city_id));
                        });
                    }
                });
            });
            //Irányítószám részek




            $(document).on('change', 'input[name="status"]', function(){
                if($(this).is(':checked')){
                    $('input[name="note"]').prop('disabled', true).val('');
                    $('input[name="note_date"]').prop('disabled', true).val('');
                }else{
                    $('input[name="note"]').prop('disabled', false).val('');
                    $('input[name="note_date"]').prop('disabled', false).val('');
                }
            });

            $(document).on('change', 'input[name="has_temporary"]', function(){
                let isChecked = $(this).is(':checked');
                let $temporaryZip = $('#temporary_zip');
                let $temporaryCity = $('#temporary_city');
                let $temporaryAddress = $('#temporary_address');
                let $temporaryFloor = $('#temporary_floor');
                let $temporaryDoor = $('#temporary_door');
                
                let $homeZip = $('#home_zip');
                let homeZipText = $homeZip.find('option:selected').text();
                let homeZipValue = $homeZip.val();
                
                let $homeCity = $('#home_city');
                let homeCityText = $homeCity.find('option:selected').text();
                let homeCityValue = $homeCity.val();
                
                let homeAddress = $('#home_address').val();
                let homeFloor = $('#home_floor').val();
                let homeDoor = $('#home_door').val();
                
                if(isChecked){
                    $temporaryZip.empty().append(new Option(homeZipText, homeZipValue));
                    $temporaryCity.empty().append(new Option(homeCityText, homeCityValue));
                    $temporaryAddress.val(homeAddress);
                    $temporaryFloor.val(homeFloor);
                    $temporaryDoor.val(homeDoor);
                } else {
                    $temporaryZip.empty();
                    $temporaryCity.empty();
                    $temporaryAddress.val('');
                    $temporaryFloor.val('');
                    $temporaryDoor.val('');
                }
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


            $('input[name=card]').change(function(){

                if($(this).is(':checked')){
                    $('input[name=card_expiration_year]').prop('disabled', false);
                    $('input[name=card_expiration_month]').prop('disabled', false);
                    $('input[name=card_limit]').prop('disabled', false);
                    $('input[name=internet_limit]').prop('disabled', false);
                    $('input[name=buy_limit]').prop('disabled', false);
                } else {
                    $('input[name=card_expiration_year]').prop('disabled', true);
                    $('input[name=card_expiration_month]').prop('disabled', true);
                    $('input[name=card_limit]').prop('disabled', true);
                    $('input[name=internet_limit]').prop('disabled', true);
                    $('input[name=buy_limit]').prop('disabled', true);
                }

            });


            $(document).on('click', '.upload_employee_doc', function() {

                
                //ha van feltöltve kép, akkor legyen hozzá megadva név
                if(!$('input[name=employee_doc_input]').val().length)
                {
                    $.notify({
                            icon: "done",
                            message: "Hiba"
                        }, {
                            type: 'danger',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    return false;
                }

                if(!$('input[name=employee_doc_comment]').val().length)
                {
                    $.notify({
                            icon: "done",
                            message: "A megjegyzés mező kitöltése kötelező!"
                        }, {
                            type: 'danger',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    return false;
                }
                
                /*
                if(!$(".employee_doc_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('.employee_doc_form')).click().remove();
                    return false;
                }
                */

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_employee_doc_data",
                    data: { employee_id: "{{ $employee->employee_id }}", 
                            employee_doc_comment: $('input[name=employee_doc_comment]').val(), 
                            doc_type_id: $('select[name=doc_type_id]').val(), 
                            employee_doc_issue_date: $('input[name=employee_doc_issue_date]').val(), 
                            employee_doc_expire_date: $('input[name=employee_doc_expire_date]').val(), 
                            employee_doc_reminder_days: $('input[name=employee_doc_reminder_days]').val(), 
                            doc_file: $('input[name=employee_doc_input]').val() }
                })
                .done(function( msg ) {

                    var myDropzone = Dropzone.forElement("#employee_doc_form");
                    myDropzone.removeAllFiles(true);

                    $('input[name=employee_doc_comment]').val('');
                    $('input[name=employee_doc_input]').val('');
                    $('select[name=doc_type_id]').val('');
                    $('input[name=employee_doc_issue_date]').val('');
                    $('input[name=employee_doc_expire_date]').val('');
                    $('input[name=employee_doc_reminder_days]').val('');

                    //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_employee_docs",
                        data: { employee_id: '{{ $employee->employee_id }}' }
                    })
                    .done(function( html ) {
                        $('.employee_doc_table tbody').html(html);
                        $.notify({
                            icon: "done",
                            message: "A dokumentum feltöltése sikeresen megtörtént."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    });
                });
            });

            $(document).on('click', '.delete_doc', function() {

                var doc_id = $(this).attr('data-document_id');
                var row = $(this).parents('tr');

                swal({
                    text: "Biztosan törli ezt a dokumentumot?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen, törlöm',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                },function() {

                    $.ajax({
                        method: "POST",
                        url: "/ajax/delete_doc",
                        data: { doc_id: doc_id }
                    }).done(function( msg ) {
                        row.remove();
                        $.notify({
                            icon: "done",
                            message: "A dokumentum törlése sikeresen megtörtént."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    });

                });

            });

            $(document).on('click', '.save_education', function() {

                if(!$('input[name=education_name]').val().length)
                {
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_education",
                    data: { employee_id: "{{ $employee->employee_id }}", 
                            education_id: $('input[name=education_id]').val(), 
                            school: $('input[name=school]').val(), 
                            education_name: $('input[name=education_name]').val(), 
                            education_start: $('input[name=education_start]').val(), 
                            education_end: $('input[name=education_end]').val()
                        }
                })
                .done(function( msg ) {

                    $('input[name=education_id]').val('');
                    $('input[name=education_name]').val('');
                    $('input[name=school]').val('');
                    $('input[name=education_start]').val('');
                    $('input[name=education_end]').val('');

                    //újra listázás
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_educations",
                        data: { employee_id: '{{ $employee->employee_id }}' }
                    })
                    .done(function( html ) {
                        $('input[name=education_id]').val('');
                        $('.education_table tbody').html(html);
                    });

                    // toastr["success"]("Az állapot sikeresen mentve.");
                });
            });

            $(document).on('click', '.edit_education', function() {
                $('input[name=education_id]').val($(this).attr('data-education_id'));
                $('input[name=education_name]').val($('.education_row_' + $(this).attr('data-education_id')).find('.education_education_name').html());
                $('input[name=school]').val($('.education_row_' + $(this).attr('data-education_id')).find('.education_school').html());
                $('input[name=education_start]').val($('.education_row_' + $(this).attr('data-education_id')).find('.education_education_start').html());
                $('input[name=education_end]').val($('.education_row_' + $(this).attr('data-education_id')).find('.education_education_end').html());

                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('input[name=contact_lastname]').focus();
            });

            $(document).on('click', '.delete_education', function() {

                var education_id = $(this).attr('data-education_id');
                swal({
                    text: "Biztosan törli ezt a végzettséget?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen, törlöm',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                },function() {

                    $.ajax({
                        method: "POST",
                        url: "/ajax/delete_education",
                        data: {
                                education_id: education_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_educations",
                            data: { employee_id: '{{ $employee->employee_id }}' }
                        })
                        .done(function( html ) {
                            $('input[name=education_id]').val('');
                            $('.education_table tbody').html(html);
                        });
                    });

                });

            });


            $('.save_holiday').click(function(){

                if(!$('input[name=holiday_days]').val().length)
                {
                    return false;
                }

                $('.holiday_row').each(function(){

                    var holiday_year = $(this).find('.holiday_year').html();
                    var holiday_days = $(this).find('input[name=holiday_days]').val();
                    var holiday_remainings = $(this).find('input[name=holiday_remainings]').val();

                    $.ajax({
                        method: "POST",
                        url: "/ajax/save_holiday",
                        data: { employee_id: "{{ $employee->employee_id }}", 
                                holiday_year: holiday_year, 
                                holiday_days: holiday_days, 
                                holiday_remainings: holiday_remainings
                            }
                    })
                    .done(function( msg ) {

                        // toastr["success"]("Az állapot sikeresen mentve.");
                        $.notify({
                            icon: "done",
                            message: "Az adatok sikeresen mentve."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    });


                });
            });

            // Dokumentum szerkesztése modal megnyitása
            $(document).on('click', '.edit_doc', function() {
                var doc_id = $(this).attr('data-document_id');
                var $btn = $(this); // Eltároljuk a gomb referenciaját
                var originalHtml = $btn.html(); // Elmentjük az eredeti HTML-t
                // Show loading state
                $(this).html('<i class="nc-icon nc-loading"></i> Betöltés...').prop('disabled', true);
                
                // Load document data via AJAX
                $.ajax({
                    method: "GET",
                    url: "/ajax/get_document/" + doc_id,
                    success: function(response) {
                        // 1. Először töltsük be a dokumentum típusokat a select listába
                        var $docTypeSelect = $('#edit_document_type_id');
                        
                        // Kiürítjük a select mezőt
                        $docTypeSelect.empty();
                        
                        // Hozzáadjuk az alapértelmezett opciót
                        $docTypeSelect.append($('<option>', {
                            value: '',
                            text: 'Válasszon típust'
                        }));
                        
                        // Végigmegyünk a documentTypes tömbön és hozzáadjuk az opciókat
                        $.each(response.documentTypes, function(index, type) {
                            $docTypeSelect.append($('<option>', {
                                value: type.doc_type_id,  // vagy type.id, attól függően, hogy mi a mező neve
                                text: type.name
                            }));
                        });
                        
                        // 2. Most beállítjuk a kiválasztott értéket (selected)
                        $docTypeSelect.val(response.document_type_id);
                        
                        // 3. Beállítjuk a többi mező értékét
                        $('#edit_document_id').val(response.document_id);
                        $('#edit_document_comment').val(response.document_comment);
                        $('#edit_doc_issue_date').val(response.doc_issue_date);
                        $('#edit_doc_expire_date').val(response.doc_expire_date);
                        $('#edit_doc_reminder_days').val(response.doc_reminder_days);
                        
                        // Update form action URL
                        $('#editDocumentForm').attr('action', '/documents/' + doc_id);
                        
                        // Show the modal
                        $('#editDocumentModal').modal('show');
                        $btn.html(originalHtml).prop('disabled', false);
                        // Reinitialize datepickers if needed
                        if ($.fn.datepicker) {
                            $('.datepicker').datepicker('update');
                        }
                        
                        // Ha Select2-t használsz, frissítsd
                        if ($.fn.select2) {
                            $docTypeSelect.trigger('change');
                        }
                        
                        // Visszaállítjuk a gomb állapotát
                        $('.edit_doc').html('<i class="nc-icon nc-simple-remove"></i> Szerkesztés').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        $.notify({
                            icon: "error",
                            message: "Hiba történt a dokumentum betöltése során."
                        }, {
                            type: 'danger',
                            timer: 3000,
                            placement: {
                                from: 'top',
                                align: 'right'
                            }
                        });
                        
                        // Visszaállítjuk a gomb állapotát hiba esetén is
                        $('.edit_doc').html('<i class="nc-icon nc-simple-remove"></i> Szerkesztés').prop('disabled', false);
                    }
                });
            });

            // Handle form submission
            $('#editDocumentForm').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var formData = form.serialize();
                var submitBtn = form.find('button[type="submit"]');
                var doc_id = $('#edit_document_id').val();
                
                // Disable submit button and show loading
                submitBtn.html('<i class="nc-icon nc-loading"></i> Mentés...').prop('disabled', true);
                
                $.ajax({
                    method: "PUT", // Using POST with _method=PUT for Laravel
                    url: "/ajax/documents/" + doc_id,
                    data: formData,
                    success: function(response) {
                        // Close modal
                        $('#editDocumentModal').modal('hide');
                          
                        // Show success message
                        $.notify({
                            icon: "done",
                            message: "A dokumentum módosítása sikeresen megtörtént."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                                from: 'top',
                                align: 'right'
                            }
                        });
                        
                        // Option 1: Update the row in the table
                        updateTableRow(doc_id, response);
                        
                        // Option 2: Reload the page or table
                        // location.reload();
                        // or if you have a DataTable: $('#your-table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = "Hiba történt a mentés során.";
                        
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                        
                        $.notify({
                            icon: "error",
                            message: errorMessage
                        }, {
                            type: 'danger',
                            timer: 5000,
                            placement: {
                                from: 'top',
                                align: 'right'
                            }
                        });
                    },
                    complete: function() {
                        // Reset submit button
                        submitBtn.html('Mentés').prop('disabled', false);
                    }
                });
            });

            // Function to update table row with new data
            function updateTableRow(doc_id, newData) {
               
                
                //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_employee_docs",
                        data: { employee_id: '{{ $employee->employee_id }}' }
                    })
                    .done(function( html ) {
                        $('.employee_doc_table tbody').html(html);
                        $.notify({
                            icon: "done",
                            message: "A dokumentum feltöltése sikeresen megtörtént."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                              from: 'top',
                              align: 'right'
                            }
                        });
                    });
                
            }

            // Clear form when modal is closed
            $('#editDocumentModal').on('hidden.bs.modal', function() {
                $('#editDocumentForm')[0].reset();
                $('#editDocumentForm').attr('action', '');
            });

        });
    </script>
@endpush