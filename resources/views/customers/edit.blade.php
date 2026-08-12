@extends('layouts.app', ['activePage' => 'customer', 'activeButton' => 'customer', 'title' => 'Zemef ERP', 'navName' => '
Ügyfél szerkesztése' ])

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
                            <a class="nav-link" id="contacts_data-tab" data-toggle="pill" href="#contacts_data" role="tab" aria-controls="contacts_data" aria-selected="true">Kapcsolattartók</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="customer_buy_history-tab" data-toggle="pill" href="#customer_buy_history" role="tab" aria-controls="customer_buy_history" aria-selected="true">Vásárlási előzmények</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="customer_markings-tab" data-toggle="pill" href="#customer_markings" role="tab" aria-controls="customer_markings" aria-selected="false">Szállítói jelzések</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0"></h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="{{ route('customer.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form id="customer_form" method="post" action="{{ route('customer.update', $customer->customer_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Ügyfél alap adatai</h6>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                                Név</label>
                                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $customer->name) }}" placeholder="" required autofocus>
                                                            @include('alerts.feedback', ['field' => 'name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if(!$customer->is_company) checked @endif class="form-check-input" type="radio" name="is_company" id="is_company0" value="0" data-gtm-form-interact-field-id="0">
                                                            <span class="form-check-sign"></span>
                                                            Magánszemély
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if($customer->is_company) checked @endif class="form-check-input" type="radio" name="is_company" id="is_company1" value="1" data-gtm-form-interact-field-id="0">
                                                            <span class="form-check-sign"></span>
                                                            Cég
                                                            </label>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                                                                Keresztnév</label>
                                                            <input type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" maxlength="100" value="{{ old('firstname', $customer->firstname) }}">
                                                            @include('alerts.feedback', ['field' => 'firstname'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                                                                Névnap</label>
                                                            <select name="name_day" id="name_day" class="form-control{{ $errors->has('name_day') ? ' is-invalid' : '' }} select2">
                                                                <option value="">- válasszon -</option>
                                                                @if($customer->name_day)
                                                                    <option value="{{ $customer->name_day }}" selected>{{ $customer->name_day }}</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('mothers_name') ? ' has-danger' : '' }}">
                                                                Anyja neve</label>
                                                            <input type="text" class="only_private form-control{{ $errors->has('mothers_name') ? ' is-invalid' : '' }}" name="mothers_name" maxlength="100" value="{{ old('mothers_name', $customer->mothers_name) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'mothers_name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_place') ? ' has-danger' : '' }}">
                                                                Születési hely</label>
                                                            <input type="text" class="only_private form-control{{ $errors->has('birth_place') ? ' is-invalid' : '' }}" name="birth_place" maxlength="100" value="{{ old('birth_place', $customer->birth_place) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'birth_place'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('birth_date') ? ' has-danger' : '' }}">
                                                                Születési idő</label>
                                                            <input type="date" class="only_private form-control{{ $errors->has('birth_date') ? ' is-invalid' : '' }}" name="birth_date" value="{{ old('birth_date', $customer->birth_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'birth_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('created_at') ? ' has-danger' : '' }}">
                                                                Mióta ügyfél?</label>
                                                            <input type="date" class="form-control{{ $errors->has('created_at') ? ' is-invalid' : '' }}" name="created_at" value="{{ old('created_at', $customer->created_at->format('Y-m-d')) }}" placeholder="" required>
                                                            @include('alerts.feedback', ['field' => 'created_at'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tax_identity') ? ' has-danger' : '' }}">
                                                                Adóazonosítójel</label>
                                                            <input
                                                                type="text"
                                                                class="only_private form-control{{ $errors->has('tax_identity') ? ' is-invalid' : '' }}"
                                                                name="tax_identity"
                                                                maxlength="10"
                                                                inputmode="numeric"
                                                                pattern="^\d{10}$"
                                                                title="Formátum: 10 számjegy"
                                                                value="{{ old('tax_identity', $customer->tax_identity) }}"
                                                                placeholder="1234567890"
                                                            >
                                                            @include('alerts.feedback', ['field' => 'tax_identity'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('card_number') ? ' has-danger' : '' }}">
                                                                Kártyaszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('card_number') ? ' is-invalid' : '' }}" name="card_number" maxlength="100" value="{{ old('card_number', $customer->card_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('identity_card_number') ? ' has-danger' : '' }}">
                                                                Személyi igazolvány szám</label>
                                                            <input type="text" class="only_private form-control{{ $errors->has('identity_card_number') ? ' is-invalid' : '' }}" name="identity_card_number" maxidentity_card_number="100" value="{{ old('identity_card_number', $customer->identity_card_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'identity_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('web_address') ? ' has-danger' : '' }}">
                                                                Web cím</label>
                                                            <input type="text" class="only_company form-control{{ $errors->has('web_address') ? ' is-invalid' : '' }}" name="web_address" maxlength="100" value="{{ old('web_address', $customer->web_address) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'web_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('company_registration_number') ? ' has-danger' : '' }}">
                                                                Cégjegyzékszám</label>
                                                            <input type="number" class="only_company form-control{{ $errors->has('company_registration_number') ? ' is-invalid' : '' }}" name="company_registration_number" value="{{ old('company_registration_number', $customer->company_registration_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'company_registration_number'])
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
                                                            <input type="text" class="only_company form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="12" inputmode="numeric" pattern="^\d{7}-\d-\d{2}$" title="Formátum: xxxxxxx-y-zz" value="{{ old('tax_number', $customer->tax_number) }}" placeholder="1234567-8-90">
                                                            @include('alerts.feedback', ['field' => 'tax_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('registration_number') ? ' has-danger' : '' }}">
                                                                Nyilvántartási szám</label>
                                                            <input type="text" class="only_company form-control{{ $errors->has('registration_number') ? ' is-invalid' : '' }}" name="registration_number" value="{{ old('registration_number', $customer->registration_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'registration_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('email') ? ' has-danger' : '' }}">
                                                                Email cím</label>
                                                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" maxlength="100" value="{{ old('email', $customer->email) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'email'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                                                Telefonszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" maxlength="100" value="{{ old('phone', $customer->phone) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'phone'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
<div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('vzj') ? ' has-danger' : '' }}">
                                                                VZJ azonosító
                                                            </label>
                                                            <input type="text" class="form-control{{ $errors->has('vzj') ? ' is-invalid' : '' }}" name="vzj" maxlength="100" value="{{ old('vzj', $customer->vzj) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'vzj'])
                                                        </div>
                                                    </fieldset>
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
                                                            <label class="form-control-label{{ $errors->has('comment') ? ' has-danger' : '' }}">
                                                                Megjegyzés</label>
                                                            <textarea rows="8" class="zm_textarea form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment">{{ old('comment', $customer->comment) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'comment'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('customer_type_ids[]') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Típusok</label>
                                                        <select multiple title="- válasszon típusokat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="customer_type_ids[]" id="input-role" class="selectpicker form-control{{ $errors->has('customer_type_ids[]') ? ' is-invalid' : '' }}" placeholder="">

                                                            @include('customer_types.customer_type_options', ['customer_types' => $customer_types, 'customer_type_ids' => $customer->customer_types->pluck('customer_type_id')->toArray()])

                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'customer_type_ids[]'])
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('payment_method_ids[]') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Fizetési mód</label>
                                                        <input type="hidden" value="8" name="payment_id">
                                                        <select multiple 
                                                                title="- válasszon fizetési módot -" 
                                                                data-style="btn-info btn-fill btn-block" 
                                                                data-menu-style="dropdown-blue" 
                                                                data-style="btn-default btn-outline" 
                                                                data-menu-style="dropdown-blue" 
                                                                name="payment_method_ids[]" 
                                                                id="input-role" 
                                                                class="selectpicker form-control{{ $errors->has('payment_method_ids[]') ? ' is-invalid' : '' }}">
                                                            
                                                            @foreach ($payments as $payment)
                                                                <option value="{{ $payment->payment_id }}"
                                                                    @if (in_array($payment->payment_id, old('payment_method_ids', $selected_payment_method_ids ?? []))) selected @endif>
                                                                    {{ $payment->name }}
                                                                </option>
                                                            @endforeach
                                            
                                                        </select>
                                            
                                                        @include('alerts.feedback', ['field' => 'payment_method_ids[]'])
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('max_failed_reservation') ? ' has-danger' : '' }}">
                                                                Sikertelen foglalás (max)</label>
                                                            <input type="number" class="form-control{{ $errors->has('max_failed_reservation') ? ' is-invalid' : '' }}" name="max_failed_reservation" maxlength="100" value="{{ old('max_failed_reservation', $customer->max_failed_reservation) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'max_failed_reservation'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('max_reservation_num') ? ' has-danger' : '' }}">
                                                                Egyidejű foglalás (darab)</label>
                                                            <input type="number" class="form-control{{ $errors->has('max_reservation_num') ? ' is-invalid' : '' }}" name="max_reservation_num" maxlength="100" value="{{ old('max_reservation_num', $customer->max_reservation_num) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'max_reservation_num'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('max_reservation_sum') ? ' has-danger' : '' }}">
                                                                Egyidejű foglalás (összeg)</label>
                                                            <input type="number" class="form-control{{ $errors->has('max_reservation_sum') ? ' is-invalid' : '' }}" name="max_reservation_sum" maxlength="100" value="{{ old('max_reservation_sum', $customer->max_reservation_sum) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'max_reservation_sum'])
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
                                                                <input @if($customer->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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

                                            <div class="row">
                                                
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">

                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($customer->terms_accept) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="terms_accept">
                                                                <span class="form-check-sign"></span>
                                                                    Elfogadja az ÁSZF-et
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">

                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($customer->marketing_accept) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="marketing_accept">
                                                                <span class="form-check-sign"></span>
                                                                    Hozzájárul a marketing emailekhez
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    
                                </div>

                                <button type="button" class="btn btn-success save_customer"><i class="fa fa-save"></i> Mentés</button>
                            </div>

                            <div class="tab-pane fade" id="addresses_data" role="tabpanel" aria-labelledby="addresses_data-tab">
                                <fieldset>
                                    <input type="hidden" name="address_id" value="">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        
                                            <div class="form-group">
                                                <label class="form-control-label" for="address_type">Cím típus</label>
                                                <select name="address_type" id="input-address_type" class="form-control">
                                                    <option value="Lakcím">Lakcím</option>
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
                                                    @foreach($customer->addresses as $address)
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
                                                    @foreach($customer->contacts as $contact)
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


                            <div class="tab-pane fade" id="customer_buy_history" role="tabpanel" aria-labelledby="customer_buy_history-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 customer_buy_history_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Bizonylat</th>
                                                        <th>Típus</th>
                                                        <th>Összeg</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sales as $sale)
                                                        <tr>
                                                            <td>{{ $sale->ticket_number }}</td>
                                                            <td>{{ $sale->type }}</td>
                                                            <td>{{ number_format($sale->sum, 0, '.', ' ').' '.$sale->currency }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </fieldset>

                            </div>

                            <div class="tab-pane fade" id="customer_markings" role="tabpanel" aria-labelledby="customer_markings-tab">
                                <fieldset>
                                    <input type="hidden" name="customer_marking_id" value="">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_marking_name">Betű jelzés</label>
                                                <input type="text" name="customer_marking_name" id="customer_marking_name" class="form-control" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_marking_product_status_id">Állapot</label>
                                                <select name="customer_marking_product_status_id" id="customer_marking_product_status_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($active_product_statuses as $product_status)
                                                        <option
                                                            value="{{ $product_status->product_status_id }}"
                                                            data-price="{{ $product_status->price }}"
                                                            data-vat-name="{{ optional($product_status->vatType)->name }}"
                                                        >
                                                            {{ $product_status->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mt-4 pt-2">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" name="customer_marking_default" value="1">
                                                        <span class="form-check-sign"></span>
                                                        Alapértelmezett
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mt-4 pt-2">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" name="customer_marking_status" value="1" checked>
                                                        <span class="form-check-sign"></span>
                                                        Aktív
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_marking_price_info">Állapot ára (információ)</label>
                                                <input type="text" id="customer_marking_price_info" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_marking_vat_info">Áfa típus (információ)</label>
                                                <input type="text" id="customer_marking_vat_info" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 d-flex align-items-end">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success save_customer_marking"><i class="fa fa-save"></i> Jelzés mentése</button>
                                                <button type="button" class="btn btn-secondary reset_customer_marking_form">Új jelzés</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 customer_markings_table">
                                                <thead>
                                                    <tr>
                                                        <th>Betű jelzés</th>
                                                        <th>Állapot</th>
                                                        <th>Ár</th>
                                                        <th>Áfa</th>
                                                        <th>Alapértelmezett</th>
                                                        <th>Aktív</th>
                                                        <th>Művelet</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customer->customer_markings as $marking)
                                                        <tr
                                                            class="grey_background customer_marking_row_{{ $marking->id }}"
                                                            data-id="{{ $marking->id }}"
                                                            data-name="{{ $marking->name }}"
                                                            data-product_status_id="{{ $marking->product_status_id }}"
                                                            data-default="{{ $marking->default ? 1 : 0 }}"
                                                            data-status="{{ $marking->status ? 1 : 0 }}"
                                                        >
                                                            <td class="customer_marking_name">{{ $marking->name }}</td>
                                                            <td class="customer_marking_product_status">{{ optional($marking->productStatus)->name }}</td>
                                                            <td class="customer_marking_price">{{ optional($marking->productStatus)->price }}</td>
                                                            <td class="customer_marking_vat">{{ optional(optional($marking->productStatus)->vatType)->name }}</td>
                                                            <td class="customer_marking_default">{{ $marking->default ? 'Igen' : 'Nem' }}</td>
                                                            <td class="customer_marking_status">{{ $marking->status ? 'Igen' : 'Nem' }}</td>
                                                            <td>
                                                                <div style="display: inline-block;" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-success edit_customer_marking" data-id="{{ $marking->id }}">
                                                                        <i class='fa fa-edit'></i>
                                                                    </button>
                                                                </div>
                                                                <div style="display: inline-block;" class="doc_wrapper">
                                                                    <button type="button" class="btn btn-danger delete_customer_marking" data-id="{{ $marking->id }}">
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
            $('.save_customer').click(function(){
                $('#customer_form').submit();
            });

            function formatTaxIdentity(rawValue) {
                return (rawValue || '').replace(/\D/g, '').substring(0, 10);
            }

            function formatTaxNumber(rawValue) {
                let digits = (rawValue || '').replace(/\D/g, '').substring(0, 10);

                if (digits.length <= 7) {
                    return digits;
                }

                if (digits.length <= 8) {
                    return digits.substring(0, 7) + '-' + digits.substring(7);
                }

                return digits.substring(0, 7) + '-' + digits.substring(7, 8) + '-' + digits.substring(8, 10);
            }

            const taxIdentityInput = $('input[name=tax_identity]');
            taxIdentityInput.on('input', function() {
                $(this).val(formatTaxIdentity($(this).val()));
            });
            taxIdentityInput.val(formatTaxIdentity(taxIdentityInput.val()));

            const taxNumberInput = $('input[name=tax_number]');
            taxNumberInput.on('input', function() {
                $(this).val(formatTaxNumber($(this).val()));
            });
            taxNumberInput.val(formatTaxNumber(taxNumberInput.val()));


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
                let selectedZip = e.params.data.id; // Az irányítószám ID-ja.

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
            //Írányítószám részek
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
            

            @if($customer->is_company)
                $('.only_private').prop('disabled', true);
                $('.only_company').prop('disabled', false);
            @else
                $('.only_private').prop('disabled', false);
                $('.only_company').prop('disabled', true);
            @endif

            $('input[name=is_company]').change(function(){
                if($('input[name="is_company"]:checked').val() == 0){
                    $('.only_private').prop('disabled', false);
                    $('.only_company').prop('disabled', true);
                } else {
                    $('.only_private').prop('disabled', true);
                    $('.only_company').prop('disabled', false);
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



            $('.save_address').click(function(){
                let zip = $('select[name=zip]').val();
                let city = $('select[name=city]').val();
                let customer_id = '{{ $customer->customer_id }}';
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
                if(!customer_id.length)
                {
                    $.notify({ icon: "done", message: "Az ügyfél kötelező." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }


                $.ajax({
                    method: "POST",
                    url: "/ajax/save_address",
                    data: { customer_id: "{{ $customer->customer_id }}", 
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
                        url: "/ajax/load_addresses",
                        data: { customer_id: '{{ $customer->customer_id }}' }
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
                        url: "/ajax/delete_address",
                        data: {
                                address_id: address_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_addresses",
                            data: { customer_id: '{{ $customer->customer_id }}' }
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
                    url: "/ajax/save_contact",
                    data: { customer_id: "{{ $customer->customer_id }}", 
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
                        url: "/ajax/load_contacts",
                        data: { customer_id: '{{ $customer->customer_id }}' }
                    })
                    .done(function( html ) {
                        $('.contact_table tbody').html(html);
                    });

                    // toastr["success"]("Az állapot sikeresen mentve.");
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
                        url: "/ajax/delete_contact",
                        data: {
                                contact_id: contact_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_contacts",
                            data: { customer_id: '{{ $customer->customer_id }}' }
                        })
                        .done(function( html ) {
                            $('.contact_table tbody').html(html);
                        });
                    });

                });

            });

            function updateCustomerMarkingStatusInfo() {
                let selected = $('#customer_marking_product_status_id').find('option:selected');
                $('#customer_marking_price_info').val(selected.data('price') || '');
                $('#customer_marking_vat_info').val(selected.data('vat-name') || '');
            }

            function resetCustomerMarkingForm() {
                $('input[name=customer_marking_id]').val('');
                $('input[name=customer_marking_name]').val('');
                $('#customer_marking_product_status_id').val('');
                $('input[name=customer_marking_default]').prop('checked', false);
                $('input[name=customer_marking_status]').prop('checked', true);
                updateCustomerMarkingStatusInfo();
            }

            function fillCustomerMarkingForm(markingId) {
                let row = $('.customer_marking_row_' + markingId);

                $('input[name=customer_marking_id]').val(row.data('id'));
                $('input[name=customer_marking_name]').val(row.data('name'));
                $('#customer_marking_product_status_id').val(row.data('product_status_id'));
                $('input[name=customer_marking_default]').prop('checked', parseInt(row.data('default'), 10) === 1);
                $('input[name=customer_marking_status]').prop('checked', parseInt(row.data('status'), 10) === 1);
                updateCustomerMarkingStatusInfo();

                $('a[href="#customer_markings"]').tab('show');
                $('input[name=customer_marking_name]').focus();
            }

            function reloadCustomerMarkings() {
                $.ajax({
                    method: 'POST',
                    url: '/ajax/load_customer_markings',
                    data: { customer_id: '{{ $customer->customer_id }}' }
                }).done(function(html) {
                    $('.customer_markings_table tbody').html(html);
                });
            }

            $('#customer_marking_product_status_id').change(function() {
                updateCustomerMarkingStatusInfo();
            });

            $('.reset_customer_marking_form').click(function() {
                resetCustomerMarkingForm();
            });

            $('.save_customer_marking').click(function() {
                let markingName = $('input[name=customer_marking_name]').val();
                let productStatusId = $('#customer_marking_product_status_id').val();

                if (!markingName.length) {
                    $.notify({ icon: 'done', message: 'A betű jelzés nem lehet üres.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                    return false;
                }

                if (!productStatusId) {
                    $.notify({ icon: 'done', message: 'Az állapot kötelező.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
                    return false;
                }

                $.ajax({
                    method: 'POST',
                    url: '/ajax/save_customer_marking',
                    data: {
                        customer_marking_id: $('input[name=customer_marking_id]').val(),
                        customer_id: '{{ $customer->customer_id }}',
                        name: markingName,
                        product_status_id: productStatusId,
                        default: $('input[name=customer_marking_default]').is(':checked') ? 1 : 0,
                        status: $('input[name=customer_marking_status]').is(':checked') ? 1 : 0
                    }
                }).done(function() {
                    reloadCustomerMarkings();
                    resetCustomerMarkingForm();
                    $.notify({ icon: 'done', message: 'Jelzés sikeresen mentve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
                });
            });

            $('body').on('click', '.edit_customer_marking', function() {
                fillCustomerMarkingForm($(this).attr('data-id'));
            });

            $('body').on('click', '.customer_markings_table tbody tr', function(e) {
                if ($(e.target).closest('button').length) {
                    return;
                }
                fillCustomerMarkingForm($(this).data('id'));
            });

            $('body').on('click', '.delete_customer_marking', function() {
                var markingId = $(this).attr('data-id');

                swal({
                    text: 'Biztosan törli ezt a szállítói jelzést?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen, törlöm',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function() {
                    $.ajax({
                        method: 'POST',
                        url: '/ajax/delete_customer_marking',
                        data: { id: markingId }
                    }).done(function() {
                        reloadCustomerMarkings();
                        if ($('input[name=customer_marking_id]').val() == markingId) {
                            resetCustomerMarkingForm();
                        }
                        $.notify({ icon: 'done', message: 'Jelzés sikeresen törölve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
                    });
                });
            });

            resetCustomerMarkingForm();


        });
    </script>
@endpush