@extends('layouts.app', ['activePage' => 'stock_entry', 'activeButton' => 'stock_entry', 'title' => 'Zemef ERP', 'navName' => 'Ékszer vásárlás rögzítése'])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                    </li>
                </ul>

                <div class="row align-items-center">
                    <div class="col-7"></div>
                    <div class="col-2 text-right mt10">
                        <a href="{{ route('stock_entry.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                    <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <form id="stock_entry_form" method="post" action="{{ route('stock_entry.update', $stock_entry->stock_entry_id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('patch')

                                        <h6 class="heading-small text-muted mb-4">Ékszer vásárlás adatai</h6>
                                        <hr>

                                        {{-- Fejrész mezők --}}
                                        @php
                                            $selectedHeaderCustomerMarkingId = old('customer_marking_id', $stock_entry->header_data['customer_marking_id'] ?? '');
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
                                            <div class="col-lg-2">
                                        <div class="form-group" style="margin-left:20px;">
                                            <br />
                                            <input type="checkbox" class="form-check-input" name="hallmarks" value="1" 
                                                id="hallmark_{{ $stock_entry->header_data['hallmarks'] ?? 0 }}" {{ ($stock_entry->header_data['hallmarks'] ?? 0) == 1 ? 'checked' : '' }} >
                                            <label class="form-control-label">Fémjelzés</label>
                                        </div>
                                    </div>
                                        </div>

                                        {{-- Ügyfél adatok --}}
                                        <div class="row">
                                            <div class="col-lg-3">
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
                                                        $zip = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->zip : '';
                                                        $zip_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->zip_id : '0';
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
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <?php
                                                        $city = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->city : '';
                                                        $city_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->city_id : '0';
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
                                                        $address = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->address : '';
                                                    ?>
                                                    <label class="form-control-label{{ $errors->has('customer_address') ? ' has-danger' : '' }}">Cím</label>
                                                    <input type="text" class="form-control{{ $errors->has('customer_address') ? ' is-invalid' : '' }}" name="customer_address" value="{{ old('customer_address', $address) }}" disabled>
                                                    @include('alerts.feedback', ['field' => 'customer_address'])
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <?php $tax_number = (isset($stock_entry->customer)) ? $stock_entry->customer->tax_number : ''; ?>
                                                    <label class="form-control-label{{ $errors->has('customer_tax_number') ? ' has-danger' : '' }}">Adószám</label>
                                                    <input type="text" class="form-control{{ $errors->has('customer_tax_number') ? ' is-invalid' : '' }}" name="customer_tax_number" value="{{ old('customer_tax_number', $tax_number) }}" disabled>
                                                    @include('alerts.feedback', ['field' => 'customer_tax_number'])
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('currency_id') ? ' has-danger' : '' }}">Deviza</label>
                                                    <select @if($stock_entry->status == 2) disabled @endif required name="currency_id" class="select2 form-control{{ $errors->has('currency_id') ? ' is-invalid' : '' }}">
                                                        @foreach($currencies as $currency)
                                                            <option 
                                                                @if($currency->currency_id == $stock_entry->currency_id) 
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
                                                                value="{{ $currency->currency_id }}">{{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @include('alerts.feedback', ['field' => 'currency_id'])
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <?php $currency_value = (isset($stock_entry->currency_value)) ? $stock_entry->currency_value : ''; ?>
                                                    <label class="form-control-label{{ $errors->has('currency_value') ? ' has-danger' : '' }}">Árfolyam</label>
                                                    <input type="number" @if($stock_entry->status == 2) disabled @endif step=".01" class="form-control{{ $errors->has('currency_value') ? ' is-invalid' : '' }}" name="currency_value" value="{{ old('currency_value', $selected_currency_value) }}" @if($selected_currency_editable == 0) disabled @endif required>
                                                    @include('alerts.feedback', ['field' => 'currency_value'])
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" value="1" id="has_contract" name="has_contract" {{ $stock_entry->has_contract ? 'checked' : '' }}>
                                                            <span class="form-check-sign"></span>
                                                            Szerződés
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('contract_number') ? ' has-danger' : '' }}">Szerződés szám</label>
                                                    <input type="text" {{ $stock_entry->has_contract ? '' : 'disabled' }} class="form-control{{ $errors->has('contract_number') ? ' is-invalid' : '' }}" id="contract_number" name="contract_number" value="{{ old('contract_number', $stock_entry->contract_number) }}">
                                                    @include('alerts.feedback', ['field' => 'contract_number'])
                                                </div>
                                            </div>
                                        </div>

                                        

                                        

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">Sorszám</label>
                                                    <input type="text" class="form-control" name="ticket_number" value="{{ $stock_entry->ticket_number }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('fulfillment_date') ? ' has-danger' : '' }}">Teljesítés dátum</label>
                                                    <input type="date" class="form-control" name="fulfillment_date" id="fulfillment_date" value="{{ $stock_entry->fulfillment_date }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('invoice_number') ? ' has-danger' : '' }}">Szállítólevél sorszáma</label>
                                                    <input type="text" @if($stock_entry->status == 2) disabled @endif class="form-control" name="invoice_number" value="{{ $stock_entry->invoice_number }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">Státusz</label>
                                                    <select @if($stock_entry->status == 2) disabled @endif name="status" disabled class="select2 form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
                                                        <option @if($stock_entry->status == "1") selected @endif value="1">Folyamatban</option>
                                                        <option @if($stock_entry->status == "2") selected @endif value="2">Feldolgozva</option>
                                                    </select>
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
                                                @php
                                            $selectedHeaderLabelType = old('label_type_default', $stock_entry->header_data['label_type'] ?? '');
                                            $selectedHeaderKaratId = old('karat_id', $stock_entry->header_data['karat_id'] ?? '');
                                        @endphp
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label">Karát</label>
                                                    <select @if($stock_entry->status == 2) disabled @endif id="header_karat_id" name="karat_id" class="selectpicker form-control">
                                                        <option value="">- válasszon -</option>
                                                        @foreach($karats ?? [] as $karat)
                                                            <option value="{{ $karat->karat_id }}" {{ (string)$selectedHeaderKaratId === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <div class="form-group d-flex align-items-end" style="gap:8px;">
                                                    <div style="flex:1;">
                                                        <label class="form-control-label">Címke típus</label>
                                                        <select id="header_label_type" name="label_type_default" class="form-control">
                                                            <option value="">-- Címke típus --</option>
                                                            <option value="1" {{ (string) $selectedHeaderLabelType === '1' ? 'selected' : '' }}>1-es típus</option>
                                                            <option value="2" {{ (string) $selectedHeaderLabelType === '2' ? 'selected' : '' }}>2-es típus</option>
                                                            <option value="3" {{ (string) $selectedHeaderLabelType === '3' ? 'selected' : '' }}>3-as típus</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-success save_stock_entry">Mentés</button>
                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-danger close_stock_entry">Mentés és lezárás</button>
                                        <button @if($stock_entry->status == 2) disabled @endif type="button" class="btn btn-success print_stock_entry">
                                            @if($stock_entry->status == 2) Lezárva: {{ date('Y. m. d. H:i', strtotime($stock_entry->printed_at)) }} @else Nyomtatás @endif
                                        </button>

                                        @if($stock_entry->is_storno || $stock_entry->storno_se_id)
                                        <div class="row mt-3">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">
                                                        @if($stock_entry->is_storno) Sztornózott @else Sztornó @endif ékszer bevételezés
                                                    </label>
                                                    <input type="text" class="form-control" value="{{ $stock_entry->relatedticketnumber }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Sztornó oka</label>
                                                    <input type="text" class="form-control" value="{{ $stock_entry->storno_reason }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </form> {{-- stock_entry_form vége --}}

                                    {{-- Tételek kezelése --}}
                                    @if($stock_entry->status == 1)
                                        <h6 class="heading-small text-muted mb-4 mt-4">Ékszer vásárlás tételek</h6>

                                        @php
                                            if (!isset($inventoryItemsForCopy)) {
                                                $inventoryItemsForCopy = [];
                                            }
                                            foreach ($groupedInventoryItems as $productId => $items) {
                                                $item = $items->first();
                                                $inventoryItemsForCopy[$item->inventory_id] = [
                                                    'parent_inventory_id' => $item->inventory_id,
                                                    'vzj' => $item->item_data['vzj'] ?? '',
                                                    'customer_marking_id' => $item->item_data['customer_marking_id'] ?? ($stock_entry->header_data['customer_marking_id'] ?? ''),
                                                    'category_id' => $item->item_data['category_id'] ?? '',
                                                    'beszerzesi_ar' => $item->item_data['beszerzesi_ar'] ?? '',
                                                    'eladasi_ar' => $item->item_data['eladasi_ar'] ?? '',
                                                    'vat_type_id' => $item->item_data['vat_type_id'] ?? '',
                                                    'size' => $item->item_data['size'] ?? '',
                                                    'factory_number' => $item->factory_number ?? '',
                                                    'weight' => $item->weight ?? '',
                                                    'oneprice' => $item->oneprice ?? '',
                                                    'description' => $item->description ?? '',
                                                    'category2_id' => $item->item_data['category2_id']
                                                        ?? (is_array($item->item_data['product_categories_2'] ?? null)
                                                            ? ($item->item_data['product_categories_2'][0] ?? '')
                                                            : ($item->item_data['product_categories_2'] ?? '')),
                                                    'label_type' => $item->item_data['label_type'] ?? 1,
                                                    'quantity' => $items->sum('quantity'),
                                                    'hallmarks' => $item->item_data['hallmarks'] ?? [],
                                                    'statuses' => $item->item_data['statuses'] ?? [],
                                                    'colors' => $item->item_data['colors'] ?? [],
                                                    'karat_id' => $item->item_data['karat_id'] ?? ($item->item_data['karats'][0] ?? ''),
                                                ];
                                            }

                                            $customerMarkingsForJs = $customer_markings->map(function ($marking) {
                                                return [
                                                    'id' => $marking->id,
                                                    'name' => $marking->name,
                                                    'default' => (int) $marking->default,
                                                    'status' => (int) $marking->status,
                                                    'price' => optional($marking->productStatus)->price,
                                                    'vat_type_id' => optional($marking->productStatus)->vat_type_id,
                                                ];
                                            })->values();
                                        @endphp

                                        <script>
                                            window.inventoryItemsForCopy = @json($inventoryItemsForCopy);
                                            window.currentCustomerMarkings = @json($customerMarkingsForJs);
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
                                                                            @foreach($customer_markings as $customer_marking)
                                                                                <option
                                                                                    value="{{ $customer_marking->id }}"
                                                                                    data-price="{{ optional($customer_marking->productStatus)->price }}"
                                                                                    data-vat-type-id="{{ optional($customer_marking->productStatus)->vat_type_id }}"
                                                                                    @if((string) $selectedInlineCustomerMarkingId === (string) $customer_marking->id) selected @endif
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
                                                                        <label class="form-control-label">Gyártási szám</label>
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
                                                                        <label class="form-control-label">Egységár</label>
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
                                                                        {{-- Képek 
                                                                        <button type="button" class="btn btn-success btn-sm load-gallery-btn me-1" 
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}" 
                                                                                data-product-id="{{ $firstItem->product_id }}">
                                                                            <i class="fa fa-image"></i> Képek
                                                                        </button>
--}}
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

                                                                        {{-- Másolás --}}
                                                                        <button type="button" class="btn btn-danger btn-sm copy_items_button me-1" 
                                                                                data-inventory-id="{{ $firstItem->inventory_id }}">
                                                                            Tétel másolása
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
                                        @endforeach
<!-- Új tétel hozzáadása -->
                                        @if($stock_entry->status != 2)
                                        
                                        <div class="stock_entry_items_rows">
                                            <input type="hidden" name="sei_id" id="sei_id"/>
                                            <input type="hidden" name="parent_inventory_id" id="parent_inventory_id"/>
                                            
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-10"></div>
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
                                                                @foreach($customer_markings as $customer_marking)
                                                                    <option
                                                                        value="{{ $customer_marking->id }}"
                                                                        data-price="{{ optional($customer_marking->productStatus)->price }}"
                                                                        data-vat-type-id="{{ optional($customer_marking->productStatus)->vat_type_id }}"
                                                                        @if((string) ($stock_entry->header_data['customer_marking_id'] ?? '') === (string) $customer_marking->id) selected @endif
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
                                                            <select name="new_category_id" class="selectpicker form-control">
                                                                @include('categories.category_options', ['categories' => $categories, 'category_id' => old('new_category_id', $stock_entry->header_data['category_id'] ?? 1)])
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Beszerzési ár</label>
                                                            <input type="number" class="form-control sei_beszerzesi_ar" name="new_beszerzesi_ar" value="{{ old('new_beszerzesi_ar', $stock_entry->header_data['beszerzesi_ar'] ?? '') }}">
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
                                                    <div class="col-lg-2">
                                                        <div class="form-group" style="margin-left:20px;">
                                                            <br />
                                                                <input type="checkbox" class="form-check-input" name="new_hallmarks" value="{{ $stock_entry->header_data['hallmarks'] ?? 0 }}" id="new_hallmarks" {{ old('new_hallmarks', $stock_entry->header_data['hallmarks'] ?? 0) == 1 ? 'checked' : '' }} >   <label class="form-control-label">Fémjelzés</label>

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
                                                                    <option value="{{ $color->product_color_id }}">{{ $color->name }}</option>
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
                                                                    <option value="{{ $category2->category2_id }}">{{ $category2->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Méret</label>
                                                            <input type="text" class="form-control" name="new_size" id="new_size">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            
                                                            <label class="form-control-label">Súly</label>
                                                            <input type="number" class="form-control sei_weight" name="new_weight">
                                                        
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
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Leírás</label>
                                                            <input type="text" class="form-control sei_description" name="new_description">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Mennyiség</label>
                                                            <input type="number" class="form-control sei_quantity" name="new_quantity" value="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Egységár</label>
                                                            <input type="number" class="form-control sei_oneprice" name="new_oneprice">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Ár</label>
                                                            <input type="number" disabled class="form-control sei_price" name="new_price">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Gyártási szám</label>
                                                            <input type="text" class="form-control sei_factory_number" name="new_factory_number">
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
                                                    <i class="fa fa-save"></i> Ékszer vásárláshoz tartozó tétel mentése
                                                </button>
                                            </div>
                                        </div>
                                    @endif {{-- status == 1 --}}

                                    @if($stock_entry->wait_to_transfer == '1' && $stock_entry->status == 2)
                                        @if(!$stock_entry->is_storno)
                                            <button @if($stock_entry->cancelled_at) disabled @endif type="button" class="btn btn-warning cancel_stock_entry">
                                                @if($stock_entry->cancelled_at) Sztornózva: {{ date('Y. m. d. H:i', strtotime($stock_entry->cancelled_at)) }} @else Sztornó @endif
                                            </button>
                                            <button type="button" class="btn btn-info copy_stock_entry">Másolás</button>
                                        @endif
                                    @endif

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
    /* Initially hide all inventory galleries */
    .gallery-images .card-img-top {
        max-height: 500px;
        object-fit: cover;
    }
    /* Keep gallery inside container and align cards to the right */
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
    /* Optional: remove the default card border from any surrounding cards */
    .card.data-tables .card-body {
        padding: 1rem;
    }
    #items-table .card {
        border: none;
    }
    /* Initially hide all inventory gallery divs */
    [id^="inventory_gallery_"] {
        display: none;
    }

    /* Ensure disabled selectpicker values are readable in item rows */
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
<script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
<script>
    // --- COPY ITEM BUTTON LOGIC ---
    $(document).on('click', '.copy_items_button', function() {
        var inventoryId = $(this).data('inventory-id');
        if (!inventoryId) {
            return;
        }

        $.ajax({
            url: '/ajax/copy_stock_entry_item',
            method: 'POST',
            data: { inventory_id: inventoryId },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                $.notify({ message: 'Tétel sikeresen másolva' }, { type: 'success' });
                location.reload();
            },
            error: function(xhr) {
                var message = 'Hiba a tétel másolása során';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $.notify({ message: message }, { type: 'danger' });
            }
        });
    });

    $(document).on('click', '.delete_items_button', function() {
        var inventoryId = $(this).data('inventory-id');
        if (!inventoryId) {
            return;
        }

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
            if (!result) {
                return;
            }

            $.ajax({
                url: '/ajax/delete_stock_entry_item',
                method: 'POST',
                data: { inventory_id: inventoryId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() {
                    $.notify({ message: 'Tétel sikeresen törölve' }, { type: 'success' });
                    location.reload();
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

    // --- INLINE ITEM EDITING LOGIC ---
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
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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

    // --- GALLERY AND LABEL PRINT ---
    function refreshInventoryGallery(inventoryId) {
        if (!inventoryId) return;
        $.post('/load_inventory_gallery', { inventory_id: inventoryId, _token: $('meta[name="csrf-token"]').attr('content') }, function(html) {
            $('#inventory_gallery_' + inventoryId).html(html);
        }).fail(function() {
            console.error('Hiba a galéria betöltésekor: ' + inventoryId);
        });
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
            if (!$labelSelect.length) {
                return;
            }
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

        // Toggle item details when summary is clicked
        $(document).on('click', '.item-summary', function() {
            var targetId = $(this).data('target');
            $('#' + targetId).slideToggle(300);
            $(this).find('.fa-chevron-down').toggleClass('fa-chevron-up');
        });
        $('.sei_item_wrapper').each(function() {
            var inventoryId = $(this).data('inventory-id');
            refreshInventoryGallery(inventoryId);
        });

        $(document).on('change', '.inventory_images', function() {
            let inventoryId = $(this).data('inventoryId');
            let files = this.files;
            if (!inventoryId || !files.length) return;
            let formData = new FormData();
            formData.append('inventory_id', inventoryId);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            $.each(files, function(index, file) {
                formData.append('inventory_images[]', file);
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

        $(document).on('change', '.sei_label_type', function() {
            let $select = $(this);
            let inventoryId = $select.closest('.sei_item_wrapper').data('inventory-id');
            let productId = $('#print_label_btn_' + inventoryId).data('product-id');
            let type = $select.val();
            if (productId) {
                $('#print_label_btn_' + inventoryId).attr('href', '/product/printLabel?product_id=' + productId + '&label_type=' + type);
            }
        });

        $('.sei_label_type').trigger('change');
        $('.selectpicker').selectpicker();
    });

    Dropzone.autoDiscover = false;

    function onlyNumbers(str) {
        return /^\d+$/.test(str);
    }

    function buildSEIGallery($initScope, mod) {
        if (typeof $initScope != 'undefined' && $initScope.length) {
            $initScope.justifiedGallery({
                border: 1,
                rowHeight: 150,
                margins: 2,
                waitThumbnailsLoad: true,
                randomize: false,
            }).on('jg.complete', function() {
                $initScope.lightGallery({
                    thumbnail: true,
                    animateThumb: true,
                    showThumbByDefault: true,
                });
                setTimeout(function() {
                    $('.hidden_image_wrapper').each(function() {
                        $(this).width($('.sei_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                        $(this).height($('.sei_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                        $(this).css('top', $('.sei_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                        $(this).css('left', $('.sei_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
                    });
                }, 2000);
            });
            $initScope.on('onAfterOpen.lg', function(event) {
                $('body').addClass("overflow-hidden");
            });
            $initScope.on('onCloseAfter.lg', function(event) {
                $('body').removeClass("overflow-hidden");
            });
        }
    }

    function initSEIGallery(sei_id, mod) {
        var $initScope_implementation = $('#lightgallery_sei_images' + mod + '_' + sei_id);
        var images = $initScope_implementation.find('img').get();
        var allImagesLoaded = images.every(function(img) {
            return img.complete;
        });
        if (allImagesLoaded) {
            buildSEIGallery($initScope_implementation, mod);
        } else {
            $(images).on('load', function() {
                var allLoaded = images.every(function(img) {
                    return img.complete;
                });
                if (allLoaded) {
                    buildSEIGallery($initScope_implementation, mod);
                }
            });
        }
    }

    function checkImages(pit_id, mod) {
        var totalImages = $('#lightgallery_sei_images' + mod + '_' + pit_id + ' img').length;
        var loadedImages = 0;
        $('#lightgallery_sei_images' + mod + '_' + pit_id + ' img').each(function(i, v) {
            loadedImages++;
            if ((i + 1) == totalImages) {
                if (loadedImages == totalImages) {
                    $('#lightgallery_sei_images' + mod + '_' + pit_id).show();
                    $('#lightgallery_sei_images' + mod + '_' + pit_id).animate({ opacity: 1 }, 'slow');
                    initSEIGallery(pit_id, mod);
                }
            }
        });
    }

    function truncateToWords(text, wordLimit = 5) {
        if (!text) return '';
        const words = text.trim().split(/\s+/);
        if (words.length <= wordLimit) return text;
        return words.slice(0, wordLimit).join(' ') + '…';
    }

    function recalculatePriceFields($container, fieldPrefix) {
        var prefix = fieldPrefix || '';
        var beszerzesiAr = parseFloat($container.find('[name="' + prefix + 'beszerzesi_ar"]').val()) || 0;
        var eladasiAr = parseFloat($container.find('[name="' + prefix + 'eladasi_ar"]').val()) || 0;
        var weight = parseFloat($container.find('[name="' + prefix + 'weight"]').val()) || 0;

        var onePrice = beszerzesiAr * weight;
        var totalPrice = eladasiAr * weight;

        $container.find('[name="' + prefix + 'oneprice"]').val(onePrice > 0 ? onePrice : '');
        $container.find('[name="' + prefix + 'price"]').val(totalPrice > 0 ? totalPrice : '');
    }

    function recalculateItemPrice($container) {
        recalculatePriceFields($container, '');
    }

    function recalculateNewPrice() {
        var $container = $('.stock_entry_items_rows').find('.sei_empty_row');
        recalculatePriceFields($container, 'new_');
    }

    $(document).ready(function() {
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $(document).on('click', '#new_item_btn', function() {
            location.reload(); 
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
            sei_data.append('new_customer_marking_id', $container.find('[name="new_customer_marking_id"]').val());
            sei_data.append('new_vzj', $container.find('[name="new_customer_marking_id"] option:selected').text());
            sei_data.append('new_category_id', $container.find('[name="new_category_id"]').val());
            sei_data.append('new_beszerzesi_ar', $container.find('[name="new_beszerzesi_ar"]').val());
            sei_data.append('new_eladasi_ar', $container.find('[name="new_eladasi_ar"]').val());
            sei_data.append('new_vat_type', $container.find('[name="new_vat_type"]').val());
            sei_data.append('new_hallmarks', $container.find('[name="new_hallmarks"]').is(':checked') ? 1 : 0);
            sei_data.append('new_karat_id', $container.find('[name="new_karat_id"]').val());
            sei_data.append('new_size', $container.find('[name="new_size"]').val());
            sei_data.append('new_label_type', $container.find('[name="new_label_type"]').val());
            sei_data.append('category2_id', $container.find('[name="new_category2_id"]').val());

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
                    location.reload();
                    return;
                }

                var uploadRequests = $.map(inventoryIds, function(inventoryId) {
                    var fd = new FormData();
                    fd.append('inventory_id', inventoryId);
                    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
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
                        location.reload();
                    })
                    .fail(function() {
                        $.notify({ message: 'A tétel mentve lett, de a képfeltöltés közben hiba történt.' }, { type: 'warning' });
                        location.reload();
                    });
            }

            $.ajax({
                url: '/ajax/save_stock_entry_item',
                method: 'POST',
                data: sei_data,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    var inventoryIds = [];
                    if (response && $.isArray(response.inventory_ids)) {
                        inventoryIds = response.inventory_ids;
                    } else if (response && response.inventory_id) {
                        inventoryIds = [response.inventory_id];
                    }

                    if (!selectedFiles.length) {
                        $.notify({ message: 'Tétel sikeresen mentve' }, { type: 'success' });
                        location.reload();
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

        $('#uploadBtn').click(function() {
            const fileInput = $('#fileInput')[0];
            if (!fileInput.files || fileInput.files.length === 0) {
                $('#result').html('<div class="error">Please select a file</div>');
                return;
            }
            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);
            $('#progressBar').removeClass('hidden');
            $('#progress').css('width', '0%');
            $.ajax({
                url: '{{ route("stock_entry.import") }}',
                type: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#progress').css('width', percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success === true) {
                        $.notify({ icon: "done", message: response.message }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                    } else {
                        $.notify({ icon: "error", message: response.message }, { type: 'danger', timer: 3000, z_index: 1060, placement: { from: 'top', align: 'center' } });
                    }
                }
            });
        });

        ClassicEditor.create(document.querySelector('#editor')).catch(error => {});

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

        $(document).on('input', '[name="new_beszerzesi_ar"], [name="new_eladasi_ar"], [name="new_weight"]', function() {
            recalculateNewPrice();
        });

        $(document).on('input', '.sei_item_wrapper [name="beszerzesi_ar"], .sei_item_wrapper [name="eladasi_ar"], .sei_item_wrapper [name="weight"]', function() {
            recalculateItemPrice($(this).closest('.sei_item_wrapper'));
        });

        $(document).on('change', '#has_contract', function() {
            $('#contract_number').prop('disabled', !$(this).is(':checked'));
        });

        $('.captureButton').on('click', function() {
            $(this).next('.imageInput').click();
        });

        $('#fulfillment_date').on('change', function() {
            let date = $(this).val();
            let stock_entry_id = '{{ $stock_entry->stock_entry_id }}';
            $.ajax({
                method: "POST",
                url: "/ajax/update_fulfillment_date",
                data: { date: date, stock_entry_id: stock_entry_id }
            }).done(function(response) {
                if (response.success) {
                    $.notify({ icon: "done", message: 'Sikeres teljesítési dátum módosítás!' }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                }
            });
        });

        $('.imageInput').on('change', function() {
            let formData = new FormData();
            formData.append('sei_image', $(this)[0].files[0]);
            let inventory_id = $(this).prev('.captureButton').data('inventory_id');
            formData.append('inventory_id', inventory_id);
            $.ajax({
                url: '/ajax/saveAndUploadSEIImage',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $.notify({ icon: "done", message: response.message }, { type: 'success', timer: 3000, placement: { from: 'top', align: 'right' } });
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_sei_gallery",
                            data: { inventory_id: inventory_id, mod: '' }
                        }).done(function(html) {
                            $('.sei_gallery_container_' + inventory_id).html(html);
                            checkImages(inventory_id, '');
                            $('.dz-complete').remove();
                            $('.dz-message').show();
                        });
                    } else {
                        $.notify({ icon: "done", message: response.message }, { type: 'danger', timer: 3000, placement: { from: 'top', align: 'right' } });
                    }
                },
                error: function(error) {
                    $.notify({ icon: "done", message: 'Hiba a kép feltöltése közben!' }, { type: 'danger', timer: 3000, placement: { from: 'top', align: 'right' } });
                }
            });
        });

        $(document).on("change", "select[name=currency_id]", function() {
            let currency_id = $(this).val();
            if (currency_id != "") {
                $.ajax({
                    method: "POST",
                    url: "/ajax/get_currency_by_id",
                    data: { currency_id: currency_id }
                }).done(function(response) {
                    if (response.editable == 1) {
                        $('input[name=currency_value]').removeAttr('disabled');
                    } else {
                        $('input[name=currency_value]').attr('disabled', true);
                    }
                    $('input[name=currency_value]').val(response.value);
                });
            } else {
                $('input[name=currency_value]').val(1);
            }
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

        $('.sei_name').select2({
            theme: 'bootstrap4',
            tags: true,
            language: { inputTooShort: function() { return 'Kérjük, adjon meg 2 vagy több karaktert'; } },
            ajax: {
                url: '/ajax/get_all_product',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { term: params.term }; },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(product) {
                            const description = product.short_description || product.long_description || '';
                            const shortDesc = truncateToWords(description, 5);
                            let displayText = product.name;
                            if (shortDesc) displayText += ` – ${shortDesc}`;
                            return { id: product.product_id, text: displayText };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Termékek'
        });

        function refreshMarkingPickers() {
            if ($.fn.selectpicker) {
                $('#vzj').selectpicker('refresh');
                $('#new_vzj').selectpicker('refresh');
                $('select.customer-marking-select-inline').selectpicker('refresh');
            }
        }

        function buildCustomerMarkingOptions(markings, selectedId) {
            let options = '<option value="">- válasszon -</option>';
            $.each(markings || [], function(_, marking) {
                let isSelected = String(selectedId || '') === String(marking.id);
                options += '<option value="' + marking.id + '" data-price="' + (marking.price || '') + '" data-vat-type-id="' + (marking.vat_type_id || '') + '" data-product-status-id="' + (marking.product_status_id || '') + '"' + (isSelected ? ' selected' : '') + '>' + (marking.name || '') + '</option>';
            });
            return options;
        }

        function applyDefaultNewStatusFromMarking(marking) {
            if (!marking || !marking.product_status_id) {
                return;
            }

            $('select[name="new_statuses[]"]').val(String(marking.product_status_id));
            if ($.fn.selectpicker) {
                $('select[name="new_statuses[]"]').selectpicker('refresh');
            }
        }

        function syncHeaderToNewDefaults() {
            $('input[name="new_beszerzesi_ar"]').val($('input[name="beszerzesi_ar"]').val());
            $('input[name="new_eladasi_ar"]').val($('input[name="eladasi_ar"]').val());
            $('select[name="new_vat_type"]').val($('select[name="vat_type"]').val());
            $('#new_vzj').val($('#vzj').val());
            $('select[name="new_karat_id"]').val($('#header_karat_id').val());
            $('select[name="new_label_type"]').val($('#header_label_type').val());
            recalculateNewPrice();
            if ($.fn.selectpicker) {
                $('select[name="new_vat_type"]').selectpicker('refresh');
                $('#new_vzj').selectpicker('refresh');
                $('select[name="new_karat_id"]').selectpicker('refresh');
                $('select[name="new_label_type"]').selectpicker('refresh');
            }

            // Ensure new item status follows the selected customer marking's product_status_id.
            applySelectedMarkingDataToNew();
        }

        function applySelectedMarkingDataToHeader() {
            let $selected = $('#vzj option:selected');
            let selectedPrice = $selected.data('price');
            let selectedVatTypeId = $selected.data('vat-type-id');

            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                $('input[name="eladasi_ar"]').val(selectedPrice);
            } else if ($('#vzj').val() === '') {
                $('input[name="eladasi_ar"]').val('');
            }
            if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                $('select[name="vat_type"]').val(String(selectedVatTypeId));
                if ($.fn.selectpicker) {
                    $('select[name="vat_type"]').selectpicker('refresh');
                }
            } else if ($('#vzj').val() === '') {
                $('select[name="vat_type"]').val('');
                if ($.fn.selectpicker) {
                    $('select[name="vat_type"]').selectpicker('refresh');
                }
            }
            syncHeaderToNewDefaults();
        }

        function applySelectedMarkingDataToNew() {
            let $selected = $('#new_vzj option:selected');
            let selectedPrice = $selected.data('price');
            let selectedVatTypeId = $selected.data('vat-type-id');
            let selectedProductStatusId = $selected.data('product-status-id');

            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                $('input[name="new_eladasi_ar"]').val(selectedPrice);
            } else if ($('#new_vzj').val() === '') {
                $('input[name="new_eladasi_ar"]').val('');
            }

            if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                $('select[name="new_vat_type"]').val(String(selectedVatTypeId));
            } else if ($('#new_vzj').val() === '') {
                $('select[name="new_vat_type"]').val('');
            }

            if (selectedProductStatusId !== undefined && selectedProductStatusId !== null && selectedProductStatusId !== '') {
                $('select[name="new_statuses[]"]').val(String(selectedProductStatusId));
            } else if ($('#new_vzj').val() === '') {
                $('select[name="new_statuses[]"]').val('');
            }

            if ($.fn.selectpicker) {
                $('select[name="new_vat_type"]').selectpicker('refresh');
                $('select[name="new_statuses[]"]').selectpicker('refresh');
            }

            recalculateNewPrice();
        }

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

        function normalizeCustomerMarkings(markingsRaw) {
            if (!markingsRaw) {
                return [];
            }

            if (Array.isArray(markingsRaw)) {
                return markingsRaw.map(function(marking) {
                    return {
                        id: marking.id,
                        name: marking.name,
                        default: parseInt(marking.default || 0, 10),
                        status: parseInt(marking.status || 0, 10),
                        price: marking.price,
                        vat_type_id: marking.vat_type_id,
                        product_status_id: marking.product_status_id
                    };
                });
            }

            // Fallback for key-value payload shape, e.g. pluck('name','id')
            return Object.keys(markingsRaw).map(function(id) {
                return {
                    id: id,
                    name: markingsRaw[id],
                    default: 0,
                    status: 1,
                    price: null,
                    vat_type_id: null,
                    product_status_id: null
                };
            });
        }

        function applyCustomerMarkings(markings, selectedId) {
            let options = buildCustomerMarkingOptions(markings, selectedId);
            $('#vzj').html(options).val(selectedId || '');
            $('#new_vzj').html(options).val(selectedId || '');

            const availableIds = (markings || []).map(function(m) { return String(m.id); });
            $('select.customer-marking-select-inline').each(function() {
                let $select = $(this);
                let preferred = String($select.data('selectedId') || $select.val() || '');
                let inlineSelected = availableIds.indexOf(preferred) !== -1 ? preferred : String(selectedId || '');

                $select.html(options).val(inlineSelected);
                $select.attr('data-selected-id', inlineSelected);
            });
            refreshMarkingPickers();
            applySelectedMarkingDataToHeader();
        }

        $(document).on('change', 'select.customer-marking-select-inline', function() {
            $(this).attr('data-selected-id', $(this).val() || '');
            applySelectedMarkingDataToInline($(this));
        });

        $('#vzj').on('change', function() {
            applySelectedMarkingDataToHeader();
        });

        $('#new_vzj').on('change', function() {
            applySelectedMarkingDataToNew();
        });

        $('input[name="eladasi_ar"], input[name="beszerzesi_ar"]').on('input', function() {
            syncHeaderToNewDefaults();
        });

        $('select[name="vat_type"]').on('change', function() {
            syncHeaderToNewDefaults();
        });

        $('#header_karat_id').on('change', function() {
            syncHeaderToNewDefaults();
        });

        $('#header_label_type').on('change', function() {
            syncHeaderToNewDefaults();
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

                    let markings = normalizeCustomerMarkings(o.customer_markings).filter(function(m) {
                        return parseInt(m.status || 0, 10) === 1;
                    });
                    window.currentCustomerMarkings = markings;
                    let defaultMarking = markings.find(function(m) { return parseInt(m.default, 10) === 1; });
                    let selectedMarkingId = defaultMarking ? defaultMarking.id : (markings[0] ? markings[0].id : '');
                    applyCustomerMarkings(markings, selectedMarkingId);
                    applyDefaultNewStatusFromMarking(defaultMarking || markings[0] || null);
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
                window.currentCustomerMarkings = [];
                applyCustomerMarkings([], '');
                $('select[name="new_statuses[]"]').val('');
                if ($.fn.selectpicker) {
                    $('select[name="new_statuses[]"]').selectpicker('refresh');
                }
                $('select[name=customer_zip]').focus();
            }
        });

        applyCustomerMarkings(window.currentCustomerMarkings || [], $('#vzj').val());

        function saveStockEntry(callback, print = false, close = false) {
            if (!$("#stock_entry_form")[0].checkValidity()) {
                $('<input type="submit">').hide().appendTo($('#stock_entry_form')).click().remove();
                return false;
            }
            var form = document.getElementById('stock_entry_form');
            var formData = new FormData(form);
            formData.delete('_method');   // remove the spoofing field  
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
                        location.href = "/stock_entry/{{ $stock_entry->stock_entry_id }}/edit";
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

        $('.copy_stock_entry').click(function() {
            swal({
                text: "Biztosan másolja ezt az ékszer bevételezést?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Nem',
                buttonsStyling: false
            }, function() {
                $.ajax({
                    method: "POST",
                    url: "/ajax/copy_stock_entry",
                    data: { stock_entry_id: '{{ $stock_entry->stock_entry_id }}' }
                }).done(function(msg) {
                    window.location.href = "/stock_entry/" + msg + "/edit";
                });
            });
        });

        $('.cancel_stock_entry').click(function() {
            swal({
                text: "Biztosan sztornózza ezt az ékszer bevételezést?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-warning',
                cancelButtonClass: 'btn btn-danger',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Nem',
                buttonsStyling: false
            }, function() {
                setTimeout(function() {
                    swal({
                        title: 'Sztornó oka:',
                        html: '<p><input type="text" class="form-control" name="reason"></p>',
                        showCancelButton: true,
                        closeOnConfirm: false,
                        allowOutsideClick: false,
                        confirmButtonText: 'Sztornó',
                        cancelButtonText: "Mégse",
                    }, function() {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/cancel_stock_entry",
                            data: { stock_entry_id: '{{ $stock_entry->stock_entry_id }}', storno_reason: $('input[name="reason"]').val() }
                        }).done(function(response) {
                            if (response.data.success == 'success') {
                                window.location.href = "/stock_entry/" + response.data.stock_entry_id + "/edit";
                            } else {
                                $.notify({ icon: "done", message: response.data.message }, { type: 'danger', timer: 3000, placement: { from: 'top', align: 'right' } });
                            }
                        });
                    });
                }, 500);
            });
        });
    });
    // Galéria betöltése (inventory + termék képek)
function loadGallery(inventoryId, productId) {
    if (!inventoryId) return;
    $.post('/load_inventory_gallery', {
        inventory_id: inventoryId,
        product_id: productId,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(html) {
        var $gallery = $('#inventory_gallery_' + inventoryId);
        $gallery.html(html);
        $gallery.show();   // Make the gallery container visible
    }).fail(function() {
        console.error('Hiba a galéria betöltésekor');
    });
}
// Képek feltöltése (csak inventory-hoz)
function uploadImages(inventoryId, files) {
    if (!inventoryId || !files.length) return;

    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
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
            // Galéria frissítése
            let productId = $('.load-gallery-btn[data-inventory-id="' + inventoryId + '"]').data('product-id');
            loadGallery(inventoryId, productId);
        },
        error: function() {
            alert('Hiba a képfeltöltés során.');
        }
    });
}
$(document).ready(function() {
    
    $(document).on('click', '.delete_inventory_image', function() {
    var imgId = $(this).data('img_id');
    if (!imgId) return;

    if (!confirm('Biztosan törli ezt a képet?')) return;

    $.ajax({
        url: '/ajax/delete_inventory_image',
        type: 'POST',
        data: {
            img_id: imgId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Refresh the gallery for the current inventory item
                var $gallery = $(this).closest('[id^="inventory_gallery_"]');
                var inventoryId = $gallery.attr('id').replace('inventory_gallery_', '');
                refreshInventoryGallery(inventoryId);
            } else {
                alert('Hiba a kép törlésekor.');
            }
        }.bind(this),
        error: function() {
            alert('Hálózati hiba történt.');
        }
    });
});
// Képek betöltése gombra
$(document).on('click', '.load-gallery-btn', function() {
    let inventoryId = $(this).data('inventory-id');
    let productId = $(this).data('product-id');
    loadGallery(inventoryId, productId);
});

// Kép készítése gomb -> fájlválasztó megnyitása
$(document).on('click', '.capture-gallery-btn', function() {
    let $btn = $(this);
    let inventoryId = $btn.data('inventory-id');
    let $wrapper = $btn.closest('.d-flex');
    let $fileInput = $wrapper.find('.gallery-file-input[data-inventory-id="' + inventoryId + '"]').first();

    if ($fileInput.length) {
        $fileInput.trigger('click');
    } else {
        console.error('Nem található fájl input az inventory azonosítóhoz:', inventoryId);
    }
});

// Fájl kiválasztás utáni feltöltés
$(document).on('change', '.gallery-file-input', function() {
    let inventoryId = $(this).data('inventory-id');
    let files = this.files;
    uploadImages(inventoryId, files);
    $(this).val(''); // reset, hogy újra lehessen választani
})
});
</script>
@endpush