@extends('layouts.app', ['activePage' => 'company', 'activeButton' => 'company', 'title' => 'Zemef ERP', 'navName' => 'Cég szerkesztése' ])

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
                            <a class="nav-link" id="addresses_data-tab" data-toggle="pill" href="#addresses_data" role="tab" aria-controls="addresses_data" aria-selected="true">Címek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="loans_data-tab" data-toggle="pill" href="#loans_data" role="tab" aria-controls="loans_data" aria-selected="true">Hitelek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="loans_statistics-tab" data-toggle="pill" href="#loans_statistics" role="tab" aria-controls="loans_statistics" aria-selected="true">Hitel kimutatás</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contacts_data-tab" data-toggle="pill" href="#contacts_data" role="tab" aria-controls="contacts_data" aria-selected="true">Kapcsolattartók</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="employees_data-tab" data-toggle="pill" href="#employees_data" role="tab" aria-controls="employees_data" aria-selected="true">Munkavállalók</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="docs_data-tab" data-toggle="pill" href="#docs_data" role="tab" aria-controls="docs_data" aria-selected="true">Dokumentumok</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0"></h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('company.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form id="company_form" method="post" action="{{ route('company.update', $company->company_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

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
                                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $company->name) }}" placeholder="" required autofocus>
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
                                                            <input type="text" class="form-control{{ $errors->has('web') ? ' is-invalid' : '' }}" name="web" maxlength="100" value="{{ old('web', $company->web) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'web'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('company_registration_number') ? ' has-danger' : '' }}">
                                                                Cégjegyzékszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('company_registration_number') ? ' is-invalid' : '' }}" name="company_registration_number" maxlength="100" value="{{ old('company_registration_number', $company->company_registration_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'company_registration_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tax_number') ? ' has-danger' : '' }}">
                                                                Adószám</label>
                                                            <input type="text" class="form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="100" value="{{ old('tax_number', $company->tax_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'tax_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('registration_number') ? ' has-danger' : '' }}">
                                                                Nyilvántartási szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('registration_number') ? ' is-invalid' : '' }}" name="registration_number" maxlength="100" value="{{ old('registration_number', $company->registration_number) }}" placeholder="">
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
                                                                <input @if($company->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('activities') ? ' has-danger' : '' }}">
                                                                Tevékenységek</label>
                                                            <select multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="activities[]" class="selectpicker form-control{{ $errors->has('activities') ? ' is-invalid' : '' }}">
                                                            @foreach($activities as $activity)
                                                                <option @if(in_array($activity->activity_id,$company->activities->pluck('activity_id')->toArray())) selected @endif value="{{ $activity->activity_id }}" >{{ $activity->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bank_accounts') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Bankszámlák</label>
                                                        <select title="- válasszon bankszámlákat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="bank_accounts[]" id="input-role" class="selectpicker form-control{{ $errors->has('bank_accounts') ? ' is-invalid' : '' }}" placeholder="">
                                                            @foreach($bank_accounts as $bank_account)
                                                                <option @if(in_array($bank_account->bank_account_id, $company->bank_accounts->pluck('bank_account_id')->toArray())) selected @endif value="{{ $bank_account->bank_account_id }}" value="{{ $bank_account->bank_account_id }}" >{{ $bank_account->bank_account_number . ' ('.$bank_account->bank_account_number.')' }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'bank_accounts'])
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                
                                </div>
                                
                                <button type="button" class="btn btn-success save_company">Mentés</button>
                            </div>

                            <div class="tab-pane fade" id="addresses_data" role="tabpanel" aria-labelledby="addresses_data-tab">
                                <fieldset>
                                    <input type="hidden" name="address_id" value="">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        
                                            <div class="form-group">
                                                <label class="form-control-label" for="address_type">Cím típus</label>
                                                <select name="address_type" id="input-address_type" class="form-control">
                                                    <option value="Székhely">Székhely</option>
                                                    <option value="Levelezési cím">Levelezési cím</option>
                                                    <option value="Telephely">Telephely</option>
                                                    <option value="Fiók">Fiók</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="zip">Irányítószám</label>
                                                <select name="zip" id="zip" class="select2 form-control" style="width: 100%;">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="city">Település</label>
                                                <select name="city" id="city" class="select2 form-control" style="width: 100%;">
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="address">Cím</label>
                                                <input type="text" name="address" id="address" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label" for="floor">Emelet</label>
                                                <input type="text" name="floor" id="floor" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label" for="door">Ajtó</label>
                                                <input type="text" name="door" id="door" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_address"><i class="fa fa-save"></i> Cím mentése</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 address_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Cím típus</th>
                                                        <th>Irányítószám</th>
                                                        <th>Település</th>
                                                        <th>Cím</th>
                                                        <th>Emelet</th>
                                                        <th>Ajtó</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($company->addresses as $address)
                                                        <tr class='grey_background address_row_{{$address->address_id}}'>
                                                            <td class="address_type">{{ $address->address_type }}</td>
                                                            <td class="address_zip" data-id="{{ $address->zip_id }}">{{ $address->zip }}</td>
                                                            <td class="address_city" data-id="{{ $address->city_id }}">{{ $address->city }}</td>
                                                            <td class="address_address">{{ $address->address }}</td>
                                                            <td class="address_floor">{{ $address->floor }}</td>
                                                            <td class="address_door">{{ $address->door }}</td>
                                                            <td>
                                                                <div style="display: inline-block;" data-address_id="{{ $address->address_id }}" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-success edit_address" data-address_id="{{ $address->address_id }}">
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>
                                                                </div>
                                                                <div style="display: inline-block;" data-address_id="{{ $address->address_id }}" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-danger delete_address" data-address_id="{{ $address->address_id }}">
                                                                        <i class="fa fa-trash"></i>
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


                            <div class="tab-pane fade" id="contacts_data" role="tabpanel" aria-labelledby="contacts_data-tab">

                                <fieldset>
                                    <input type="hidden" name="contact_id" value="">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="lastname">Vezetéknév</label>
                                                <input type="text" name="contact_lastname" id="lastname" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="firstname">Keresztnév</label>
                                                <input type="text" name="contact_firstname" id="firstname" class="form-control">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="phone">Telefonszám</label>
                                                <input type="text" name="contact_phone" id="phone" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="email">Email cím</label>
                                                <input type="email" name="contact_email" id="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="rank">Beosztás</label>
                                                <input type="text" name="contact_rank" id="rank" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="comment">Megjegyzés</label>
                                                <textarea type="text" name="contact_comment" id="comment" class="form-control zm_textarea"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_contact"><i class="fa fa-save"></i> Kapcsolattartó mentése</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 contact_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Vezetéknév</th>
                                                        <th>Keresztnév</th>
                                                        <th>Telefonszám</th>
                                                        <th>Email cím</th>
                                                        <th>Beosztás</th>
                                                        <th>Megjegyzés</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($company->contacts as $contact)
                                                        <tr class='grey_background contact_row_{{$contact->contact_id}}'>
                                                            <td class="contact_lastname">{{ $contact->lastname }}</td>
                                                            <td class="contact_firstname">{{ $contact->firstname }}</td>
                                                            <td class="contact_phone">{{ $contact->phone }}</td>
                                                            <td class="contact_email">{{ $contact->email }}</td>
                                                            <td class="contact_rank">{{ $contact->rank }}</td>
                                                            <td class="contact_comment">{{ $contact->comment }}</td>
                                                            <td>
                                                                <div style='display: inline-block;' data-contact_id="{{ $contact->contact_id }}" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-success edit_contact" data-contact_id="{{ $contact->contact_id }}">
                                                                        <i class='fa fa-edit'></i>
                                                                    </button>
                                                                </div>
                                                                <div style='display: inline-block;' data-contact_id="{{ $contact->contact_id }}" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-danger delete_contact" data-contact_id="{{ $contact->contact_id }}">
                                                                        <i class='fa fa-trash'></i>
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


                            <div class="tab-pane fade" id="employees_data" role="tabpanel" aria-labelledby="employees_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 employee_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Vezetéknév</th>
                                                        <th>Keresztnév</th>
                                                        <th>Céges telefonszám</th>
                                                        <th>Magán telefonszám</th>
                                                        <th>Magán email cím</th>
                                                        <th>Szül. hely, idő</th>
                                                        <th>Bankkártya</th>
                                                        <th>Bankkártya lejárati dátum</th>
                                                        <th>Pénzfelvételi limit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($company->employees as $employee)
                                                        <tr class='grey_background employee_row' data-employee_id="{{ $employee->employee_id }}">
                                                            <td class="employee_lastname">{{ $employee->lastname }}</td>
                                                            <td class="employee_firstname">{{ $employee->firstname }}</td>
                                                            <td class="employee_company_phone">{{ $employee->company_phone }}</td>
                                                            <td class="employee_private_phone">{{ $employee->private_phone }}</td>
                                                            <td class="employee_private_email">{{ $employee->private_email }}</td>
                                                            <td class="employee_birth">{{ $employee->birth_place . ', ' . $employee->birth_date }}</td>
                                                            <td class="employee_card">
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <label class="form-check-label">
                                                                        <input data-employee_id="{{ $employee->employee_id }}" @if($employee->card) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="card">
                                                                        <span class="form-check-sign"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="employee_card_expiration">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <input data-employee_id="{{ $employee->employee_id }}" @if(!$employee->card) disabled @endif type="number" max="99" class="input-sm form-control{{ $errors->has('card_expiration_year') ? ' is-invalid' : '' }}" name="card_expiration_year" value="{{ old('card_expiration_year', $employee->card_expiration_year) }}" placeholder="ÉÉ">
                                                                    </div>
                                                                    <div class="col-lg-1">
                                                                        <span class="slash">/</span>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <input data-employee_id="{{ $employee->employee_id }}" @if(!$employee->card) disabled @endif type="number" min="1" max="12" class="input-sm form-control{{ $errors->has('card_expiration_month') ? ' is-invalid' : '' }}" name="card_expiration_month" value="{{ old('card_expiration_month', $employee->card_expiration_month) }}" placeholder="HH">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="employee_card_limit">
                                                                <input data-employee_id="{{ $employee->employee_id }}" @if(!$employee->card) disabled @endif type="number" class="input-sm form-control{{ $errors->has('card_limit') ? ' is-invalid' : '' }}" name="card_limit" value="{{ old('card_limit', $employee->card_limit) }}" placeholder="">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-success save_employees">Munkavállalók mentése</button>
                                                    </div>
                                                </div>
                                            </div>
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
                                                        <form action="" class="company_doc_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-company_doc_comment">Megjegyzés</label>
                                                                <input type="text" name="company_doc_comment" id="input-company_doc_comment" class="form-control">
                                                                <input type="hidden" name="company_doc_input" value="">
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
                                                                <label class="form-control-label" for="input-company_doc_issue_date">Kiállítás dátuma</label>
                                                                <input type="date" name="company_doc_issue_date" id="input-company_doc_issue_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-company_doc_expire_date">Lejárat dátuma</label>
                                                                <input type="date" name="company_doc_expire_date" id="input-company_doc_expire_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-company_doc_reminder_days">Emlékeztető határideje (nap)</label>
                                                                <input type="number" name="company_doc_reminder_days" id="input-company_doc_reminder_days" class="form-control">
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr class='grey_background'>
                                                    <td>
                                                        <div class="dropzone_area" >
                                                            <form action="/ajax/upload_company_doc" id="company_doc_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                <div class="dz-message needsclick">
                                                                    <span class="">Húzza ide a fájlt vagy kattintson a feltöltéshez! Egyszerre maximum 1 fájlt tud feltölteni.</span>
                                                                    <input type="hidden" name="company_id" value="{{ $company->company_id }}">
                                                                    @csrf
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success mt-4 upload_company_doc">Feltöltés</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 company_doc_table" >
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
                                                    @foreach($company->documents as $doc)
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


                            <div class="tab-pane fade" id="loans_data" role="tabpanel" aria-labelledby="loans_data-tab">
                                <fieldset>
                                    <input type="hidden" name="company_id" value="">
                                    <div class="row">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="education_name">Pénzintézet</label>
                                                <select required name="bank_id" id="input-bank_id" class="form-control">
                                                    <option value="">-</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->bank_id }}">{{ $bank->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="private">
                                                    <span class="form-check-sign"></span>
                                                    Magán
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="loan_type">Típus</label>
                                                <input type="text" name="loan_type" id="loan_type" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="amount">Összeg</label>
                                                <input type="number" name="amount" id="amount" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="loan_end_date">Lejárat</label>
                                                <input type="date" name="loan_end_date" id="loan_end_date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="loan_monthly">Havidíj</label>
                                                <input type="number" step="0.01" name="loan_monthly" id="loan_monthly" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_loan">Hitel mentése</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 loan_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Pénzintézet</th>
                                                        <th>Magán</th>
                                                        <th>Típus</th>
                                                        <th>Összeg</th>
                                                        <th>Lejárat</th>
                                                        <th>Havídíj</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($company->loans as $loan)
                                                        <tr class='grey_background loan_row_{{$loan->loan_id}}'>
                                                            <td data-bank_id="{{ $loan->bank->bank_id }}" class="loan_bank_name">{{ $loan->bank->name }}</td>
                                                            <td class="loan_private">@if($loan->private) Igen @else Nem @endif</td>
                                                            <td class="loan_type">{{ $loan->loan_type }}</td>
                                                            <td class="loan_amount">{{ $loan->amount }}</td>
                                                            <td class="loan_end_date">{{ $loan->loan_end_date }}</td>
                                                            <td class="loan_monthly">{{ $loan->loan_monthly }}</td>
                                                            <td>
                                                                <div data-loan_id="{{ $loan->loan_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-success edit_loan" data-loan_id="{{ $loan->loan_id }}">
                                                                        Szerkesztés
                                                                    </button>
                                                                </div>
                                                                <div data-loan_id="{{ $loan->loan_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_loan" data-loan_id="{{ $loan->loan_id }}">
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


                            <div class="tab-pane fade" id="loans_statistics" role="tabpanel" aria-labelledby="loans_statistics-tab">
                                <fieldset>
                                    <div class="row">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="education_name">Hitel(ek) kiválasztása</label>
                                                <select title="- hitel(ek) kiválasztása -"  data-actions-box="true" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="selected_loans[]" id="loan_select" class="selectpicker form-control" placeholder="">
                                                    @foreach ($company->loans as $loan)
                                                        <option value="{{ $loan->loan_id }}">{{ $loan->loan_type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>   
                                        <div class="col-lg-3 mb-0">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success mt-4 get_loan_statistics">Statisztika lekérdezése</button>
                                            </div>
                                        </div>                                  
                                    </div>
                                </fieldset>  
                                
                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- loanProgressChart -->
                                            <canvas id="loanProgressChart"></canvas>
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

        Dropzone.autoDiscover = false;


        $("#company_doc_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'company_doc',
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
                $('input[name=company_doc_input]').val(response);
            },
            removedfile: function(file) {
                $('input[name=company_doc_input]').val('');

                var class_name = file.name;
                class_name = class_name.replace(/[. ]/g,'');
                jQuery('.' + class_name).remove();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
        });

        let loanChart;

        function renderLoanProgressChart(data) {
            let labels = data.map(item => item.loan_type);
            let progressData = data.map(item => item.progress_percentage);

            if (loanChart) {
                loanChart.destroy();
            }

            let ctx = document.getElementById('loanProgressChart').getContext('2d');
            loanChart = new Chart(ctx, {
                type: 'bar', // Használhatsz 'line'-t is, ha vonalgörbét szeretnél
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hitel előrehaladás (%)',
                            data: progressData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }


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

            //get_loan_statistics
            $('.get_loan_statistics').click(function(){

                let selected_loans = $('#loan_select').val();
                console.log(selected_loans);
                if(selected_loans.length == 0)
                {
                    $.notify({ icon: "done", message: "Válasszon ki legalább egy hitelt." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/getCompanyLoansProgress",
                    data: { selected_loans: selected_loans,company_id: '{{ $company->company_id }}' }
                })
                .done(function( msg ) {
                    renderLoanProgressChart(msg);
                });

            });


            $('.upload_company_doc').click(function(){

                
                //ha van feltöltve kép, akkor legyen hozzá megadva név
                if(!$('input[name=company_doc_input]').val().length)
                {
                    return false;
                }
                
                /*
                if(!$(".company_doc_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('.company_doc_form')).click().remove();
                    return false;
                }
                */

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_company_doc_data",
                    data: { company_id: "{{ $company->company_id }}", 
                            company_doc_comment: $('input[name=company_doc_comment]').val(), 
                            doc_type_id: $('select[name=doc_type_id]').val(), 
                            company_doc_issue_date: $('input[name=company_doc_issue_date]').val(), 
                            company_doc_expire_date: $('input[name=company_doc_expire_date]').val(), 
                            company_doc_reminder_days: $('input[name=company_doc_reminder_days]').val(), 
                            doc_file: $('input[name=company_doc_input]').val() }
                })
                .done(function( msg ) {

                    var myDropzone = Dropzone.forElement("#company_doc_form");
                    myDropzone.removeAllFiles(true);

                    $('input[name=company_doc_comment]').val('');
                    $('input[name=company_doc_input]').val('');
                    $('select[name=doc_type_id]').val('');
                    $('input[name=company_doc_issue_date]').val('');
                    $('input[name=company_doc_expire_date]').val('');
                    $('input[name=company_doc_reminder_days]').val('');

                    //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_company_docs",
                        data: { company_id: '{{ $company->company_id }}' }
                    })
                    .done(function( html ) {
                        $('.company_doc_table tbody').html(html);
                    });

                    // toastr["success"]("A fájl sikeresen feltöltve.");
                });
            });

            $('body').on('click', '.delete_doc', function() {

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
                        toastr.success('Dokumentum sikeresen törölve.');
                    });

                });

            });

            //Írányítószám részek
            $('#zip').select2({
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


            $('#zip').on('select2:select', function(e) {
                let selectedZip = e.params.data.id;

                $.ajax({
                    url: '/ajax/get-cities/' + selectedZip,
                    type: 'GET',
                    success: function(data) {
                        let citySelector = $('#city');
                        citySelector.empty();
                        $.each(data, function(key, value) {
                            citySelector.append(new Option(value.name, value.city_id));
                        });
                    }
                });
            });
            //Irányítószám részek
            $('#city').select2({
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

            $('.save_address').click(function(){

                let zip = $('select[name=zip]').val();
                let city = $('select[name=city]').val();
                if(!$('input[name=address]').val().length)
                {
                    $.notify({ icon: "done", message: "A cím nem lehet üres." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }
                if(!zip.length)
                {
                    $.notify({ icon: "done", message: "Az irányítószám nem lehet üres." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }
                if(!city.length)
                {
                    $.notify({ icon: "done", message: "A város nem lehet üres." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_company_address",
                    data: { company_id: "{{ $company->company_id }}", 
                            address_id: $('input[name=address_id]').val(), 
                            address_type: $('select[name=address_type]').val(), 
                            zip: $('select[name=zip]').val(), 
                            city: $('select[name=city]').val(), 
                            address: $('input[name=address]').val(), 
                            floor: $('input[name=floor]').val(), 
                            door: $('input[name=door]').val()
                        }
                })
                .done(function( msg ) {

                    $('select[name=zip]').empty().trigger('change');
                    $('select[name=city]').empty().trigger('change');
                    $('input[name=address]').val('');
                    $('input[name=floor]').val('');
                    $('input[name=door]').val('');
                    $('input[name=address_id]').val('');

                    //újra listázás
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_company_addresses",
                        data: { company_id: '{{ $company->company_id }}' }
                    })
                    .done(function( html ) {
                        $('.address_table tbody').html(html);
                    });

                    $.notify({ icon: "done", message: "Sikeres mentés!" }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                });
            });


            $('body').on('click', '.edit_address', function() {

                let address_id = $(this).attr('data-address_id');
                let address_type = $('.address_row_' + $(this).attr('data-address_id')).find('.address_type').html();
                let zip = $('.address_row_' + $(this).attr('data-address_id')).find('.address_zip').html();
                let zip_id = $('.address_row_' + $(this).attr('data-address_id')).find('.address_zip').data('id');
                let city = $('.address_row_' + $(this).attr('data-address_id')).find('.address_city').html();
                let city_id = $('.address_row_' + $(this).attr('data-address_id')).find('.address_city').data('id');
                let address = $('.address_row_' + $(this).attr('data-address_id')).find('.address_address').html();
                let floor = $('.address_row_' + $(this).attr('data-address_id')).find('.address_floor').html();
                let door = $('.address_row_' + $(this).attr('data-address_id')).find('.address_door').html();
                
                $('input[name=address_id]').val(address_id);
                $('select[name=address_type]').val(address_type);
                $('#zip').append('<option selected value="'+zip_id+'">'+zip+'</option>').trigger('change');
                $('#city').append('<option selected value="'+city_id+'">'+city+'</option>').trigger('change');
                $('input[name=address]').val(address);
                $('input[name=floor]').val(floor);
                $('input[name=door]').val(door);
                

                $("html, body").animate({ scrollTop: 0 }, "slow");
            });

            $('body').on('click', '.delete_address', function() {

                var address_id = $(this).attr('data-address_id');
                swal({
                    text: "Biztosan törli ezt a címet?",
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
                        url: "/ajax/delete_company_address",
                        data: {
                                address_id: address_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_company_addresses",
                            data: { company_id: '{{ $company->company_id }}' }
                        })
                        .done(function( html ) {
                            $('.address_table tbody').html(html);
                            $.notify({ icon: "done", message: "Sikeres törlés!" }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        });
                    });

                });

            });


            $('.save_contact').click(function(){

                if(!$('input[name=contact_lastname]').val().length)
                {
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_company_contact",
                    data: { company_id: "{{ $company->company_id }}", 
                            contact_id: $('input[name=contact_id]').val(), 
                            firstname: $('input[name=contact_firstname]').val(), 
                            lastname: $('input[name=contact_lastname]').val(), 
                            phone: $('input[name=contact_phone]').val(), 
                            email: $('input[name=contact_email]').val(), 
                            rank: $('input[name=contact_rank]').val(),
                            comment: $('textarea[name=contact_comment]').val()
                        }
                })
                .done(function( msg ) {

                    $('input[name=contact_firstname]').val('');
                    $('input[name=contact_lastname]').val('');
                    $('input[name=contact_phone]').val('');
                    $('input[name=contact_email]').val('');
                    $('input[name=contact_rank]').val('');
                    $('input[name=contact_id]').val('');
                    $('textarea[name=contact_comment]').val('');

                    //újra listázás
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_company_contacts",
                        data: { company_id: '{{ $company->company_id }}' }
                    })
                    .done(function( html ) {
                        $('.contact_table tbody').html(html);
                    });

                    // toastr["success"]("Az állapot sikeresen mentve.");
                });
            });


            $('.save_employees').click(function(){

                $('.employee_row').each(function(){
                    var emp_id = $(this).attr('data-employee_id');
                    $.ajax({
                        method: "POST",
                        url: "/ajax/save_company_employee",
                        data: { company_id: "{{ $company->company_id }}", 
                                employee_id: emp_id, 
                                card: $('input[name=card][data-employee_id='+emp_id+']').is(':checked'), 
                                card_expiration_year: $('input[name=card_expiration_year][data-employee_id='+emp_id+']').val(), 
                                card_expiration_month: $('input[name=card_expiration_month][data-employee_id='+emp_id+']').val(), 
                                card_limit: $('input[name=card_limit][data-employee_id='+emp_id+']').val()
                            }
                    });
                });

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



            $('body').on('click', '.edit_contact', function() {

                $('input[name=contact_id]').val($(this).attr('data-contact_id'));
                $('input[name=contact_firstname]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_firstname').html());
                $('input[name=contact_lastname]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_lastname').html());
                $('input[name=contact_phone]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_phone').html());
                $('input[name=contact_email]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_email').html());
                $('input[name=contact_rank]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_rank').html());
                $('textarea[name=contact_comment]').val($('.contact_row_' + $(this).attr('data-contact_id')).find('.contact_comment').html());

                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('input[name=contact_lastname]').focus();
            });

            $('body').on('click', '.delete_contact', function() {

                var contact_id = $(this).attr('data-contact_id');
                swal({
                    text: "Biztosan törli ezt a kapcsolattartót?",
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
                        url: "/ajax/delete_company_contact",
                        data: {
                                contact_id: contact_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_company_contacts",
                            data: { company_id: '{{ $company->company_id }}' }
                        })
                        .done(function( html ) {
                            $('.contact_table tbody').html(html);
                        });
                    });

                });

            });



            $('.save_loan').click(function(){

                if(!$('select[name=bank_id]').val().length)
                {
                    return false;
                }

                var priv = 0;
                if($('input[name=private]').is(':checked')){
                    priv = 1;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_loan",
                    data: { company_id: "{{ $company->company_id }}", 
                            loan_id: $('input[name=loan_id]').val(), 
                            bank_id: $('select[name=bank_id]').val(), 
                            amount: $('input[name=amount]').val(), 
                            loan_end_date: $('input[name=loan_end_date]').val(), 
                            loan_monthly: $('input[name=loan_monthly]').val(),
                            loan_type: $('input[name=loan_type]').val(),
                            private: priv
                        }
                })
                .done(function( msg ) {

                    $('input[name=loan_id]').val('');
                    $('select[name=bank_id]').val('');
                    $('input[name=amount]').val('');
                    $('input[name=loan_end_date]').val('');
                    $('input[name=loan_monthly]').val('');
                    $('input[name=loan_type]').val('');

                    //újra listázás
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_loans",
                        data: { company_id: '{{ $company->company_id }}' }
                    })
                    .done(function( html ) {
                        $('.loan_table tbody').html(html);
                    });

                    // toastr["success"]("Az állapot sikeresen mentve.");
                });
            });


            $('input[name=card]').change(function(){

                if($(this).is(':checked')){
                    $('input[name=card_expiration_year][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', false);
                    $('input[name=card_expiration_month][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', false);
                    $('input[name=card_limit][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', false);
                } else {
                    $('input[name=card_expiration_year][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', true);
                    $('input[name=card_expiration_month][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', true);
                    $('input[name=card_limit][data-employee_id='+$(this).attr('data-employee_id')+']').prop('disabled', true);
                }

            });


            $('body').on('click', '.edit_loan', function() {
                $('input[name=loan_id]').val($(this).attr('data-loan_id'));
                $('select[name=bank_id]').val($('.loan_row_' + $(this).attr('data-loan_id')).find('.loan_bank_id').attr('data-loan_id'));
                $('input[name=amount]').val($('.loan_row_' + $(this).attr('data-loan_id')).find('.loan_amount').html());
                $('input[name=loan_end_date]').val($('.loan_row_' + $(this).attr('data-loan_id')).find('.loan_loan_end_date').html());
                $('input[name=loan_monthly]').val($('.loan_row_' + $(this).attr('data-loan_id')).find('.loan_loan_monthly').html());
                $('input[name=loan_type]').val($('.loan_row_' + $(this).attr('data-loan_id')).find('.loan_loan_type').html());

                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('select[name=bank_id]').focus();
            });

            $('body').on('click', '.delete_loan', function() {

                var loan_id = $(this).attr('data-loan_id');
                swal({
                    text: "Biztosan törli ezt a hitelt?",
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
                        url: "/ajax/delete_loan",
                        data: {
                                loan_id: loan_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_loans",
                            data: { company_id: '{{ $company->company_id }}' }
                        })
                        .done(function( html ) {
                            $('.loan_table tbody').html(html);
                        });
                    });

                });

            });
        });
    </script>
@endpush