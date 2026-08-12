@extends('layouts.app', [
    'activePage' => 'package', 
    'activeButton' => 'package', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomag szerkesztése'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Csomag szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('package.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form id="package_form" method="post" action="{{ route('package.update', $package->id) }}">
                    @csrf
                    @method('patch')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Alap információk</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Csomag szám</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $package->package_number }}" readonly>
                                            <small class="form-text text-muted">A csomag szám automatikusan generált</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Rendelés</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $package->order->order_id ?? 'Ismeretlen' }} - {{ $package->order->getJsonField('billing')['last_name'] ?? '' }} {{ $package->order->getJsonField('billing')['first_name'] ?? '' }}" readonly>
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
                                                @foreach($shippingServices as $service)
                                                    <option value="{{ $service->id }}"
                                                            data-code="{{ $service->code }}"
                                                            data-base-price="{{ $service->base_price }}"
                                                            data-supports-cod="{{ $service->supports_cod ? 1 : 0 }}"
                                                        {{ (string) old('shipping_service_id', $package->shipping_service_id) === (string) $service->id ? 'selected' : '' }}>
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
                                                   name="shipping_price" value="{{ old('shipping_price', $package->shipping_price) }}" 
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
                                                   name="tracking_number" value="{{ old('tracking_number', $package->tracking_number) }}" 
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
                                                    name="package_size_id" id="package_size_selector">
                                                <option value="">- válasszon méretet -</option>
                                                @foreach($packageSizes as $size)
                                                    <option value="{{ $size->id }}" 
                                                        {{ old('package_size_id', $package->package_size_id) == $size->id ? 'selected' : '' }}
                                                        data-price="{{ $size->price }}">
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
                                                    name="discount_price_id" id="discount_price_selector">
                                                <option value="">- válasszon kedvezményt -</option>
                                                @foreach($discountPrices as $discount)
                                                    <option value="{{ $discount->id }}" 
                                                        {{ old('discount_price_id', $package->discount_price_id) == $discount->id ? 'selected' : '' }}
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
                                                   name="weight" value="{{ old('weight', $package->weight) }}" 
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
                                                <option value="created" {{ old('status', $package->status) == 'created' ? 'selected' : '' }}>Létrehozva</option>
                                                <option value="shipped" {{ old('status', $package->status) == 'shipped' ? 'selected' : '' }}>Kiszállítva</option>
                                                <option value="delivered" {{ old('status', $package->status) == 'delivered' ? 'selected' : '' }}>Kézbesítve</option>
                                                <option value="cancelled" {{ old('status', $package->status) == 'cancelled' ? 'selected' : '' }}>Törölve</option>
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
                                                      placeholder="Egyéb megjegyzések">{{ old('notes', $package->notes) }}</textarea>
                                            @include('alerts.feedback', ['field' => 'notes'])
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Csomag tartalma</h6>
                                
                                @if($package->items->count() > 0)
                                    @foreach($package->items as $item)
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h6 class="card-title">{{ $item->product_name }}</h6>
                                                        <p class="card-text text-muted small">
                                                            SKU: {{ $item->sku }}<br>
                                                            Egységár: {{ number_format($item->unit_price, 0, ',', ' ') }} Ft<br>
                                                            Mennyiség: {{ $item->quantity }} db
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="alert alert-info">
                                        <strong>Összesen:</strong> {{ $package->total_items }} termék
                                    </div>
                                @else
                                    <p class="text-muted">Nincsenek tételek a csomagban</p>
                                @endif

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
                                                       name="cash_on_delivery_amount" value="{{ old('cash_on_delivery_amount', $package->cashOnDelivery->amount ?? '') }}" 
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
                                                       name="cash_on_delivery_bank_account" value="{{ old('cash_on_delivery_bank_account', $package->cashOnDelivery->bank_account_number ?? '') }}" 
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
                                                       name="cash_on_delivery_recipient" value="{{ old('cash_on_delivery_recipient', $package->cashOnDelivery->recipient_name ?? '') }}" 
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
                                                       name="cash_on_delivery_bank_name" value="{{ old('cash_on_delivery_bank_name', $package->cashOnDelivery->bank_name ?? '') }}" 
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
                                                          placeholder="Egyéb megjegyzések az utánvétel kapcsán">{{ old('cash_on_delivery_notes', $package->cashOnDelivery->notes ?? '') }}</textarea>
                                                @include('alerts.feedback', ['field' => 'cash_on_delivery_notes'])
                                            </div>
                                        </div>
                                    </div>

                                    @if($package->cashOnDelivery)
                                        <div class="alert alert-info">
                                            <strong>Utánvét státusz:</strong> 
                                            <span class="badge badge-{{ $package->cashOnDelivery->status == 'pending' ? 'warning' : ($package->cashOnDelivery->status == 'collected' ? 'success' : 'info') }}">
                                                {{ $package->cashOnDelivery->status_label }}
                                            </span>
                                            @if($package->cashOnDelivery->collected_at)
                                                <br><strong>Beszedve:</strong> {{ $package->cashOnDelivery->collected_at->format('Y.m.d H:i') }}
                                            @endif
                                            @if($package->cashOnDelivery->transferred_at)
                                                <br><strong>Átutalva:</strong> {{ $package->cashOnDelivery->transferred_at->format('Y.m.d H:i') }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <!-- End Cash on Delivery Section -->

                                @if($package->shipped_at)
                                    <div class="alert alert-info mt-3">
                                        <strong>Kiszküldve:</strong> {{ $package->shipped_at->format('Y.m.d H:i') }}
                                    </div>
                                @endif

                                @if($package->delivered_at)
                                    <div class="alert alert-success mt-3">
                                        <strong>Kézbesítve:</strong> {{ $package->delivered_at->format('Y.m.d H:i') }}
                                    </div>
                                @endif
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
                                    <i class="fa fa-save"></i> Módosítások mentése
                                </button>
                                <a href="{{ route('package.index') }}" class="btn btn-warning">
                                    <i class="fa fa-arrow-left"></i> Mégse
                                </a>

                                @if($package->status === 'created')
                                    <form class="d-inline-block" 
                                          action="{{ route('package.mark-as-shipped', $package->id) }}" 
                                          method="POST">
                                        @method('patch')
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-info"
                                                onclick="return confirm('Biztosan kiszállítottként jelöli a csomagot?')">
                                            <i class="fa fa-shipping-fast"></i> Kiszállítva
                                        </button>
                                    </form>
                                @endif

                                @if($package->status === 'shipped')
                                    <form class="d-inline-block" 
                                          action="{{ route('package.mark-as-delivered', $package->id) }}" 
                                          method="POST">
                                        @method('patch')
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-success"
                                                onclick="return confirm('Biztosan kézbesítettként jelöli a csomagot?')">
                                            <i class="fa fa-check"></i> Kézbesítve
                                        </button>
                                    </form>
                                @endif

                                
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
            // Shipping service selector change
            $('#shipping_service_selector').change(function(){
                toggleCashOnDeliverySection();
                syncShippingPrice();
            });

            // Package size and discount price change for price calculation
            $('#package_size_selector, #discount_price_selector').change(function(){
                calculatePrice();
            });

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
                var packageSizeId = $('#package_size_selector').val();
                var discountPriceId = $('#discount_price_selector').val();
                var packageSizePrice = 0;
                var discountAmount = 0;
                var finalPrice = 0;

                // Get package size price
                if (packageSizeId) {
                    var selectedSize = $('#package_size_selector option:selected');
                    packageSizePrice = parseFloat(selectedSize.data('price')) || 0;
                }

                // Calculate discount
                if (discountPriceId) {
                    var selectedDiscount = $('#discount_price_selector option:selected');
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
            toggleCashOnDeliverySection();
            syncShippingPrice();

            // Initialize price calculation
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