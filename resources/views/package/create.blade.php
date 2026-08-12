@extends('layouts.app', [
    'activePage' => 'package', 
    'activeButton' => 'package', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomag létrehozása'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Csomag létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('package.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form id="package_form" method="post" action="{{ route('package.store') }}">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Alap információk</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('order_id') ? ' has-danger' : '' }}">
                                                Rendelés *
                                            </label>
                                            <select class="form-control{{ $errors->has('order_id') ? ' is-invalid' : '' }}" 
                                                    name="order_id" required id="order_selector">
                                                <option value="">- válasszon rendelést -</option>
                                                @foreach($orders as $order)
                                                    <option value="{{ $order->id }}" 
                                                        {{ old('order_id') == $order->id ? 'selected' : '' }}
                                                        data-shipping="{{ $order->shipping_name }}">
                                                        @if ($order->getJsonField('billing'))
                                                            {{ $order->getJsonField('billing')['last_name'] }} 
                                                            {{ $order->getJsonField('billing')['first_name'] }} - 
                                                            {{ $order->getJsonField('billing')['address_1'] }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'order_id'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('shipping_service_id') ? ' has-danger' : '' }}">
                                                Szállítási szolgáltató *
                                            </label>
                                            <select class="form-control{{ $errors->has('shipping_service_id') ? ' is-invalid' : '' }}" 
                                                    name="shipping_service_id" required id="shipping_service_selector">
                                                <option value="">- válasszon szolgáltatót -</option>
                                                @foreach($shippingServices as $service)
                                                    <option value="{{ $service->id }}"
                                                            data-code="{{ $service->code }}"
                                                            data-base-price="{{ $service->base_price }}"
                                                            data-supports-cod="{{ $service->supports_cod ? 1 : 0 }}"
                                                        {{ (string) old('shipping_service_id') === (string) $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }} ({{ number_format($service->base_price, 0, ',', ' ') }} Ft)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'shipping_service_id'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('shipping_price') ? ' has-danger' : '' }}">
                                                Szállítási ár (Ft)
                                            </label>
                                            <input type="number" step="0.01" min="0" class="form-control{{ $errors->has('shipping_price') ? ' is-invalid' : '' }}" 
                                                   name="shipping_price" value="{{ old('shipping_price') }}"
                                                   placeholder="Szállítási ár" id="shipping_price_input">
                                            @include('alerts.feedback', ['field' => 'shipping_price'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('tracking_number') ? ' has-danger' : '' }}">
                                                Nyomkövetési szám
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('tracking_number') ? ' is-invalid' : '' }}" 
                                                   name="tracking_number" value="{{ old('tracking_number') }}" 
                                                   placeholder="Nyomkövetési szám">
                                            @include('alerts.feedback', ['field' => 'tracking_number'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('package_size_id') ? ' has-danger' : '' }}">
                                                Csomag méret
                                            </label>
                                            <select class="form-control{{ $errors->has('package_size_id') ? ' is-invalid' : '' }}" 
                                                    name="package_size_id">
                                                <option value="">- válasszon méretet -</option>
                                                @foreach($packageSizes as $size)
                                                    <option value="{{ $size->id }}" 
                                                        {{ old('package_size_id') == $size->id ? 'selected' : '' }}>
                                                        {{ $size->name }} ({{ $size->dimensions }})
                                                        @if($size->weight_limit)
                                                            - max {{ $size->weight_limit }} kg
                                                        @endif
                                                        @if($size->price > 0)
                                                            - {{ number_format($size->price, 0, ',', ' ') }} Ft
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'package_size_id'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('discount_price_id') ? ' has-danger' : '' }}">
                                                Kedvezmény
                                            </label>
                                            <select class="form-control{{ $errors->has('discount_price_id') ? ' is-invalid' : '' }}" 
                                                    name="discount_price_id">
                                                <option value="">- válasszon kedvezményt -</option>
                                                @foreach($discountPrices as $discount)
                                                    <option value="{{ $discount->id }}" 
                                                        {{ old('discount_price_id') == $discount->id ? 'selected' : '' }}
                                                        data-type="{{ $discount->type }}"
                                                        data-value="{{ $discount->value }}"
                                                        data-max-discount="{{ $discount->max_discount_amount }}">
                                                        {{ $discount->name }} ({{ $discount->display_value }})
                                                        @if($discount->min_order_amount)
                                                            - min. {{ number_format($discount->min_order_amount, 0, ',', ' ') }} Ft
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'discount_price_id'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('weight') ? ' has-danger' : '' }}">
                                                Súly (kg)
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}" 
                                                   name="weight" value="{{ old('weight') }}" 
                                                   placeholder="Súly kilogrammban">
                                            @include('alerts.feedback', ['field' => 'weight'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">
                                                Státusz *
                                            </label>
                                            <select class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" 
                                                    name="status" required>
                                                <option value="created" {{ old('status', 'created') == 'created' ? 'selected' : '' }}>Létrehozva</option>
                                                <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Kiszállítva</option>
                                                <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Kézbesítve</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'status'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('notes') ? ' has-danger' : '' }}">
                                                Megjegyzés
                                            </label>
                                            <textarea class="form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" 
                                                      name="notes" rows="3" 
                                                      placeholder="Egyéb megjegyzések">{{ old('notes') }}</textarea>
                                            @include('alerts.feedback', ['field' => 'notes'])
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Termék tételek</h6>
                                
                                <div id="order_items_container" style="display: none;">
                                    <p class="text-muted" id="no_items_message">Nincsenek elérhető tételek</p>
                                    <div id="items_list"></div>
                                </div>

                                <div class="alert alert-info" id="no_order_selected">
                                    Válasszon ki egy rendelést a tételek megjelenítéséhez
                                </div>

                                <!-- Cash on Delivery Section (only for MPL) -->
                                <div id="cash_on_delivery_section" style="display: none;">
                                    <h6 class="heading-small text-muted mb-4">Utánvét</h6>
                                    <div class="alert alert-warning">
                                        <small>
                                            <strong>Utánvét információ:</strong><br>
                                            A vevő a csomag átvételkor fizet a futárnak. Az összeget a futár később átutalja a megadott bankszámlaszámra.
                                        </small>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('cash_on_delivery_amount') ? ' has-danger' : '' }}">
                                                    Utánvét összege *
                                                </label>
                                                <input type="number" step="0.01" class="form-control{{ $errors->has('cash_on_delivery_amount') ? ' is-invalid' : '' }}" 
                                                       name="cash_on_delivery_amount" value="{{ old('cash_on_delivery_amount') }}" 
                                                       placeholder="0.00" id="cash_on_delivery_amount">
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_amount'])
                                                <small class="form-text text-muted">A vevő által fizetendő összeg forintban</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('cash_on_delivery_bank_account') ? ' has-danger' : '' }}">
                                                    Bankszámlaszám *
                                                </label>
                                                <input type="text" class="form-control{{ $errors->has('cash_on_delivery_bank_account') ? ' is-invalid' : '' }}" 
                                                       name="cash_on_delivery_bank_account" value="{{ old('cash_on_delivery_bank_account') }}" 
                                                       placeholder="12345678-12345678-12345678">
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_bank_account'])
                                                <small class="form-text text-muted">A számla formátuma: 8-8-8 számjegy</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('cash_on_delivery_recipient') ? ' has-danger' : '' }}">
                                                    Kedvezményezett neve *
                                                </label>
                                                <input type="text" class="form-control{{ $errors->has('cash_on_delivery_recipient') ? ' is-invalid' : '' }}" 
                                                       name="cash_on_delivery_recipient" value="{{ old('cash_on_delivery_recipient') }}" 
                                                       placeholder="Kedvezményezett teljes neve">
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_recipient'])
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('cash_on_delivery_bank_name') ? ' has-danger' : '' }}">
                                                    Bank neve
                                                </label>
                                                <input type="text" class="form-control{{ $errors->has('cash_on_delivery_bank_name') ? ' is-invalid' : '' }}" 
                                                       name="cash_on_delivery_bank_name" value="{{ old('cash_on_delivery_bank_name') }}" 
                                                       placeholder="Bank neve">
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_bank_name'])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('cash_on_delivery_notes') ? ' has-danger' : '' }}">
                                                    Utánvét megjegyzés
                                                </label>
                                                <textarea class="form-control{{ $errors->has('cash_on_delivery_notes') ? ' is-invalid' : '' }}" 
                                                          name="cash_on_delivery_notes" rows="2" 
                                                          placeholder="Egyéb megjegyzések az utánvétel kapcsán">{{ old('cash_on_delivery_notes') }}</textarea>
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_notes'])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Cash on Delivery Section -->
                            </div>
                        </div>

                        <!-- Price Calculation Preview -->
                        <div class="row mt-4" id="price_calculation_section" style="display: none;">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Ár kalkuláció</h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <strong>Alapár:</strong>
                                                <span id="base_price_display">0 Ft</span>
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>Kedvezmény:</strong>
                                                <span id="discount_display">0 Ft</span>
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>Végső ár:</strong>
                                                <span id="final_price_display" class="font-weight-bold">0 Ft</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Csomag létrehozása
                                </button>
                                <a href="{{ route('package.index') }}" class="btn btn-warning">
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
            // Order selector change
            $('#order_selector').change(function(){
                var orderId = $(this).val();
                loadOrderItems(orderId);
            });

            // Shipping service selector change
            $('#shipping_service_selector').change(function(){
                toggleCashOnDeliverySection();
                syncShippingPrice();
            });

            // Package size and discount price change for price calculation
            $('select[name="package_size_id"], select[name="discount_price_id"]').change(function(){
                calculatePrice();
            });

            function loadOrderItems(orderId) {
                if (!orderId) {
                    $('#no_order_selected').show();
                    $('#order_items_container').hide();
                    return;
                }

                $('#no_order_selected').hide();
                $('#order_items_container').show();

                $.ajax({
                    url: '{{ route("package.order-items", "") }}/' + orderId,
                    type: 'GET',
                    success: function(response) {
                        if (response.length > 0) {
                            $('#no_items_message').hide();
                            $('#items_list').empty();
                            
                            response.forEach(function(item) {
                                var itemHtml = `
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h6 class="card-title">${item.product_name}</h6>
                                                    <p class="card-text text-muted small">
                                                        SKU: ${item.sku}<br>
                                                        Egységár: ${item.price} Ft<br>
                                                        Rendelt mennyiség: ${item.quantity} db
                                                    </p>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Csomagolni kívánt mennyiség</label>
                                                        <input type="number" 
                                                               name="items[${item.id}][quantity]" 
                                                               class="form-control package-quantity" 
                                                               value="${item.quantity}" 
                                                               min="1" 
                                                               max="${item.quantity}"
                                                               data-item-id="${item.id}">
                                                        <input type="hidden" 
                                                               name="items[${item.id}][order_item_id]" 
                                                               value="${item.id}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#items_list').append(itemHtml);
                            });
                        } else {
                            $('#no_items_message').show();
                            $('#items_list').empty();
                        }
                    },
                    error: function() {
                        alert('Hiba történt a tételek betöltése során!');
                    }
                });
            }

            function toggleCashOnDeliverySection() {
                var cashOnDeliverySection = $('#cash_on_delivery_section');
                var selectedOption = $('#shipping_service_selector option:selected');
                var supportsCod = parseInt(selectedOption.data('supports-cod') || 0, 10) === 1;
                
                if (supportsCod) {
                    cashOnDeliverySection.show();
                    // Make cash on delivery fields required
                    $('input[name="cash_on_delivery_amount"]').prop('required', true);
                    $('input[name="cash_on_delivery_bank_account"]').prop('required', true);
                    $('input[name="cash_on_delivery_recipient"]').prop('required', true);
                } else {
                    cashOnDeliverySection.hide();
                    // Remove required attribute
                    $('input[name="cash_on_delivery_amount"]').prop('required', false);
                    $('input[name="cash_on_delivery_bank_account"]').prop('required', false);
                    $('input[name="cash_on_delivery_recipient"]').prop('required', false);
                }
            }

            function syncShippingPrice() {
                var selectedOption = $('#shipping_service_selector option:selected');
                var basePrice = selectedOption.data('base-price');
                var priceInput = $('#shipping_price_input');

                if ((priceInput.val() === '' || priceInput.val() === null) && basePrice !== undefined) {
                    priceInput.val(basePrice);
                }
            }

            function calculatePrice() {
                var packageSizeId = $('select[name="package_size_id"]').val();
                var discountPriceId = $('select[name="discount_price_id"]').val();
                var packageSizePrice = 0;
                var discountAmount = 0;
                var finalPrice = 0;

                // Get package size price
                if (packageSizeId) {
                    var selectedSize = $('select[name="package_size_id"] option:selected');
                    // Extract price from the option text or use data attribute
                    // This is a simplified version - you might want to store prices in data attributes
                    var sizeText = selectedSize.text();
                    var priceMatch = sizeText.match(/(\d[\d\s]*) Ft$/);
                    if (priceMatch) {
                        packageSizePrice = parseFloat(priceMatch[1].replace(/\s/g, '')) || 0;
                    }
                }

                // Calculate discount
                if (discountPriceId) {
                    var selectedDiscount = $('select[name="discount_price_id"] option:selected');
                    var discountType = selectedDiscount.data('type');
                    var discountValue = parseFloat(selectedDiscount.data('value')) || 0;
                    var maxDiscount = parseFloat(selectedDiscount.data('max-discount')) || 0;

                    if (discountType === 'percentage') {
                        discountAmount = packageSizePrice * (discountValue / 100);
                        if (maxDiscount > 0 && discountAmount > maxDiscount) {
                            discountAmount = maxDiscount;
                        }
                    } else if (discountType === 'fixed') {
                        discountAmount = discountValue;
                    }
                }

                finalPrice = Math.max(0, packageSizePrice - discountAmount);

                // Update display
                $('#base_price_display').text(formatCurrency(packageSizePrice));
                $('#discount_display').text(formatCurrency(discountAmount));
                $('#final_price_display').text(formatCurrency(finalPrice));

                // Show/hide price calculation section
                if (packageSizeId) {
                    $('#price_calculation_section').show();
                } else {
                    $('#price_calculation_section').hide();
                }
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('hu-HU', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount) + ' Ft';
            }

            // Initialize on page load
            var initialOrder = $('#order_selector').val();
            if (initialOrder) {
                loadOrderItems(initialOrder);
            }

            toggleCashOnDeliverySection();
            syncShippingPrice();
                
            // Initialize price calculation if there are values
            calculatePrice();

            // Bank account number formatting
            $('input[name="cash_on_delivery_bank_account"]').on('input', function() {
                var value = $(this).val().replace(/-/g, '').replace(/\s/g, '');
                if (value.length > 0) {
                    value = value.match(new RegExp('.{1,8}', 'g')).join('-');
                }
                $(this).val(value);
            });
        });
    </script>
@endpush