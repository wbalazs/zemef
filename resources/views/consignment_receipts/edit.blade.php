@extends('layouts.app', ['activePage' => 'consignment_receipt', 'activeButton' => 'consignment_receipt', 'title' => 'Zemef ERP', 'navName' => 'Bizományosi átvétel rögzítése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                    </ul>




                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="{{ route('consignment_receipt.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>



                <div class="card-body ">
                    

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body ">

                                            <form id="consignment_receipt_form" method="post" action="{{ route('consignment_receipt.update', $consignment_receipt->consignment_receipt_id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('patch')

                                            <h6 class="heading-small text-muted mb-4">Bizományosi átvétel adatai</h6>
                                            <hr>
                                            @php
                                                $selectedHeaderCustomerMarkingId = old('customer_marking_id', $consignment_receipt->header_data['customer_marking_id'] ?? '');
                                                $selectedHeaderCategoryId = old('category_id', $consignment_receipt->header_data['category_id'] ?? 1);
                                                $selectedHeaderVatType = old('vat_type', $consignment_receipt->header_data['vat_type_id'] ?? '');
                                                $selectedHeaderKaratId = old('karat_id', $consignment_receipt->header_data['karat_id'] ?? '');
                                                $selectedHeaderLabelType = old('label_type_default', $consignment_receipt->header_data['label_type'] ?? '');
                                                $selectedHeaderCategory2Id = old('category2_id', $consignment_receipt->header_data['category2_id'] ?? '');
                                                $selectedHeaderTermekTipusId = old('termek_tipus_id', $consignment_receipt->header_data['termek_tipus_id'] ?? '');
                                            @endphp
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Szállító</label>
                                                        <select name="customer_marking_id" id="vzj" class="selectpicker form-control" data-live-search="true">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($customer_markings as $customer_marking)
                                                                <option
                                                                    value="{{ $customer_marking->id }}"
                                                                    data-price="{{ optional($customer_marking->productStatus)->price }}"
                                                                    data-vat-type-id="{{ optional($customer_marking->productStatus)->vat_type_id }}"
                                                                    @if((string) $selectedHeaderCustomerMarkingId === (string) $customer_marking->id) selected @endif
                                                                >
                                                                    {{ $customer_marking->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Kategória</label>
                                                        <select name="category_id" class="selectpicker form-control">
                                                            @include('categories.category_options', ['categories' => $categories, 'category_id' => $selectedHeaderCategoryId])
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Beszerzési ár</label>
                                                        <input type="number" class="form-control" name="beszerzesi_ar" value="{{ old('beszerzesi_ar', $consignment_receipt->header_data['beszerzesi_ar'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Eladási ár</label>
                                                        <input type="number" class="form-control" name="eladasi_ar" value="{{ old('eladasi_ar', $consignment_receipt->header_data['eladasi_ar'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Áfa típus</label>
                                                        <select name="vat_type" class="selectpicker form-control">
                                                            @foreach($vat_types as $vat_type)
                                                                <option value="{{ $vat_type->vat_type_id }}" {{ (string)$selectedHeaderVatType === (string)$vat_type->vat_type_id ? 'selected' : '' }}>
                                                                    {{ $vat_type->name.' - '.$vat_type->value }} %
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group" style="margin-left:20px;">
                                                        <br />
                                                        <input type="checkbox" class="form-check-input" name="hallmarks" value="1" id="hallmarks" {{ (int) old('hallmarks', $consignment_receipt->header_data['hallmarks'] ?? 0) === 1 ? 'checked' : '' }}>
                                                        <label class="form-control-label">Fémjelzés</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Karát</label>
                                                        <select name="karat_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($karats ?? [] as $karat)
                                                                <option value="{{ $karat->karat_id }}" {{ (string)$selectedHeaderKaratId === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Kategória II</label>
                                                        <select name="category2_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($category2s ?? [] as $category2)
                                                                <option value="{{ $category2->category2_id }}" {{ (string)$selectedHeaderCategory2Id === (string)$category2->category2_id ? 'selected' : '' }}>{{ $category2->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Méret</label>
                                                        <input type="text" class="form-control" name="size" value="{{ old('size', $consignment_receipt->header_data['size'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Termék típus</label>
                                                        <select name="termek_tipus_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedHeaderTermekTipusId])
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Címke típus</label>
                                                        <select name="label_type_default" class="form-control">
                                                            <option value="">-- Címke típus --</option>
                                                            <option value="1" {{ (string) $selectedHeaderLabelType === '1' ? 'selected' : '' }}>1-es típus</option>
                                                            <option value="2" {{ (string) $selectedHeaderLabelType === '2' ? 'selected' : '' }}>2-es típus</option>
                                                            <option value="3" {{ (string) $selectedHeaderLabelType === '3' ? 'selected' : '' }}>3-as típus</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">
                                                                Ügyfél</label>
                                                            <select @if($consignment_receipt->printed_at) disabled @endif required name="customer_id" class="select2 form-control{{ $errors->has('customer_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($customers as $customer)
                                                                    <option @if($customer->customer_id == $consignment_receipt->customer_id) selected @endif value="{{ $customer->customer_id }}" >{{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $zip = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->addresses->where('address_type', 'Lakcím')->first()->zip : '';
                                                                $zip_id = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->addresses->where('address_type', 'Lakcím')->first()->zip_id : '0';
                                                            ?>
                                                            <label class="form-control-label{{ $errors->has('customer_zip') ? ' has-danger' : '' }}">
                                                                Irányítószám</label>
                                                            <select class="form-control{{ $errors->has('customer_zip') ? ' is-invalid' : '' }} select2" name="customer_zip" value="{{ old('customer_zip', $zip_id) }}" id="customer_zip" required disabled>
                                                                @if(isset($zip) && isset($zip_id))
                                                                    <option value="{{ $zip_id }}">{{ $zip }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'customer_zip'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $city = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->addresses->where('address_type', 'Lakcím')->first()->city : '';
                                                                $city_id = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->addresses->where('address_type', 'Lakcím')->first()->city_id : '0';
                                                            ?>
                                                            <label class="form-control-label{{ $errors->has('customer_city') ? ' has-danger' : '' }}">
                                                                Város</label>
                                                            <select class="form-control{{ $errors->has('customer_city') ? ' is-invalid' : '' }} select2" name="customer_city" value="{{ old('customer_city', $city_id) }}" id="customer_city" required disabled>
                                                                @if(isset($city) && isset($city_id))
                                                                    <option value="{{ $city_id }}">{{ $city }}</option>
                                                                @endif
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'customer_city'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $address = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->addresses->where('address_type', 'Lakcím')->first()->address : '';
                                                            ?>
                                                            <label class="form-control-label{{ $errors->has('customer_address') ? ' has-danger' : '' }}">
                                                                Cím</label>
                                                            <input type="text" class="form-control{{ $errors->has('customer_address') ? ' is-invalid' : '' }}" name="customer_address" value="{{ old('customer_address', $address) }}" placeholder="" disabled>
                                                            @include('alerts.feedback', ['field' => 'customer_address'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            @php
                                                                $isCompanyDefault = old('customer_is_company', $consignment_receipt->header_data['customer_is_company'] ?? 0);
                                                            @endphp
                                                            <div class="form-check" style="margin-top:32px;">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="checkbox" id="customer_is_company" name="customer_is_company" value="1" {{ (int)$isCompanyDefault === 1 ? 'checked' : '' }}>
                                                                    <span class="form-check-sign"></span>
                                                                    cég: I/N
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $customer_is_company = (isset($consignment_receipt->customer) && (int) $consignment_receipt->customer->is_company === 1);
                                                                $tax_identity = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->tax_identity : '';
                                                                $tax_number = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->tax_number : '';
                                                                $identity_card_number = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->identity_card_number : '';
                                                                $tax_identifier_fallback = $customer_is_company
                                                                    ? ($tax_number ?: $tax_identity)
                                                                    : ($tax_identity ?: $tax_number ?: $identity_card_number);
                                                                $tax_identifier_value = old('customer_tax_identifier', $consignment_receipt->header_data['tax_identifier'] ?? $tax_identifier_fallback);
                                                            ?>
                                                            <label id="tax_identifier_label" class="form-control-label{{ $errors->has('customer_tax_identifier') ? ' has-danger' : '' }}">
                                                                Adóazonosító
                                                            </label>
                                                            <input type="text" class="form-control{{ $errors->has('customer_tax_identifier') ? ' is-invalid' : '' }}" id="customer_tax_identifier" name="customer_tax_identifier" value="{{ $tax_identifier_value }}" placeholder="" disabled>
                                                            @include('alerts.feedback', ['field' => 'customer_tax_identifier'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('currency_id') ? ' has-danger' : '' }}">
                                                                Deviza</label>
                                                            <select @if($consignment_receipt->printed_at) disabled @endif required name="currency_id" class="select2 form-control{{ $errors->has('currency_id') ? ' is-invalid' : '' }}">
                                                                @foreach($currencies as $currency)
                                                                    <option 
                                                                        @if($currency->currency_id == $consignment_receipt->currency_id) 
                                                                            selected 
                                                                            @php $selected_currency_editable = $currency->editable; 
                                                                                 $selected_currency_value = $currency->value;
                                                                            @endphp
                                                                        @elseif(!isset($selected) && $currency->default == 1)
                                                                            @php $selected_currency_editable = $currency->editable; 
                                                                                 $selected_currency_value = $currency->value;
                                                                            @endphp
                                                                            selected
                                                                            @php $selected = true; @endphp
                                                                        @endif 
                                                                        value="{{ $currency->currency_id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'currency_id'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php $currency_value = (isset($consignment_receipt->currency_value)) ? $consignment_receipt->currency_value : ''; ?>
                                                            <label class="form-control-label{{ $errors->has('currency_value') ? ' has-danger' : '' }}">
                                                                Árfolyam</label>
                                                            <input type="number" @if($consignment_receipt->printed_at) disabled @endif step=".01" class="form-control{{ $errors->has('currency_value') ? ' is-invalid' : '' }}" name="currency_value" value="{{ old('currency_value', $selected_currency_value) }}" placeholder="" @if($selected_currency_editable == '0') disabled @endif required>
                                                            @include('alerts.feedback', ['field' => 'currency_value'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                Sorszám</label>
                                                            <input type="text" class="form-control" name="ticket_number" value="{{ $consignment_receipt->ticket_number }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label @if($consignment_receipt->printed_at) disabled @endif class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">
                                                                Státusz</label>
                                                            <select @if($consignment_receipt->printed_at) disabled @endif title="- válasszon -" name="status" disabled class="select2 form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
                                                                <option @if($consignment_receipt->status != "2") selected @endif value="1">Folyamatban</option>
                                                                <option @if($consignment_receipt->status == "2") selected @endif value="2">Feldolgozva</option>
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                
                                                                
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('expire_date') ? ' has-danger' : '' }}">
                                                                Lejárati dátum</label>
                                                            <input type="date" class="form-control" name="expire_date" @if($consignment_receipt->printed_at) disabled @endif id="expire_date" value="{{ $consignment_receipt->expire_date }}" required>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($consignment_receipt->is_hallmark_requested == 1) checked @endif @if($consignment_receipt->printed_at) disabled @endif class="form-check-input cri_is_hallmark_requested" type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="is_hallmark_requested">
                                                                <span class="form-check-sign"></span>
                                                                Fémjelzést kér?
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                                                                Üzlet</label>
                                                            <select @if($consignment_receipt->printed_at) disabled @endif required name="business_id" id="business_id" class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($businesses as $business)
                                                                    <option @if($business->business_id == $consignment_receipt->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label @if($consignment_receipt->printed_at) disabled @endif class="form-control-label{{ $errors->has('storage_id') ? ' has-danger' : '' }}">
                                                                Raktár</label>
                                                            <select @if($consignment_receipt->printed_at) disabled @endif title="- válasszon -" required name="storage_id" id="storage_id" class="select2 form-control{{ $errors->has('storage_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @if(isset($warehouse))
                                                                    <option value="{{ $warehouse->warehouse_id }}" selected>{{ $warehouse->name }}</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            @if($consignment_receipt->is_storno || $consignment_receipt->storno_cr_id)
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                @if($consignment_receipt->is_storno) Sztornózott @else Sztornó @endif bizományosi átvétel</label>
                                                            <input type="text" class="form-control" name="ticket_number" value="{{ $consignment_receipt->relatedticketnumber }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                Sztornó oka</label>
                                                            <input type="text" class="form-control" name="storno_reason" value="{{ $consignment_receipt->storno_reason }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            @endif

                                            <hr>
                                            </form>

                                            <h6 class="heading-small text-muted mb-4">Bizományosi átvétel tételek</h6>

                                            @foreach($consignment_receipt->inventoryItems as $item)

                                                <hr>
                                                <div class="cri_item_wrapper @if($item->status != 2) orange_bg @else green_bg @endif">
                                                    <input type="hidden" name="cri_id" class="cri_id" value="{{ $item->inventory_id }}">
                                                    <div class="row">

                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label{{ $errors->has('cri_name') ? ' has-danger' : '' }}">
                                                                        Megnevezés</label>
                                                                    <select disabled required name="cri_name[]" class="select2 cri_name form-control{{ $errors->has('cri_name') ? ' is-invalid' : '' }}">
                                                                        <option value="">- válasszon -</option>
                                                                        @foreach($products as $product)
                                                                            <option @if($product->product_id == $item->product_id) selected @endif value="{{ $product->product_id }}" >{{ $product->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <label class="form-check-label">
                                                                        <input @if($item->product?->is_unique == 1) checked @endif class="form-check-input cri_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" disabled name="cri_is_unique[]">
                                                                        <span class="form-check-sign"></span>
                                                                        Egyedi?
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Leírás</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="text" class="form-control cri_description" name="cri_description[]" value="{{ $item->description }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Mennyiség</label>
                                                                    <input disabled type="number" class="form-control cri_quantity" name="cri_quantity[]" value="{{ $item->quantity }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Egységár</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="number" class="form-control cri_oneprice" name="cri_oneprice[]" value="{{ $item->oneprice }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Ár</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="number" class="form-control cri_price" name="cri_price[]" value="{{ $item->price }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Gyártási szám</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="text" class="form-control cri_factory_number" name="cri_factory_number[]" value="{{ $item->factory_number }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Súly</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="number" class="form-control cri_weight" name="cri_weight[]" value="{{ $item->weight }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <button data-inventory_id="{{ $item->inventory_id }}" data-toggle="modal" data-target="#cri_images_modal_{{ $item->inventory_id }}" type="button" class="btn btn-success cri_button consignment_receipt_item_images">Képek <i class="fa fa-image"></i></button>
                                                            <button data-inventory_id="{{ $item->inventory_id }}" class="btn btn-success cri_button captureButton"><i class="fa fa-camera"></i> Kép készítése</button>
                                                            <input type="file" accept="image/*" capture="camera" name="image" class="imageInput" style="display: none;">
                                                            @if(!$item->investigation && $consignment_receipt->printed_at)
                                                                <button id="examine_{{ $item->inventory_id }}" data-inventory_id="{{ $item->inventory_id }}" data-toggle="modal" data-target="#cri_product_modal_{{ $item->inventory_id }}" type="button" class="btn btn-success cri_button consignment_receipt_item_inv">Vizsgálat<i class="fa fa-search"></i></button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="modal fade" id="cri_images_modal_{{ $item->inventory_id }}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                      
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Bizományosi átvétel tétel képek</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <!-- Modal body -->
                                                            <div class="modal-body">
                                                                
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="dropzone_area" >
                                                                            <form action="/ajax/upload_cri_image" id="cri_image_form_{{ $item->inventory_id }}" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                                <div class="dz-message needsclick">
                                                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                                                    <input type="hidden" name="cri_id" value="{{ $item->inventory_id }}">
                                                                                    @csrf
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                

                                                                <div class="row">
                                                                    <div class="col-lg-12">

                                                                        <div class="gallery_container cri_gallery_container_{{ $item->inventory_id }}">
                                                                            
                                                                            <div class="delete_buttons">
                                                                                @if($item->consignment_receipt_images)
                                                                                @foreach($item->consignment_receipt_images as $img)
                                                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                                                        <button data-mod="" type="button" data-inventory_id="{{ $item->inventory_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                @endforeach
                                                                                @endif
                                                                            </div>
                                                                            
                                                                            <div data-lightGallery="1" id="lightgallery_cri_images_{{ $item->inventory_id }}" class="">
                                                                                @if($item->consignment_receipt_images)
                                                                                @foreach($item->consignment_receipt_images as $img)
                                                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="cri_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path; ?>">
                                                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive cri_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path; ?>" alt="">
                                                                                    </div>
                                                                                @endforeach
                                                                                @endif
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>
                                                            
                                                            <!-- Modal footer -->
                                                            <hr>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger fr" data-dismiss="modal">Bezárás</button>
                                                            </div>
                                                        
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                                <div class="modal fade" id="cri_product_modal_{{ $item->inventory_id }}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                      
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Termék adatlap</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <!-- Modal body -->
                                                            <div class="modal-body nomargin">
                                                                
                                                                <form id="product_form_{{ $item->inventory_id }}" method="post" action="">
                                                                    @include('products.edit_body', ['product' => $item->product, 'categories' => $categories, 'hallmarks' => $hallmarks, 'product_colors' => $product_colors, 'product_statuses' => $product_statuses, 'product_signs' => $product_signs])
                                                                </form>

                                                            </div>
                                                            
                                                            <!-- Modal footer -->
                                                            <hr>
                                                            <div class="modal-footer">
                                                                <button data-inventory_id="{{ $item->inventory_id }}" type="button" class="btn btn-success fr save_product">Mentés</button>
                                                                <button type="button" class="btn btn-danger fr" data-dismiss="modal">Bezárás</button>
                                                            </div>
                                                        
                                                        </div>
                                                    </div>
                                                </div>



                                            @endforeach

                                            @if(!$consignment_receipt->printed_at)
                                            <div class="consignment_receipt_items_rows">

                                                <hr>
                                                <div class="orange_bg cri_empty_row">
                                                    <input type="hidden" name="cri_id" class="cri_id">
                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Szállító</label>
                                                                <select class="selectpicker form-control cri_customer_marking_id" data-live-search="true">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($customer_markings as $customer_marking)
                                                                        <option
                                                                            value="{{ $customer_marking->id }}"
                                                                            data-price="{{ optional($customer_marking->productStatus)->price }}"
                                                                            data-vat-type-id="{{ optional($customer_marking->productStatus)->vat_type_id }}"
                                                                        >
                                                                            {{ $customer_marking->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kategória</label>
                                                                <select class="selectpicker form-control cri_category_id">
                                                                    @include('categories.category_options', ['categories' => $categories, 'category_id' => 1])
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Beszerzési ár</label>
                                                                <input type="number" class="form-control cri_beszerzesi_ar">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Eladási ár</label>
                                                                <input type="number" class="form-control cri_eladasi_ar">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Áfa típus</label>
                                                                <select class="selectpicker form-control cri_vat_type">
                                                                    @foreach($vat_types as $vat_type)
                                                                        <option value="{{ $vat_type->vat_type_id }}">{{ $vat_type->name.' - '.$vat_type->value }} %</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group" style="margin-left:20px;">
                                                                <br />
                                                                <input type="checkbox" class="form-check-input cri_hallmarks" value="1" id="cri_hallmarks_new">
                                                                <label class="form-control-label">Fémjelzés</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Állapot</label>
                                                                <select class="selectpicker form-control cri_statuses">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($product_statuses as $product_status)
                                                                        <option value="{{ $product_status->product_status_id }}">{{ $product_status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Karát</label>
                                                                <select class="selectpicker form-control cri_karat_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($karats ?? [] as $karat)
                                                                        <option value="{{ $karat->karat_id }}">{{ $karat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Szín</label>
                                                                <select multiple class="selectpicker form-control cri_colors">
                                                                    @foreach($product_colors ?? [] as $color)
                                                                        <option value="{{ $color->product_color_id }}">{{ $color->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kategória II</label>
                                                                <select class="selectpicker form-control cri_category2_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($category2s ?? [] as $category2)
                                                                        <option value="{{ $category2->category2_id }}">{{ $category2->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Méret</label>
                                                                <input type="text" class="form-control cri_size">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Termék típus</label>
                                                                <select class="selectpicker form-control cri_termek_tipus_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => ''])
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label{{ $errors->has('cri_name') ? ' has-danger' : '' }}">
                                                                        Megnevezés
                                                                    </label>
                                                                    <select @if($consignment_receipt->printed_at) disabled @endif required name="cri_name[]" class="select2 cri_name form-control{{ $errors->has('cri_name') ? ' is-invalid' : '' }}">
                                                                        <option value="">- válasszon -</option>
                                                                    </select>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <label class="form-check-label">
                                                                            <input @if($consignment_receipt->printed_at) disabled @endif checked class="form-check-input cri_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="cri_is_unique[]">
                                                                            <span class="form-check-sign"></span>
                                                                            Egyedi?
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Leírás</label>
                                                                    <input type="text" class="form-control cri_description" name="cri_description[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Mennyiség</label>
                                                                    <input type="number" class="form-control cri_quantity" name="cri_quantity[]" value="1">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Egységár</label>
                                                                    <input type="number" class="form-control cri_oneprice" name="cri_oneprice[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Ár</label>
                                                                    <input type="number" disabled class="form-control cri_price" name="cri_price[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Gyártási szám</label>
                                                                    <input type="text" class="form-control cri_factory_number" name="cri_factory_number[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Súly</label>
                                                                    <input type="number" class="form-control cri_weight" name="cri_weight[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif


                                            <hr>
                                        </div>
                                    </div>
                                
                                </div>

                                <button @if($consignment_receipt->printed_at) disabled @endif type="button" class="btn btn-success save_consignment_receipt">Mentés</button>
                                <button @if($consignment_receipt->printed_at) disabled @endif type="button" class="btn btn-success print_consignment_receipt">@if($consignment_receipt->printed_at) Nyomtatva: {{ date('Y. m. d. H:i', strtotime($consignment_receipt->printed_at)) }} @else Mentés és nyomtatás @endif</button>

                                @if($consignment_receipt->wait_to_transfer == '1' && $consignment_receipt->printed_at)
                                    @if(!$consignment_receipt->is_storno)
                                        <button @if($consignment_receipt->cancelled_at) disabled @endif type="button" class="btn btn-warning cancel_consignment_receipt">@if($consignment_receipt->cancelled_at) Sztornózva: {{ date('Y. m. d. H:i', strtotime($consignment_receipt->cancelled_at)) }} @else Sztornó @endif</button>
                                        <button type="button" class="btn btn-info copy_consignment_receipt">Másolás</button>
                                    @endif
                                @endif

                            </div>

                           
                </div>


            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>

        Dropzone.autoDiscover = false;

        function onlyNumbers(str) {
            return /^\d+$/.test(str);
        }


        function buildCRIGallery($initScope)
        {

            console.log($initScope);
            if (typeof $initScope != 'undefined' && $initScope.length)
            {
                console.log('build');
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
                                $(this).width($('.cri_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                                $(this).height($('.cri_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                                $(this).css('top', $('.cri_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                                $(this).css('left', $('.cri_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
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


        function initCRIGallery(inventory_id, mod="") {
            var $initScope_implementation = $('#lightgallery_cri_images'+mod+'_' + inventory_id);

            console.log('init #lightgallery_cri_images'+mod+'_' + inventory_id);

            var images = $initScope_implementation.find('img').get();
            var allImagesLoaded = images.every(function(img) {
                return img.complete;
            });

            if (allImagesLoaded) {
                console.log('allImagesLoaded');
                buildCRIGallery($initScope_implementation, mod);
            } else {
                $(images).on('load', function() {
                    var allLoaded = images.every(function(img) {
                        return img.complete;
                    });
                    if (allLoaded) {
                        buildCRIGallery($initScope_implementation, mod);
                    }
                });
            }
        }


        function checkImages(pit_id, mod){
            var totalImages = $('#lightgallery_cri_images'+mod+'_'+pit_id+' img').length;
            console.log('totalImages');
            console.log(totalImages);
            var loadedImages = 0;
            $('#lightgallery_cri_images'+mod+'_'+pit_id+' img').each(function(i, v) {
                loadedImages++;
                console.log((i+1));
                console.log(loadedImages);
                if ((i+1) == totalImages){
                    if (loadedImages == totalImages) {
                        console.log('LOADED');
                        $('#lightgallery_cri_images'+mod+'_'+pit_id).show();
                        $('#lightgallery_cri_images'+mod+'_'+pit_id).animate({ opacity: 1 }, 'slow');
                        initCRIGallery(pit_id, mod);
                    }
                }
            });
        }



        $(document).ready(function () {
            let valnow = $('input[name=currency_id]').val();

            function updateTaxIdentifierLabel() {
                const isCompany = $('#customer_is_company').is(':checked');
                $('#tax_identifier_label').text(isCompany ? 'Adószám' : 'Adóazonosító');
            }

            function getRowOrHeaderValue($row, rowSelector, headerSelector) {
                const rowValue = $row.find(rowSelector).val();
                if (rowValue !== undefined && rowValue !== null && rowValue !== '') {
                    return rowValue;
                }
                const headerValue = $(headerSelector).val();
                return headerValue !== undefined ? headerValue : '';
            }

            updateTaxIdentifierLabel();
            $('#customer_is_company').on('change', updateTaxIdentifierLabel);



            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );

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

            $(document).on('input', '.cri_quantity, .cri_oneprice', function() {
                let row = $(this).closest('.row');
                let quantity = row.find('.cri_quantity').val();
                let onePrice = row.find('.cri_oneprice').val();

                if(quantity && onePrice) {
                    let totalPrice = quantity * onePrice;
                    row.find('.cri_price').val(totalPrice);
                }
            });

            $(document).on('change', '#has_contract', function() {
                let isChecked = $(this).is(':checked');
                if (isChecked) {
                    $('#contract_number').prop('disabled', false);
                } else {
                    $('#contract_number').prop('disabled', true);
                }
            });


            $('.captureButton').on('click', function() {
                $(this).next('.imageInput').click();
            });

            $('select[name=business_id]').change(function(){
                let business_id = $(this).val();

                if(onlyNumbers(business_id)){
                    //Raktárak lekérése üzlet azonosító alapján a munkavállalóhoz
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_storages_by_business_id_employee_consignment_receipt",
                        data: { business_id: business_id }
                    })
                    .done(function( resp ) {
                        $('select[name=storage_id]').empty();
                        $.each(resp, function(index, warehouse) {
                            $('select[name=storage_id]').append($('<option>', {
                                value: warehouse.warehouse_id,
                                text: warehouse.name
                            }));
                        });    
                    });

                }else{
                    let textdef = '<option value="">- válasszon -</option>';
                    $('select[name=storage_id]').empty();
                    $('select[name=storage_id]').append(textdef);
                }
            });


            $('#expire_date').on('change', function() {
                let date = $(this).val();
                let consignment_receipt_id = '{{ $consignment_receipt->consignment_receipt_id }}';
                $.ajax({
                        method: "POST",
                        url: "/ajax/update_expire_date_cri",
                        data: { date: date,consignment_receipt_id : consignment_receipt_id }
                    })
                    .done(function( response ) {
                        if(response.success){
                            $.notify({ icon: "done", message: 'Sikeres lejárati dátum módosítás!' }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                        else{
                           
                        }
                    });
            });

            $('.imageInput').on('change', function() {
                let formData = new FormData();
                formData.append('cri_image', $(this)[0].files[0]);
                let inventory_id = $(this).prev('.captureButton').data('inventory_id');
                formData.append('inventory_id', inventory_id);

                $.ajax({
                    url: '/ajax/saveAndUploadCRIImage',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.status == 'success'){
                            $.notify({ icon: "done", message: response.message }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});

                            // A galéria újratöltése
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_cri_gallery",
                                data: { inventory_id: inventory_id, mod: '' }
                            })
                            .done(function(html) {
                                $('.cri_gallery_container_' + inventory_id).html(html);
                                checkImages(inventory_id, '');
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        } else {
                            $.notify({ icon: "done", message: response.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                    },
                    error: function(error) {
                        $.notify({ icon: "done", message: 'Hiba a kép feltöltése közben!' }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    }
                });
            });


            //Deviza árfolyam
            $(document).on("change", "select[name=currency_id]", function(){
                let currency_id = $(this).val();
                if(currency_id != "") {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_currency_by_id",
                        data: { currency_id: currency_id }
                    })
                    .done(function( response ) {
                        if(response.editable == 1){
                            $('input[name=currency_value]').removeAttr('disabled');
                        }
                        else{
                            $('input[name=currency_value]').attr('disabled', true);
                        }
                        $('input[name=currency_value]').val(response.value);
                    });
                } else {
                    $('input[name=currency_value]').val(1);
                }
            });





            $('select[name=customer_id]').select2({
                theme:"bootstrap4",
                tags: true
            });


            @foreach($consignment_receipt->inventoryItems as $item)
                $('#lightgallery_cri_images_{{ $item->inventory_id }}').hide();
                $('#lightgallery_cri_images_{{ $item->inventory_id }}').css('opacity', 0);
                $('#cri_images_modal_{{ $item->inventory_id }}').on('shown.bs.modal', function (e) {
                    $("#cri_image_form_{{ $item->inventory_id }}").dropzone({
                        maxFilesize: 50, // MB
                        parallelUploads: 1,
                        paramName: 'cri_image',
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
                                url: "/ajax/auto_save_cri_images",
                                data: { inventory_id: {{ $item->inventory_id }}, image: response }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_cri_gallery",
                                data: { inventory_id: {{ $item->inventory_id }}, mod: '' }
                            })
                            .done(function( html ) {
                                $('.cri_gallery_container_{{ $item->inventory_id }}').html(html);
                                checkImages({{ $item->inventory_id }}, '');
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        }
                    });

                    checkImages({{ $item->inventory_id }}, '');
                });


                $('#lightgallery_cri_images2_{{ $item->inventory_id }}').hide();
                $('#lightgallery_cri_images2_{{ $item->inventory_id }}').css('opacity', 0);
                $('#cri_inv_modal_{{ $item->inventory_id }}').on('shown.bs.modal', function (e) {

                    $("#cri_image_form2_{{ $item->inventory_id }}").dropzone({
                        maxFilesize: 50, // MB
                        parallelUploads: 1,
                        paramName: 'cri_image',
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
                                url: "/ajax/auto_save_cri_images",
                                data: { inventory_id: {{ $item->inventory_id }}, image: response, module: "sei2" }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_cri_gallery",
                                data: { inventory_id: {{ $item->inventory_id }}, mod: '2' }
                            })
                            .done(function( html ) {
                                $('.cri_gallery_container2_{{ $item->inventory_id }}').html(html);
                                checkImages({{ $item->inventory_id }}, 2);
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        }
                    });

                    checkImages({{ $item->inventory_id }}, 2);

                });
            @endforeach




            $("body").on("click", ".delete_img", function(e){
                var img_id = $(this).attr('data-img_id');
                var inventory_id = $(this).attr('data-inventory_id');
                var mod = $(this).attr('data-mod');

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
                        url: "/ajax/delete_cri_image",
                        data: { img_id: img_id }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_cri_gallery",
                            data: { inventory_id: inventory_id, mod: mod }
                        })
                        .done(function( html ) {
                            $('.cri_gallery_container'+mod+'_' + inventory_id).html(html);
                            checkImages(inventory_id, mod);
                        });
                    });

                });

            });



            $("body").on("mouseenter", ".cri_img_wrapper", function(e){
                $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
            });
            $("body").on("mouseleave", ".cri_img_wrapper", function(e){
                if( !$(e.toElement).hasClass('delete_img') ) {
                    $('.delete_img').stop(true).hide(200);
                }
            });


            //Írányítószám részek
            $('#customer_zip').select2({
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




            $('#customer_zip').on('select2:select', function(e) {
                let selectedZip = e.params.data.id;

                $.ajax({
                    url: '/ajax/get-cities/' + selectedZip,
                    type: 'GET',
                    success: function(data) {
                        let citySelector = $('#customer_city');
                        citySelector.empty();
                        $.each(data, function(key, value) {
                            citySelector.append(new Option(value.name, value.city_id));
                        });
                    }
                });
            });

            $('.cri_name').select2({
                theme: 'bootstrap4',
                tags: true,
                language: {
                    inputTooShort: function() {
                        return 'Kérjük, adjon meg 2 vagy több karaktert';
                    }
                },
                ajax: {
                    url: '/ajax/get_all_product',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(product) {
                                return {
                                    id: product.product_id,
                                    text: product.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Termékek'
            });


            function applyExistingCustomerData(resp) {
                var o = (typeof resp === 'string') ? JSON.parse(resp) : resp;

                $('select[name=customer_zip]').prop('disabled', true);
                $('select[name=customer_city]').prop('disabled', true);
                $('input[name=customer_address]').prop('disabled', true);
                $('input[name=customer_tax_identifier]').prop('disabled', true);

                const existingIsCompany = !!(o.customer && Number(o.customer.is_company) === 1);
                $('#customer_is_company').prop('checked', existingIsCompany);
                updateTaxIdentifierLabel();

                $('select[name=customer_zip]').empty();
                $('select[name=customer_city]').empty();

                if (o.address) {
                    $('select[name=customer_zip]').append('<option selected value="' + o.address.zip_id + '">' + o.address.zip + '</option>').trigger('change');
                    $('select[name=customer_city]').append('<option selected value="' + o.address.city_id + '">' + o.address.city + '</option>').trigger('change');
                    $('input[name=customer_address]').val(o.address.address || '');
                } else {
                    $('select[name=customer_zip]').trigger('change');
                    $('select[name=customer_city]').trigger('change');
                    $('input[name=customer_address]').val('');
                }

                const customerTaxIdentifier = existingIsCompany
                    ? (o.customer.tax_number || o.customer.tax_identity || '')
                    : (o.customer.tax_identity || o.customer.tax_number || o.customer.identity_card_number || '');
                $('input[name=customer_tax_identifier]').val(customerTaxIdentifier);
            }

            function resetNewCustomerFields() {
                $('select[name=customer_zip]').empty().trigger('change');
                $('select[name=customer_city]').empty().trigger('change');
                $('input[name=customer_address]').val('');
                $('input[name=customer_tax_identifier]').val('');
                $('#customer_is_company').prop('checked', false);
                updateTaxIdentifierLabel();

                $('select[name=customer_zip]').prop('disabled', false);
                $('select[name=customer_city]').prop('disabled', false);
                $('input[name=customer_address]').prop('disabled', false);
                $('input[name=customer_tax_identifier]').prop('disabled', false);
            }

            let customerRequestInFlight = null;
            let lastRequestedCustomerId = null;

            function handleCustomerSelection() {
                var customer_id = $('select[name=customer_id]').val();

                if(onlyNumbers(customer_id)){
                    if (customerRequestInFlight && String(lastRequestedCustomerId) === String(customer_id)) {
                        return;
                    }

                    if (customerRequestInFlight) {
                        customerRequestInFlight.abort();
                        customerRequestInFlight = null;
                    }

                    lastRequestedCustomerId = customer_id;
                    customerRequestInFlight = $.ajax({
                        method: "POST",
                        url: "/ajax/get_customer",
                        data: { customer_id: customer_id }
                    })
                    .done(function(resp) {
                        try {
                            applyExistingCustomerData(resp);
                        } catch (e) {
                            console.error('Hiba az ügyfél adatok feldolgozásakor', e, resp);
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            console.error('Hiba az ügyfél adatok lekérdezésekor', xhr);
                        }
                    })
                    .always(function() {
                        customerRequestInFlight = null;
                    });
                } else {
                    if (customerRequestInFlight) {
                        customerRequestInFlight.abort();
                        customerRequestInFlight = null;
                    }
                    lastRequestedCustomerId = null;
                    resetNewCustomerFields();
                    $('input[name=customer_zip]').focus();
                }
            }

            // Egyetlen eseményút: a select2 is kiváltja a change eseményt.
            $(document).on('change', 'select[name=customer_id]', handleCustomerSelection);

            function isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            }


            $(document).on('change', '.cri_name', function() {

                let cri_name = $(this).val();
                let sei = $(this);

                if (isNumeric(cri_name)) {
                    cri_name = parseInt(cri_name);
                    //ajax lekérdezés
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_one_product_by_id",
                        data: { product_id: cri_name }
                    }).done(function( resp ) {
                        if(resp.is_unique == 1){
                            sei.parents('.orange_bg').find('.cri_is_unique').prop('checked', true).prop('disabled', true);
                        } else {
                            sei.parents('.orange_bg').find('.cri_is_unique').prop('checked', false).prop('disabled', true);
                        }
                        sei.parents('.orange_bg').find('.cri_description').val(resp.description);
                    });
                }else{
                    sei.parents('.orange_bg').find('.cri_is_unique').prop('checked', true).prop('disabled', false);
                    sei.parents('.orange_bg').find('.cri_description').val('');
                }

                if($(this).val().length && $(this).parents('.orange_bg').hasClass('cri_empty_row')){
                    $(this).parents('.orange_bg').removeClass('cri_empty_row').addClass('cri_item_wrapper');

                    var $newRow = $(this).parents('.cri_item_wrapper').clone();
                    $newRow.removeClass('cri_item_wrapper').addClass('cri_empty_row');
                    $newRow.find('.cri_id, .inventory_id').val('');
                    $newRow.find('input[type=text], input[type=number]').not('.cri_quantity').val('');
                    $newRow.find('.cri_quantity').val(1);
                    $newRow.find('.cri_price').val('');
                    $newRow.find('.cri_is_unique').prop('checked', true).prop('disabled', false);
                    $newRow.find('.cri_hallmarks').prop('checked', false);
                    $newRow.find('select').each(function() {
                        $(this).val('');
                    });

                    $('.consignment_receipt_items_rows').append('<hr>');
                    $('.consignment_receipt_items_rows').append($newRow);
                    $('.cri_name').select2({
                        theme: 'bootstrap4',
                        tags: true,
                        language: {
                            inputTooShort: function() {
                                return 'Kérjük, adjon meg 2 vagy több karaktert';
                            }
                        },
                        ajax: {
                            url: '/ajax/get_all_product',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    term: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: $.map(data, function(product) {
                                        return {
                                            id: product.product_id,
                                            text: product.name
                                        };
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 2,
                        placeholder: 'Termékek'
                    });

                    if ($.fn.selectpicker) {
                        $('.consignment_receipt_items_rows .selectpicker').selectpicker('refresh');
                    }

                } else if(!$(this).val() && $('.cri_empty_row').length < 1) {
                    $(this).parents('.orange_bg').addClass('cri_empty_row');
                }


            });


            function saveConsignmentReceipt(callback, print = false) {
                if(!$("#consignment_receipt_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('#consignment_receipt_form')).click().remove();
                    return false;
                }

                var customer_id = $('select[name=customer_id]').val();
                var business_id = $('select[name=business_id]').val();
                let storage_id = $('select[name=storage_id]').val();
                let currency_id = $('select[name=currency_id]').val();
                let currency_value = $('input[name=currency_value]').val();
                let expire_date = $('input[name=expire_date]').val();
                let invoice_number = $('input[name=invoice_number]').val();
                let is_hallmark_requested = $('input[name=is_hallmark_requested]').is(':checked') ? 1 : 0;
                let customer_is_company = $('#customer_is_company').is(':checked') ? 1 : 0;
                let tax_identifier = $('input[name=customer_tax_identifier]').val();

                let customer_marking_id = $('select[name=customer_marking_id]').val();
                let category_id = $('select[name=category_id]').val();
                let beszerzesi_ar = $('input[name=beszerzesi_ar]').val();
                let eladasi_ar = $('input[name=eladasi_ar]').val();
                let vat_type = $('select[name=vat_type]').val();
                let hallmarks = $('input[name=hallmarks]').is(':checked') ? 1 : 0;
                let karat_id = $('select[name=karat_id]').val();
                let label_type_default = $('select[name=label_type_default]').val();
                let category2_id = $('select[name=category2_id]').val();
                let size = $('input[name=size]').val();
                let termek_tipus_id = $('select[name=termek_tipus_id]').val();

                var zip = "";
                var city = "";
                var address = "";

                if(!onlyNumbers(customer_id)){
                    zip = $('select[name=customer_zip]').val();
                    city = $('select[name=customer_city]').val();
                    address = $('input[name=customer_address]').val();
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_consignment_receipt",
                    data: { 
                        consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}', 
                        customer_id: customer_id, 
                        business_id: business_id, 
                        zip: zip, 
                        city: city, 
                        address: address, 
                        customer_is_company: customer_is_company,
                        tax_identifier: tax_identifier,
                        currency_id: currency_id,
                        currency_value: currency_value,
                        expire_date: expire_date,
                        is_hallmark_requested: is_hallmark_requested,
                        invoice_number: invoice_number,
                        storage_id: storage_id,
                        customer_marking_id: customer_marking_id,
                        category_id: category_id,
                        beszerzesi_ar: beszerzesi_ar,
                        eladasi_ar: eladasi_ar,
                        vat_type: vat_type,
                        hallmarks: hallmarks,
                        karat_id: karat_id,
                        label_type_default: label_type_default,
                        category2_id: category2_id,
                        size: size,
                        termek_tipus_id: termek_tipus_id
                    }
                })
                .done(function(resp) {
                    var consignment_receipt_id = '{{ $consignment_receipt->consignment_receipt_id }}';
                    let storage_id = $('select[name=storage_id]').val();
                    var ajaxRequests = [];
                    $('.cri_name').each(function() {
                        if($(this).val()){
                            var sei = $(this);
                            var cri_data = {
                                print: print,
                                consignment_receipt_id: consignment_receipt_id,
                                inventory_id: sei.parents('.cri_item_wrapper').find('.inventory_id').val(),
                                cri_name: sei.val(),
                                cri_is_unique: sei.parents('.cri_item_wrapper').find('.cri_is_unique').is(':checked'),
                                cri_description: sei.parents('.cri_item_wrapper').find('.cri_description').val(),
                                cri_quantity: sei.parents('.cri_item_wrapper').find('.cri_quantity').val(),
                                cri_oneprice: sei.parents('.cri_item_wrapper').find('.cri_oneprice').val(),
                                cri_price: sei.parents('.cri_item_wrapper').find('.cri_price').val(),
                                cri_factory_number: sei.parents('.cri_item_wrapper').find('.cri_factory_number').val(),
                                cri_weight: sei.parents('.cri_item_wrapper').find('.cri_weight').val(),
                                storage_id: storage_id,
                                new_customer_marking_id: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_customer_marking_id', 'select[name=customer_marking_id]'),
                                new_category_id: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_category_id', 'select[name=category_id]'),
                                new_beszerzesi_ar: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_beszerzesi_ar', 'input[name=beszerzesi_ar]'),
                                new_eladasi_ar: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_eladasi_ar', 'input[name=eladasi_ar]'),
                                new_vat_type: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_vat_type', 'select[name=vat_type]'),
                                new_hallmarks: sei.parents('.cri_item_wrapper').find('.cri_hallmarks').is(':checked') ? 1 : $('input[name=hallmarks]').is(':checked') ? 1 : 0,
                                new_karat_id: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_karat_id', 'select[name=karat_id]'),
                                new_size: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_size', 'input[name=size]'),
                                new_label_type: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_label_type', 'select[name=label_type_default]'),
                                new_category2_id: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_category2_id', 'select[name=category2_id]'),
                                new_termek_tipus_id: getRowOrHeaderValue(sei.parents('.cri_item_wrapper'), '.cri_termek_tipus_id', 'select[name=termek_tipus_id]')
                            };

                            var selectedStatuses = sei.parents('.cri_item_wrapper').find('.cri_statuses').val();
                            if (Array.isArray(selectedStatuses) && selectedStatuses.length) {
                                cri_data.new_statuses = selectedStatuses;
                            } else if (selectedStatuses) {
                                cri_data.new_statuses = [selectedStatuses];
                            }

                            var selectedColors = sei.parents('.cri_item_wrapper').find('.cri_colors').val();
                            if (Array.isArray(selectedColors)) {
                                cri_data.new_colors = selectedColors;
                            } else if (selectedColors) {
                                cri_data.new_colors = [selectedColors];
                            }

                            console.log(cri_data);

                            var request = $.ajax({
                                method: "POST",
                                url: "/ajax/save_consignment_receipt_item",
                                data: cri_data
                            }).done(function(respItem) {
                                sei.parents('.cri_item_wrapper').find('.inventory_id').val(respItem);
                            });

                            ajaxRequests.push(request);
                        }else{
                            console.log('Nincs termék kiválasztva, nem mentjük a sort.');
                        }
                    });

                    $.when.apply($, ajaxRequests).done(function() {
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

                        if(callback) callback();
                        location.href = "/consignment_receipt/{{ $consignment_receipt->consignment_receipt_id }}/edit";
                    });
                });
            }


            @if (session('print'))
                // Nyomtatás
                $.ajax({
                    method: "POST",
                    url: "/ajax/pdf/consignment_receipt",
                    data: { consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}' }
                })
                .done(function( msg ) {
                    var file_path = "/pdf/consignment_receipts/" + msg;
                    printJS(file_path);
                });
            @endif




            $('.save_consignment_receipt').click(function(){
                saveConsignmentReceipt();
            });


            $('.print_consignment_receipt').click(function(){
                if(!$("#consignment_receipt_form")[0].checkValidity()) {
                            $('<input type="submit">').hide().appendTo($('#consignment_receipt_form')).click().remove();
                            return false;
                        }
                swal({
                    text: "Biztosan nyomtatja az bizományosi átvétel bizonylatot?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(result) {
                    if (result) {
                        saveConsignmentReceipt(function() {
                            
                        }, true);
                    }
                });
            });



            $('.copy_consignment_receipt').click(function(){

                swal({
                    text: "Biztosan másolja ezt a bizományosi átvételt?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                },function() {
                    
                    $.ajax({
                        method: "POST",
                        url: "/ajax/copy_consignment_receipt",
                        data: { consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}' }
                    })
                    .done(function( msg ) {
                        window.location.href = "/consignment_receipt/" + msg + "/edit";
                    });

                });

            });


            $('.cancel_consignment_receipt').click(function(){

                swal({
                    text: "Biztosan sztornózza ezt a bizományosi átvételt?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-warning',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                },function() {
                    
                    setTimeout(function() { 
                        swal({
                            title: 'Sztornó oka:',
                            html: '<p><input type="text" class="form-control" name="reason"></p>',
                            showCancelButton: true,
                            closeOnConfirm: false,
                            allowOutsideClick: false,
                            confirmButtonText: 'Sztornó',
                            cancelButtonText: "Mégse",
                        },
                        function() {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/cancel_consignment_receipt",
                                data: { consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}', storno_reason: $('input[name="reason"]').val() }
                            })
                            .done(function(response) {
                                if(response.data.success == 'success'){
                                    window.location.href = "/consignment_receipt/" + response.data.consignment_receipt_id + "/edit";
                                }else{
                                    $.notify({ icon: "done", message: response.data.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                }
                            });
                        });
                    }, 500);
                });

            });


            $('.product_data').click(function(){
                //termék adatlap betöltése
                var cri_id = $(this).attr('data-inventory_id');
                $('#cri_inv_modal_' + cri_id).modal('hide');

                setTimeout(function() { 
                    $('#cri_product_modal_' + cri_id).modal('show');
                }, 500);
            });


            $('.save_product').click(function(){

                var inventory_id = $(this).attr('data-inventory_id');
                var storage_id = $('select[name=storage_id]').val();
                var formData = $("#product_form_"+inventory_id).serialize();
                

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_cri_product",
                    data: { inventory_id: inventory_id,storage_id:storage_id, form_data: formData }
                })
                .done(function( resp ) {
                    $('#examine_'+inventory_id).addClass('hidden_element');
                    $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                }).fail(function() {
                    $.notify({ icon: "done", message: "Hiba a mentés közben." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                });


            });


        });
    </script>
@endpush