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
                                                $selectedHeaderVatType = old('vat_type', $consignment_receipt->header_data['vat_type_id'] ?? '');
                                                $selectedHeaderConsignmentStatus = old('consignment_status', $consignment_receipt->header_data['consignment_status'] ?? 1);
                                            @endphp
                                            <!-- Fejléc mezők, amik nem szerkeszthetők tételszinten, egy helyen rejtett mezőkként -->
                                            <input type="hidden" name="filenumber" id="filenumber" value="{{ old('filenumber', $consignment_receipt->header_data['filenumber'] ?? '') }}">
                                            <input type="hidden" name="beszerzesi_ar" value="{{ old('beszerzesi_ar', $consignment_receipt->header_data['beszerzesi_ar'] ?? '') }}">
                                            <input type="hidden" name="eladasi_ar" value="{{ old('eladasi_ar', $consignment_receipt->header_data['eladasi_ar'] ?? '') }}">
                                            <input type="hidden" name="vat_type" value="{{ $selectedHeaderVatType }}">
                                            <input type="hidden" name="hallmarks" id="hallmarks" value="{{ (int) old('hallmarks', $consignment_receipt->header_data['hallmarks'] ?? 0) }}">
                                            <input type="hidden" name="consignment_status" id="consignment_status" value="{{ $selectedHeaderConsignmentStatus }}">

                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">
                                                                Ügyfél
                                                                <button @if($consignment_receipt->printed_at) disabled @endif type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#new_customer_modal" title="Új ügyfél">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </label>
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
                                                    @php
                                                        $selectedConsignmentStatus = $item->item_data['consignment_status'] ?? $selectedHeaderConsignmentStatus;
                                                        $selectedKaratId = ($item->item_data['karat_id'] ?? '') ?: ($item->item_data['karats'][0] ?? '');
                                                        $selectedCategory2Id = $item->item_data['category2_id']
                                                            ?? (is_array($item->item_data['product_categories_2'] ?? null)
                                                                ? ($item->item_data['product_categories_2'][0] ?? '')
                                                                : ($item->item_data['product_categories_2'] ?? ''));
                                                        $selectedTermekTipusId = $item->item_data['termek_tipus_id'] ?? $item->termek_tipus_id;
                                                        $itemSorszam = $item->item_data['sorszam'] ?? $item->sorszam ?? $loop->iteration;
                                                        $itemTicketDisplay = ($consignment_receipt->ticket_number && $consignment_receipt->ticket_number !== '--empty_row--')
                                                            ? $consignment_receipt->ticket_number . '/' . $itemSorszam
                                                            : $itemSorszam;
                                                    @endphp
                                                    <input type="hidden" name="cri_id" class="cri_id inventory_id" value="{{ $item->inventory_id }}">

                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <b style="font-size: 15px;">{{ $itemTicketDisplay }}</b>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kategória</label>
                                                                <select @if($consignment_receipt->printed_at) disabled @endif class="selectpicker form-control cri_category_id">
                                                                    @include('categories.category_options', ['categories' => $categories, 'category_id' => old('category_id', $item->item_data['category_id'] ?? 1)])
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kategória II</label>
                                                                <select @if($consignment_receipt->printed_at) disabled @endif class="selectpicker form-control cri_category2_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($category2s ?? [] as $category2)
                                                                        <option value="{{ $category2->category2_id }}" @if((string) $selectedCategory2Id === (string) $category2->category2_id) selected @endif>{{ $category2->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Termék típus</label>
                                                                <select @if($consignment_receipt->printed_at) disabled @endif class="selectpicker form-control cri_termek_tipus_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedTermekTipusId])
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Karát</label>
                                                                <select @if($consignment_receipt->printed_at) disabled @endif class="selectpicker form-control cri_karat_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @foreach($karats ?? [] as $karat)
                                                                        <option value="{{ $karat->karat_id }}" @if((string) $selectedKaratId === (string) $karat->karat_id) selected @endif>{{ $karat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Szín</label>
                                                                <select @if($consignment_receipt->printed_at) disabled @endif multiple class="selectpicker form-control cri_colors">
                                                                    @foreach($product_colors ?? [] as $color)
                                                                        <option value="{{ $color->product_color_id }}" @if(in_array($color->product_color_id, old('colors', $item->item_data['colors'] ?? []))) selected @endif>{{ $color->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_hallmarks', 'label' => 'Fémjelzés I/N', 'selectedValue' => $item->item_data['hallmarks'] ?? null, 'disabled' => (bool) $consignment_receipt->printed_at, 'yesInputId' => null])
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Méret</label>
                                                                <input @if($consignment_receipt->printed_at) disabled @endif type="text" class="form-control cri_size" value="{{ $item->item_data['size'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Súly</label>
                                                                <input @if($consignment_receipt->printed_at) disabled @endif type="number" class="form-control cri_weight" name="cri_weight[]" value="{{ $item->weight }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">Mennyiség</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="number" class="form-control cri_quantity" name="cri_quantity[]" value="{{ $item->quantity }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">Gyártási szám</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="text" class="form-control cri_factory_number" name="cri_factory_number[]" value="{{ $item->factory_number }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check" style="margin-top: 30px;">
                                                                        <label class="form-check-label">
                                                                            <input @if($consignment_receipt->printed_at) disabled @endif @if($item->product?->is_unique == 1) checked @endif class="form-check-input cri_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="cri_is_unique[]">
                                                                            <span class="form-check-sign"></span>
                                                                            Egyedi?
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_sell_elsewhere', 'label' => 'Máshol értékesíthető I/N', 'selectedValue' => $item->item_data['sell_elsewhere'] ?? null, 'disabled' => (bool) $consignment_receipt->printed_at, 'yesInputId' => null])
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-lg-2">
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_webshop', 'label' => 'Webshop-ba I/N', 'selectedValue' => $item->item_data['webshop'] ?? null, 'disabled' => (bool) $consignment_receipt->printed_at, 'yesInputId' => null])
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">Leírás</label>
                                                                    <input @if($consignment_receipt->printed_at) disabled @endif type="text" class="form-control cri_description" name="cri_description[]" value="{{ $item->description }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-6" style="margin-top: 25px;">
                                                            @if(!$consignment_receipt->printed_at)
                                                                <button type="button" class="btn btn-warning cri_button save_cri_item_changes" data-inventory_id="{{ $item->inventory_id }}">Tétel módosítás mentése <i class="fa fa-save"></i></button>
                                                                <button type="button" class="btn btn-danger cri_button delete_cri_item" data-inventory_id="{{ $item->inventory_id }}" title="Tétel törlése"><i class="fa fa-trash"></i> Tétel törlése</button>
                                                            @endif
                                                            
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


                                            @endforeach

                                            @if(!$consignment_receipt->printed_at)
                                            <div class="consignment_receipt_items_rows">

                                                <hr>
                                                <div class="orange_bg cri_empty_row">
                                                    <input type="hidden" name="cri_id" class="cri_id inventory_id">
                                                    <div class="row">
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
                                                                <label class="form-control-label">Termék típus</label>
                                                                <select class="selectpicker form-control cri_termek_tipus_id">
                                                                    <option value="">- válasszon -</option>
                                                                    @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => ''])
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
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_hallmarks', 'label' => 'Fémjelzés I/N', 'selectedValue' => null, 'disabled' => false, 'yesInputId' => 'cri_hallmarks_new'])
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Méret</label>
                                                                <input type="text" class="form-control cri_size">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Súly</label>
                                                                <input type="number" class="form-control cri_weight" name="cri_weight[]">
                                                            </div>
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
                                                                        Gyártási szám</label>
                                                                    <input type="text" class="form-control cri_factory_number" name="cri_factory_number[]">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check" style="margin-top: 30px;">
                                                                        <label class="form-check-label">
                                                                            <input @if($consignment_receipt->printed_at) disabled @endif checked class="form-check-input cri_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="cri_is_unique[]">
                                                                            <span class="form-check-sign"></span>
                                                                            Egyedi?
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_sell_elsewhere', 'label' => 'Máshol értékesíthető I/N', 'selectedValue' => null, 'disabled' => false, 'yesInputId' => null])
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-2">
                                                            @include('consignment_receipts.boolean_choice', ['field' => 'cri_webshop', 'label' => 'Webshop-ba I/N', 'selectedValue' => null, 'disabled' => false, 'yesInputId' => null])
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
                                                    </div>
                                                </div>
                                            </div>
                                            @endif


                                            <hr>
                                            <h6 class="heading-small text-muted mb-4">II. Az értékesítés feltételei</h6>

                                            <div class="card mb-4 p-3" style="background-color: #fafafa; border: 1px solid #e3e3e3;">
                                                <div id="hallmark-sales-condition" class="d-none" data-readonly="{{ $consignment_receipt->printed_at ? '1' : '0' }}">
                                                    <div class="form-group mb-3">
                                                        <p class="mb-2">Az arany nem rendelkezik fémjellel vagy nem magyarországi fémjellel rendelkezik.</p>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" type="checkbox" id="sales_condition_hallmark" name="sales_condition_hallmark" value="1" {{ old('sales_condition_hallmark', $consignment_receipt->header_data['sales_condition_hallmark'] ?? 0) ? 'checked' : '' }} @if($consignment_receipt->printed_at) disabled @endif>
                                                                <span class="form-check-sign"></span>
                                                                Megbízó kéri a fémjeleztetést
                                                            </label>
                                                        </div>
                                                        <p class="small mt-2">Amennyiben IGEN kéri a fémjeleztetést, úgy annak időtartama - 1 hónap - nem számít bele a szerződés időtartamába.</p>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="sales_condition_hallmark_cost" class="form-control-label">Fémjelzés költsége (Ft)</label>
                                                                <input type="number" min="0" step="1" class="form-control" id="sales_condition_hallmark_cost" name="sales_condition_hallmark_cost" value="{{ old('sales_condition_hallmark_cost', $consignment_receipt->header_data['sales_condition_hallmark_cost'] ?? '') }}" @if($consignment_receipt->printed_at) disabled @endif>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                                <!-- 1. Feltétel -->
                                                <div class="form-group mb-3">
                                                    <div class="form-check">
                                                        <label class="form-check-label font-weight-bold" style="color: #333; font-size: 14px;">
                                                            <input class="form-check-input" type="checkbox" id="sales_condition_1" name="sales_condition_1" value="1" {{ !empty($consignment_receipt->header_data['sales_condition_1']) ? 'checked' : '' }} @if($consignment_receipt->printed_at) disabled @endif>
                                                            <span class="form-check-sign"></span>
                                                            Felek által megállapított legalacsonyabb értékesítési összeg (továbbiakban minimum eladási ár)
                                                        </label>
                                                    </div>
                                                    <div class="row align-items-center mt-2 pl-4">
                                                        <div class="col-md-4">
                                                            <label class="form-control-label mb-1">Minimum eladási ár (Ft):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_1_min_price" name="sales_condition_1_min_price" value="{{ $consignment_receipt->header_data['sales_condition_1_min_price'] ?? '' }}" placeholder="pl. 100000" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-8 text-muted small pt-3">
                                                            A bizományos kötelezi magát, hogy értékesítés esetén a megállapított érték 80%-át megfizeti a megbízónak 2 munkanapon belül a vételi jegy alapján.
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- 2. Feltétel -->
                                                <div class="form-group mb-3">
                                                    <div class="form-check">
                                                        <label class="form-check-label font-weight-bold" style="color: #333; font-size: 14px;">
                                                            <input class="form-check-input" type="checkbox" id="sales_condition_2" name="sales_condition_2" value="1" {{ !empty($consignment_receipt->header_data['sales_condition_2']) ? 'checked' : '' }} @if($consignment_receipt->printed_at) disabled @endif>
                                                            <span class="form-check-sign"></span>
                                                            A bizományos által a későbbiekben meghatározott értéken történő értékesítés
                                                        </label>
                                                    </div>
                                                    <div class="row align-items-center mt-2 pl-4">
                                                        <div class="col-md-4">
                                                            <label class="form-control-label mb-1">Megbízót megillető összeg (Ft):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_2_amount" name="sales_condition_2_amount" value="{{ $consignment_receipt->header_data['sales_condition_2_amount'] ?? '' }}" placeholder="pl. 80000" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-8 text-muted small pt-3">
                                                            Értékesítés esetén a megbízót a megadott összeg illeti meg.
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- 3. Feltétel -->
                                                <div class="form-group mb-3">
                                                    <div class="form-check">
                                                        <label class="form-check-label font-weight-bold" style="color: #333; font-size: 14px;">
                                                            <input class="form-check-input" type="checkbox" id="sales_condition_3" name="sales_condition_3" value="1" {{ !empty($consignment_receipt->header_data['sales_condition_3']) ? 'checked' : '' }} @if($consignment_receipt->printed_at) disabled @endif>
                                                            <span class="form-check-sign"></span>
                                                            Felek által megállapított minimum eladási ár és átadott biztosíték összeg
                                                        </label>
                                                    </div>
                                                    <div class="row align-items-center mt-2 pl-4">
                                                        <div class="col-md-4">
                                                            <label class="form-control-label mb-1">Minimum eladási ár (Ft):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_3_min_price" name="sales_condition_3_min_price" value="{{ $consignment_receipt->header_data['sales_condition_3_min_price'] ?? '' }}" placeholder="pl. 100000" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-control-label mb-1">Átadott rész (%):</label>
                                                            <input type="number" step="0.1" class="form-control" id="sales_condition_3_percent" name="sales_condition_3_percent" value="{{ $consignment_receipt->header_data['sales_condition_3_percent'] ?? '' }}" placeholder="pl. 50" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-control-label mb-1">Átadott összeg (Ft):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_3_amount" name="sales_condition_3_amount" value="{{ $consignment_receipt->header_data['sales_condition_3_amount'] ?? '' }}" placeholder="pl. 50000" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1 pl-4">
                                                        <div class="col-12 text-muted small">
                                                            Bizományos a sikeres értékesítés biztosítékaként jelen szerződés aláírásával megbízónak átadja a megadott összeget.
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- 4. Feltétel -->
                                                <div class="form-group mb-2">
                                                    <div class="form-check">
                                                        <label class="form-check-label font-weight-bold" style="color: #333; font-size: 14px;">
                                                            <input class="form-check-input" type="checkbox" id="sales_condition_4" name="sales_condition_4" value="1" {{ !empty($consignment_receipt->header_data['sales_condition_4']) ? 'checked' : '' }} @if($consignment_receipt->printed_at) disabled @endif>
                                                            <span class="form-check-sign"></span>
                                                            A szerződés határozott időtartamra jött létre / futamidő végi megvásárlás
                                                        </label>
                                                    </div>
                                                    <div class="row align-items-center mt-2 pl-4">
                                                        <div class="col-md-3">
                                                            <label class="form-control-label mb-1">Időtartam (hónap):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_4_months" name="sales_condition_4_months" value="{{ $consignment_receipt->header_data['sales_condition_4_months'] ?? '' }}" placeholder="pl. 1" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-control-label mb-1">Vételi érték a futamidő végén (Ft):</label>
                                                            <input type="number" step="1" class="form-control" id="sales_condition_4_buyout_price" name="sales_condition_4_buyout_price" value="{{ $consignment_receipt->header_data['sales_condition_4_buyout_price'] ?? '' }}" placeholder="pl. 60000" @if($consignment_receipt->printed_at) disabled @endif>
                                                        </div>
                                                        <div class="col-md-5 text-muted small pt-3">
                                                            A megbízó hozzájárul ahhoz, hogy a bizományos a futamidő végén a megadott értéken megvásárolja az ingóságot.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                
                                </div>

                                <button @if($consignment_receipt->printed_at) disabled @endif type="button" class="btn btn-success save_consignment_receipt">Mentés</button>
                                <button @if($consignment_receipt->printed_at) disabled @endif type="button" class="btn btn-success print_consignment_receipt">@if($consignment_receipt->printed_at) Nyomtatva: {{ date('Y. m. d. H:i', strtotime($consignment_receipt->printed_at)) }} @else Mentés és nyomtatás @endif</button>

                                @if($consignment_receipt->printed_at && !$consignment_receipt->is_storno && !$consignment_receipt->cancelled_at && !$consignment_receipt->storno_cr_id)
                                    <button type="button" class="btn btn-warning cancel_consignment_receipt" data-create-new="0"><i class="fa fa-ban"></i> Sztornó</button>
                                    <button type="button" class="btn btn-info cancel_consignment_receipt" data-create-new="1"><i class="fa fa-copy"></i> Sztornó és új</button>
                                    @if($consignment_receipt->wait_to_transfer == '1')
                                        <button type="button" class="btn btn-info copy_consignment_receipt">Másolás</button>
                                    @endif
                                @endif
                                @if($consignment_receipt->is_storno || $consignment_receipt->storno_cr_id)
                                    <a href="{{ route('consignment_receipt.storno.print', $consignment_receipt->is_storno ? $consignment_receipt->consignment_receipt_id : $consignment_receipt->storno_cr_id) }}" target="_blank" rel="noopener" class="btn btn-info"><i class="fa fa-print"></i> Sztornó nyomtatása</a>
                                @endif

                            </div>
            
                           
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade" id="storno_consignment_receipt_modal" tabindex="-1" role="dialog" aria-labelledby="storno_consignment_receipt_title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="storno_consignment_receipt_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="storno_consignment_receipt_title">Sztornó</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="storno_create_new" value="0">
                        <div class="form-group">
                            <label for="consignment_storno_reason">Sztornó oka</label>
                            <textarea id="consignment_storno_reason" class="form-control" rows="4" maxlength="2000" required></textarea>
                        </div>
                        <div id="consignment_storno_error" class="alert alert-danger d-none" role="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                        <button type="submit" class="btn btn-warning" id="confirm_consignment_storno">Sztornó</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new_customer_modal" tabindex="-1" role="dialog" aria-labelledby="new_customer_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new_customer_modal_label">Új ügyfél</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new_customer_form" method="post" action="{{ route('customers.modal.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="mr-4">
                                <input type="radio" name="is_company" value="0" checked>
                                Magánszemély
                            </label>
                            <label>
                                <input type="radio" name="is_company" value="1">
                                Cég
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="new_customer_name">Név</label>
                            <input type="text" class="form-control" id="new_customer_name" name="name" required>
                        </div>
                        <div class="new-customer-private-fields">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="new_customer_birth_name">Születési név</label>
                                        <input type="text" class="form-control" id="new_customer_birth_name" name="birth_name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="new_customer_mothers_name">Anyja neve</label>
                                        <input type="text" class="form-control" id="new_customer_mothers_name" name="mothers_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="new_customer_birth_place">Születési hely</label>
                                        <input type="text" class="form-control" id="new_customer_birth_place" name="birth_place">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="new_customer_birth_date">Születési idő</label>
                                        <input type="date" class="form-control" id="new_customer_birth_date" name="birth_date">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="new_customer_tax_identity">Adóazonosító jel</label>
                                <input type="text" class="form-control" id="new_customer_tax_identity" name="tax_identity" maxlength="10" inputmode="numeric" pattern="^\d{10}$" title="Formátum: 10 számjegy" placeholder="1234567890">
                            </div>
                        </div>
                        <div class="new-customer-company-fields d-none">
                            <div class="form-group">
                                <label for="new_customer_tax_number">Adószám</label>
                                <input type="text" class="form-control" id="new_customer_tax_number" name="tax_number" maxlength="12" inputmode="numeric" pattern="^\d{7}-\d-\d{2}$" title="Formátum: xxxxxxx-y-zz" placeholder="1234567-8-90">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="new_customer_email">E-mail</label>
                                    <input type="email" class="form-control" id="new_customer_email" name="email" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="new_customer_phone">Telefonszám</label>
                                    <input type="text" class="form-control" id="new_customer_phone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="new_customer_zip">Irányítószám</label>
                                    <select class="form-control" id="new_customer_zip" name="zip" required></select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="new_customer_city">Település</label>
                                    <select class="form-control" id="new_customer_city" name="city" required></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_customer_address">Cím</label>
                            <input type="text" class="form-control" id="new_customer_address" name="address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                    <button type="submit" form="new_customer_form" class="btn btn-primary">Mentés</button>
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

            $('#new_customer_zip').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#new_customer_modal'),
                ajax: {
                    url: '/ajax/get-zips',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { term: params.term };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(zip) {
                                return { id: zip.id, text: zip.name };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Irányítószám'
            });

            $('#new_customer_city').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#new_customer_modal'),
                placeholder: 'Település'
            });

            function updateNewCustomerTypeFields() {
                const isCompany = $('#new_customer_form input[name="is_company"]:checked').val() === '1';
                $('.new-customer-private-fields').toggleClass('d-none', isCompany);
                $('.new-customer-company-fields').toggleClass('d-none', !isCompany);
                $('.new-customer-private-fields input').prop('required', !isCompany);
                $('.new-customer-company-fields input').prop('required', isCompany);
            }

            $('#new_customer_form input[name="is_company"]').on('change', updateNewCustomerTypeFields);
            updateNewCustomerTypeFields();

            function formatNewCustomerTaxIdentity(rawValue) {
                return (rawValue || '').replace(/\D/g, '').substring(0, 10);
            }

            function formatNewCustomerTaxNumber(rawValue) {
                const digits = (rawValue || '').replace(/\D/g, '').substring(0, 10);

                if (digits.length <= 7) {
                    return digits;
                }
                if (digits.length <= 8) {
                    return digits.substring(0, 7) + '-' + digits.substring(7);
                }

                return digits.substring(0, 7) + '-' + digits.substring(7, 8) + '-' + digits.substring(8, 10);
            }

            $('#new_customer_tax_identity').on('input', function() {
                $(this).val(formatNewCustomerTaxIdentity($(this).val()));
            });

            $('#new_customer_tax_number').on('input', function() {
                $(this).val(formatNewCustomerTaxNumber($(this).val()));
            });

            $('#new_customer_zip').on('select2:select', function(e) {
                $.ajax({
                    url: '/ajax/get-cities/' + e.params.data.id,
                    type: 'GET'
                }).done(function(data) {
                    const $city = $('#new_customer_city').empty();
                    $.each(data, function(_, city) {
                        $city.append(new Option(city.name, city.city_id));
                    });
                    $city.trigger('change');
                });
            });

            $('#new_customer_form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize()
                }).done(function(response) {
                    const newCustomer = response.data;
                    const newOption = new Option(newCustomer.name, newCustomer.customer_id, true, true);
                    $('select[name=customer_id]').append(newOption).trigger('change');
                    $('#new_customer_modal').modal('hide');
                    $form[0].reset();
                    $('#new_customer_zip, #new_customer_city').empty().trigger('change');
                    updateNewCustomerTypeFields();
                    $.notify({ icon: 'done', message: response.message }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                }).fail(function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(fieldName, messages) {
                            const $field = $form.find('[name="' + fieldName + '"]');
                            $field.addClass('is-invalid');
                            $field.closest('.form-group').append('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                        });
                        return;
                    }

                    $.notify({ icon: 'error', message: 'Az ügyfél mentése sikertelen.' }, { type: 'danger', timer: 4000, placement: { from: 'top', align: 'right' } });
                });
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

            function rowHasCriData($row) {
                const value = function(selector) {
                    return ($row.find(selector).val() || '').toString().trim();
                };
                const numberValue = function(selector) {
                    const raw = value(selector);
                    if (raw === '') {
                        return 0;
                    }
                    const parsed = parseFloat(raw);
                    return isNaN(parsed) ? 0 : parsed;
                };

                return !!(
                    value('.cri_termek_tipus_id') ||
                    value('.cri_description') ||
                    numberValue('.cri_weight') > 0 ||
                    value('.cri_factory_number') ||
                    (value('.cri_category_id') && value('.cri_category_id') !== '1') ||
                    value('.cri_karat_id') ||
                    value('.cri_size') ||
                    value('.cri_category2_id')
                );
            }

            function updateHallmarkSalesCondition() {
                const $items = $('.cri_item_wrapper');
                let hasMissingHallmark = false;
                $items.each(function() {
                    if ($(this).find('.cri_hallmarks:checked').val() === '0') {
                        hasMissingHallmark = true;
                    }
                });
                if (!$items.length) {
                    hasMissingHallmark = $('.cri_empty_row').first().find('.cri_hallmarks:checked').val() === '0';
                }
                const $condition = $('#hallmark-sales-condition');
                $condition.toggleClass('d-none', !hasMissingHallmark);
                $condition.find('input').prop('disabled', !hasMissingHallmark || $condition.attr('data-readonly') === '1');
            }

            function criChoicesComplete($row, showErrors = false) {
                let complete = true;
                ['cri_hallmarks', 'cri_webshop', 'cri_sell_elsewhere'].forEach(function(field) {
                    const answered = $row.find('.' + field + ':checked').length === 1;
                    if (!answered) complete = false;
                    if (showErrors) {
                        const $group = $row.find('[data-cri-choice="' + field + '"]');
                        $group.find('input').toggleClass('is-invalid', !answered);
                        $group.find('.invalid-feedback').toggleClass('d-block', !answered);
                    }
                });
                return complete;
            }

            $(document).on('change', '.cri-binary-choice', function() {
                const $row = $(this).closest('.cri_item_wrapper, .cri_empty_row');
                if (this.checked) {
                    $row.find('.' + $(this).attr('data-choice-field')).not(this).prop('checked', false);
                }
                criChoicesComplete($row, true);
                updateHallmarkSalesCondition();
            });

            updateHallmarkSalesCondition();
            $(document).on('change', '.cri_hallmarks', updateHallmarkSalesCondition);

            function appendNewEmptyCriRowFrom($sourceRow) {
                if (!$sourceRow.hasClass('cri_empty_row') || !rowHasCriData($sourceRow) || !criChoicesComplete($sourceRow)) {
                    return;
                }

                const hasHallmark = $sourceRow.find('.cri_hallmarks:checked').val() === '1';
                $sourceRow.removeClass('cri_empty_row').addClass('cri_item_wrapper');

                var $newRow = $sourceRow.clone();
                $newRow.removeClass('cri_item_wrapper').addClass('cri_empty_row');

                // A klónozott bootstrap-select wrapper eltávolítása, hogy ne duplázódjon a vezérlő.
                $newRow.find('select.selectpicker').each(function() {
                    var $select = $(this);
                    if ($select.parent().hasClass('bootstrap-select')) {
                        var $wrapper = $select.parent();
                        $wrapper.after($select);
                        $wrapper.remove();
                    }
                    $select.removeClass('bs-select-hidden').css('display', '');
                });

                $newRow.find('.cri_id, .inventory_id').val('');
                $newRow.find('input[type=text], input[type=number]').not('.cri_quantity').val('');
                $newRow.find('.cri_quantity').val(1);
                $newRow.find('.cri_price').val('');
                $newRow.find('.cri_is_unique').prop('checked', true).prop('disabled', false);
                $newRow.find('.cri_hallmarks').prop('checked', false);
                $newRow.find('.cri_sell_elsewhere, .cri_webshop').prop('checked', false);
                $newRow.find('select').each(function() {
                    $(this).val('');
                });
                $newRow.find('.cri_consignment_status').val($('#consignment_status').val());
                $newRow.find('.cri-binary-choice').removeAttr('id').removeClass('is-invalid');
                $newRow.find('.invalid-feedback').removeClass('d-block');

                $('.consignment_receipt_items_rows').append('<hr>');
                $('.consignment_receipt_items_rows').append($newRow);

                if ($.fn.selectpicker) {
                    $newRow.find('select.selectpicker').selectpicker();
                    $newRow.find('select.selectpicker').selectpicker('refresh');
                }
                updateHallmarkSalesCondition();
                if (!hasHallmark) {
                    $.notify({ icon: 'warning', message: 'Az utolsó kitöltött Bizományosi átvétel tételnél nincs jelölve a fémjelzés.' }, { type: 'warning', timer: 5000, placement: { from: 'top', align: 'right' } });
                }
            }

            $(document).on('input change', '.consignment_receipt_items_rows .cri_empty_row input, .consignment_receipt_items_rows .cri_empty_row select', function() {
                appendNewEmptyCriRowFrom($(this).closest('.orange_bg'));
            });

            $('#consignment_status').on('change', function() {
                const $newItemStatus = $('.consignment_receipt_items_rows .cri_empty_row .cri_consignment_status');
                $newItemStatus.val($(this).val());
                if ($.fn.selectpicker) {
                    $newItemStatus.selectpicker('refresh');
                }
            });

            function buildCriDataFromRow($row, print) {
                const storageId = $('select[name=storage_id]').val();
                const inventoryId = $row.find('.inventory_id').val();
                const description = $row.find('.cri_description').val();
                const quantity = $row.find('.cri_quantity').val();
                const onePrice = $row.find('.cri_oneprice').val();
                const weight = $row.find('.cri_weight').val();
                const factoryNumber = $row.find('.cri_factory_number').val();

                const criData = {
                    print: print ? 1 : 0,
                    consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}',
                    inventory_id: inventoryId,
                    cri_is_unique: $row.find('.cri_is_unique').is(':checked'),
                    cri_description: description,
                    cri_quantity: quantity,
                    cri_oneprice: onePrice,
                    cri_price: $row.find('.cri_price').val(),
                    cri_factory_number: factoryNumber,
                    cri_weight: weight,
                    new_sell_elsewhere: $row.find('.cri_sell_elsewhere:checked').val(),
                    new_webshop: $row.find('.cri_webshop:checked').val(),
                    storage_id: storageId,
                    new_category_id: $row.find('.cri_category_id').val(),
                    new_beszerzesi_ar: getRowOrHeaderValue($row, '.cri_beszerzesi_ar', 'input[name=beszerzesi_ar]'),
                    new_eladasi_ar: getRowOrHeaderValue($row, '.cri_eladasi_ar', 'input[name=eladasi_ar]'),
                    new_vat_type: getRowOrHeaderValue($row, '.cri_vat_type', 'input[name=vat_type]'),
                    new_hallmarks: $row.find('.cri_hallmarks:checked').val(),
                    new_karat_id: $row.find('.cri_karat_id').val(),
                    new_size: $row.find('.cri_size').val(),
                    new_label_type: $row.find('.cri_label_type').val(),
                    new_category2_id: $row.find('.cri_category2_id').val(),
                    new_termek_tipus_id: $row.find('.cri_termek_tipus_id').val()
                };

                criData.new_consignment_status = $row.find('.cri_consignment_status').val() || $('#consignment_status').val();

                const selectedColors = $row.find('.cri_colors').val();
                if (Array.isArray(selectedColors)) {
                    criData.new_colors = selectedColors;
                } else if (selectedColors) {
                    criData.new_colors = [selectedColors];
                }

                return criData;
            }

            $(document).on('click', '.save_cri_item_changes', function() {
                const inventoryId = $(this).attr('data-inventory_id');
                const $row = $('.cri_item_wrapper .inventory_id[value="' + inventoryId + '"]').closest('.cri_item_wrapper');

                if (!$row.length) {
                    $.notify({ icon: "error", message: 'A tételsor nem található.' }, { type: 'danger', timer: 3000, placement: { from: 'top', align: 'right' } });
                    return;
                }

                if (!criChoicesComplete($row, true)) {
                    $.notify({ message: 'A fémjelzés, a webshop és a máshol értékesíthető mezőknél válasszon Igent vagy Nemet.' }, { type: 'warning' });
                    return;
                }
                const criData = buildCriDataFromRow($row, false);
                if (!criData.inventory_id) {
                    $.notify({ icon: "error", message: 'Csak meglévő tétel menthető ezzel a gombbal.' }, { type: 'warning', timer: 3000, placement: { from: 'top', align: 'right' } });
                    return;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_consignment_receipt_item",
                    data: criData
                }).done(function(respItem) {
                    var savedInventoryId = (respItem && respItem.inventory_id) ? respItem.inventory_id : respItem;
                    $row.find('.inventory_id').val(savedInventoryId);
                    $.notify({ icon: "done", message: 'A tétel módosítása sikeresen mentve.' }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                }).fail(function() {
                    $.notify({ icon: "error", message: 'A tétel mentése sikertelen.' }, { type: 'danger', timer: 4000, placement: { from: 'top', align: 'right' } });
                });
            });


            function saveConsignmentReceipt(callback, print = false) {
                let choicesComplete = true;
                $('.cri_item_wrapper, .cri_empty_row').each(function() {
                    const $row = $(this);
                    if ($row.find('.inventory_id').val() || rowHasCriData($row)) {
                        if (!criChoicesComplete($row, true)) choicesComplete = false;
                    }
                });
                if (!choicesComplete) {
                    $.notify({ message: 'Minden kitöltött tételnél válasszon Igent vagy Nemet a három I/N mezőnél.' }, { type: 'warning' });
                    return false;
                }
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
                let is_hallmark_requested = !$('#hallmark-sales-condition').hasClass('d-none') && $('#sales_condition_hallmark').is(':checked') ? 1 : 0;
                let customer_is_company = $('#customer_is_company').is(':checked') ? 1 : 0;
                let tax_identifier = $('input[name=customer_tax_identifier]').val();

                let filenumber = $('#filenumber').val();
                let beszerzesi_ar = $('input[name=beszerzesi_ar]').val();
                let eladasi_ar = $('input[name=eladasi_ar]').val();
                let vat_type = $('input[name=vat_type]').val();
                let hallmarks = parseInt($('input[name=hallmarks]').val()) === 1 ? 1 : 0;
                let consignment_status = $('#consignment_status').val();

                let sales_condition_1 = $('#sales_condition_1').is(':checked') ? 1 : 0;
                let sales_condition_1_min_price = $('#sales_condition_1_min_price').val();
                let sales_condition_2 = $('#sales_condition_2').is(':checked') ? 1 : 0;
                let sales_condition_2_amount = $('#sales_condition_2_amount').val();
                let sales_condition_3 = $('#sales_condition_3').is(':checked') ? 1 : 0;
                let sales_condition_3_min_price = $('#sales_condition_3_min_price').val();
                let sales_condition_3_percent = $('#sales_condition_3_percent').val();
                let sales_condition_3_amount = $('#sales_condition_3_amount').val();
                let sales_condition_4 = $('#sales_condition_4').is(':checked') ? 1 : 0;
                let sales_condition_4_months = $('#sales_condition_4_months').val();
                let sales_condition_4_buyout_price = $('#sales_condition_4_buyout_price').val();

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
                        filenumber: filenumber,
                        beszerzesi_ar: beszerzesi_ar,
                        eladasi_ar: eladasi_ar,
                        vat_type: vat_type,
                        hallmarks: hallmarks,
                        consignment_status: consignment_status,
                        sales_condition_1: sales_condition_1,
                        sales_condition_1_min_price: sales_condition_1_min_price,
                        sales_condition_2: sales_condition_2,
                        sales_condition_2_amount: sales_condition_2_amount,
                        sales_condition_3: sales_condition_3,
                        sales_condition_3_min_price: sales_condition_3_min_price,
                        sales_condition_3_percent: sales_condition_3_percent,
                        sales_condition_3_amount: sales_condition_3_amount,
                        sales_condition_4: sales_condition_4,
                        sales_condition_4_months: sales_condition_4_months,
                        sales_condition_4_buyout_price: sales_condition_4_buyout_price,
                        sales_condition_hallmark: !$('#hallmark-sales-condition').hasClass('d-none') && $('#sales_condition_hallmark').is(':checked') ? 1 : 0,
                        sales_condition_hallmark_cost: !$('#hallmark-sales-condition').hasClass('d-none') ? $('#sales_condition_hallmark_cost').val() : null
                    }
                })
                .done(function(resp) {
                    if (resp && resp.success === false) {
                        $.notify({
                            icon: "error",
                            message: resp.message || "A mentés sikertelen."
                        }, {
                            type: 'danger',
                            timer: 4000,
                            placement: { from: 'top', align: 'right' }
                        });
                        return;
                    }

                    var consignment_receipt_id = '{{ $consignment_receipt->consignment_receipt_id }}';
                    let storage_id = $('select[name=storage_id]').val();
                    var ajaxRequests = [];
                    $('.consignment_receipt_items_rows .cri_item_wrapper, .consignment_receipt_items_rows .cri_empty_row').each(function() {
                            var $row = $(this);
                            var inventoryId = $row.find('.inventory_id').val();
                            var termekTipusId = $row.find('.cri_termek_tipus_id').val();
                            var description = $row.find('.cri_description').val();
                            var quantity = $row.find('.cri_quantity').val();
                            var onePrice = $row.find('.cri_oneprice').val();
                            var weight = $row.find('.cri_weight').val();
                            var factoryNumber = $row.find('.cri_factory_number').val();

                            var hasData = rowHasCriData($row);

                            if(inventoryId || hasData){
                            var cri_data = buildCriDataFromRow($row, print);

                            console.log(cri_data);

                            var request = $.ajax({
                                method: "POST",
                                url: "/ajax/save_consignment_receipt_item",
                                data: cri_data
                            }).done(function(respItem) {
                                var savedInventoryId = (respItem && respItem.inventory_id) ? respItem.inventory_id : respItem;
                                $row.find('.inventory_id').val(savedInventoryId);
                                $row.removeClass('cri_empty_row').addClass('cri_item_wrapper');
                            });

                            ajaxRequests.push(request);
                        }else{
                            console.log('Üres sor, nem mentjük.');
                        }
                    });

                    if (ajaxRequests.length === 0) {
                        $.notify({
                            icon: "done",
                            message: "A fejléc adatok sikeresen mentve."
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: { from: 'top', align: 'right' }
                        });
                        if (print) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/pdf/consignment_receipt",
                                data: { consignment_receipt_id: consignment_receipt_id }
                            }).done(function(fileName) {
                                printJS({
                                    printable: "/pdf/consignment_receipts/" + fileName,
                                    type: 'pdf',
                                    onPrintDialogClose: function() {
                                        location.href = "/consignment_receipt/" + consignment_receipt_id + "/edit";
                                    }
                                });
                            }).fail(function() {
                                $.notify({ icon: "error", message: "A nyomtatás sikertelen." }, { type: 'danger', timer: 4000, placement: { from: 'top', align: 'right' } });
                            });
                            return;
                        }
                        if(callback) callback();
                        location.href = "/consignment_receipt/{{ $consignment_receipt->consignment_receipt_id }}/edit";
                        return;
                    }

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
                })
                .fail(function(xhr) {
                    var message = 'A fejléc mentése sikertelen.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $.notify({
                        icon: "error",
                        message: message
                    }, {
                        type: 'danger',
                        timer: 5000,
                        placement: { from: 'top', align: 'right' }
                    });
                });
            }


            @if ((string) session('print') === (string) $consignment_receipt->consignment_receipt_id)
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


            $(document).on('click', '.delete_cri_item', function() {
                const inventoryId = $(this).attr('data-inventory_id');
                swal({
                    text: 'Biztosan törli ezt a tételt?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Igen, törlöm',
                    cancelButtonText: 'Mégse'
                }, function(confirmed) {
                    if (!confirmed) return;
                    $.ajax({
                        method: 'POST',
                        url: '/ajax/delete_consignment_receipt_item',
                        data: { consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}', inventory_id: inventoryId }
                    }).done(function() {
                        window.location.reload();
                    }).fail(function(xhr) {
                        $.notify({ message: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'A tétel törlése sikertelen.' }, { type: 'danger' });
                    });
                });
            });

            let stornoRequestInFlight = false;
            $('.cancel_consignment_receipt').on('click', function() {
                const createNew = $(this).attr('data-create-new') === '1';
                $('#storno_create_new').val(createNew ? '1' : '0');
                $('#consignment_storno_reason').val('');
                $('#consignment_storno_error').addClass('d-none').text('');
                $('#storno_consignment_receipt_title, #confirm_consignment_storno').text(createNew ? 'Sztornó és új' : 'Sztornó');
                $('#storno_consignment_receipt_modal').modal('show');
            });

            $('#storno_consignment_receipt_form').on('submit', function(event) {
                event.preventDefault();
                const reason = $('#consignment_storno_reason').val().trim();
                if (stornoRequestInFlight) return;
                if (!reason) {
                    $('#consignment_storno_error').removeClass('d-none').text('Adja meg a sztornó okát.');
                    return;
                }
                stornoRequestInFlight = true;
                $('#confirm_consignment_storno').prop('disabled', true);
                $('#consignment_storno_error').addClass('d-none');
                const printWindow = window.open('', '_blank');
                if (printWindow) printWindow.opener = null;

                $.ajax({
                    method: 'POST',
                    url: '/ajax/cancel_consignment_receipt',
                    data: {
                        consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}',
                        storno_reason: reason,
                        create_new: $('#storno_create_new').val()
                    }
                }).done(function(response) {
                    if (printWindow) printWindow.location.href = response.data.print_url;
                    window.location.href = response.data.redirect_url;
                }).fail(function(xhr) {
                    if (printWindow) printWindow.close();
                    $('#consignment_storno_error').removeClass('d-none').text(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'A sztornózás sikertelen.');
                }).always(function() {
                    stornoRequestInFlight = false;
                    $('#confirm_consignment_storno').prop('disabled', false);
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