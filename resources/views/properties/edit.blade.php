@extends('layouts.app', ['activePage' => 'property', 'activeButton' => 'property', 'title' => 'Zemef ERP', 'navName' => 'Intaglan szerkesztése' ])

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
                            <a class="nav-link" id="others_data-tab" data-toggle="pill" href="#others_data" role="tab" aria-controls="others_data" aria-selected="true">Egyéb adatok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="docs_data-tab" data-toggle="pill" href="#docs_data" role="tab" aria-controls="docs_data" aria-selected="true">Dokumentumok</a>
                        </li>
                        <li class="nav-item gallery_tab">
                            <a class="nav-link" id="images_data-tab" data-toggle="pill" href="#images_data" role="tab" aria-controls="images_data" aria-selected="true">Képek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contacts_data-tab" data-toggle="pill" href="#contacts_data" role="tab" aria-controls="contacts_data" aria-selected="true">Kapcsolattartók</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0"></h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="{{ route('property.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form id="property_form" method="post" action="{{ route('property.update', $property->property_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

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
                                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $property->name) }}" placeholder="" required autofocus>
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
                                                                Irányítószám
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('zip') ? ' is-invalid' : '' }}" id="zip" name="zip_id">
                                                                @if($property->zip)
                                                                    <option value="{{ $property->zip_id }}">{{ $property->zip }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('city') ? ' has-danger' : '' }}">
                                                                Település
                                                            </label>
                                                            <select class="form-control select2{{ $errors->has('city') ? ' is-invalid' : '' }}" id="city" name="city_id">
                                                                @if($property->city)
                                                                    <option value="{{ $property->city_id }}">{{ $property->city }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'city'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-5">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('address') ? ' has-danger' : '' }}">
                                                                Cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" maxlength="100" value="{{ old('address', $property->address) }}" placeholder="">
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
                                                            <input type="text" class="form-control{{ $errors->has('floor') ? ' is-invalid' : '' }}" name="floor" maxlength="100" value="{{ old('floor', $property->floor) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'floor'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('door') ? ' has-danger' : '' }}">
                                                                Ajtó</label>
                                                            <input type="text" class="form-control{{ $errors->has('door') ? ' is-invalid' : '' }}" name="door" maxlength="100" value="{{ old('door', $property->door) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'door'])
                                                        </div>
                                                    </fieldset>
                                                </div>

                                                <!-- <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($property->is_business) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_business">
                                                                <span class="form-check-sign"></span>
                                                                Üzlet
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>-->
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if(!$property->is_own) checked @endif class="form-check-input" type="radio" name="is_own" id="is_own0" value="0" data-gtm-form-interact-field-id="0">
                                                            <span class="form-check-sign"></span>
                                                            Saját
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-radio">
                                                            <label class="form-check-label">
                                                            <input @if($property->is_own) checked @endif class="form-check-input" type="radio" name="is_own" id="is_own1" value="1" data-gtm-form-interact-field-id="0">
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
                                                            <input @if($property->is_franchise) checked @endif class="form-check-input" value="1" type="checkbox" value="" data-gtm-form-interact-field-id="1" name="is_franchise">
                                                            <span class="form-check-sign"></span>
                                                            Franchise
                                                            </label>
                                                        </div>
                                                    </fieldset>
                                                </div>

                                            </div>

                                            <div class="row is_own_row" @if(!$property->is_own) style="display:none;" @endif>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('reservation_type') ? ' has-danger' : '' }}">
                                                                Bérlet típusa</label>
                                                            <select class="form-control{{ $errors->has('reservation_type') ? ' is-invalid' : '' }}" name="reservation_type" placeholder="">
                                                                <option @if($property->reservation_type == '0') selected @endif value="0">Határozatlan</option>
                                                                <option @if($property->reservation_type == '1') selected @endif value="1">Határozott</option>
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'reservation_type'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4 reservation_date_div" @if($property->reservation_type == '0') style="display:none;" @endif>
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('reservation_to_date') ? ' has-danger' : '' }}">
                                                                Lejárat dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('reservation_to_date') ? ' is-invalid' : '' }}" name="reservation_to_date" value="{{ old('reservation_to_date', $property->reservation_to_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'reservation_to_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4 reservation_date_div" @if($property->reservation_type == '0') style="display:none;" @endif>
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('reservation_expire_in_days') ? ' has-danger' : '' }}">
                                                                Értesítés előtte (nap)</label>
                                                            <input type="number" class="form-control{{ $errors->has('reservation_expire_in_days') ? ' is-invalid' : '' }}" name="reservation_expire_in_days" value="{{ old('reservation_expire_in_days', $property->reservation_expire_in_days) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'reservation_expire_in_days'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('stock_value') ? ' has-danger' : '' }}">
                                                                Készlet érték</label>
                                                            <input type="number" class="form-control{{ $errors->has('stock_value') ? ' is-invalid' : '' }}" name="stock_value" value="{{ old('stock_value', $property->stock_value) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'stock_value'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('pledge_value') ? ' has-danger' : '' }}">
                                                                Zálogérték</label>
                                                            <input type="number" class="form-control{{ $errors->has('pledge_value') ? ' is-invalid' : '' }}" name="pledge_value" value="{{ old('pledge_value', $property->pledge_value) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'pledge_value'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('pledge_insurance_value') ? ' has-danger' : '' }}">
                                                                Zálog biztosítási érték</label>
                                                            <input type="number" class="form-control{{ $errors->has('pledge_insurance_value') ? ' is-invalid' : '' }}" name="pledge_insurance_value" value="{{ old('pledge_insurance_value', $property->pledge_insurance_value) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'pledge_insurance_value'])
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
                                                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', $property->email) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'email'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                                                Telefonszám</label>
                                                            <input type="number" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone', $property->phone) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'phone'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('facebook') ? ' has-danger' : '' }}">
                                                                Facebook</label>
                                                            <input type="text" class="form-control{{ $errors->has('facebook') ? ' is-invalid' : '' }}" name="facebook" value="{{ old('facebook', $property->facebook) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'facebook'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('instagram') ? ' has-danger' : '' }}">
                                                                Instagram</label>
                                                            <input type="text" class="form-control{{ $errors->has('instagram') ? ' is-invalid' : '' }}" name="instagram" value="{{ old('instagram', $property->instagram) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'instagram'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tiktok') ? ' has-danger' : '' }}">
                                                                Tiktok</label>
                                                            <input type="text" class="form-control{{ $errors->has('tiktok') ? ' is-invalid' : '' }}" name="tiktok" value="{{ old('tiktok', $property->tiktok) }}" placeholder="">
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
                                                                <input @if($property->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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
                                                    <div class="form-group{{ $errors->has('connected_business') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Kapcsolódó üzlet</label>
                                                        <select title="- válasszon üzletet -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="connected_business" id="input-role" class="selectpicker form-control{{ $errors->has('connected_business') ? ' is-invalid' : '' }}" placeholder="">
                                                            <option value=""></option>
                                                            @foreach($businesses as $business)
                                                                <option @if($property->connected_business == $business->business_id) selected @endif value="{{ $business->business_id }}">{{ $business->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'connected_business'])
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('tools') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Eszközök</label>
                                                        <select multiple title="- válasszon eszközöket -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="tools[]" id="input-role" class="selectpicker form-control{{ $errors->has('tools') ? ' is-invalid' : '' }}" placeholder="">
                                                            <option value=""></option>

                                                            @foreach($tools as $tool)
                                                                <option @if(in_array($tool->tool_id, $property->tools->pluck('tool_id')->toArray())) selected @endif value="{{ $tool->tool_id }}">{{ $tool->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'tools'])
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('users') ? ' has-danger' : '' }}">
                                                        <label class="form-control-label" for="input-role">Felhasználók</label>
                                                        <select multiple title="- válasszon felhasználókat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="users[]" id="input-role" class="selectpicker form-control{{ $errors->has('users') ? ' is-invalid' : '' }}" placeholder="">
                                                            <option value=""></option>

                                                            @foreach($users as $user)
                                                                <option @if(in_array($user->id, $property->users->pluck('id')->toArray())) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @include('alerts.feedback', ['field' => 'users'])
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                
                                </div>


                                <button type="button" class="btn btn-success save_property">Mentés</button>
                            </div>


                            <div class="tab-pane fade" id="others_data" role="tabpanel" aria-labelledby="others_data-tab">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('attributes') ? ' has-danger' : '' }}">
                                                    Ingatlan adatai</label>
                                                <select multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="attributes[]" class="selectpicker form-control{{ $errors->has('attributes') ? ' is-invalid' : '' }}">
                                                @foreach($attributes as $attribute)
                                                    <option @if(in_array($attribute->attribute_id,$property->attributes->pluck('attribute_id')->toArray())) selected @endif value="{{ $attribute->attribute_id }}" >{{ $attribute->name }}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                @foreach($property->attributes as $attribute)
                                    <?php $attribute_key = 'attribute_' . $attribute->attribute_id; ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">

                                                    @if($attribute->type == 'checkbox')
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input type="hidden" data-attribute_id="{{ $attribute->attribute_id }}" name="attribute_{{ $attribute->attribute_id }}" value="{{$attribute->pivot->value}}"> 

                                                            <input @if($attribute->pivot->value) checked @endif data-attribute_id="{{ $attribute->attribute_id }}" class="form-check-input attribute_checkes" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="attribute_check_{{ $attribute->attribute_id }}">
                                                            <span class="form-check-sign"></span>
                                                            {{$attribute->name}}
                                                            </label>
                                                        </div>
                                                    @else
                                                        <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                            {{$attribute->name}}</label>
                                                        <input type="{{$attribute->type}}" class="form-control{{ $errors->has($attribute_key) ? ' is-invalid' : '' }}" name="{{$attribute_key}}" value="{{ old($attribute_key, $attribute->pivot->value) }}">
                                                        @include('alerts.feedback', ['field' => $attribute_key])
                                                    @endif
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                @endforeach
                            </div>



                            <div class="tab-pane fade" id="docs_data" role="tabpanel" aria-labelledby="docs_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0" >
                                                <tr class='grey_background'>
                                                    <td>
                                                        <form action="" class="property_doc_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-property_doc_comment">Megjegyzés</label>
                                                                <input type="text" name="property_doc_comment" id="input-property_doc_comment" class="form-control">
                                                                <input type="hidden" name="property_doc_input" value="">
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
                                                                <label class="form-control-label" for="input-property_doc_issue_date">Kiállítás dátuma</label>
                                                                <input type="date" name="property_doc_issue_date" id="input-property_doc_issue_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-property_doc_expire_date">Lejárat dátuma</label>
                                                                <input type="date" name="property_doc_expire_date" id="input-property_doc_expire_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-property_doc_reminder_days">Emlékeztető határideje (nap)</label>
                                                                <input type="number" name="property_doc_reminder_days" id="input-property_doc_reminder_days" class="form-control">
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr class='grey_background'>
                                                    <td>
                                                        <div class="dropzone_area" >
                                                            <form action="/ajax/upload_property_doc" id="property_doc_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                <div class="dz-message needsclick">
                                                                    <span class="">Húzza ide a fájlt vagy kattintson a feltöltéshez! Egyszerre maximum 1 fájlt tud feltölteni.</span>
                                                                    <input type="hidden" name="property_id" value="{{ $property->property_id }}">
                                                                    @csrf
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success mt-4 upload_property_doc">Feltöltés</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 property_doc_table" >
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
                                                    @foreach($property->documents as $doc)
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
                                                <button type="button" class="btn btn-success save_contact">Kapcsolattartó mentése</button>
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
                                                    @foreach($property->contacts as $contact)
                                                        <tr class='grey_background contact_row_{{$contact->contact_id}}'>
                                                            <td class="contact_lastname">{{ $contact->lastname }}</td>
                                                            <td class="contact_firstname">{{ $contact->firstname }}</td>
                                                            <td class="contact_phone">{{ $contact->phone }}</td>
                                                            <td class="contact_email">{{ $contact->email }}</td>
                                                            <td class="contact_rank">{{ $contact->rank }}</td>
                                                            <td class="contact_comment">{{ $contact->comment }}</td>
                                                            <td>
                                                                <div data-contact_id="{{ $contact->contact_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-success edit_contact" data-contact_id="{{ $contact->contact_id }}">
                                                                        Szerkesztés
                                                                    </button>
                                                                </div>
                                                                <div data-contact_id="{{ $contact->contact_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_contact" data-contact_id="{{ $contact->contact_id }}">
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
                                                <form action="/ajax/upload_property_image" id="property_image_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                    <div class="dz-message needsclick">
                                                        <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                        <input type="hidden" name="property_id" value="{{ $property->property_id }}">
                                                        @csrf
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <div class="property_gallery_container">
                                                
                                                <div class="delete_buttons">
                                                    @foreach($property->property_images as $img)
                                                        <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                            <button type="button" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                                <i data-img_id="{{ $img->survey_image_id }}" class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                <div data-lightGallery="1" id="lightgallery_property_images" class="">
                                                    @foreach($property->property_images as $img)
                                                        <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="property_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/property_images/' . $img->image_path; ?>">
                                                            <img data-img_id="{{ $img->image_id }}" class="img-responsive property_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/property_images/' . $img->image_path; ?>" alt="@if($img->image_date) {{ date('Y. m. d.', strtotime($img->image_date)) }} @else - @endif ">
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

        $("#property_doc_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'property_doc',
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
                $('input[name=property_doc_input]').val(response);
            },
            removedfile: function(file) {
                $('input[name=property_doc_input]').val('');

                var class_name = file.name;
                class_name = class_name.replace(/[. ]/g,'');
                jQuery('.' + class_name).remove();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
        });


        function buildpropertyGallery($initScope)
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
                                $(this).width($('.property_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                                $(this).height($('.property_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                                $(this).css('top', $('.property_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                                $(this).css('left', $('.property_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
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


        function initPropertyGallery() {
            var $initScope_implementation = $('#lightgallery_property_images');

            var images = $initScope_implementation.find('img').get();
            var allImagesLoaded = images.every(function(img) {
                return img.complete;
            });

            if (allImagesLoaded) {
                buildpropertyGallery($initScope_implementation);
            } else {
                $(images).on('load', function() {
                    var allLoaded = images.every(function(img) {
                        return img.complete;
                    });
                    if (allLoaded) {
                        buildpropertyGallery($initScope_implementation);
                    }
                });
            }
        }


        $("#property_image_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'property_image',
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
                    url: "/ajax/auto_save_property_images",
                    data: { property_id: '{{ $property->property_id }}', image: response, image_date: $('input[name=image_date]').val() }
                })
                .done(function( msg ) {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_property_gallery",
                        data: { property_id: '{{ $property->property_id }}' }
                    })
                    .done(function( html ) {
                        $('.property_gallery_container').html(html);
                        initPropertyGallery();
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
        

        $('.save_property').click(function(){
            $('#property_form').submit();
        });

        //is_own1 on change
        $('input[name=is_own]').change(function(){
            let value = $(this).val();
            if(value == 1){
                $('.is_own_row').show();
            } else {
                $('.is_own_row').hide();
            }
        });

        $('select[name=reservation_type]').change(function(){
            let value = $(this).val();
            if(value == 1){
                $('.reservation_date_div').show();
            } else {
                $('.reservation_date_div').hide();
            }
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
                placeholder: 'Irányítószám'
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


        $('.attribute_checkes').change(function(){

            if($(this).is(':checked')){
                $('input[name=attribute_'+$(this).attr('data-attribute_id')+']').val(1);
            } else {
                $('input[name=attribute_'+$(this).attr('data-attribute_id')+']').val(0);
            }

        });



        $('.upload_property_doc').click(function(){

                
                //ha van feltöltve kép, akkor legyen hozzá megadva név
                if(!$('input[name=property_doc_input]').val().length)
                {
                    return false;
                }
                
                /*
                if(!$(".property_doc_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('.property_doc_form')).click().remove();
                    return false;
                }
                */

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_property_doc_data",
                    data: { property_id: "{{ $property->property_id }}", 
                            property_doc_comment: $('input[name=property_doc_comment]').val(), 
                            doc_type_id: $('select[name=doc_type_id]').val(), 
                            property_doc_issue_date: $('input[name=property_doc_issue_date]').val(), 
                            property_doc_expire_date: $('input[name=property_doc_expire_date]').val(), 
                            property_doc_reminder_days: $('input[name=property_doc_reminder_days]').val(), 
                            doc_file: $('input[name=property_doc_input]').val() }
                })
                .done(function( msg ) {

                    var myDropzone = Dropzone.forElement("#property_doc_form");
                    myDropzone.removeAllFiles(true);

                    $('input[name=property_doc_comment]').val('');
                    $('input[name=property_doc_input]').val('');
                    $('select[name=doc_type_id]').val('');
                    $('input[name=property_doc_issue_date]').val('');
                    $('input[name=property_doc_expire_date]').val('');
                    $('input[name=property_doc_reminder_days]').val('');

                    //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_property_docs",
                        data: { property_id: '{{ $property->property_id }}' }
                    })
                    .done(function( html ) {
                        $('.property_doc_table tbody').html(html);
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


            //gallery
            $('.gallery_tab').click(function(){
                initPropertyGallery();
            });



            $("body").on("mouseenter", ".property_img_wrapper", function(e){
                $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
            });
            $("body").on("mouseleave", ".property_img_wrapper", function(e){
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
                        url: "/ajax/delete_property_image",
                        data: { img_id: img_id }
                    })
                    .done(function( msg ) {
                        //kép hozzáadása a listához
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_property_gallery",
                            data: { property_id: '{{ $property->property_id }}' }
                        })
                        .done(function( html ) {
                            $('.property_gallery_container').html(html);
                            initPropertyGallery();
                        });
                    });


                });

            });


            $('.save_contact').click(function(){

                if(!$('input[name=contact_lastname]').val().length || !$('input[name=contact_firstname]').val().length)
                {
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_property_contact",
                    data: { property_id: "{{ $property->property_id }}", 
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
                        url: "/ajax/load_property_contacts",
                        data: { property_id: '{{ $property->property_id }}' }
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
                        url: "/ajax/delete_property_contact",
                        data: {
                                contact_id: contact_id
                            }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_property_contacts",
                            data: { property_id: '{{ $property->property_id }}' }
                        })
                        .done(function( html ) {
                            $('.contact_table tbody').html(html);
                        });
                    });

                });

            });
    });
    </script>
@endpush