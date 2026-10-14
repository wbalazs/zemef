@extends('layouts.app', ['activePage' => 'item_list', 'activeButton' => 'item_list', 'title' => 'Zemef ERP', 'navName' => 'Kényszerlista tétel átemelése'])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="card-title mb-0">Kényszerlista tétel átemelése</h4>
                        <p class="card-category">Kényszerlista #{{ $itemList->id }} &mdash; Sor: #{{ $sor }} | Zálogjegy: <strong>{{ $tetel['zalogjegy_szama'] ?? '–' }}</strong></p>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('item_list.subindex', $itemList->id) }}" class="btn btn-sm btn-warning">
                            <i class="fa fa-arrow-left"></i> Vissza a kényszerlistához
                        </a>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 13px;">
                            <div class="row">
                                <div class="col-md-3"><strong>Ügyfél:</strong> {{ $tetel['ugyfel']['nev'] ?? '–' }}</div>
                                <div class="col-md-3"><strong>Tárgyleírás:</strong> {{ $tetel['targyleiras'] ?? '–' }}</div>
                                <div class="col-md-2"><strong>Karát:</strong> {{ $tetel['karat'] ?? ($tetel['karat_finomsag'] ?? '–') }}</div>
                                <div class="col-md-2"><strong>Súly:</strong> {{ $tetel['netto_suly'] ?? ($tetel['brutto_suly'] ?? '–') }} g</div>
                                <div class="col-md-2"><strong>Átvételi ár:</strong> {{ number_format((float)($tetel['atveteli_ar'] ?? 0), 0, '.', ' ') }} Ft</div>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mt-3" id="custom-tabs-two-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Átemelési adatok</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <form id="stock_entry_form" method="post" action="{{ route('stock_entry.update', $stock_entry->stock_entry_id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('patch')

                                        <input type="hidden" name="transfer_item_list_id" value="{{ $itemList->id }}">
                                        <input type="hidden" name="transfer_item_list_sor" value="{{ $sor }}">
                                        <input type="hidden" name="ticket_number" value="{{ $stock_entry->ticket_number }}">
                                        <input type="hidden" name="currency_id" value="{{ $stock_entry->currency_id ?? $currencies->first()?->currency_id ?? 1 }}">
                                        <input type="hidden" name="currency_value" value="{{ $stock_entry->currency_value ?? 1 }}">
                                        <input type="hidden" name="has_contract" value="1">
                                        <input type="hidden" name="fulfillment_date" id="fulfillment_date" value="{{ $stock_entry->fulfillment_date }}">
                                        <input type="hidden" name="status" value="{{ $stock_entry->status }}">

                                        <h6 class="heading-small text-muted mb-4">Ékszer vásárlás fej adatai</h6>
                                        <hr>

                                        {{-- 1. Sor: Szállító, Kategória, Beszerzési ár, Eladási ár, Áfa típus --}}
                                        @php
                                            $selectedHeaderCustomerMarkingId = old('customer_marking_id', $stock_entry->header_data['customer_marking_id'] ?? '');
                                        @endphp
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Szállító</label>
                                                    <select name="customer_marking_id" id="vzj" class="selectpicker form-control" data-live-search="true">
                                                        <option value="">- válasszon -</option>
                                                        @foreach($business_markings as $business_marking)
                                                            <option
                                                                value="{{ $business_marking->id }}"
                                                                data-price="{{ optional($business_marking->productStatus)->price }}"
                                                                data-vat-type-id="{{ optional($business_marking->productStatus)->vat_type_id }}"
                                                                data-product-status-id="{{ $business_marking->product_status_id }}"
                                                                @if((string) $selectedHeaderCustomerMarkingId === (string) $business_marking->id) selected @endif
                                                            >
                                                                {{ $business_marking->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Kategória</label>
                                                    <select name="category_id" id="category_id" class="selectpicker form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}">
                                                        @include('categories.category_options', ['categories' => $categories, 'category_id' => old('category_id', $stock_entry->header_data['category_id'] ?? 1)])
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-control-label">Beszerzési ár</label>
                                                    <input type="text" class="form-control" name="beszerzesi_ar" id="beszerzesi_ar" value="{{ old('beszerzesi_ar', $stock_entry->header_data['beszerzesi_ar'] ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-control-label">Eladási ár</label>
                                                    <input type="text" class="form-control" name="eladasi_ar" id="eladasi_ar" value="{{ old('eladasi_ar', $stock_entry->header_data['eladasi_ar'] ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-control-label">Áfa típus</label>
                                                    <select name="vat_type" id="vat_type" class="selectpicker form-control{{ $errors->has('vat_type') ? ' is-invalid' : '' }}">
                                                        @foreach($vat_types as $vat_type)
                                                            <option value="{{ $vat_type->vat_type_id }}" {{ old('vat_type', $stock_entry->header_data['vat_type_id'] ?? '') == $vat_type->vat_type_id ? 'selected' : '' }}>
                                                                {{ $vat_type->name.' - '.$vat_type->value }} %
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Sor: Ügyfél adatok --}}
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">Ügyfél</label>
                                                    <select @if($stock_entry->status == 2) disabled @endif required name="customer_id" id="customer_id" class="select2 form-control{{ $errors->has('customer_id') ? ' is-invalid' : '' }}">
                                                        <option value="">- válasszon -</option>
                                                        @foreach($customers as $customer)
                                                            <option @if($customer->customer_id == $stock_entry->customer_id) selected @endif value="{{ $customer->customer_id }}">
                                                                {{ $customer->name }} 
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <?php
                                                        $zip = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()?->zip : '';
                                                        $zip_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()?->zip_id : '0';
                                                    ?>
                                                    <label class="form-control-label{{ $errors->has('customer_zip') ? ' has-danger' : '' }}">Irányítószám</label>
                                                    <select class="form-control{{ $errors->has('customer_zip') ? ' is-invalid' : '' }} select2" name="customer_zip" id="customer_zip" required disabled>
                                                        @if(isset($zip) && isset($zip_id))
                                                            <option value="{{ $zip_id }}">{{ $zip }}</option>
                                                        @endif
                                                    </select>
                                                    @include('alerts.feedback', ['field' => 'customer_zip'])
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <?php
                                                        $city = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()?->city : '';
                                                        $city_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()?->city_id : '0';
                                                    ?>
                                                    <label class="form-control-label{{ $errors->has('customer_city') ? ' has-danger' : '' }}">Város</label>
                                                    <select class="form-control{{ $errors->has('customer_city') ? ' is-invalid' : '' }} select2" name="customer_city" id="customer_city" required disabled>
                                                        @if(isset($city) && isset($city_id))
                                                            <option value="{{ $city_id }}">{{ $city }}</option>
                                                        @endif
                                                    </select>
                                                    @include('alerts.feedback', ['field' => 'customer_city'])
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <?php
                                                        $address = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()?->address : '';
                                                    ?>
                                                    <label class="form-control-label{{ $errors->has('customer_address') ? ' has-danger' : '' }}">Cím</label>
                                                    <input type="text" class="form-control{{ $errors->has('customer_address') ? ' is-invalid' : '' }}" name="customer_address" value="{{ old('customer_address', $address) }}" disabled>
                                                    @include('alerts.feedback', ['field' => 'customer_address'])
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3. Sor: Bizonylat száma, Zálogjegy sorszáma, Üzlet, Raktár --}}
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('contract_number') ? ' has-danger' : '' }}">Bizonylat száma</label>
                                                    <input type="text" @if($stock_entry->status == 2) disabled @endif class="form-control{{ $errors->has('contract_number') ? ' is-invalid' : '' }}" id="contract_number" name="contract_number" value="{{ old('contract_number', $stock_entry->contract_number ?? ($itemList->json['header']['bizonylat_szama_1'] ?? '')) }}">
                                                    @include('alerts.feedback', ['field' => 'contract_number'])
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('invoice_number') ? ' has-danger' : '' }}">Zálogjegy sorszáma</label>
                                                    <input type="text" @if($stock_entry->status == 2) disabled @endif class="form-control{{ $errors->has('invoice_number') ? ' is-invalid' : '' }}" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $stock_entry->invoice_number ?? ($tetel['zalogjegy_szama'] ?? '')) }}" required>
                                                    @include('alerts.feedback', ['field' => 'invoice_number'])
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">Üzlet</label>
                                                        <select id="business_id" name="business_id" class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($businesses as $business)
                                                                <option @if($business->business_id == $stock_entry->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-3">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('storage_id') ? ' has-danger' : '' }}"> Raktár</label>
                                                        <select title="- válasszon -" required name="storage_id" id="storage_id" class="select2 form-control{{ $errors->has('storage_id') ? ' is-invalid' : '' }}">
                                                            <option value="">- válasszon -</option>
                                                            @if(isset($warehouse))
                                                                <option value="{{ $warehouse->warehouse_id }}" selected>{{ $warehouse->name }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        {{-- 4. Sor: Teljesítés dátum, Státusz, Címke típus, Fémjelzés --}}
                                        @php
                                            $selectedHeaderLabelType = old('label_type_default', $stock_entry->header_data['label_type'] ?? '');
                                        @endphp
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('fulfillment_date') ? ' has-danger' : '' }}">Teljesítés dátum</label>
                                                    <input type="date" class="form-control" id="fulfillment_date_display" value="{{ $stock_entry->fulfillment_date }}" disabled readonly style="background-color: #e9ecef;">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Státusz</label>
                                                    <select disabled class="form-control" style="background-color: #e9ecef;">
                                                        <option value="0" @selected((int)$stock_entry->status === 0)>Nyitott</option>
                                                        <option value="1" @selected((int)$stock_entry->status === 1)>Folyamatban</option>
                                                        <option value="2" @selected((int)$stock_entry->status === 2)>Lezárt</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Címke típus</label>
                                                    <select id="header_label_type" name="label_type_default" class="form-control">
                                                        <option value="">-- Címke típus --</option>
                                                        <option value="1" {{ (string) $selectedHeaderLabelType === '1' ? 'selected' : '' }}>1-es típus</option>
                                                        <option value="2" {{ (string) $selectedHeaderLabelType === '2' ? 'selected' : '' }}>2-es típus</option>
                                                        <option value="3" {{ (string) $selectedHeaderLabelType === '3' ? 'selected' : '' }}>3-as típus</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group" style="margin-top: 30px;">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" class="form-check-input" name="hallmarks" value="1" 
                                                                id="hallmark_{{ $stock_entry->header_data['hallmarks'] ?? 0 }}" {{ ($stock_entry->header_data['hallmarks'] ?? 0) == 1 ? 'checked' : '' }} >
                                                            <span class="form-check-sign"></span>
                                                            Fémjelzés
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-success save_stock_entry">Mentés</button>
                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-danger close_stock_entry">Mentés és lezárás</button>
                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-info print_stock_entry d-none">
                                            @if($stock_entry->status == 2) Lezárva: {{ date('Y. m. d. H:i', strtotime($stock_entry->printed_at)) }} @else Nyomtatás @endif
                                        </button>
                                    </form> {{-- stock_entry_form vége --}}

                                    {{-- Tételek kezelése --}}
                                    @if($stock_entry->status != 2)
                                        <h6 class="heading-small text-muted mb-4 mt-4">Átemelt tételek</h6>

                                        @php
                                            $businessMarkingsForJs = $business_markings->map(function ($marking) {
                                                return [
                                                    'id' => $marking->id,
                                                    'name' => $marking->name,
                                                    'default' => (int) $marking->default,
                                                    'status' => (int) $marking->status,
                                                    'price' => optional($marking->productStatus)->price,
                                                    'vat_type_id' => optional($marking->productStatus)->vat_type_id,
                                                    'product_status_id' => $marking->product_status_id,
                                                ];
                                            })->values();
                                        @endphp

                                        <script>
                                            window.currentBusinessMarkings = @json($businessMarkingsForJs);
                                        </script>

                                        @foreach($groupedInventoryItems as $productId => $items)
                                            @php $firstItem = $items->first(); @endphp

                                            <div class="mb-3">
                                                <div class="card-body p-3 item-summary" data-target="item-detail-{{ $firstItem->inventory_id }}">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <strong>Kód:</strong> {{ $firstItem->code ?? '–' }}
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Megnevezés:</strong> {{ $firstItem->description ?? '' }}
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Súly:</strong> {{ number_format($items->sum('weight'), 2) }} g
                                                            <i class="fa fa-chevron-down float-right"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div id="item-detail-{{ $firstItem->inventory_id }}" class="item-detail">
                                                    <div class="sei_item_wrapper green_bg" data-inventory-id="{{ $firstItem->inventory_id }}">
                                                        <form class="sei_item_form">
                                                            @csrf
                                                            <b>{{ $items->first()->code ?? '' }}</b>
                                                            
                                                            {{-- Felső sor --}}
                                                            <div class="row">
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Szállító</label>
                                                                        @php
                                                                            $selectedInlineCustomerMarkingId = $items->first()->item_data['customer_marking_id']
                                                                                ?? ($stock_entry->header_data['customer_marking_id'] ?? '');
                                                                        @endphp
                                                                        <select disabled name="customer_marking_id" class="selectpicker form-control customer-marking-select-inline" data-live-search="true" data-selected-id="{{ $selectedInlineCustomerMarkingId }}">
                                                                            <option value="">- válasszon -</option>
                                                                            @foreach($business_markings as $business_marking)
                                                                                <option
                                                                                    value="{{ $business_marking->id }}"
                                                                                    data-price="{{ optional($business_marking->productStatus)->price }}"
                                                                                    data-vat-type-id="{{ optional($business_marking->productStatus)->vat_type_id }}"
                                                                                    data-product-status-id="{{ $business_marking->product_status_id }}"
                                                                                    @if((string) $selectedInlineCustomerMarkingId === (string) $business_marking->id) selected @endif
                                                                                >
                                                                                    {{ $business_marking->name }} 
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Kategória</label>
                                                                        <select disabled name="category_id" class="selectpicker form-control">
                                                                            @include('categories.category_options', ['categories' => $categories, 'category_id' => old('category_id', $items->first()->item_data['category_id'] ?? 1)])
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Beszerzési ár</label>
                                                                        <input disabled type="number" class="form-control" name="beszerzesi_ar" value="{{ ($items->first()->item_data['beszerzesi_ar'] ?? '') ?: ($stock_entry->header_data['beszerzesi_ar'] ?? '') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Eladási ár</label>
                                                                        <input disabled type="number" class="form-control" name="eladasi_ar" value="{{ ($items->first()->item_data['eladasi_ar'] ?? '') ?: ($stock_entry->header_data['eladasi_ar'] ?? '') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Áfa típus</label>
                                                                        <select disabled name="vat_type_id" class="selectpicker form-control">
                                                                            @foreach($vat_types as $vat_type)
                                                                                <option value="{{ $vat_type->vat_type_id }}" {{ old('vat_type_id', $items->first()->item_data['vat_type_id'] ?? '') == $vat_type->vat_type_id ? 'selected' : '' }}>
                                                                                    {{ $vat_type->name.' - '.$vat_type->value }} %
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group" style="margin-left:20px;">
                                                                        <br />
                                                                        <input type="checkbox" class="form-check-input" name="hallmarks" value="1" 
                                                                            id="hallmark" {{ old('hallmarks', $items->first()->item_data['hallmarks'] ?? 0) == 1 ? 'checked' : '' }}>
                                                                        <label class="form-control-label">Fémjelzés</label>
                                                                    </div>
                                                                </div>                                                  
                                                            </div>

                                                            {{-- Alsó sor --}}
                                                            <div class="row mt-2">
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Állapot</label>
                                                                        @php
                                                                            $rawSelectedStatus = old(
                                                                                'statuses.0',
                                                                                old(
                                                                                    'statuses',
                                                                                    $items->first()->item_data['statuses'][0]
                                                                                        ?? ($items->first()->item_data['statuses'] ?? ($stock_entry->header_data['product_status_id'] ?? ''))
                                                                                )
                                                                            );
                                                                            $selectedStatusId = is_array($rawSelectedStatus) ? ($rawSelectedStatus[0] ?? '') : $rawSelectedStatus;
                                                                        @endphp
                                                                        <select disabled name="statuses[]" class="selectpicker product_statuses_select form-control">
                                                                            @foreach($product_statuses as $product_status)
                                                                                <option value="{{ $product_status->product_status_id }}" {{ (string)$selectedStatusId === (string)$product_status->product_status_id ? 'selected' : '' }}>
                                                                                    {{ $product_status->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Karát</label>
                                                                        @php
                                                                            $selectedKaratId = ($items->first()->item_data['karat_id'] ?? '') ?: ($items->first()->item_data['karats'][0] ?? '') ?: ($stock_entry->header_data['karat_id'] ?? '');
                                                                        @endphp
                                                                        <select disabled name="karat_id" class="selectpicker form-control">
                                                                            <option value="">- válasszon -</option>
                                                                            @foreach($karats ?? [] as $karat)
                                                                                <option value="{{ $karat->karat_id }}" {{ (string)$selectedKaratId === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Szín</label>
                                                                        <select disabled multiple name="colors[]" class="selectpicker product_colors_select form-control">
                                                                            @foreach($product_colors ?? [] as $color)
                                                                                <option value="{{ $color->product_color_id }}" {{ in_array($color->product_color_id, old('colors', $items->first()->item_data['colors'] ?? [])) ? 'selected' : '' }}>
                                                                                    {{ $color->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Kategória II</label>
                                                                        @php
                                                                            $selectedCategory2Id = $items->first()->item_data['category2_id']
                                                                                ?? (is_array($items->first()->item_data['product_categories_2'] ?? null)
                                                                                    ? ($items->first()->item_data['product_categories_2'][0] ?? '')
                                                                                    : (($items->first()->item_data['product_categories_2'] ?? '') ?: ($stock_entry->header_data['category2_id'] ?? '')));
                                                                        @endphp
                                                                        <select disabled name="category2_id" class="selectpicker form-control">
                                                                            <option value="">- válasszon -</option>
                                                                            @foreach($category2s ?? [] as $category2)
                                                                                <option value="{{ $category2->category2_id }}" {{ (string)$selectedCategory2Id === (string)$category2->category2_id ? 'selected' : '' }}>{{ $category2->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Méret</label>
                                                                        <input disabled type="text" class="form-control" name="size" value="{{ ($items->first()->item_data['size'] ?? '') ?: ($stock_entry->header_data['size'] ?? '') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Egyedi azonosító</label>
                                                                        <input disabled type="text" class="form-control" name="factory_number" value="{{ $items->first()->factory_number }}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Alap tételek --}}
                                                            <div class="row mt-2">
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Termék típus</label>
                                                                        <select disabled title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="termek_tipus_id" id="termek_tipus_id" class="selectpicker form-control{{ $errors->has('termek_tipus_id') ? ' is-invalid' : '' }}" placeholder="">
                                                                            @php
                                                                                $selectedTermekTipusId = $items->first()->item_data['termek_tipus_id']
                                                                                    ?? $items->first()->termek_tipus_id;
                                                                            @endphp
                                                                            <option value="">- válasszon -</option>
                                                                            @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedTermekTipusId])
                                                                    </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Leírás</label>
                                                                        <input disabled type="text" class="form-control" name="description" value="{{ $items->first()->description }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Mennyiség</label>
                                                                        <input disabled type="number" class="form-control" name="quantity" value="{{ $items->sum('quantity') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Beszerzési gm ár</label>
                                                                        <input disabled type="number" class="form-control" name="oneprice" value="{{ $items->first()->oneprice }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Ár</label>
                                                                        <input disabled type="number" class="form-control" name="price" value="{{ $items->sum('price') }}">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-lg-2">
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Súly</label>
                                                                        <input disabled type="number" class="form-control" name="weight" value="{{ $items->sum('weight') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-12">
                                                                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
                                                                        {{-- Kép készítése --}}
                                                                        <button type="button" class="btn btn-success btn-sm capture-gallery-btn me-1" 
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}" 
                                                                                data-product-id="{{ $firstItem->product_id }}">
                                                                            <i class="fa fa-camera"></i> Kép készítése
                                                                        </button>

                                                                        {{-- Szerkesztés --}}
                                                                        <button type="button" class="btn btn-warning btn-sm edit_items_button me-1" 
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}">
                                                                            Tétel szerkesztése
                                                                        </button>

                                                                        {{-- Mentés (rejtett) --}}
                                                                        <button type="button" class="btn btn-success btn-sm save_items_button d-none me-1" 
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}">
                                                                            Mentés
                                                                        </button>

                                                                        <div class="form-group">
                                                                            @php
                                                                                $selectedItemLabelType = $items->first()->item_data['label_type'] ?? $selectedHeaderLabelType;
                                                                            @endphp
                                                                            <div class="d-flex align-items-center" style="gap:6px;">
                                                                                <select id="label_type_{{ $firstItem->inventory_id }}" name="label_type" class="form-control label-type" data-inventory-id="{{ $firstItem->inventory_id }}">
                                                                                    <option value="">-- Címke típus --</option>
                                                                                    <option value="1" {{ (string) $selectedItemLabelType === '1' ? 'selected' : '' }}>1-es típus</option>
                                                                                    <option value="2" {{ (string) $selectedItemLabelType === '2' ? 'selected' : '' }}>2-es típus</option>
                                                                                    <option value="3" {{ (string) $selectedItemLabelType === '3' ? 'selected' : '' }}>3-as típus</option>
                                                                                </select>
                                                                                <button type="button" class="btn btn-sm btn-info item-label-print-btn" data-inventory-id="{{ $firstItem->inventory_id }}" title="Címke nyomtatás">
                                                                                    <i class="fa fa-print"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Törlés --}}
                                                                        <button type="button" class="btn btn-danger btn-sm delete_items_button me-1"
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}">
                                                                            Tétel Törlése
                                                                        </button>
                                                                        
                                                                        {{-- File input --}}
                                                                        <input type="file" class="gallery-file-input d-none" 
                                                                            data-inventory-id="{{ $firstItem->inventory_id }}" 
                                                                            data-product-id="{{ $firstItem->product_id }}" 
                                                                            accept="image/*" multiple>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div id="inventory_gallery_{{ $firstItem->inventory_id }}" class="row"></div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Új tétel hozzáadása (Kényszerlista átemelés) --}}
                                        @if($stock_entry->status != 2)
                                        <div class="stock_entry_items_rows">
                                            <input type="hidden" name="sei_id" id="sei_id"/>
                                            <input type="hidden" name="parent_inventory_id" id="parent_inventory_id"/>
                                            
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <h6 class="heading-small text-muted mb-2">Új tétel rögzítése az átemeléshez</h6>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" id="new_item_btn" class="btn btn-success btn-block new_item_btn">Új tétel</button>
                                                </div>
                                            </div>
                                            <div class="orange_bg sei_empty_row">
                                                        
                                                {{-- Felső sor új tételhez --}}
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Szállító</label>
                                                            <select name="new_customer_marking_id" id="new_vzj" class="selectpicker form-control" data-live-search="true">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($business_markings as $business_marking)
                                                                    <option
                                                                        value="{{ $business_marking->id }}"
                                                                        data-price="{{ optional($business_marking->productStatus)->price }}"
                                                                        data-vat-type-id="{{ optional($business_marking->productStatus)->vat_type_id }}"
                                                                        data-product-status-id="{{ $business_marking->product_status_id }}"
                                                                        @if((string) ($stock_entry->header_data['customer_marking_id'] ?? '') === (string) $business_marking->id) selected @endif
                                                                    >
                                                                        {{ $business_marking->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Kategória</label>
                                                            <select name="new_category_id" class="selectpicker form-control">
                                                                @include('categories.category_options', ['categories' => $categories, 'category_id' => old('new_category_id', $stock_entry->header_data['category_id'] ?? 1)])
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Beszerzési ár</label>
                                                            <input type="number" class="form-control sei_beszerzesi_ar" name="new_beszerzesi_ar" value="{{ old('new_beszerzesi_ar', $stock_entry->header_data['beszerzesi_ar'] ?? ($prefill['oneprice'] ?? '')) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Eladási ár</label>
                                                            <input type="number" class="form-control" name="new_eladasi_ar" value="{{ old('new_eladasi_ar', $stock_entry->header_data['eladasi_ar'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Áfa típus</label>
                                                            <select name="new_vat_type" class="selectpicker form-control">
                                                                @foreach($vat_types as $vat_type)
                                                                    <option value="{{ $vat_type->vat_type_id }}" {{ old('new_vat_type', $stock_entry->header_data['vat_type_id'] ?? '') == $vat_type->vat_type_id ? 'selected' : '' }}>
                                                                        {{ $vat_type->name.' - '.$vat_type->value }} %
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Alsó sor új tételhez --}}
                                                <div class="row mt-2">
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Állapot</label>
                                                            @php
                                                                $selectedNewStatusId = old('new_statuses.0', old('new_statuses', $stock_entry->header_data['product_status_id'] ?? ''));
                                                                $selectedNewStatusId = is_array($selectedNewStatusId) ? ($selectedNewStatusId[0] ?? '') : $selectedNewStatusId;
                                                            @endphp
                                                            <select name="new_statuses[]" class="selectpicker product_statuses_select form-control">
                                                                @foreach($product_statuses as $product_status)
                                                                    <option value="{{ $product_status->product_status_id }}" {{ (string)$selectedNewStatusId === (string)$product_status->product_status_id ? 'selected' : '' }}>{{ $product_status->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Karát</label>
                                                            @php
                                                                $selectedNewKaratId = old('new_karat_id', $stock_entry->header_data['karat_id'] ?? '');
                                                            @endphp
                                                            <select name="new_karat_id" class="selectpicker form-control">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($karats ?? [] as $karat)
                                                                    <option value="{{ $karat->karat_id }}" {{ (string)$selectedNewKaratId === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Szín</label>
                                                            <select multiple name="new_colors[]" class="selectpicker product_colors_select form-control">
                                                                @foreach($product_colors ?? [] as $color)
                                                                    <option value="{{ $color->product_color_id }}" {{ in_array($color->product_color_id, old('new_colors', $prefill['colors'] ?? [])) ? 'selected' : '' }}>{{ $color->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Kategória II</label>
                                                            <select name="new_category2_id" id="new_category2_id" class="selectpicker form-control">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($category2s ?? [] as $category2)
                                                                    <option value="{{ $category2->category2_id }}" {{ (string) old('new_category2_id', $prefill['category2_id'] ?? '') === (string) $category2->category2_id ? 'selected' : '' }}>{{ $category2->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Méret</label>
                                                            <input type="text" class="form-control" name="new_size" id="new_size" value="{{ old('new_size', $prefill['size'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Súly</label>
                                                            <input type="number" class="form-control sei_weight" name="new_weight" value="{{ old('new_weight', $prefill['weight'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Alap tételek új tételhez --}}
                                                <div class="row mt-2">
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Termék típus</label>
                                                            <select @if($show) disabled @endif title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="new_termek_tipus_id" id="new_termek_tipus_id" class="selectpicker form-control{{ $errors->has('termek_tipus_id') ? ' is-invalid' : '' }}" placeholder="">
                                                                @php
                                                                    $selectedNewTermekTipusId = old('new_termek_tipus_id', $stock_entry->header_data['termek_tipus_id'] ?? '');
                                                                @endphp
                                                                <option value="">- válasszon -</option>
                                                                @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedNewTermekTipusId])
                                                            </select>
                                                            <input type="hidden" value="1" name="new_sei_is_unique">
                                                            <input type="hidden" name="item_list_id" value="{{ $prefill['item_list_id'] ?? $itemList->id }}">
                                                            <input type="hidden" name="item_list_sor" value="{{ $prefill['item_list_sor'] ?? $sor }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Leírás</label>
                                                            <input type="text" class="form-control sei_description" name="new_description" value="{{ old('new_description', $prefill['description'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Mennyiség</label>
                                                            <input type="number" class="form-control sei_quantity" name="new_quantity" value="{{ old('new_quantity', $prefill['quantity'] ?? 1) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Beszerzési gm ár</label>
                                                            <input type="number" class="form-control sei_oneprice" name="new_oneprice" value="{{ old('new_oneprice', $prefill['oneprice'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Ár</label>
                                                            <input type="number" class="form-control sei_price" name="new_price" value="{{ old('new_price', $prefill['price'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Egyedi azonosító</label>
                                                            <input type="text" class="form-control sei_factory_number" name="new_factory_number" value="{{ old('new_factory_number', $prefill['factory_number'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-lg-3">
                                                        <div class="form-group mt-2">
                                                            <label class="form-control-label transfer-scrap-label">
                                                                <input type="checkbox" class="transfer-scrap-checkbox new-hallmark-choice" value="0" @checked((string) old('new_hallmarks', '') === '0')>
                                                                <strong>Nincs fémjelzés</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group mt-2">
                                                            <label class="form-control-label transfer-scrap-label">
                                                                <input type="checkbox" class="transfer-scrap-checkbox new-hallmark-choice" value="1" @checked((string) old('new_hallmarks', '') === '1')>
                                                                <strong>Van fémjelzés</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-lg-3">
                                                        <div class="form-group mt-2">
                                                            <label class="form-control-label transfer-scrap-label">
                                                                <input type="checkbox" class="transfer-scrap-checkbox" name="new_is_broken_gold" value="1" @checked(old('new_is_broken_gold', $prefill['is_broken_gold'] ?? false))>
                                                                <strong>Törtarany/ezüst</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> {{-- .sei_empty_row vége --}}
                                        </div> {{-- .stock_entry_items_rows vége --}}
                                        @endif
                                        
                                        <hr>
                                        <div class="row mt-3">
                                            <div class="col-md-8">
                                                <div class="form-group mb-0">
                                                    <label class="form-control-label">Új tétel képfeltöltés</label>
                                                    <input type="file" class="form-control" id="new_item_images" accept="image/*" multiple>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <button type="button" class="btn btn-success" id="save_new_item_btn">
                                                    <i class="fa fa-save"></i> Tétel mentése az átemeléshez
                                                </button>
                                            </div>
                                        </div>
                                    @endif {{-- status == 1 --}}

                                </div> {{-- .card-body --}}
                            </div> {{-- .col-lg-12 --}}
                        </div> {{-- .row --}}
                    </div> {{-- .tab-pane --}}
                </div> {{-- .tab-content --}}
            </div> {{-- .card-body --}}
        </div> {{-- .card --}}
    </div> {{-- .col-md-12 --}}
</div> {{-- .content --}}

@include('receipts.print_modal')
@endsection

@push('css')
<style>
    .transfer-scrap-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0;
        margin: 0;
        line-height: 20px;
        cursor: pointer;
    }
    .transfer-scrap-label .transfer-scrap-checkbox {
        position: static;
        flex: 0 0 18px;
        width: 18px;
        height: 18px;
        margin: 0;
        opacity: 1;
        visibility: visible;
    }
    .gallery-images .card-img-top {
        max-height: 500px;
        object-fit: cover;
    }
    [id^="inventory_gallery_"] .gallery-images {
        margin-left: 0;
        margin-right: 0;
        justify-content: flex-end;
    }
    [id^="inventory_gallery_"] .gallery-images > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
    }
    .inventory-gallery-img {
        max-width: 500px;
        max-height: 500px;
        object-fit: cover;
        cursor: pointer;
    }
    .gallery-image-container {
        position: relative;
        display: inline-block;
        margin: 5px;
    }
    .gallery-image-container .delete-inventory-image {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(0,0,0,0.6);
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 12px;
        color: white;
    }
    .item-wrapper {
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .item-wrapper:last-child {
        border-bottom: none !important;
    }
    .item-summary {
        cursor: pointer;
        padding: 8px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .item-summary:hover {
        background-color: #e9ecef;
    }
    .item-detail {
        display: none;
        background-color: #fff;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-top: 0.5rem;
    }
    [id^="inventory_gallery_"] {
        display: none;
    }
    .sei_item_wrapper .bootstrap-select.disabled > .dropdown-toggle,
    .sei_item_wrapper .bootstrap-select > .dropdown-toggle:disabled {
        background: #f8f9fa !important;
        border-color: #ced4da !important;
        color: #212529 !important;
        opacity: 1 !important;
    }
    .sei_item_wrapper .bootstrap-select.disabled .filter-option-inner-inner,
    .sei_item_wrapper .bootstrap-select > .dropdown-toggle:disabled .filter-option-inner-inner,
    .sei_item_wrapper .bootstrap-select .filter-option,
    .sei_item_wrapper .bootstrap-select .filter-option-inner,
    .sei_item_wrapper .bootstrap-select .dropdown-toggle .filter-option-inner-inner {
        color: #212529 !important;
    }
    .btn{
        color: #212529 !important;
    }
    .sei_item_wrapper .bootstrap-select.disabled .bs-caret,
    .sei_item_wrapper .bootstrap-select > .dropdown-toggle:disabled .bs-caret {
        color: #6c757d !important;
    }
</style>
@endpush

@push('js')
<script>
    const transferPageUrl = "{{ route('item_list.row.transfer', ['itemList' => $itemList->id, 'sor' => $sor]) }}";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $(document).on('click', '.delete_items_button', function() {
        var inventoryId = $(this).data('inventory-id');
        if (!inventoryId) return;

        swal({
            text: 'Biztosan törli ezt a tételt?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-danger',
            cancelButtonClass: 'btn btn-secondary',
            confirmButtonText: 'Igen, törlöm',
            cancelButtonText: 'Mégse',
            buttonsStyling: false
        }, function(result) {
            if (!result) return;

            $.ajax({
                url: '/ajax/delete_stock_entry_item',
                method: 'POST',
                data: { inventory_id: inventoryId },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function() {
                    $.notify({ message: 'Tétel sikeresen törölve' }, { type: 'success' });
                    window.location.href = transferPageUrl;
                },
                error: function(xhr) {
                    var message = 'Hiba a tétel törlése során';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $.notify({ message: message }, { type: 'danger' });
                }
            });
        });
    });

    $(document).on('click', '.edit_items_button', function() {
        var inventoryId = $(this).data('inventory-id');
        var $wrapper = $('.sei_item_wrapper[data-inventory-id="' + inventoryId + '"]');
        $wrapper.find('input, select, textarea').prop('disabled', false);
        $wrapper.find('.edit_items_button').addClass('d-none');
        $wrapper.find('.save_items_button').removeClass('d-none');
        if ($.fn.selectpicker) {
            $wrapper.find('select.selectpicker').selectpicker('refresh');
        }
    });

    $(document).on('click', '.save_items_button', function(e) {
        e.preventDefault();
        var inventoryId = $(this).data('inventory-id');
        var $wrapper = $('.sei_item_wrapper[data-inventory-id="' + inventoryId + '"]');
        var $form = $wrapper.find('form.sei_item_form');
        var formData = $form.serializeArray();
        
        formData.push({name: 'inventory_id', value: inventoryId});

        $.ajax({
            url: '/ajax/save_inventory_item_inline',
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                $.notify({ message: "Tétel sikeresen mentve" }, { type: 'success' });
                $wrapper.find('input, select, textarea').prop('disabled', true);
                $wrapper.find('.edit_items_button').removeClass('d-none');
                $wrapper.find('.save_items_button').addClass('d-none');
                if ($.fn.selectpicker) {
                    $wrapper.find('select.selectpicker').selectpicker('refresh');
                }
            },
            error: function(error) {
                $.notify({ message: "Hiba a mentés során" }, { type: 'danger' });
            }
        });
    });

    function refreshInventoryGallery(inventoryId) {
        if (!inventoryId) return;
        $.post('/load_inventory_gallery', { inventory_id: inventoryId, _token: csrfToken }, function(html) {
            $('#inventory_gallery_' + inventoryId).html(html);
        }).fail(function() {
            console.error('Hiba a galéria betöltésekor: ' + inventoryId);
        });
    }

    function onlyNumbers(str) {
        return /^\d+$/.test(str);
    }

    function recalculatePriceFields($container, fieldPrefix) {
        var prefix = fieldPrefix || '';
        var beszerzesiAr = parseFloat($container.find('[name="' + prefix + 'beszerzesi_ar"]').val()) || 0;
        var eladasiAr = parseFloat($container.find('[name="' + prefix + 'eladasi_ar"]').val()) || 0;
        var weight = parseFloat($container.find('[name="' + prefix + 'weight"]').val()) || 0;

        var onePrice = (weight > 0 && beszerzesiAr > 0) ? (beszerzesiAr / weight) : 0;
        var totalPrice = eladasiAr * weight;

        $container.find('[name="' + prefix + 'oneprice"]').val(onePrice > 0 ? (Math.round(onePrice * 100) / 100) : '');
        $container.find('[name="' + prefix + 'price"]').val(totalPrice > 0 ? (Math.round(totalPrice * 100) / 100) : '');
    }

    function recalculateItemPrice($container) {
        recalculatePriceFields($container, '');
    }

    function recalculateNewPrice() {
        var $container = $('.stock_entry_items_rows').find('.sei_empty_row');
        recalculatePriceFields($container, 'new_');
    }

    $(document).ready(function() {
        $(document).on('change', '.label-type', function() {
            let inventoryId = $(this).data('inventory-id');
            let type = $(this).val();
            if (inventoryId && type) {
                let url = '/product/printLabel?inventory_id=' + inventoryId + '&label_type=' + type;
                window.open(url, '_blank');
            }
        });

        function syncHeaderLabelTypeToItems() {
            let headerType = $('#header_label_type').val() || '';
            $('.label-type').val(headerType);
        }

        $('#header_label_type').on('change', function() {
            syncHeaderLabelTypeToItems();
        });

        $(document).on('click', '.item-label-print-btn', function() {
            let inventoryId = $(this).data('inventory-id');
            let $labelSelect = $('#label_type_' + inventoryId);
            if (!$labelSelect.length) return;
            if (!$labelSelect.val()) {
                $.notify({ message: 'Válassz címke típust.' }, { type: 'warning' });
                return;
            }
            $labelSelect.trigger('change');
        });

        syncHeaderLabelTypeToItems();

        $('select[name=business_id]').change(function(){
            let business_id = $(this).val();
            if(business_id){
                $.ajax({
                    method: "POST",
                    url: "/ajax/get_storages_by_business_id_employee_consignment_receipt",
                    data: { business_id: business_id }
                }).done(function( resp ) {
                    $('select[name=storage_id]').empty();
                    $.each(resp, function(index, warehouse) {
                        $('select[name=storage_id]').append($('<option>', {
                            value: warehouse.warehouse_id,
                            text: warehouse.name
                        }));
                    });    
                });

                $.ajax({
                    method: "POST",
                    url: "/ajax/get_business",
                    data: { business_id: business_id }
                }).done(function( resp ) {
                    var markings = (resp.business_markings || []).filter(function(m) {
                        return parseInt(m.status || 0, 10) === 1;
                    });
                    applyBusinessMarkings(markings);
                });
            } else {
                let textdef = '<option value="">- válasszon -</option>';
                $('select[name=storage_id]').empty();
                $('select[name=storage_id]').append(textdef);
                applyBusinessMarkings([]);
            }
        });

        $(document).on('click', '.item-summary', function() {
            var targetId = $(this).data('target');
            $('#' + targetId).slideToggle(300);
            $(this).find('.fa-chevron-down').toggleClass('fa-chevron-up');
        });

        $('.sei_item_wrapper').each(function() {
            var inventoryId = $(this).data('inventory-id');
            refreshInventoryGallery(inventoryId);
        });

        $('.selectpicker').selectpicker();

        $(document).on('click', '#new_item_btn', function() {
            window.location.href = transferPageUrl; 
        });

        $(document).on('change', '.new-hallmark-choice', function() {
            if (this.checked) {
                $('.new-hallmark-choice').not(this).prop('checked', false);
            }
        });

        $(document).on('click', '#save_new_item_btn', function() {
            var $container = $('.stock_entry_items_rows').find('.sei_empty_row');
            var $newImageInput = $('#new_item_images');
            var selectedFiles = $newImageInput.length ? ($newImageInput[0].files || []) : [];

            var sei_data = new FormData();
            sei_data.append('stock_entry_id', '{{ $stock_entry->stock_entry_id }}');
            sei_data.append('sei_id', $('#sei_id').val());
            sei_data.append('parent_inventory_id', $('#parent_inventory_id').val());
            sei_data.append('new_termek_tipus_id', $container.find('[name="new_termek_tipus_id"]').val());
            sei_data.append('new_sei_is_unique', $container.find('[name="new_sei_is_unique"]').val() || 1);
            sei_data.append('new_description', $container.find('[name="new_description"]').val());
            sei_data.append('new_quantity', $container.find('[name="new_quantity"]').val());
            sei_data.append('new_oneprice', $container.find('[name="new_oneprice"]').val());
            sei_data.append('new_price', $container.find('[name="new_price"]').val());
            sei_data.append('new_factory_number', $container.find('[name="new_factory_number"]').val());
            sei_data.append('new_weight', $container.find('[name="new_weight"]').val());
            sei_data.append('new_is_broken_gold', $container.find('[name="new_is_broken_gold"]').is(':checked') ? 1 : 0);
            sei_data.append('new_customer_marking_id', $container.find('[name="new_customer_marking_id"]').val());
            sei_data.append('new_vzj', $container.find('[name="new_customer_marking_id"] option:selected').text());
            sei_data.append('new_category_id', $container.find('[name="new_category_id"]').val());
            sei_data.append('new_beszerzesi_ar', $container.find('[name="new_beszerzesi_ar"]').val());
            sei_data.append('new_eladasi_ar', $container.find('[name="new_eladasi_ar"]').val());
            sei_data.append('new_vat_type', $container.find('[name="new_vat_type"]').val());
            var selectedHallmark = $container.find('.new-hallmark-choice:checked').val();
            sei_data.append('new_hallmarks', selectedHallmark === undefined ? '' : selectedHallmark);
            sei_data.append('new_karat_id', $container.find('[name="new_karat_id"]').val());
            sei_data.append('new_size', $container.find('[name="new_size"]').val());
            sei_data.append('new_label_type', $container.find('[name="new_label_type"]').val());
            sei_data.append('category2_id', $container.find('[name="new_category2_id"]').val());
            sei_data.append('item_list_id', '{{ $itemList->id }}');
            sei_data.append('item_list_sor', '{{ $sor }}');

            var selectedNewStatuses = $container.find('[name="new_statuses[]"]').val();
            if (Array.isArray(selectedNewStatuses)) {
                $.each(selectedNewStatuses, function(_, value) {
                    sei_data.append('new_statuses[]', value);
                });
            } else if (selectedNewStatuses) {
                sei_data.append('new_statuses[]', selectedNewStatuses);
            }
            $.each($container.find('[name="new_colors[]"]').val() || [], function(_, value) {
                sei_data.append('new_colors[]', value);
            });

            function uploadNewItemImages(inventoryIds) {
                if (!selectedFiles.length || !inventoryIds.length) {
                    window.location.href = transferPageUrl;
                    return;
                }

                var uploadRequests = $.map(inventoryIds, function(inventoryId) {
                    var fd = new FormData();
                    fd.append('inventory_id', inventoryId);
                    fd.append('_token', csrfToken);
                    $.each(selectedFiles, function(index, file) {
                        fd.append('images[]', file);
                    });
                    return $.ajax({
                        url: '/upload_inventory_image',
                        type: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false
                    });
                });

                $.when.apply($, uploadRequests)
                    .done(function() {
                        $.notify({ message: 'Tétel és kép(ek) sikeresen mentve' }, { type: 'success' });
                        window.location.href = transferPageUrl;
                    })
                    .fail(function() {
                        $.notify({ message: 'A tétel mentve lett, de a képfeltöltés közben hiba történt.' }, { type: 'warning' });
                        window.location.href = transferPageUrl;
                    });
            }

            $.ajax({
                url: '/ajax/save_stock_entry_item',
                method: 'POST',
                data: sei_data,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    var inventoryIds = [];
                    if (response && $.isArray(response.inventory_ids)) {
                        inventoryIds = response.inventory_ids;
                    } else if (response && response.inventory_id) {
                        inventoryIds = [response.inventory_id];
                    }

                    if (!selectedFiles.length) {
                        $.notify({ message: 'Tétel sikeresen mentve' }, { type: 'success' });
                        window.location.href = transferPageUrl;
                        return;
                    }

                    uploadNewItemImages(inventoryIds);
                },
                error: function(error) {
                    console.error('Hiba a mentés során:', error);
                    recalculateNewPrice();
                }
            });
        });

        $(document).on('input', '[name="new_beszerzesi_ar"], [name="new_eladasi_ar"], [name="new_weight"]', function() {
            recalculateNewPrice();
        });

        $(document).on('input', '.sei_item_wrapper [name="beszerzesi_ar"], .sei_item_wrapper [name="eladasi_ar"], .sei_item_wrapper [name="weight"]', function() {
            recalculateItemPrice($(this).closest('.sei_item_wrapper'));
        });

        $('select[name=customer_id]').select2({ theme: "bootstrap4", tags: true });
        $('#customer_zip').select2({
            theme: 'bootstrap4',
            language: { inputTooShort: function() { return 'Kérjük, adjon meg 2 vagy több karaktert'; } },
            ajax: {
                url: '/ajax/get-zips',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { term: params.term }; },
                processResults: function(data) {
                    return { results: $.map(data, function(zip) { return { id: zip.id, text: zip.name }; }) };
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

        function applyBusinessMarkings(markings, selectedId) {
            let options = '<option value="">- válasszon -</option>';
            let defaultMarking = null;

            $.each(markings || [], function(_, marking) {
                if (!defaultMarking && parseInt(marking.default || 0, 10) === 1) {
                    defaultMarking = marking;
                }
                let isSelected = String(selectedId || '') === String(marking.id);
                options += '<option value="' + marking.id + '" data-price="' + (marking.price !== null && marking.price !== undefined ? marking.price : '') + '" data-vat-type-id="' + (marking.vat_type_id || '') + '" data-product-status-id="' + (marking.product_status_id || '') + '"' + (isSelected ? ' selected' : '') + '>' + (marking.name || '') + '</option>';
            });

            if (!defaultMarking && markings && markings.length > 0) {
                defaultMarking = markings[0];
            }

            let effectiveId = selectedId || (defaultMarking ? defaultMarking.id : '');

            $('#vzj').html(options).val(effectiveId);
            $('#new_vzj').html(options).val(effectiveId);

            $('select.customer-marking-select-inline').each(function() {
                let $select = $(this);
                let preferred = String($select.data('selectedId') || $select.val() || '');
                let inlineSelected = (markings || []).some(function(m) { return String(m.id) === preferred; }) ? preferred : String(effectiveId || '');
                $select.html(options).val(inlineSelected).attr('data-selected-id', inlineSelected);
            });

            if ($.fn.selectpicker) {
                $('#vzj').selectpicker('refresh');
                $('#new_vzj').selectpicker('refresh');
                $('select.customer-marking-select-inline').selectpicker('refresh');
            }

            let chosenMarking = (markings || []).find(function(m) { return String(m.id) === String(effectiveId); }) || defaultMarking;
            if (chosenMarking) {
                if (chosenMarking.price !== undefined && chosenMarking.price !== null && chosenMarking.price !== '') {
                    $('input[name="eladasi_ar"]').val(chosenMarking.price);
                    $('input[name="new_eladasi_ar"]').val(chosenMarking.price);
                } else {
                    $('input[name="eladasi_ar"]').val('');
                    $('input[name="new_eladasi_ar"]').val('');
                }

                if (chosenMarking.vat_type_id !== undefined && chosenMarking.vat_type_id !== null && chosenMarking.vat_type_id !== '') {
                    $('select[name="vat_type"]').val(String(chosenMarking.vat_type_id));
                    $('select[name="new_vat_type"]').val(String(chosenMarking.vat_type_id));
                } else {
                    $('select[name="vat_type"]').val('');
                    $('select[name="new_vat_type"]').val('');
                }

                if (chosenMarking.product_status_id !== undefined && chosenMarking.product_status_id !== null && chosenMarking.product_status_id !== '') {
                    $('select[name="new_statuses[]"]').val(String(chosenMarking.product_status_id));
                }

                if ($.fn.selectpicker) {
                    $('select[name="vat_type"]').selectpicker('refresh');
                    $('select[name="new_vat_type"]').selectpicker('refresh');
                    $('select[name="new_statuses[]"]').selectpicker('refresh');
                }

                recalculateNewPrice();
            }
        }

        $(document).on('change', 'select.customer-marking-select-inline', function() {
            $(this).attr('data-selected-id', $(this).val() || '');
            applySelectedMarkingDataToInline($(this));
        });

        $('#vzj').on('change', function() {
            let $selected = $(this).find('option:selected');
            let selectedPrice = $selected.data('price');
            let selectedVatTypeId = $selected.data('vat-type-id');
            let selectedProductStatusId = $selected.data('product-status-id');

            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                $('input[name="eladasi_ar"]').val(selectedPrice);
                $('input[name="new_eladasi_ar"]').val(selectedPrice);
            } else if ($(this).val() === '') {
                $('input[name="eladasi_ar"]').val('');
                $('input[name="new_eladasi_ar"]').val('');
            }

            if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                $('select[name="vat_type"]').val(String(selectedVatTypeId));
                $('select[name="new_vat_type"]').val(String(selectedVatTypeId));
            } else if ($(this).val() === '') {
                $('select[name="vat_type"]').val('');
                $('select[name="new_vat_type"]').val('');
            }

            $('#new_vzj').val($(this).val());

            if (selectedProductStatusId !== undefined && selectedProductStatusId !== null && selectedProductStatusId !== '') {
                $('select[name="new_statuses[]"]').val(String(selectedProductStatusId));
            }

            if ($.fn.selectpicker) {
                $('select[name="vat_type"]').selectpicker('refresh');
                $('select[name="new_vat_type"]').selectpicker('refresh');
                $('#new_vzj').selectpicker('refresh');
                $('select[name="new_statuses[]"]').selectpicker('refresh');
            }

            recalculateNewPrice();
        });

        $('#new_vzj').on('change', function() {
            let $selected = $(this).find('option:selected');
            let selectedPrice = $selected.data('price');
            let selectedVatTypeId = $selected.data('vat-type-id');
            let selectedProductStatusId = $selected.data('product-status-id');

            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                $('input[name="new_eladasi_ar"]').val(selectedPrice);
            } else if ($(this).val() === '') {
                $('input[name="new_eladasi_ar"]').val('');
            }

            if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                $('select[name="new_vat_type"]').val(String(selectedVatTypeId));
            } else if ($(this).val() === '') {
                $('select[name="new_vat_type"]').val('');
            }

            if (selectedProductStatusId !== undefined && selectedProductStatusId !== null && selectedProductStatusId !== '') {
                $('select[name="new_statuses[]"]').val(String(selectedProductStatusId));
            } else if ($(this).val() === '') {
                $('select[name="new_statuses[]"]').val('');
            }

            if ($.fn.selectpicker) {
                $('select[name="new_vat_type"]').selectpicker('refresh');
                $('select[name="new_statuses[]"]').selectpicker('refresh');
            }

            recalculateNewPrice();
        });

        function applySelectedMarkingDataToInline($select) {
            let $wrapper = $select.closest('.sei_item_wrapper');
            let $selected = $select.find('option:selected');
            let selectedPrice = $selected.data('price');
            let selectedVatTypeId = $selected.data('vat-type-id');

            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                $wrapper.find('input[name="eladasi_ar"]').val(selectedPrice);
            } else if (!$select.val()) {
                $wrapper.find('input[name="eladasi_ar"]').val('');
            }

            if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                $wrapper.find('select[name="vat_type_id"]').val(String(selectedVatTypeId));
            } else if (!$select.val()) {
                $wrapper.find('select[name="vat_type_id"]').val('');
            }

            if ($.fn.selectpicker) {
                $wrapper.find('select[name="vat_type_id"]').selectpicker('refresh');
            }

            recalculateItemPrice($wrapper);
        }

        $('input[name="beszerzesi_ar"]').on('input change', function() {
            $('input[name="new_beszerzesi_ar"]').val($(this).val());
            recalculateNewPrice();
        });

        $('input[name="eladasi_ar"]').on('input change', function() {
            $('input[name="new_eladasi_ar"]').val($(this).val());
            recalculateNewPrice();
        });

        $('select[name="vat_type"]').on('change', function() {
            $('select[name="new_vat_type"]').val($(this).val());
            if ($.fn.selectpicker) {
                $('select[name="new_vat_type"]').selectpicker('refresh');
            }
        });

        $('#header_label_type').on('change', function() {
            $('select[name="new_label_type"]').val($(this).val());
            if ($.fn.selectpicker) {
                $('select[name="new_label_type"]').selectpicker('refresh');
            }
        });

        $('#customer_id').on('change', function() {
            var customer_id = $(this).val();
            if (onlyNumbers(customer_id)) {
                $.ajax({
                    method: "POST",
                    url: "/ajax/get_customer",
                    data: { customer_id: customer_id }
                }).done(function(resp) {
                    var o = (typeof resp === 'string') ? $.parseJSON(resp) : (resp || {});
                    $('select[name=customer_zip]').prop('disabled', true);
                    $('select[name=customer_city]').prop('disabled', true);
                    $('input[name=customer_address]').prop('disabled', true);
                    $('input[name=customer_identity_card_number]').prop('disabled', true);
                    $('input[name=customer_tax_number]').prop('disabled', true);
                    $('select[name=customer_zip]').empty();
                    $('select[name=customer_city]').empty();
                    if (o.address) {
                        $('select[name=customer_zip]').append('<option selected value="' + (o.address.zip_id || '') + '">' + (o.address.zip || '') + '</option>').trigger('change');
                        $('select[name=customer_city]').append('<option selected value="' + (o.address.city_id || '') + '">' + (o.address.city || '') + '</option>').trigger('change');
                        $('input[name=customer_address]').val(o.address.address || '');
                    } else {
                        $('input[name=customer_address]').val('');
                    }
                    $('input[name=customer_identity_card_number]').val(o.customer.identity_card_number || '');
                    $('input[name=customer_tax_number]').val(o.customer.tax_number || '');
                });
            } else {
                $('select[name=customer_zip]').empty().trigger('change');
                $('select[name=customer_city]').empty().trigger('change');
                $('input[name=customer_address]').val('');
                $('input[name=customer_identity_card_number]').val('');
                $('input[name=customer_tax_number]').val('');
                $('select[name=customer_zip]').prop('disabled', false);
                $('select[name=customer_city]').prop('disabled', false);
                $('input[name=customer_address]').prop('disabled', false);
                $('input[name=customer_identity_card_number]').prop('disabled', false);
                $('input[name=customer_tax_number]').prop('disabled', false);
                $('select[name=customer_zip]').focus();
            }
        });

        function saveStockEntry(callback, print = false, close = false) {
            if (!$("#stock_entry_form")[0].checkValidity()) {
                $('<input type="submit">').hide().appendTo($('#stock_entry_form')).click().remove();
                return false;
            }
            var form = document.getElementById('stock_entry_form');
            var formData = new FormData(form);
            formData.delete('_method');
            formData.append('stock_entry_id', '{{ $stock_entry->stock_entry_id }}');
            formData.append('close_entry', close ? 1 : 0);
            $.ajax({
                method: "POST",
                url: "/ajax/save_stock_entry",
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                    if (print) {
                        $.ajax({
                            type: "POST",
                            url: '/ajax/print_stock_entry',
                            data: { stock_entry_id: '{{ $stock_entry->stock_entry_id }}' },
                            success: function(response) {
                                let iframeHTML = '<iframe src="' + response + '" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                                $('#printmodal .modal-body').html(iframeHTML);
                                $('#printmodalLabel').empty().html('Nyomtatási kép');
                                $('#printmodal').modal('show');
                            }
                        });
                    } else {
                        window.location.href = transferPageUrl;
                    }
                    if (callback) callback();
                },
                error: function(xhr) {
                    var message = 'A mentés sikertelen.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $.notify({ icon: "error", message: message }, { type: 'danger', timer: 4000, placement: { from: 'top', align: 'right' } });
                }
            });
        }

        $('.save_stock_entry').click(function() { saveStockEntry(); });

        $('.close_stock_entry').click(function() {
            if (!$("#stock_entry_form")[0].checkValidity()) {
                $('<input type="submit">').hide().appendTo($('#stock_entry_form')).click().remove();
                return false;
            }
            swal({
                text: "Biztosan menti és lezárja a bizonylatot? Lezárás után nem módosítható.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-secondary',
                confirmButtonText: 'Igen, lezárom',
                cancelButtonText: 'Mégse',
                buttonsStyling: false
            }, function(result) {
                if (result) {
                    saveStockEntry(function() {}, false, true);
                }
            });
        });

        $('.print_stock_entry').click(function() {
            if (!$("#stock_entry_form")[0].checkValidity()) {
                $('<input type="submit">').hide().appendTo($('#stock_entry_form')).click().remove();
                return false;
            }
            swal({
                text: "Biztosan nyomtatja az ékszer vásárlási bizonylatot?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Nem',
                buttonsStyling: false
            }, function(result) {
                if (result) saveStockEntry(function() {}, true);
            });
        });

        $(document).on('click', '.capture-gallery-btn', function() {
            let $btn = $(this);
            let inventoryId = $btn.data('inventory-id');
            let $wrapper = $btn.closest('.d-flex');
            let $fileInput = $wrapper.find('.gallery-file-input[data-inventory-id="' + inventoryId + '"]').first();

            if ($fileInput.length) {
                $fileInput.trigger('click');
            }
        });

        $(document).on('change', '.gallery-file-input', function() {
            let inventoryId = $(this).data('inventory-id');
            let files = this.files;
            if (!inventoryId || !files.length) return;

            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('inventory_id', inventoryId);

            $.each(files, function(i, file) {
                formData.append('images[]', file);
            });

            $.ajax({
                url: '/upload_inventory_image',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    refreshInventoryGallery(inventoryId);
                },
                error: function() {
                    alert('Hiba a képfeltöltés során.');
                }
            });
            $(this).val('');
        });
    });
</script>
@endpush
