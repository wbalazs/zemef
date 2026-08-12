{{-- resources/views/customercard/edit.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'customercard', 
    'activeButton' => 'customercard', 
    'title' => 'Zemef ERP', 
    'navName' => 'Ügyfél Kártya szerkesztése'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Ügyfél Kártya szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('customercard.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form id="customer_card_form" method="post" action="{{ route('customercard.update', $customercard->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Kártya alap adatai</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                Név *
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                                   name="name" maxlength="255" value="{{ old('name', $customercard->name) }}" 
                                                   placeholder="Kártya neve" required autofocus>
                                            @include('alerts.feedback', ['field' => 'name'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('code') ? ' has-danger' : '' }}">
                                                Kód *
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" 
                                                   name="code" maxlength="50" value="{{ old('code', $customercard->code) }}" 
                                                   placeholder="Egyedi kód" required>
                                            @include('alerts.feedback', ['field' => 'code'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('type') ? ' has-danger' : '' }}">
                                                Típus *
                                            </label>
                                            <select class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" 
                                                    name="type" required id="type_selector">
                                                <option value="">- válasszon típust -</option>
                                                <option value="percent" {{ old('type', $customercard->type) == 'percent' ? 'selected' : '' }}>Százalékos</option>
                                                <option value="price" {{ old('type', $customercard->type) == 'price' ? 'selected' : '' }}>Fix összeg</option>
                                                <option value="product_item" {{ old('type', $customercard->type) == 'product_item' ? 'selected' : '' }}>Termék alapú</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'type'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="value_field">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('value') ? ' has-danger' : '' }}">
                                                Érték *
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}" 
                                                   name="value" value="{{ old('value', $customercard->value) }}" 
                                                   placeholder="" id="value_input">
                                            @include('alerts.feedback', ['field' => 'value'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('start_date') ? ' has-danger' : '' }}">
                                                Kezdő dátum *
                                            </label>
                                            <input type="date" class="form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" 
                                                   name="start_date" value="{{ old('start_date', $customercard->start_date->format('Y-m-d')) }}" required>
                                            @include('alerts.feedback', ['field' => 'start_date'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('end_date') ? ' has-danger' : '' }}">
                                                Vég dátum *
                                            </label>
                                            <input type="date" class="form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}" 
                                                   name="end_date" value="{{ old('end_date', $customercard->end_date->format('Y-m-d')) }}" required>
                                            @include('alerts.feedback', ['field' => 'end_date'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="1" 
                                                       name="is_active" {{ old('is_active', $customercard->is_active) ? 'checked' : '' }}>
                                                <span class="form-check-sign"></span>
                                                Aktív
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Szabályok és korlátozások</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Üzletek</label>
                                            <select multiple name="businesses[]" class="form-control select2" 
                                                    data-placeholder="- válasszon üzleteket -">
                                                @foreach($businesses as $business)
                                                    <option value="{{ $business->business_id }}" 
                                                        {{ in_array($business->business_id, old('businesses', $customercard->businesses->pluck('business_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $business->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Ügyfelek</label>
                                            <select multiple name="customers[]" class="form-control select2" 
                                                    data-placeholder="- válasszon ügyfeleket -">
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->customer_id }}"
                                                        {{ in_array($customer->customer_id, old('customers', $customercard->customers->pluck('customer_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="products_field" style="display: none;">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Termékek</label>
                                            <select multiple name="products[]" class="form-control select2" 
                                                    data-placeholder="- válasszon termékeket -">
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_id }}"
                                                        {{ in_array($product->product_id, old('products', $customercard->products->pluck('product_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="product_rules_fields">
                                    <div class="col-lg-12">
                                        <h6 class="heading-small text-muted mb-4">Termék szabályok</h6>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">Termék típusok</label>
                                            @foreach($productTypes as $type)
                                                @php
                                                    $typeRule = $customercard->productTypeRules->where('product_type_id', $type->product_type_id)->first();
                                                    $percentValue = old('product_type_rules.' . $type->product_type_id, $typeRule ? $typeRule->percent : '');
                                                @endphp
                                                <div class="row mb-2">
                                                    <div class="col-8">
                                                        <label class="form-control-label">{{ $type->name }}</label>
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="number" step="0.01" min="0" max="100" 
                                                               class="form-control" 
                                                               name="product_type_rules[{{ $type->product_type_id }}]" 
                                                               value="{{ $percentValue }}"
                                                               placeholder="%">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="form-group">
                                            <label class="form-control-label">Termék csoportok</label>
                                            @foreach($productGroups as $group)
                                                @php
                                                    $groupRule = $customercard->productGroupRules->where('product_group_id', $group->product_group_id)->first();
                                                    $percentValue = old('product_group_rules.' . $group->product_group_id, $groupRule ? $groupRule->percent : '');
                                                @endphp
                                                <div class="row mb-2">
                                                    <div class="col-8">
                                                        <label class="form-control-label">{{ $group->name }}</label>
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="number" step="0.01" min="0" max="100" 
                                                               class="form-control" 
                                                               name="product_group_rules[{{ $group->product_group_id }}]" 
                                                               value="{{ $percentValue }}"
                                                               placeholder="%">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Módosítások mentése
                                </button>
                                <a href="{{ route('customercard.index') }}" class="btn btn-warning">
                                    <i class="fa fa-arrow-left"></i> Mégse
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Type selector change
            $('#type_selector').change(function(){
                var type = $(this).val();
                toggleFields(type);
            });

            function toggleFields(type) {
                var valueField = $('#value_field');
                var productsField = $('#products_field');
                var productRulesFields = $('#product_rules_fields');
                var valueInput = $('#value_input');

                switch(type) {
                    case 'percent':
                        valueField.show();
                        productsField.hide();
                        productRulesFields.show();
                        valueInput.attr('placeholder', 'Százalék érték (pl. 15)');
                        break;
                    case 'price':
                        valueField.show();
                        productsField.hide();
                        productRulesFields.hide();
                        valueInput.attr('placeholder', 'Fix összeg HUF-ban (pl. 1000)');
                        break;
                    case 'product_item':
                        valueField.hide();
                        productsField.show();
                        productRulesFields.hide();
                        break;
                    default:
                        valueField.hide();
                        productsField.hide();
                        productRulesFields.hide();
                }
            }

            // Initialize fields on page load
            var initialType = $('#type_selector').val();
            toggleFields(initialType);

            // Date validation
            $('input[name="start_date"], input[name="end_date"]').change(function(){
                var startDate = new Date($('input[name="start_date"]').val());
                var endDate = new Date($('input[name="end_date"]').val());
                
                if (startDate && endDate && startDate > endDate) {
                    alert('A kezdő dátum nem lehet későbbi, mint a vég dátum!');
                    $('input[name="end_date"]').val('');
                }
            });
        });
    </script>
@endpush