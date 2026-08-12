@extends('layouts.app', ['activePage' => 'vehicle', 'activeButton' => 'vehicle', 'title' => 'Zemef ERP', 'navName' => 'Jármű szerkesztése' ])

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
                            <a class="nav-link" id="conditions_data-tab" data-toggle="pill" href="#conditions_data" role="tab" aria-controls="conditions_data" aria-selected="true">Állapot</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="docs_data-tab" data-toggle="pill" href="#docs_data" role="tab" aria-controls="docs_data" aria-selected="true">Dokumentumok</a>
                        </li>
                        <li class="nav-item gallery_tab">
                            <a class="nav-link" id="images_data-tab" data-toggle="pill" href="#images_data" role="tab" aria-controls="images_data" aria-selected="true">Képek</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="{{ route('vehicle.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form id="vehicle_form" method="post" action="{{ route('vehicle.update', $vehicle->vehicle_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Jármű alap adatai</h6>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                                Megnevezés</label>
                                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $vehicle->name) }}" placeholder="" required autofocus>
                                                            @include('alerts.feedback', ['field' => 'name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('plate_number') ? ' has-danger' : '' }}">
                                                                Rendszám</label>
                                                            <input type="text" class="form-control{{ $errors->has('plate_number') ? ' is-invalid' : '' }}" name="plate_number" maxlength="100" value="{{ old('plate_number', $vehicle->plate_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'plate_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('plate_number') ? ' has-danger' : '' }}">
                                                                Üzemanyag</label>
                                                            <select class="form-control" name="fuel">
                                                                <option @if($vehicle->fuel == 'benzin') selected @endif value="benzin">Benzin</option>
                                                                <option @if($vehicle->fuel == 'dizel') selected @endif value="dizel">Gázolaj</option>
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('cylinder') ? ' has-danger' : '' }}">
                                                                Hengerűrtartalom</label>
                                                            <input type="number" class="form-control{{ $errors->has('cylinder') ? ' is-invalid' : '' }}" name="cylinder" value="{{ old('cylinder', $vehicle->cylinder) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'cylinder'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('responsible_id') ? ' has-danger' : '' }}">
                                                                Felelős</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="responsible_id" class="selectpicker form-control{{ $errors->has('responsible_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $vehicle->responsible_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('start_km') ? ' has-danger' : '' }}">
                                                                Induló km</label>
                                                            <input type="number" class="form-control{{ $errors->has('start_km') ? ' is-invalid' : '' }}" name="start_km" maxlength="100" value="{{ old('start_km', $vehicle->start_km) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'start_km'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('end_km') ? ' has-danger' : '' }}">
                                                                Záró km</label>
                                                            <input type="number" class="form-control{{ $errors->has('end_km') ? ' is-invalid' : '' }}" name="end_km" maxlength="100" value="{{ old('end_km', $vehicle->end_km) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'end_km'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('condition') ? ' has-danger' : '' }}">
                                                                Állapot</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('condition') ? ' is-invalid' : '' }}" name="condition">{{ old('condition', $vehicle->condition) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'condition'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('condition_date') ? ' has-danger' : '' }}">
                                                                Állapot dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('condition_date') ? ' is-invalid' : '' }}" name="condition_date" value="{{ old('condition_date', $vehicle->condition_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'condition_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <label class="form-control-label{{ $errors->has('is_fleet') ? ' has-danger' : '' }}">
                                                                Tulajdonos</label>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if(!$vehicle->is_fleet) checked @endif class="form-check-input" type="radio" name="is_fleet" id="is_fleet0" value="0" data-gtm-form-interact-field-id="0">
                                                            <span class="form-check-sign"></span>
                                                            Lízing
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if($vehicle->is_fleet) checked @endif class="form-check-input" type="radio" name="is_fleet" id="is_fleet1" value="1" data-gtm-form-interact-field-id="0">
                                                            <span class="form-check-sign"></span>
                                                            Flotta
                                                            </label>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('owner_customer_id') ? ' has-danger' : '' }}">
                                                                Tulajdonos</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="owner_customer_id" class="selectpicker form-control{{ $errors->has('owner_customer_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($customers as $customer)
                                                                    <option @if($customer->customer_id == $vehicle->owner_customer_id) selected @endif value="{{ $customer->customer_id }}" >{{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <h6 class="heading-small text-muted mb-4">Nyári gumi</h6>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('summer_tyre_size') ? ' has-danger' : '' }}">
                                                                Méret</label>
                                                            <input type="text" class="form-control{{ $errors->has('summer_tyre_size') ? ' is-invalid' : '' }}" name="summer_tyre_size" value="{{ old('summer_tyre_size', $vehicle->summer_tyre_size) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('summer_tyre_type') ? ' has-danger' : '' }}">
                                                                Típus</label>
                                                            <input type="text" class="form-control{{ $errors->has('summer_tyre_type') ? ' is-invalid' : '' }}" name="summer_tyre_type" value="{{ old('summer_tyre_type', $vehicle->summer_tyre_type) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('summer_tyre_location') ? ' has-danger' : '' }}">
                                                                Hol van?</label>
                                                            <input type="text" class="form-control{{ $errors->has('summer_tyre_location') ? ' is-invalid' : '' }}" name="summer_tyre_location" value="{{ old('summer_tyre_location', $vehicle->summer_tyre_location) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('summer_tyre_bought_at') ? ' has-danger' : '' }}">
                                                                Vásárlás dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('summer_tyre_bought_at') ? ' is-invalid' : '' }}" name="summer_tyre_bought_at" value="{{ old('summer_tyre_bought_at', $vehicle->summer_tyre_bought_at) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('summer_tyre_condition') ? ' has-danger' : '' }}">
                                                                Állapot</label>
                                                            <input type="text" class="form-control{{ $errors->has('summer_tyre_condition') ? ' is-invalid' : '' }}" name="summer_tyre_condition" value="{{ old('summer_tyre_condition', $vehicle->summer_tyre_condition) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    
                                                </div>
                                            </div>
                                            <hr>

                                            <h6 class="heading-small text-muted mb-4">Téli gumi</h6>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('winter_tyre_size') ? ' has-danger' : '' }}">
                                                                Méret</label>
                                                            <input type="text" class="form-control{{ $errors->has('winter_tyre_size') ? ' is-invalid' : '' }}" name="winter_tyre_size" value="{{ old('winter_tyre_size', $vehicle->winter_tyre_size) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('winter_tyre_type') ? ' has-danger' : '' }}">
                                                                Típus</label>
                                                            <input type="text" class="form-control{{ $errors->has('winter_tyre_type') ? ' is-invalid' : '' }}" name="winter_tyre_type" value="{{ old('winter_tyre_type', $vehicle->winter_tyre_type) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('winter_tyre_location') ? ' has-danger' : '' }}">
                                                                Hol van?</label>
                                                            <input type="text" class="form-control{{ $errors->has('winter_tyre_location') ? ' is-invalid' : '' }}" name="winter_tyre_location" value="{{ old('winter_tyre_location', $vehicle->winter_tyre_location) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('winter_tyre_bought_at') ? ' has-danger' : '' }}">
                                                                Vásárlás dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('winter_tyre_bought_at') ? ' is-invalid' : '' }}" name="winter_tyre_bought_at" value="{{ old('winter_tyre_bought_at', $vehicle->winter_tyre_bought_at) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('winter_tyre_condition') ? ' has-danger' : '' }}">
                                                                Állapot</label>
                                                            <input type="text" class="form-control{{ $errors->has('winter_tyre_condition') ? ' is-invalid' : '' }}" name="winter_tyre_condition" value="{{ old('winter_tyre_condition', $vehicle->winter_tyre_condition) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    
                                                </div>
                                            </div>
                                            <hr>
                                            
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">

                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($vehicle->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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
                                                            <input type="text" @if($vehicle->status) disabled @endif class="form-control{{ $errors->has('note') ? ' is-invalid' : '' }}" name="note" maxlength="100" value="{{ old('note', $vehicle->note) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'note'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('note_date') ? ' has-danger' : '' }}">
                                                                Dátum</label>
                                                            <input type="date" @if($vehicle->status) disabled @endif class="form-control{{ $errors->has('note_date') ? ' is-invalid' : '' }}" name="note_date" maxlength="100" value="{{ old('note_date', $vehicle->note_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'note_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">&nbsp;</h6>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('insurance_number') ? ' has-danger' : '' }}">
                                                                Biztosítás száma</label>
                                                            <input type="text" class="form-control{{ $errors->has('insurance_number') ? ' is-invalid' : '' }}" name="insurance_number" maxlength="100" value="{{ old('insurance_number', $vehicle->insurance_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'insurance_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('insurance_date') ? ' has-danger' : '' }}">
                                                                Biztosítás évforduló</label>
                                                            <input type="date" class="form-control{{ $errors->has('insurance_date') ? ' is-invalid' : '' }}" name="insurance_date" value="{{ old('insurance_date', $vehicle->insurance_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'insurance_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('insurance_days') ? ' has-danger' : '' }}">
                                                                Értesítés ennyi nappal a lejárat előtt</label>
                                                            <input type="number" class="form-control{{ $errors->has('insurance_days') ? ' is-invalid' : '' }}" name="insurance_days" value="{{ old('insurance_days', $vehicle->insurance_days) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('insurance_employee_id') ? ' has-danger' : '' }}">
                                                                Értesítést kapja</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="insurance_employee_id" class="selectpicker form-control{{ $errors->has('insurance_employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $vehicle->insurance_employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <hr>


                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('casco_number') ? ' has-danger' : '' }}">
                                                                Casco száma</label>
                                                            <input type="text" class="form-control{{ $errors->has('casco_number') ? ' is-invalid' : '' }}" name="casco_number" maxlength="100" value="{{ old('casco_number', $vehicle->casco_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'casco_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('casco_date') ? ' has-danger' : '' }}">
                                                                Casco évforduló</label>
                                                            <input type="date" class="form-control{{ $errors->has('casco_date') ? ' is-invalid' : '' }}" name="casco_date" value="{{ old('casco_date', $vehicle->casco_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'casco_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('casco_days') ? ' has-danger' : '' }}">
                                                                Értesítés ennyi nappal a lejárat előtt</label>
                                                            <input type="number" class="form-control{{ $errors->has('casco_days') ? ' is-invalid' : '' }}" name="casco_days" value="{{ old('casco_days', $vehicle->casco_days) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('casco_employee_id') ? ' has-danger' : '' }}">
                                                                Értesítést kapja</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="casco_employee_id" class="selectpicker form-control{{ $errors->has('casco_employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $vehicle->casco_employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <hr>


                                            <div class="row">
                                                <div class="col-lg-6">
                                                
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tech_date') ? ' has-danger' : '' }}">
                                                                Műszaki lejárat</label>
                                                            <input type="date" class="form-control{{ $errors->has('tech_date') ? ' is-invalid' : '' }}" name="tech_date" value="{{ old('tech_date', $vehicle->tech_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'tech_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tech_days') ? ' has-danger' : '' }}">
                                                                Értesítés ennyi nappal a lejárat előtt</label>
                                                            <input type="number" class="form-control{{ $errors->has('tech_days') ? ' is-invalid' : '' }}" name="tech_days" value="{{ old('tech_days', $vehicle->tech_days) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tech_employee_id') ? ' has-danger' : '' }}">
                                                                Értesítést kapja</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="tech_employee_id" class="selectpicker form-control{{ $errors->has('tech_employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $vehicle->tech_employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('highway_ticket') ? ' has-danger' : '' }}">
                                                                Autópályamatrica</label>
                                                            <input type="text" class="form-control{{ $errors->has('highway_ticket') ? ' is-invalid' : '' }}" name="highway_ticket" value="{{ old('highway_ticket', $vehicle->highway_ticket) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'highway_ticket'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('highway_date') ? ' has-danger' : '' }}">
                                                                Érvényesség vége</label>
                                                            <input type="date" class="form-control{{ $errors->has('highway_date') ? ' is-invalid' : '' }}" name="highway_date" value="{{ old('highway_date', $vehicle->highway_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'highway_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('highway_days') ? ' has-danger' : '' }}">
                                                                Értesítés ennyi nappal a lejárat előtt</label>
                                                            <input type="number" class="form-control{{ $errors->has('highway_days') ? ' is-invalid' : '' }}" name="highway_days" value="{{ old('highway_days', $vehicle->highway_days) }}" placeholder="">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('highway_employee_id') ? ' has-danger' : '' }}">
                                                                Értesítést kapja</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="highway_employee_id" class="selectpicker form-control{{ $errors->has('highway_employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $vehicle->highway_employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <hr>

                                            

                                            
                                        </div>
                                    </div>
                                
                                </div>

                                <button type="button" class="btn btn-success save_vehicle">Mentés</button>
                            </div>

                            <div class="tab-pane fade" id="conditions_data" role="tabpanel" aria-labelledby="conditions_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0" >
                                                <tr class='grey_background'>
                                                    <td>
                                                        <form action="" class="vehicle_condition_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="condition_description">Állapot leírása</label>
                                                                <textarea name="condition_description" id="condition_description" class="zm_textarea form-control"> </textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-control-label" for="vehicle_condition_date">Dátum</label>
                                                                <input type="date" name="vehicle_condition_date" id="vehicle_condition_date" class="form-control">
                                                            </div>
                                                            <input type="hidden" name="condition_id" value="">

                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-success save_condition">Állapot hozzáadása</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 vehicle_condition_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Állapot leírása</th>
                                                        <th>Dátum</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->conditions as $condition)
                                                        <tr class='grey_background'>
                                                            <td class="condition_description_row_{{$condition->vehicle_condition_id}}">{{ $condition->condition_description }}</td>
                                                            <td class="condition_date_row_{{$condition->condition_date}}">{{ $condition->condition_date }}</td>
                                                            <td>
                                                                <div data-condition_id="{{ $condition->vehicle_condition_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-success edit_condition" data-condition_id="{{ $condition->vehicle_condition_id }}">
                                                                        Szerkesztés
                                                                    </button>
                                                                </div>
                                                                <div data-condition_id="{{ $condition->vehicle_condition_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_condition" data-condition_id="{{ $condition->vehicle_condition_id }}">
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

                            <div class="tab-pane fade" id="docs_data" role="tabpanel" aria-labelledby="docs_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0" >
                                                <tr class='grey_background'>
                                                    <td>
                                                        <form action="" class="vehicle_doc_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-vehicle_doc_comment">Megjegyzés</label>
                                                                <input type="text" name="vehicle_doc_comment" id="input-vehicle_doc_comment" class="form-control">
                                                                <input type="hidden" name="vehicle_doc_input" value="">
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
                                                                <label class="form-control-label" for="input-vehicle_doc_issue_date">Kiállítás dátuma</label>
                                                                <input type="date" name="vehicle_doc_issue_date" id="input-vehicle_doc_issue_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-vehicle_doc_expire_date">Lejárat dátuma</label>
                                                                <input type="date" name="vehicle_doc_expire_date" id="input-vehicle_doc_expire_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-vehicle_doc_reminder_days">Emlékeztető határideje (nap)</label>
                                                                <input type="number" name="vehicle_doc_reminder_days" id="input-vehicle_doc_reminder_days" class="form-control">
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr class='grey_background'>
                                                    <td>
                                                        <div class="dropzone_area" >
                                                            <form action="/ajax/upload_vehicle_doc" id="vehicle_doc_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                <div class="dz-message needsclick">
                                                                    <span class="">Húzza ide a fájlt vagy kattintson a feltöltéshez! Egyszerre maximum 1 fájlt tud feltölteni.</span>
                                                                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->vehicle_id }}">
                                                                    @csrf
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success mt-4 upload_vehicle_doc">Feltöltés</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 vehicle_doc_table" >
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
                                                    @foreach($vehicle->documents as $doc)
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



                            <div class="tab-pane fade" id="images_data" role="tabpanel" aria-labelledby="images_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="dropzone_area" >
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('image_date') ? ' has-danger' : '' }}">
                                                        Dátum</label>
                                                    <input type="date" class="form-control{{ $errors->has('image_date') ? ' is-invalid' : '' }}" name="image_date" value="{{ old('image_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'image_date'])
                                                </div>
                                            </fieldset>
                                            <form action="/ajax/upload_vehicle_image" id="vehicle_image_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                <div class="dz-message needsclick">
                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->vehicle_id }}">
                                                    @csrf
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="vehicle_gallery_container">
                                            
                                            <div class="delete_buttons">
                                                @foreach($vehicle->vehicle_images as $img)
                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                        <button type="button" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div data-lightGallery="1" id="lightgallery_vehicle_images" class="">
                                                @foreach($vehicle->vehicle_images as $img)
                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="vehicle_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/vehicle_images/' . $img->image_path; ?>">
                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive vehicle_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/vehicle_images/' . $img->image_path; ?>" alt="@if($img->image_date) {{ date('Y. m. d.', strtotime($img->image_date)) }} @else - @endif ">
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
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

    Dropzone.autoDiscover = false;

    $("#vehicle_doc_form").dropzone({
        maxFilesize: 50, // MB
        parallelUploads: 1,
        paramName: 'vehicle_doc',
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
            $('input[name=vehicle_doc_input]').val(response);
        },
        removedfile: function(file) {
            $('input[name=vehicle_doc_input]').val('');

            var class_name = file.name;
            class_name = class_name.replace(/[. ]/g,'');
            jQuery('.' + class_name).remove();
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
    });


    function buildVehicleGallery($initScope)
    {
        if (typeof $initScope != 'undefined' && $initScope.length)
        {
            $initScope.justifiedGallery(
            {
                border: 1,
                rowHeight: 150,
                margins: 2,
                waitThumbnailsLoad: true,
                randomize: false,
            }).on('jg.complete', function()
            {


                $initScope.lightGallery(
                {
                    thumbnail: true,
                    animateThumb: true,
                    showThumbByDefault: true,
                });

                setTimeout(function(){
                    $('.hidden_image_wrapper').each(function(){
                            $(this).width($('.vehicle_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                            $(this).height($('.vehicle_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                            $(this).css('top', $('.vehicle_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                            $(this).css('left', $('.vehicle_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
                    });
                }, 2000);

            });
            $initScope.on('onAfterOpen.lg', function(event)
            {
                $('body').addClass("overflow-hidden");
            });
            $initScope.on('onCloseAfter.lg', function(event)
            {
                $('body').removeClass("overflow-hidden");
            });
        }
    }


    function initVehicleGallery() {
        var $initScope_implementation = $('#lightgallery_vehicle_images');

        var images = $initScope_implementation.find('img').get();
        var allImagesLoaded = images.every(function(img) {
            return img.complete;
        });

        if (allImagesLoaded) {
            buildVehicleGallery($initScope_implementation);
        } else {
            $(images).on('load', function() {
                var allLoaded = images.every(function(img) {
                    return img.complete;
                });
                if (allLoaded) {
                    buildVehicleGallery($initScope_implementation);
                }
            });
        }
    }


    $("#vehicle_image_form").dropzone({
        maxFilesize: 50, // MB
        parallelUploads: 1,
        paramName: 'vehicle_image',
        uploadMultiple: false,
        addRemoveLinks: true,
        maxFiles: 100,
        acceptedFiles: '.jpg,.jpeg,.png',
        dictRemoveFile: 'Törlés',
        dictCancelUpload: 'mégse',
        dictDefaultMessage: 'Kattintson vagy húzza ide a fájlokat! <br /> (max 50MB)',
        renameFilename: cleanImageNameFn,
        success: function(file, response) {
            //automatikus mentés feltöltés után
            $.ajax({
                method: "POST",
                url: "/ajax/auto_save_vehicle_images",
                data: { vehicle_id: '{{ $vehicle->vehicle_id }}', image: response, image_date: $('input[name=image_date]').val() }
            })
            .done(function( msg ) {
                $.ajax({
                    method: "POST",
                    url: "/ajax/load_vehicle_gallery",
                    data: { vehicle_id: '{{ $vehicle->vehicle_id }}' }
                })
                .done(function( html ) {
                    $('.vehicle_gallery_container').html(html);
                    initVehicleGallery();
                    $('.dz-complete').remove();
                    $('.dz-message').show();
                });
            });
        }
    });


    $(document).ready(function () {
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
            } )
            .catch( error => {
        } );


        $('.save_vehicle').click(function(){
            $('#vehicle_form').submit();
        });

        $(document).on('change', 'input[name="status"]', function(){
            if($(this).is(':checked')){
                $('input[name="note"]').prop('disabled', true).val('');
                $('input[name="note_date"]').prop('disabled', true).val('');
            }else{
                $('input[name="note"]').prop('disabled', false);
                $('input[name="note_date"]').prop('disabled', false);
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


        $('input[name=is_fleet]').change(function(){
            
            if($('input[name=is_fleet]:checked').val() == '1'){
                $('select[name=owner_customer_id]').prop('disabled', true);
                $('select[name=owner_customer_id]').addClass('disabled');
                $('.selectpicker').selectpicker('refresh');
            } else {
                $('select[name=owner_customer_id]').prop('disabled', false);
                $('.selectpicker').selectpicker('refresh');
            }
        });


        $('.upload_vehicle_doc').click(function(){

                
                //ha van feltöltve kép, akkor legyen hozzá megadva név
                if(!$('input[name=vehicle_doc_input]').val().length)
                {
                    $.notify({ icon: "done", message: "Hiányzik a feltöltött file." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }
                
                if(!$('input[name=vehicle_doc_comment]').val().length)
                {
                    $.notify({ icon: "done", message: "A megjegyzés kitöltése kötelező." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_vehicle_doc_data",
                    data: { vehicle_id: "{{ $vehicle->vehicle_id }}", 
                            vehicle_doc_comment: $('input[name=vehicle_doc_comment]').val(), 
                            doc_type_id: $('select[name=doc_type_id]').val(), 
                            vehicle_doc_issue_date: $('input[name=vehicle_doc_issue_date]').val(), 
                            vehicle_doc_expire_date: $('input[name=vehicle_doc_expire_date]').val(), 
                            vehicle_doc_reminder_days: $('input[name=vehicle_doc_reminder_days]').val(), 
                            doc_file: $('input[name=vehicle_doc_input]').val() }
                })
                .done(function( msg ) {

                    var myDropzone = Dropzone.forElement("#vehicle_doc_form");
                    myDropzone.removeAllFiles(true);

                    $('input[name=vehicle_doc_comment]').val('');
                    $('input[name=vehicle_doc_input]').val('');
                    $('select[name=doc_type_id]').val('');
                    $('input[name=vehicle_doc_issue_date]').val('');
                    $('input[name=vehicle_doc_expire_date]').val('');
                    $('input[name=vehicle_doc_reminder_days]').val('');

                    //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_vehicle_docs",
                        data: { vehicle_id: '{{ $vehicle->vehicle_id }}' }
                    })
                    .done(function( html ) {
                        $('.vehicle_doc_table tbody').html(html);
                    });

                    $.notify({ icon: "done", message: "A dokumentum sikeresen feltöltve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                });
            });

            $('body').on('click', '.delete_doc', function() {

            let doc_id = $(this).attr('data-document_id');
            let row = $(this).parents('tr');

            swal({
                title: "Megerősítés",
                text: "Biztosan törli ezt a dokumentumot?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                confirmButtonText: 'Igen, törlöm',
                cancelButtonText: 'Nem',
                closeOnConfirm: true, 
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/delete_doc",
                        data: { doc_id: doc_id }
                    }).done(function( msg ) {
                        row.remove();
                        $.notify({ icon: "done", message: "A dokumentum sikeresen törölve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                    });
                }
            });

            });


            $('.save_condition').click(function(){

                if(!$('textarea[name=condition_description]').val().length)
                {
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_vehicle_condition",
                    data: { vehicle_id: "{{ $vehicle->vehicle_id }}", 
                            condition_id: $('input[name=condition_id]').val(), 
                            condition_description: $('textarea[name=condition_description]').val(), 
                            condition_date: $('input[name=vehicle_condition_date]').val() }
                })
                .done(function( msg ) {

                    $('textarea[name=condition_description]').val('');
                    $('input[name=vehicle_condition_date]').val('');
                    $('input[name=condition_id]').val('');

                    //újra listázás
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_vehicle_conditions",
                        data: { vehicle_id: '{{ $vehicle->vehicle_id }}' }
                    })
                    .done(function( html ) {
                        $('.vehicle_condition_table tbody').html(html);
                    });

                    // toastr["success"]("Az állapot sikeresen mentve.");
                });
            });


            $('body').on('click', '.edit_condition', function() {
                $('input[name=condition_id]').val($(this).attr('data-condition_id'));
                $('input[name=condition_date]').val($('.condition_date_row_' + $(this).attr('data-condition_id')).html());
                $('textarea[name=condition_description]').val($('.condition_description_row_' + $(this).attr('data-condition_id')).html());

                $("html, body").animate({ scrollTop: 0 }, "slow");                
                $('textarea[name=condition_description]').focus();
            });

            $('body').on('click', '.delete_condition', function() {

                var cond_id = $(this).attr('data-condition_id');
                swal({
                    text: "Biztosan törli ezt az állapotot?",
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
                        url: "/ajax/delete_condition",
                        data: {
                                condition_id: cond_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_vehicle_conditions",
                            data: { vehicle_id: '{{ $vehicle->vehicle_id }}' }
                        })
                        .done(function( html ) {
                            $('.vehicle_condition_table tbody').html(html);
                        });
                    });

                });

            });


        //gallery
        $('.gallery_tab').click(function(){
            initVehicleGallery();
        });



        $("body").on("mouseenter", ".vehicle_img_wrapper", function(e){
            $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
        });
        $("body").on("mouseleave", ".vehicle_img_wrapper", function(e){
            if( !$(e.toElement).hasClass('delete_img') ) {
                $('.delete_img').stop(true).hide(200);
            }
        });

        $("body").on("click", ".delete_img", function(e){
            var img_id = $(this).attr('data-img_id');


            swal({
                text: "Biztosan törli ezt az képet?",
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
                    url: "/ajax/delete_vehicle_image",
                    data: { img_id: img_id }
                })
                .done(function( msg ) {
                    //kép hozzáadása a listához
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_vehicle_gallery",
                        data: { vehicle_id: '{{ $vehicle->vehicle_id }}' }
                    })
                    .done(function( html ) {
                        $('.vehicle_gallery_container').html(html);
                        initVehicleGallery();
                    });
                });


            });

        });




    });
    </script>
@endpush