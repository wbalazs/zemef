@extends('layouts.app', ['activePage' => 'business', 'activeButton' => 'business', 'title' => 'Zemef ERP', 'navName' => 'Üzlet szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Üzlet szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="{{ route('business.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <ul class="nav nav-tabs mb-4" id="business-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="business-data-tab" data-toggle="pill" href="#business-data" role="tab" aria-controls="business-data" aria-selected="true">Alap adatok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="business-markings-tab" data-toggle="pill" href="#business-markings" role="tab" aria-controls="business-markings" aria-selected="false">Szállítói jelzések</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="business-aliases-tab" data-toggle="pill" href="#business-aliases" role="tab" aria-controls="business-aliases" aria-selected="false">Aliasok</a>
                        </li>
                    </ul>
                    <form id="business_form" method="post" action="{{ route('business.update', $business->business_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="business-tabs-content">
                        <div class="tab-pane fade show active" id="business-data" role="tabpanel" aria-labelledby="business-data-tab">
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
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $business->name) }}" placeholder="" required autofocus>
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
                                                        @if($business->zip)
                                                            <option value="{{ $business->zip_id }}">{{ $business->zip }}</option>
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
                                                        @if($business->city)
                                                            <option value="{{ $business->city_id }}">{{ $business->city }}</option>
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
                                                    <input type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" maxlength="100" value="{{ old('address', $business->address) }}" placeholder="">
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
                                                    <input type="text" class="form-control{{ $errors->has('floor') ? ' is-invalid' : '' }}" name="floor" maxlength="100" value="{{ old('floor', $business->floor) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'floor'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('door') ? ' has-danger' : '' }}">
                                                        Ajtó</label>
                                                    <input type="text" class="form-control{{ $errors->has('door') ? ' is-invalid' : '' }}" name="door" maxlength="100" value="{{ old('door', $business->door) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'door'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group{{ $errors->has('warehouses') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-role">Raktárak</label>
                                                <select title="- válasszon raktárakat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="warehouses[]" id="input-role" class="selectpicker form-control{{ $errors->has('warehouses') ? ' is-invalid' : '' }}" placeholder="">
                                                    @foreach($warehouses as $warehouse)
                                                        <option @if(in_array($warehouse->warehouse_id, $business->warehouses->pluck('warehouse_id')->toArray())) selected @endif value="{{ $warehouse->warehouse_id }}" value="{{ $warehouse->warehouse_id }}" >{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>

                                                @include('alerts.feedback', ['field' => 'warehouses'])
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('email') ? ' has-danger' : '' }}">
                                                        E-mail</label>
                                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" maxlength="255" value="{{ old('email', $business->email) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'email'])
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
                                                        <input @if($business->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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
                                                    <input type="text" @if($business->status) disabled @endif class="form-control{{ $errors->has('note') ? ' is-invalid' : '' }}" name="note" maxlength="100" value="{{ old('note', $business->note) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'note'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('note_date') ? ' has-danger' : '' }}">
                                                        Dátum</label>
                                                    <input type="date" @if($business->status) disabled @endif class="form-control{{ $errors->has('note_date') ? ' is-invalid' : '' }}" name="note_date" maxlength="100" value="{{ old('note_date', $business->note_date) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'note_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <hr/>
                                    <h6 class="heading-small text-muted mb-4">Árrrés</h6>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group{{ $errors->has('discount_type') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-role">Típus</label>
                                                <select name="discount_type" id="discount_type" class="select2 form-control{{ $errors->has('discount_type') ? ' is-invalid' : '' }}" placeholder="">
                                                    <option @if($business->discount_type == "") selected @endif value="">- válasszon típust -</option>
                                                    <option @if($business->discount_type == 1) selected @endif value="1">Fix összegű</option>
                                                    <option @if($business->discount_type == 2) selected @endif value="2">%-os</option>
                                                </select>

                                                @include('alerts.feedback', ['field' => 'discount_type'])
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('discount_value') ? ' has-danger' : '' }}">
                                                        Mérték</label>
                                                    <input type="number" class="form-control{{ $errors->has('discount_value') ? ' is-invalid' : '' }}" name="discount_value" id="discount_value" value="{{ old('discount_value', $business->discount_value) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'discount_value'])
                                                </div>
                                            </fieldset>
                                        </div>


                                    </div>

                                    <button type="button" class="btn btn-success save_business"><i class="fa fa-save"></i> Mentés</button>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="heading-small text-muted mb-0">Naptár szerkesztése</h6>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="prevMonth">
                                                <i class="fa fa-chevron-left"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="currentMonth" disabled>
                                                {{ \Carbon\Carbon::now()->format('Y. F') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="nextMonth">
                                                <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div id="calendar-container">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>H</th>
                                                    <th>K</th>
                                                    <th>Sze</th>
                                                    <th>Cs</th>
                                                    <th>P</th>
                                                    <th>Szo</th>
                                                    <th>V</th>
                                                </tr>
                                            </thead>
                                            <tbody id="calendar-body">
                                                <!-- Ide kerül dinamikusan a naptár -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Kattintson a napokra a munkanap státusz módosításához. A zöld szín jelzi a munkanapokat.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                        </div>

                        <div class="tab-pane fade" id="business-markings" role="tabpanel" aria-labelledby="business-markings-tab">
                            <fieldset>
                                <input type="hidden" name="business_marking_id" value="">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="business_marking_name">Betű jelzés</label>
                                            <input type="text" name="business_marking_name" id="business_marking_name" class="form-control" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="business_marking_product_status_id">Állapot</label>
                                            <select name="business_marking_product_status_id" id="business_marking_product_status_id" class="form-control">
                                                <option value="">- válasszon -</option>
                                                @foreach($active_product_statuses as $product_status)
                                                    <option value="{{ $product_status->product_status_id }}" data-price="{{ $product_status->price }}" data-vat-name="{{ optional($product_status->vatType)->name }}">
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
                                                    <input class="form-check-input" type="checkbox" name="business_marking_default" value="1">
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
                                                    <input class="form-check-input" type="checkbox" name="business_marking_status" value="1" checked>
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
                                            <label class="form-control-label" for="business_marking_price_info">Állapot ára (információ)</label>
                                            <input type="text" id="business_marking_price_info" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="business_marking_vat_info">Áfa típus (információ)</label>
                                            <input type="text" id="business_marking_vat_info" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 d-flex align-items-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-success save_business_marking"><i class="fa fa-save"></i> Jelzés mentése</button>
                                            <button type="button" class="btn btn-secondary reset_business_marking_form">Új jelzés</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-sm m-0 business_markings_table">
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
                                                @foreach($business->business_markings as $marking)
                                                    <tr class="grey_background business_marking_row_{{ $marking->id }}" data-id="{{ $marking->id }}" data-name="{{ $marking->name }}" data-product_status_id="{{ $marking->product_status_id }}" data-default="{{ $marking->default ? 1 : 0 }}" data-status="{{ $marking->status ? 1 : 0 }}">
                                                        <td class="business_marking_name">{{ $marking->name }}</td>
                                                        <td class="business_marking_product_status">{{ optional($marking->productStatus)->name }}</td>
                                                        <td class="business_marking_price">{{ optional($marking->productStatus)->price }}</td>
                                                        <td class="business_marking_vat">{{ optional(optional($marking->productStatus)->vatType)->name }}</td>
                                                        <td class="business_marking_default">{{ $marking->default ? 'Igen' : 'Nem' }}</td>
                                                        <td class="business_marking_status">{{ $marking->status ? 'Igen' : 'Nem' }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-success edit_business_marking" data-id="{{ $marking->id }}"><i class="fa fa-edit"></i></button>
                                                            <button type="button" class="btn btn-danger delete_business_marking" data-id="{{ $marking->id }}"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="tab-pane fade" id="business-aliases" role="tabpanel" aria-labelledby="business-aliases-tab">
                            <fieldset>
                                <input type="hidden" name="business_alias_id" value="">

                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="form-control-label" for="business_alias_name">Név (alias)</label>
                                            <input type="text" name="business_alias_name" id="business_alias_name" class="form-control" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="business_alias_warehouse_id">Raktár</label>
                                            <select name="business_alias_warehouse_id" id="business_alias_warehouse_id" class="form-control">
                                                <option value="">- válasszon -</option>
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mt-4 pt-2">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" name="business_alias_aktiv" value="1" checked>
                                                    <span class="form-check-sign"></span>
                                                    Aktív
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-success save_business_alias"><i class="fa fa-save"></i> Mentés</button>
                                            <button type="button" class="btn btn-secondary reset_business_alias_form">Új alias</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-sm m-0 business_aliases_table">
                                            <thead>
                                                <tr>
                                                    <th>Név (alias)</th>
                                                    <th>Raktár</th>
                                                    <th>Aktív</th>
                                                    <th>Művelet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($business->business_aliases as $alias)
                                                    <tr class="grey_background business_alias_row_{{ $alias->id }}" data-id="{{ $alias->id }}" data-name="{{ $alias->name }}" data-warehouse_id="{{ $alias->warehouse_id }}" data-aktiv="{{ $alias->aktiv ? 1 : 0 }}">
                                                        <td class="business_alias_name">{{ $alias->name }}</td>
                                                        <td class="business_alias_warehouse">{{ optional($alias->warehouse)->name }}</td>
                                                        <td class="business_alias_aktiv">{{ $alias->aktiv ? 'Igen' : 'Nem' }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-success edit_business_alias" data-id="{{ $alias->id }}"><i class="fa fa-edit"></i></button>
                                                            <button type="button" class="btn btn-danger delete_business_alias" data-id="{{ $alias->id }}"><i class="fa fa-trash"></i></button>
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
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    
    
    <style>
        .calendar-day {
            height: 80px;
            vertical-align: top;
            padding: 5px !important;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .calendar-day:hover {
            background-color: #f8f9fa !important;
        }
        
        .calendar-day .day-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .calendar-day.other-month {
            opacity: 0.5;
        }
        
        .calendar-day.today .day-number {
            color: #007bff;
            text-decoration: underline;
        }
        
        .calendar-day .form-check {
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .calendar-day .form-check .form-check-sign {
            top: 0;
        }
        
        #calendar-container table {
            table-layout: fixed;
        }
        
        #calendar-container th {
            text-align: center;
            background-color: #f8f9fa;
        }
    </style>
    <script>
$(document).ready(function () {
     $('.save_business').click(function(){
        $('#business_form').submit();
    });

    $('#discount_type').on('change', function(){
        let selectedType = $(this).val();
        let discountValueInput = $('#discount_value');
        if (selectedType == '') {
            // Ha nincs kiválasztott típus, tiltsuk le a mezőt és állítsuk nullára
            discountValueInput.val('0').prop('disabled', true);
        } else {
            // Engedélyezzük a mezőt
            discountValueInput.prop('disabled', false);

            if (selectedType == '1') {
                // Fix összegű
                discountValueInput.attr('type', 'number');
                discountValueInput.attr('min', '0');
                discountValueInput.removeAttr('max'); // Nincs maximum
            } else if (selectedType == '2') {
                // Százalékos
                discountValueInput.val('0');
                discountValueInput.attr('type', 'number');
                discountValueInput.attr('min', '0');
                discountValueInput.attr('max', '100');
            }
        }
    });

    $('.workday-checkbox').change(function() {
        $(this).closest('tr').find('.is-updated').val('1');
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


    $(document).on('change', 'input[name="status"]', function(){
        if($(this).is(':checked')){
            $('input[name="note"]').prop('disabled', true);
            $('input[name="note_date"]').prop('disabled', true);
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
        
    // Calendar data from server - betöltjük a meglévő adatokat
     const calendarData = {
        @foreach($business->calendars as $calendar)
            "{{ \Carbon\Carbon::parse($calendar->day)->format('Y-m-d') }}": {
                id: {{ $calendar->id }},
                is_workday: {{ $calendar->is_workday ? 'true' : 'false' }},
                day: "{{ \Carbon\Carbon::parse($calendar->day)->format('Y-m-d') }}",
                day_name: "{{ $calendar->day_name }}"
            }@if(!$loop->last),@endif
        @endforeach
    };
    
    console.log('Calendar data loaded:', calendarData);
    
    let currentDate = luxon.DateTime.now().startOf('month');
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    function renderCalendar() {
        const startOfMonth = currentDate.startOf('month');
        const endOfMonth = currentDate.endOf('month');
        const startOfCalendar = startOfMonth.startOf('week');
        const endOfCalendar = endOfMonth.endOf('week');
        
        let currentDay = startOfCalendar;
        let weekRows = [];
        let weekRow = '<tr>';
        let dayCounter = 0;
        
        while (currentDay <= endOfCalendar) {
            const dateStr = currentDay.toFormat('yyyy-MM-dd');
            const dayName = currentDay.toFormat('cccc');
            const isCurrentMonth = currentDay.hasSame(currentDate, 'month');
            const isToday = currentDay.hasSame(luxon.DateTime.now(), 'day');
            
            // DEBUG: Nézzük meg pontosan mi van a calendarData-ban
            console.log('Looking for date:', dateStr);
            console.log('calendarData[dateStr]:', calendarData[dateStr]);
            
            const calendarItem = calendarData[dateStr];
            
            let cellClass = 'calendar-day';
            if (!isCurrentMonth) cellClass += ' other-month';
            if (isToday) cellClass += ' today';
            
            let itemId = '';
            let isWorkday = false;
            
            if (calendarItem && calendarItem.id) {
                itemId = calendarItem.id;
                isWorkday = calendarItem.is_workday;
                console.log(`Date ${dateStr} has existing ID: ${itemId}, isWorkday: ${isWorkday}`);
            } else {
                itemId = 'new_' + dateStr;
                console.log(`Date ${dateStr} is new, using: ${itemId}`);
            }
            
            let workdayClass = isWorkday ? 'workday' : '';
            
            weekRow += `
                <td class="${cellClass} ${workdayClass}" 
                    data-date="${dateStr}" 
                    data-dayname="${dayName}" 
                    data-itemid="${itemId}" 
                    data-isworkday="${isWorkday}">
                    <div class="day-number">${currentDay.day}</div>
                    <div class="workday-status">
                        ${isWorkday ? '✓ Munkanap' : '✗ Szabadnap'}
                    </div>
                </td>
            `;
            
            dayCounter++;
            currentDay = currentDay.plus({ days: 1 });
            
            if (dayCounter % 7 === 0) {
                weekRow += '</tr>';
                weekRows.push(weekRow);
                if (currentDay <= endOfCalendar) {
                    weekRow = '<tr>';
                }
            }
        }
        
        $('#calendar-body').html(weekRows.join(''));
        $('#currentMonth').text(currentDate.toFormat('yyyy. MMMM'));
        
        // Apply colors based on workday status
        $('.calendar-day').each(function() {
            const $td = $(this);
            if ($td.data('isworkday')) {
                $td.css('background-color', '#d4edda');
            }
        });
    }
    // Handle click on calendar day
    $(document).on('click', '.calendar-day', function() {
        const $td = $(this);
        const dateStr = $td.data('date');
        const itemId = $td.data('itemid');
        const currentIsWorkday = $td.data('isworkday');
        const newIsWorkday = !currentIsWorkday;
        
        // Ellenőrizzük, hogy az itemId egyezik-e a calendarData-ban lévő ID-val
        if (calendarData[dateStr] && calendarData[dateStr].id != itemId) {
            console.warn('ID MISMATCH! Fixing...');
            // Javítsuk ki a helyes ID-ra
            const correctId = calendarData[dateStr].id;
            $td.data('itemid', correctId);
            itemId = correctId;
        }
        
        // Validate itemId
        if (!itemId) {
            alert('Hiba: Nem található azonosító a naphoz');
            return;
        }
        
        // Visual feedback
        $td.css('opacity', '0.6');
        
        // Send AJAX update
        $.ajax({
            url: '{{ route("business.calendar.update", $business) }}',
            method: 'POST',
            data: {
                _token: csrfToken,
                calendar_id: itemId,
                is_workday: newIsWorkday ? 1 : 0,
                date: dateStr
            },
            success: function(response) {
                 if (response.success) {
                    // Update UI with the returned data
                    $td.data('isworkday', response.is_workday);
                    $td.data('itemid', response.calendar_id); // Update with real ID
                    $td.find('.workday-status').text(response.is_workday ? '✓ Munkanap' : '✗ Szabadnap');
                    
                    if (response.is_workday) {
                        $td.css('background-color', '#d4edda');
                    } else {
                        $td.css('background-color', '');
                    }
                    
                    // Update calendarData with the real ID
                    calendarData[dateStr] = {
                        id: response.calendar_id,
                        is_workday: response.is_workday,
                        day: dateStr
                    };
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Hiba a mentés során');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Szerver hiba');
                }
            },
            complete: function() {
                $td.css('opacity', '1');
            }
        });
    });
    
    // Initialize calendar
    renderCalendar();
    
    // Month navigation
    $('#prevMonth').click(function() {
        currentDate = currentDate.minus({ months: 1 });
        renderCalendar();
    });
    
    $('#nextMonth').click(function() {
        currentDate = currentDate.plus({ months: 1 });
        renderCalendar();
    });

    function updateBusinessMarkingStatusInfo() {
        let selected = $('#business_marking_product_status_id').find('option:selected');
        $('#business_marking_price_info').val(selected.data('price') || '');
        $('#business_marking_vat_info').val(selected.data('vat-name') || '');
    }

    function resetBusinessMarkingForm() {
        $('input[name=business_marking_id]').val('');
        $('input[name=business_marking_name]').val('');
        $('#business_marking_product_status_id').val('');
        $('input[name=business_marking_default]').prop('checked', false);
        $('input[name=business_marking_status]').prop('checked', true);
        updateBusinessMarkingStatusInfo();
    }

    function fillBusinessMarkingForm(markingId) {
        let row = $('.business_marking_row_' + markingId);

        $('input[name=business_marking_id]').val(row.data('id'));
        $('input[name=business_marking_name]').val(row.data('name'));
        $('#business_marking_product_status_id').val(row.data('product_status_id'));
        $('input[name=business_marking_default]').prop('checked', parseInt(row.data('default'), 10) === 1);
        $('input[name=business_marking_status]').prop('checked', parseInt(row.data('status'), 10) === 1);
        updateBusinessMarkingStatusInfo();

        $('a[href="#business-markings"]').tab('show');
        $('input[name=business_marking_name]').focus();
    }

    function reloadBusinessMarkings() {
        $.ajax({
            method: 'POST',
            url: '/ajax/load_business_markings',
            data: { business_id: '{{ $business->business_id }}' }
        }).done(function(html) {
            $('.business_markings_table tbody').html(html);
        });
    }

    $('#business_marking_product_status_id').change(function() {
        updateBusinessMarkingStatusInfo();
    });

    $('.reset_business_marking_form').click(function() {
        resetBusinessMarkingForm();
    });

    $('.save_business_marking').click(function() {
        let markingName = $('input[name=business_marking_name]').val();
        let productStatusId = $('#business_marking_product_status_id').val();

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
            url: '/ajax/save_business_marking',
            data: {
                business_marking_id: $('input[name=business_marking_id]').val(),
                business_id: '{{ $business->business_id }}',
                name: markingName,
                product_status_id: productStatusId,
                default: $('input[name=business_marking_default]').is(':checked') ? 1 : 0,
                status: $('input[name=business_marking_status]').is(':checked') ? 1 : 0
            }
        }).done(function() {
            reloadBusinessMarkings();
            resetBusinessMarkingForm();
            $.notify({ icon: 'done', message: 'Jelzés sikeresen mentve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
        });
    });

    $('body').on('click', '.edit_business_marking', function() {
        fillBusinessMarkingForm($(this).attr('data-id'));
    });

    $('body').on('click', '.business_markings_table tbody tr', function(e) {
        if ($(e.target).closest('button').length) {
            return;
        }
        fillBusinessMarkingForm($(this).data('id'));
    });

    $('body').on('click', '.delete_business_marking', function() {
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
                url: '/ajax/delete_business_marking',
                data: { id: markingId }
            }).done(function() {
                reloadBusinessMarkings();
                if ($('input[name=business_marking_id]').val() == markingId) {
                    resetBusinessMarkingForm();
                }
                $.notify({ icon: 'done', message: 'Jelzés sikeresen törölve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
            });
        });
    });

    resetBusinessMarkingForm();

    function resetBusinessAliasForm() {
        $('input[name=business_alias_id]').val('');
        $('input[name=business_alias_name]').val('');
        $('#business_alias_warehouse_id').val('');
        $('input[name=business_alias_aktiv]').prop('checked', true);
    }

    function fillBusinessAliasForm(aliasId) {
        let row = $('.business_alias_row_' + aliasId);

        $('input[name=business_alias_id]').val(row.data('id'));
        $('input[name=business_alias_name]').val(row.data('name'));
        $('#business_alias_warehouse_id').val(row.data('warehouse_id'));
        $('input[name=business_alias_aktiv]').prop('checked', parseInt(row.data('aktiv'), 10) === 1);

        $('a[href="#business-aliases"]').tab('show');
        $('input[name=business_alias_name]').focus();
    }

    function reloadBusinessAliases() {
        $.ajax({
            method: 'POST',
            url: '/ajax/load_business_aliases',
            data: { business_id: '{{ $business->business_id }}' }
        }).done(function(html) {
            $('.business_aliases_table tbody').html(html);
        });
    }

    $('.reset_business_alias_form').click(function() {
        resetBusinessAliasForm();
    });

    $('.save_business_alias').click(function() {
        let aliasName = $('input[name=business_alias_name]').val();

        if (!aliasName.length) {
            $.notify({ icon: 'done', message: 'A név (alias) nem lehet üres.' }, {type: 'danger', timer: 3000, placement: {from: 'top', align: 'right'}});
            return false;
        }

        $.ajax({
            method: 'POST',
            url: '/ajax/save_business_alias',
            data: {
                business_alias_id: $('input[name=business_alias_id]').val(),
                business_id: '{{ $business->business_id }}',
                name: aliasName,
                warehouse_id: $('#business_alias_warehouse_id').val(),
                aktiv: $('input[name=business_alias_aktiv]').is(':checked') ? 1 : 0
            }
        }).done(function() {
            reloadBusinessAliases();
            resetBusinessAliasForm();
            $.notify({ icon: 'done', message: 'Alias sikeresen mentve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
        });
    });

    $('body').on('click', '.edit_business_alias', function() {
        fillBusinessAliasForm($(this).attr('data-id'));
    });

    $('body').on('click', '.business_aliases_table tbody tr', function(e) {
        if ($(e.target).closest('button').length) {
            return;
        }
        fillBusinessAliasForm($(this).data('id'));
    });

    $('body').on('click', '.delete_business_alias', function() {
        var aliasId = $(this).attr('data-id');

        swal({
            text: 'Biztosan törli ezt az aliast?',
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
                url: '/ajax/delete_business_alias',
                data: { id: aliasId }
            }).done(function() {
                reloadBusinessAliases();
                if ($('input[name=business_alias_id]').val() == aliasId) {
                    resetBusinessAliasForm();
                }
                $.notify({ icon: 'done', message: 'Alias sikeresen törölve.' }, {type: 'success', timer: 3000, placement: {from: 'top', align: 'right'}});
            });
        });
    });

    resetBusinessAliasForm();
    
    // Save button for business data
    $('.save_business').click(function(){
        $('#business_form').submit();
    });
    

});
</script>
@endpush