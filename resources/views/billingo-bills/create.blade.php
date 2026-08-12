@extends('layouts.app', [
    'activePage' => 'billingo-bills',
    'activeButton' => 'billingo-bills',
    'title' => 'Zemef ERP',
    'navName' => 'Új számla'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Új számla létrehozása</h3>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('billingo-bills.store') }}" id="bill-form">
                            @csrf
                            
                            <!-- Partner Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Partner adatai</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_search">Partner keresése</label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           id="customer_search" 
                                                           class="form-control" 
                                                           placeholder="Keresés név, email, adószám vagy telefonszám alapján..."
                                                           autocomplete="off">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary" id="clear-search">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Kezdj el gépelni a partner nevének első betűit</small>
                                                <div id="customer-search-results" class="search-results-dropdown"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="partner_name">Partner neve *</label>
                                                <input type="text" 
                                                       name="partner_name" 
                                                       id="partner_name" 
                                                       class="form-control" 
                                                       required
                                                       value="{{ old('partner_name') }}">
                                                @error('partner_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="partner_tax_number">Adószám</label>
                                                <input type="text" 
                                                       name="partner_tax_number" 
                                                       id="partner_tax_number" 
                                                       class="form-control"
                                                       value="{{ old('partner_tax_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="partner_email">E-mail cím</label>
                                                <input type="email" 
                                                       name="partner_email" 
                                                       id="partner_email" 
                                                       class="form-control"
                                                       value="{{ old('partner_email') }}">
                                                @error('partner_email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="partner_zip_code">Irányítószám *</label>
                                                <input type="text" 
                                                       name="partner_zip_code" 
                                                       id="partner_zip_code" 
                                                       class="form-control" 
                                                       required
                                                       value="{{ old('partner_zip_code') }}">
                                                @error('partner_zip_code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="partner_city">Város *</label>
                                                <input type="text" 
                                                       name="partner_city" 
                                                       id="partner_city" 
                                                       class="form-control" 
                                                       required
                                                       value="{{ old('partner_city') }}">
                                                @error('partner_city')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="partner_address">Cím *</label>
                                                <input type="text" 
                                                       name="partner_address" 
                                                       id="partner_address" 
                                                       class="form-control" 
                                                       required
                                                       value="{{ old('partner_address') }}">
                                                @error('partner_address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="partner_phone">Telefonszám</label>
                                                <input type="text" 
                                                       name="partner_phone" 
                                                       id="partner_phone" 
                                                       class="form-control"
                                                       value="{{ old('partner_phone') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="partner_is_company">Típus</label>
                                                <select name="partner_is_company" id="partner_is_company" class="form-control">
                                                    <option value="0">Magánszemély</option>
                                                    <option value="1" {{ old('partner_is_company') == '1' ? 'selected' : '' }}>Cég</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bill Details Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Számla részletei</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="type">Számla típusa *</label>
                                                <select name="type" id="type" class="form-control" required>
                                                    <option value="outgoing" {{ old('type') == 'outgoing' ? 'selected' : '' }}>Kimenő számla</option>
                                                    <option value="incoming" {{ old('type') == 'incoming' ? 'selected' : '' }}>Bejövő számla</option>
                                                    <option value="proforma" {{ old('type') == 'proforma' ? 'selected' : '' }}>Proforma számla</option>
                                                    <option value="advance" {{ old('type') == 'advance' ? 'selected' : '' }}>Előlegszámla</option>
                                                </select>
                                                @error('type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="currency">Pénznem *</label>
                                                <select name="currency" id="currency" class="form-control" required>
                                                    <option value="HUF" {{ old('currency') == 'HUF' ? 'selected' : '' }}>HUF - Magyar Forint</option>
                                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - Amerikai dollár</option>
                                                </select>
                                                @error('currency')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fulfillment_date">Teljesítés dátuma *</label>
                                                <input type="date" 
                                                       name="fulfillment_date" 
                                                       id="fulfillment_date" 
                                                       class="form-control" 
                                                       value="{{ old('fulfillment_date', date('Y-m-d')) }}" 
                                                       required>
                                                @error('fulfillment_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="due_date">Fizetési határidő *</label>
                                                <input type="date" 
                                                       name="due_date" 
                                                       id="due_date" 
                                                       class="form-control" 
                                                       value="{{ old('due_date', date('Y-m-d', strtotime('+8 days'))) }}" 
                                                       required>
                                                @error('due_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_method">Fizetési mód *</label>
                                                <select name="payment_method" id="payment_method" class="form-control" required>
                                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Készpénz</option>
                                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Átutalás</option>
                                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Bankkártya</option>
                                                    <option value="cash_on_delivery" {{ old('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>Utánvét</option>
                                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Egyéb</option>
                                                </select>
                                                @error('payment_method')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="comment">Megjegyzés</label>
                                                <textarea name="comment" id="comment" class="form-control" rows="2">{{ old('comment') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="mb-0">Tételek</h5>
                                        </div>
                                        <div class="col-6 text-right">
                                            <button type="button" id="add-item" class="btn btn-sm btn-primary">
                                                <i class="fa fa-plus"></i> Új tétel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="items-container">
                                        <div class="item-row row mb-3 border-bottom pb-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Termék/szolgáltatás neve *</label>
                                                    <input type="text" 
                                                           name="items[0][name]" 
                                                           class="form-control item-name" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Mennyiség *</label>
                                                    <input type="number" 
                                                           name="items[0][quantity]" 
                                                           class="form-control item-quantity" 
                                                           value="1" 
                                                           min="0.001" 
                                                           step="0.001" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Egység</label>
                                                    <select name="items[0][unit]" class="form-control item-unit">
                                                        <option value="db" selected>db</option>
                                                        <option value="h">óra</option>
                                                        <option value="kg">kg</option>
                                                        <option value="m">m</option>
                                                        <option value="m2">m²</option>
                                                        <option value="nap">nap</option>
                                                        <option value="hó">hó</option>
                                                        <option value="év">év</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Nettó ár ({{ old('currency', 'HUF') }}) *</label>
                                                    <input type="number" 
                                                           name="items[0][unit_price]" 
                                                           class="form-control item-price" 
                                                           min="0" 
                                                           step="0.01" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>ÁFA kulcs *</label>
                                                    <select name="items[0][vat]" class="form-control item-vat">
                                                        <option value="0%">0% (ÁFA mentes)</option>
                                                        <option value="5%">5% (kedvezményes)</option>
                                                        <option value="18%">18% (alacsony)</option>
                                                        <option value="27%" selected>27% (normál)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-sm btn-danger remove-item" disabled>
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Summary -->
                                    <div class="row mt-4">
                                        <div class="col-md-4 offset-md-8">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th>Nettó összeg:</th>
                                                        <td class="text-right"><span id="net-total">0</span> {{ old('currency', 'HUF') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>ÁFA összeg:</th>
                                                        <td class="text-right"><span id="vat-total">0</span> {{ old('currency', 'HUF') }}</td>
                                                    </tr>
                                                    <tr class="table-active">
                                                        <th>Bruttó összeg:</th>
                                                        <td class="text-right"><strong><span id="gross-total">0</span> {{ old('currency', 'HUF') }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <a href="{{ route('billingo-bills.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Mégse
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Számla létrehozása
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .search-results-dropdown {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 300px;
        overflow-y: auto;
        display: none;
        width: 100%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .search-result-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    
    .search-result-item:hover {
        background-color: #f8f9fa;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .search-result-name {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .search-result-details {
        font-size: 0.9em;
        color: #666;
    }
    
    .item-row {
        position: relative;
    }
    
    .remove-item:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        // Customer search functionality
        let searchTimeout;
        let currentSearch = '';
        
        $('#customer_search').on('input', function() {
            const searchTerm = $(this).val().trim();
            
            if (searchTerm === currentSearch) return;
            currentSearch = searchTerm;
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                $('#customer-search-results').hide().empty();
                return;
            }
            
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route("billingo-bills.search-customers") }}',
                    method: 'GET',
                    data: { q: searchTerm },
                    success: function(response) {
                        displaySearchResults(response);
                    },
                    error: function() {
                        $('#customer-search-results').html(
                            '<div class="search-result-item text-danger">Hiba történt a keresés során</div>'
                        ).show();
                    }
                });
            }, 300);
        });
        
        function displaySearchResults(customers) {
            const $results = $('#customer-search-results');
            
            if (customers.length === 0) {
                $results.html(
                    '<div class="search-result-item text-muted">Nincs találat</div>'
                ).show();
                return;
            }
            
            let html = '';
            customers.forEach(function(customer) {
                html += `
                    <div class="search-result-item" data-customer='${JSON.stringify(customer)}'>
                        <div class="search-result-name">${customer.name}</div>
                        <div class="search-result-details">
                            ${customer.tax_number ? 'Adószám: ' + customer.tax_number + '<br>' : ''}
                            ${customer.email ? 'Email: ' + customer.email + '<br>' : ''}
                            ${customer.city ? 'Cím: ' + customer.city + (customer.zip_code ? ' (' + customer.zip_code + ')' : '') : ''}
                        </div>
                    </div>
                `;
            });
            
            $results.html(html).show();
        }
        
        // Handle customer selection
        $(document).on('click', '.search-result-item', function() {
            const customer = $(this).data('customer');
            
            // Fill form fields
            $('#partner_name').val(customer.name);
            $('#partner_tax_number').val(customer.tax_number || '');
            $('#partner_email').val(customer.email || '');
            $('#partner_phone').val(customer.phone || '');
            $('#partner_zip_code').val(customer.zip_code || '');
            $('#partner_city').val(customer.city || '');
            $('#partner_address').val(customer.address || '');
            $('#partner_is_company').val(customer.is_company ? '1' : '0');
            
            // Clear search and hide results
            $('#customer_search').val('');
            $('#customer-search-results').hide().empty();
            currentSearch = '';
            
            // Focus on next field
            $('#partner_email').focus();
        });
        
        // Clear search
        $('#clear-search').click(function() {
            $('#customer_search').val('');
            $('#customer-search-results').hide().empty();
            currentSearch = '';
        });
        
        // Hide results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customer_search, #customer-search-results').length) {
                $('#customer-search-results').hide();
            }
        });
        
        // Items management
        let itemIndex = 1;
        
        $('#add-item').click(function() {
            const newRow = `
                <div class="item-row row mb-3 border-bottom pb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" 
                                   name="items[${itemIndex}][name]" 
                                   class="form-control item-name" 
                                   placeholder="Termék/szolgáltatás neve" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" 
                                   name="items[${itemIndex}][quantity]" 
                                   class="form-control item-quantity" 
                                   value="1" 
                                   min="0.001" 
                                   step="0.001" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <select name="items[${itemIndex}][unit]" class="form-control item-unit">
                                <option value="db" selected>db</option>
                                <option value="h">óra</option>
                                <option value="kg">kg</option>
                                <option value="m">m</option>
                                <option value="m2">m²</option>
                                <option value="nap">nap</option>
                                <option value="hó">hó</option>
                                <option value="év">év</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" 
                                   name="items[${itemIndex}][unit_price]" 
                                   class="form-control item-price" 
                                   placeholder="Nettó ár" 
                                   min="0" 
                                   step="0.01" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="items[${itemIndex}][vat]" class="form-control item-vat">
                                <option value="0%">0% (ÁFA mentes)</option>
                                <option value="5%">5% (kedvezményes)</option>
                                <option value="18%">18% (alacsony)</option>
                                <option value="27%" selected>27% (normál)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            
            $('#items-container').append(newRow);
            itemIndex++;
            
            // Enable remove button for all items except first
            $('.remove-item').prop('disabled', false);
            
            // Recalculate totals
            calculateTotals();
        });
        
        // Remove item row
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                reindexItems();
                calculateTotals();
            }
            
            // Disable remove button if only one item left
            if ($('.item-row').length === 1) {
                $('.remove-item').prop('disabled', true);
            }
        });
        
        // Reindex items after removal
        function reindexItems() {
            itemIndex = 0;
            $('.item-row').each(function() {
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${itemIndex}]`);
                        $(this).attr('name', newName);
                    }
                });
                itemIndex++;
            });
        }
        
        // Calculate totals
        function calculateTotals() {
            let netTotal = 0;
            let vatTotal = 0;
            
            $('.item-row').each(function() {
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                const vatRate = parseInt($(this).find('.item-vat').val()) || 27;
                
                const itemNet = quantity * price;
                const itemVat = itemNet * (vatRate / 100);
                
                netTotal += itemNet;
                vatTotal += itemVat;
            });
            
            const grossTotal = netTotal + vatTotal;
            
            // Update display
            $('#net-total').text(netTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
            $('#vat-total').text(vatTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
            $('#gross-total').text(grossTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
        }
        
        // Recalculate when item values change
        $(document).on('input', '.item-quantity, .item-price', function() {
            calculateTotals();
        });
        
        $(document).on('change', '.item-vat', function() {
            calculateTotals();
        });
        
        // Auto-calculate due date based on type
        $('#type').change(function() {
            const type = $(this).val();
            const fulfillmentDate = $('#fulfillment_date').val();
            
            if (fulfillmentDate) {
                const date = new Date(fulfillmentDate);
                if (type === 'outgoing') {
                    date.setDate(date.getDate() + 8); // 8 days for outgoing
                } else if (type === 'incoming') {
                    date.setDate(date.getDate() + 30); // 30 days for incoming
                } else if (type === 'proforma') {
                    date.setDate(date.getDate() + 30); // 30 days for proforma
                } else if (type === 'advance') {
                    date.setDate(date.getDate() + 0); // 0 days for advance (immediate)
                }
                $('#due_date').val(date.toISOString().split('T')[0]);
            }
        });
        
        // Update currency in totals when changed
        $('#currency').change(function() {
            const currency = $(this).val();
            $('.table td span').each(function() {
                const text = $(this).text();
                if (text.includes('HUF') || text.includes('EUR') || text.includes('USD')) {
                    $(this).text(text.replace(/(HUF|EUR|USD)/g, currency));
                }
            });
        });
        
        // Initial calculation
        calculateTotals();
        
        // Form validation
        $('#bill-form').submit(function(e) {
            // Check if at least one item has price
            let hasValidItems = false;
            $('.item-price').each(function() {
                if (parseFloat($(this).val()) > 0) {
                    hasValidItems = true;
                }
            });
            
            if (!hasValidItems) {
                e.preventDefault();
                alert('Legalább egy tételnek meg kell adnia egy pozitív árat!');
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush