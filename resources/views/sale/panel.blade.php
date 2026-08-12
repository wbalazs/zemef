@extends('layouts.app', ['activePage' => 'sale_panel', 'activeButton' => 'sale_panel', 'title' => 'Zemef ERP', 'navName' => 'Értékesítés' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <!--<h3 class="mb-0 text-center"><th> <img class="img-thumbnail" style="max-width:100px;max-height:100px;border:none;" src="{{ asset('pdf/images/purchase_ticket/zemef_back.png') }}"/> Értékesítés</th></h3>-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-7">
                                            <label for="customer_select">Ügyfél kiválasztása</label>
                                            <select class="form-control select2" id="customer_select">
                                                <option value="0">Válasszon...</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->customer_id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-1" style="align-self: end;text-align: left;">
                                            <button type="button" class="btn btn-primary mb-1" id="customer_reg_btn"><i class="fa fa-user-plus"></i> </button>
                                        </div>
                                        <div class="col-4">
                                            <label for="cardbalance">Hűségkártya p.egyenleg</label>
                                            <input type="number" class="form-control" id="loyalty_card_balance" readonly>
                                        </div>
                                    </div>
                           
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="customer_address">Lakcím</label>
                                            <input type="text" class="form-control" id="customer_address" readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="cardbalance">Kedvezmény (%)</label>
                                            <input type="number" class="form-control" id="customer_discount">
                                        </div>
                                        <div class="col-2">
                                            <label for="cardbalance">Kupon</label>
                                            <input type="number" class="form-control" id="customer_coupon">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-8">
                                            <label for="customer_born_place">Születési hely</label>
                                            <input type="text" class="form-control" id="customer_born_place" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="cardbalance">Ajándékkártya pont egyenleg</label>
                                            <input type="number" class="form-control" id="gift_card_balance" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="customer_address">Alapadatok</label>
                                            <textarea type="text" rows="3" class="form-control" id="customer_details" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="cardbalance">Foglalós ékszer</label>
                                            <input type="text" class="form-control" id="reserved_diamond" value="0 db ; 0 Ft" readonly>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <div class="row">
                                        <div class="col-7" id="product_select_div">
                                            <label for="product_select">Termék kiválasztása</label>
                                            <select class="form-control select2" id="product_select">
                                            </select>
                                        </div>
                                        
                                        <div class="col-4">
                                            <label for="cardbalance">Státusz</label>
                                            <input type="text" class="form-control" id="prod_status" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive py-4" id="items-table">
                                                <table id="prod_first_table" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%" style="width:100% ">
                                                    <thead>
                                                        <tr>
                                                            <th>Terméknév</th>
                                                            <th>Kép</th>
                                                            <th>Mennyiség</th>
                                                            <th>Leírás</th>
                                                            <th>Művelet</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="prod_first_table_tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-3" style="align-self: end;text-align: left;">
                                            <button type="button" class="btn btn-secondary mb-0" id="gls_package_add_btn"><i class="fa fa-plus"></i> GLS Csomag</button>
                                        </div>
                                        <div class="col-3" style="align-self: end;text-align: left;">
                                            <button type="button" class="btn btn-secondary mb-0" id="gls_cod_add_btn"><i class="fa fa-plus"></i> GLS Utánvét</button>
                                        </div>
                                        <div class="col-6" style="align-self: end;">
                                            <small id="sale_package_info" class="text-muted d-block"></small>
                                            <small id="sale_cod_info" class="text-muted d-block"></small>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0" id="remove_sale_shipping_btn" style="display:none;">Szállítás törlése</button>
                                        </div>
                                    </div>



                                </div>
                                <div class="col-5">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive py-4" id="items-table">
                                                <table id="prod_table" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%" style="width:100% ">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="4" class="text-center">Összesítés</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Terméknév</th>
                                                            <th>Áfa kulcs</th>
                                                            <th>Nettó</th>
                                                            <th>Bruttó</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            
                                        </div>
                                        <div class="col-6">
                                            
                                            
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                                
                                                <label class="btn btn-outline-primary flex-fill" style="color:white !important;">
                                                    <input type="radio" name="receipt_type" id="receipt" value="receipt" autocomplete="off"> Nyugta
                                                </label>
                                                <label class="btn btn-outline-primary flex-fill" style="color:white !important;">
                                                    <input type="radio" name="receipt_type" id="invoice" value="invoice" autocomplete="off"> Számla
                                                </label>
                                                <label class="btn btn-outline-primary flex-fill" style="color:white !important;">
                                                    <input type="radio" name="receipt_type" id="booking" value="booking" autocomplete="off"> Foglaló
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="btn-group d-flex">
                                                <label class="btn btn-outline-success flex-fill payment-method-option" style="color:white !important;">
                                                    <input type="checkbox" name="payment_methods[]" id="cash" value="cash" autocomplete="off"> Készpénz
                                                </label>
                                                <label class="btn btn-outline-success flex-fill payment-method-option" style="color:white !important;">
                                                    <input type="checkbox" name="payment_methods[]" id="bank_transfer" value="transfer" autocomplete="off"> Átutalás
                                                </label>
                                                <label class="btn btn-outline-success flex-fill payment-method-option" style="color:white !important;">
                                                    <input type="checkbox" name="payment_methods[]" id="card" value="card" autocomplete="off"> Bankkártya
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2" id="split_payment_section" style="display:none;">
                                        <div class="col-12">
                                            <div class="card card-body py-2">
                                                <strong>Vegyes fizetés</strong>
                                                <small class="text-muted d-block mb-2">Add meg az összegeket a kiválasztott fizetési módokhoz.</small>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label for="split_cash_amount">Készpénz (Ft)</label>
                                                        <input type="number" min="0" step="1" id="split_cash_amount" class="form-control" value="0">
                                                    </div>
                                                    <div class="col-4">
                                                        <label for="split_card_amount">Bankkártya (Ft)</label>
                                                        <input type="number" min="0" step="1" id="split_card_amount" class="form-control" value="0">
                                                    </div>
                                                    <div class="col-4">
                                                        <label for="split_transfer_amount">Átutalás (Ft)</label>
                                                        <input type="number" min="0" step="1" id="split_transfer_amount" class="form-control" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-success" id="create"><i class="fa fa-save"></i> Bizonylat létrehozása</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new_customer_modal" tabindex="-1" role="dialog" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newCustomerModalLabel">Új ügyfél regisztrálása</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new_customer_form" method="POST" action="{{ route('customers.modal.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <label for="customer_name">Név</label>
                                <input type="text" class="form-control" id="customer_name" name="name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="customer_email">E-mail</label>
                                <input type="email" class="form-control" id="customer_email" name="email" required>
                            </div>
                            <div class="col-6">
                                <label for="customer_phone">Telefonszám</label>
                                <input type="text" class="form-control" id="customer_phone" name="phone" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="zip">Irányítószám</label>
                                    <select name="zip" id="customer_zip" class="select2 form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="city">Település</label>
                                    <select name="city" id="customer_city" class="select2 form-control" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="address">Cím</label>
                                    <input type="text" name="address" id="customer_address" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <!-- Add more fields as necessary -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                    <button type="submit" form="new_customer_form" class="btn btn-primary">Mentés</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking (foglaló) beállító modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalLabel">Foglaló részletek megadása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="booking_form">
          <div class="form-group">
            <label for="booking_expiry">Válasszon időtartamot</label>
            <select class="form-control" id="booking_expiry" required>
              <option value="1">1 hónap</option>
              <option value="3">3 hónap</option>
            </select>
          </div>
          <div class="form-group">
            <label for="booking_amount">Foglaló összege (Ft)</label>
            <input type="number" class="form-control" id="booking_amount" required>
          </div>
          <button type="submit" class="btn btn-success">Mentés és bizonylat létrehozása</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="customerCardModal" tabindex="-1" role="dialog" aria-labelledby="customerCardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerCardModalLabel">Érvényes ügyfélkártyák</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Válassz egy kártyát:</p>
                <select class="form-control" id="customer_card_select">
                    <option value="">- válasszon -</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                <button type="button" class="btn btn-primary" id="apply_customer_card_btn">Alkalmazás</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="salePackageModal" tabindex="-1" role="dialog" aria-labelledby="salePackageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salePackageModalLabel">Csomag feladás rögzítése</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="sale_package_form">
                    <div class="form-group">
                        <label>Szállítási szolgáltató *</label>
                        <select class="form-control" id="sale_package_shipping_service" required>
                            <option value="">- válasszon -</option>
                            @foreach($shippingServices as $service)
                                <option value="{{ $service->id }}" data-supports-cod="{{ $service->supports_cod ? 1 : 0 }}" data-base-price="{{ $service->base_price }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Szállítási díj (0% ÁFA) *</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sale_package_shipping_price" required>
                    </div>
                    <div class="form-group">
                        <label>Nyomkövetési szám</label>
                        <input type="text" class="form-control" id="sale_package_tracking_number">
                    </div>
                    <div class="form-group">
                        <label>Súly (kg)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sale_package_weight">
                    </div>
                    <div class="form-group mb-0">
                        <label>Megjegyzés</label>
                        <textarea class="form-control" id="sale_package_notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                <button type="button" class="btn btn-primary" id="save_sale_package_btn">Csomag mentése</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="saleCodModal" tabindex="-1" role="dialog" aria-labelledby="saleCodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleCodModalLabel">Utánvétes csomag rögzítése</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="sale_cod_form">
                    <div class="form-group">
                        <label>Szállítási szolgáltató *</label>
                        <select class="form-control" id="sale_cod_shipping_service" required>
                            <option value="">- válasszon -</option>
                            @foreach($shippingServices as $service)
                                @if($service->supports_cod)
                                    <option value="{{ $service->id }}" data-base-price="{{ $service->base_price }}">{{ $service->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Szállítási díj (0% ÁFA) *</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sale_cod_shipping_price" required>
                    </div>
                    <div class="form-group">
                        <label>Utánvét összege *</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sale_cod_amount" required>
                    </div>
                    <div class="form-group">
                        <label>Kedvezményezett neve *</label>
                        <input type="text" class="form-control" id="sale_cod_recipient" required>
                    </div>
                    <div class="form-group">
                        <label>Bankszámlaszám *</label>
                        <input type="text" class="form-control" id="sale_cod_bank_account" required>
                    </div>
                    <div class="form-group">
                        <label>Bank neve</label>
                        <input type="text" class="form-control" id="sale_cod_bank_name">
                    </div>
                    <div class="form-group">
                        <label>Nyomkövetési szám</label>
                        <input type="text" class="form-control" id="sale_cod_tracking_number">
                    </div>
                    <div class="form-group mb-0">
                        <label>Megjegyzés</label>
                        <textarea class="form-control" id="sale_cod_notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                <button type="button" class="btn btn-primary" id="save_sale_cod_btn">Utánvét mentése</button>
            </div>
        </div>
    </div>
</div>

@include('receipts.print_modal')
@endsection

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        // ==================== GLOBAL VARIABLES ====================
        let selectedProducts = [];
        let lastSaleData = null;
        let selectedCustomerCard = null;
        let selectedCoupon = null;
        let salePackageDraft = null;
        let saleCodDraft = null;
        
        // ==================== INITIALIZATION ====================
        function initializePage() {
            initProductSelect();
            initCustomerZip();
            initCustomerForm();
            initEventListeners();
            initItemListSelect();
            //loadAllProductOptions();
        }
        
        // ==================== PRODUCT SELECT ====================
        function initProductSelect() {
            $('#product_select').select2({
                placeholder: 'Válasszon...',
                allowClear: true,
                ajax: {
                    url: '{{ route("api.products.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.next_page_url ? true : false
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            }).on('change', handleProductSelectChange);
            
            $('#product_list_add_btn').hide();
        }
        
        function handleProductSelectChange() {
            let selectedData = $(this).select2('data')[0] || null;
            let inventory_id = selectedData ? selectedData.inventory_id : null;
            let prod_status = selectedData ? selectedData.status : '';
            
            $('#prod_status').val(prod_status);
            
            if (inventory_id > 0 && !selectedProducts.find(p => p.inventory_id == inventory_id)) {
                selectedProducts.push({
                    inventory_id: inventory_id,
                    product_id: selectedData.product_id || null,
                    name: selectedData.name || selectedData.text,
                    price_net: selectedData.price_net || 0,
                    price_gross: selectedData.price_gross || 0,
                    quantity: 1,
                    warehouse_id: selectedData.warehouse_id || null,
                    warehouse_name: selectedData.warehouse_name || '',
                    vat_rate: 27 // Default VAT rate
                });
                refreshSummary();
            }
        }
        
        // ==================== ITEM LIST SELECT ====================
        function initItemListSelect() {
            $('#item_list_select').on('change', handleItemListSelectChange);
        }
        
        function handleItemListSelectChange() {
            let selectedOption = $(this).find('option:selected');
            let item_id = selectedOption.val();
            let item_list_id = selectedOption.val();
            let prod_status = selectedOption.data('status');
            
            $('#prod_status').val(prod_status);
            
            if (item_list_id > 0 && !selectedProducts.find(p => p.item_list_id == item_list_id)) {
                selectedProducts.push({
                    item_list_id: item_list_id,
                    item_id: item_id,
                    name: selectedOption.data('product_name') || selectedOption.text(),
                    price: selectedOption.data('price'),
                    quantity: 1,
                    warehouse_id: selectedOption.data('warehouse_id'),
                    warehouse_name: selectedOption.data('warehouse_name'),
                    vat_rate: 27
                });
                refreshSummary();
            }
        }
        
        // ==================== CUSTOMER ZIP/CITY ====================
        function initCustomerZip() {
            $('#customer_zip').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#new_customer_modal'),
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
                placeholder: 'Válasszon irányítószámot..'
            }).on('select2:select', handleZipSelect);
        }
        
        function handleZipSelect(e) {
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
        }
        
        // ==================== CUSTOMER FORM ====================
        function initCustomerForm() {
            $('#new_customer_form').on('submit', handleCustomerFormSubmit);
        }
        
        function handleCustomerFormSubmit(e) {
            e.preventDefault();
            
            let form = $(this);
            let formData = form.serialize();
            
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        
                        let newOption = new Option(
                            response.data.name, 
                            response.data.customer_id, 
                            false, 
                            true
                        );
                        $('#customer_select').append(newOption).trigger('change');
                        
                        $('#new_customer_modal').modal('hide');
                        form[0].reset();
                    } else {
                        showNotification(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            let field = form.find('[name="' + key + '"]');
                            field.addClass('is-invalid');
                            field.after('<div class="invalid-feedback">' + value[0] + '</div>');
                        });
                    } else {
                        showNotification('Váratlan hiba történt az ügyfél mentésekor.', 'danger');
                    }
                }
            });
        }
        
        // ==================== EVENT LISTENERS ====================
        function initEventListeners() {
            // Customer registration
            $('#customer_reg_btn').on('click', function() {
                $('#new_customer_modal').modal('show');
            });
            
            // Create sale
            $('#create').on('click', handleCreateSale);
            
            // Discount/Coupon inputs
            $('#customer_discount, #customer_coupon, #loyalty_card_balance, #gift_card_balance')
                .on('input', refreshSummary);
            
            // Customer select
            $('#customer_select').on('change', handleCustomerSelectChange);
            $('#customer_coupon').on('change blur', handleCouponChange);
            $('#apply_customer_card_btn').on('click', applySelectedCustomerCard);
            $('input[name="payment_methods[]"]').on('change', toggleSplitPaymentSection);
            $('#split_cash_amount').on('input', autoFillRemainingSplitAmount);

            // Package / COD draft creation
            $('#gls_package_add_btn').on('click', function() {
                $('#salePackageModal').modal('show');
            });
            $('#gls_cod_add_btn').on('click', function() {
                $('#saleCodModal').modal('show');
            });
            $('#save_sale_package_btn').on('click', saveSalePackageDraft);
            $('#save_sale_cod_btn').on('click', saveSaleCodDraft);
            $('#sale_package_shipping_service').on('change', function() {
                let basePrice = parseFloat($(this).find('option:selected').data('base-price') || 0);
                if (!$('#sale_package_shipping_price').val()) {
                    $('#sale_package_shipping_price').val(basePrice);
                }
            });
            $('#sale_cod_shipping_service').on('change', function() {
                let basePrice = parseFloat($(this).find('option:selected').data('base-price') || 0);
                if (!$('#sale_cod_shipping_price').val()) {
                    $('#sale_cod_shipping_price').val(basePrice);
                }
            });
            $('#remove_sale_shipping_btn').on('click', clearSaleShippingDraft);

            // Product remove from summary table
            $(document).on('click', '.remove-sale-product-btn', function() {
                let inventoryId = parseInt($(this).data('inventory-id') || 0, 10);
                if (!inventoryId) {
                    return;
                }

                selectedProducts = selectedProducts.filter(function(p) {
                    return parseInt(p.inventory_id || 0, 10) !== inventoryId;
                });

                refreshSummary();
                showNotification('A tétel törölve lett a listából.', 'success');
            });
            
            // Product list toggle
            $('#item_list_add_btn').on('click', showItemListSelect);
            $('#product_list_add_btn').on('click', showProductSelect);
            
            // Modal close
            $('#printmodal').on('hidden.bs.modal', resetModalContent);
        }
        
        // ==================== SALE CREATION ====================
        async function handleCreateSale() {
            let customer_id = $('#customer_select').val();
            let customer_name = $('#customer_select option:selected').text();
            let customer_address = $('#customer_address').val();
            let receipt_type = $('input[name="receipt_type"]:checked').val();
            let paymentSelection = collectPaymentSelection();
            
            // Reset booking form
            $('#booking_expiry').val('1').trigger('change');
            $('#booking_amount').val('');
            
            // Validation
            if (!customer_id || customer_id <= 0) {
                alert('Kérjük, válasszon ügyfelet!');
                return;
            }
            
            if (!receipt_type) {
                alert('Kérjük, válasszon bizonylat típust!');
                return;
            }
            
            if (!paymentSelection.valid) {
                alert(paymentSelection.message || 'Kérjük, válasszon fizetési módot!');
                return;
            }

            if (paymentSelection.paymentMethods.length > 1) {
                let payableAmount = getCurrentPayableAmount();
                let splitTotal = paymentSelection.splitPayments.reduce(function(sum, row) {
                    return sum + parseFloat(row.amount || 0);
                }, 0);

                if (payableAmount > 0 && Math.abs(splitTotal - payableAmount) > 1) {
                    alert('A megadott fizetési részösszegek nem egyeznek a fizetendő végösszeggel.');
                    return;
                }
            }
            
            if (selectedProducts.length === 0) {
                alert('Kérjük, válasszon terméket!');
                return;
            }
            
            // Handle booking type
            if (receipt_type === 'booking') {
                showBookingModal(customer_id, customer_name, customer_address, paymentSelection);
            } else {
                await sendCreateRequest(customer_id, customer_name, customer_address, receipt_type, paymentSelection);
            }
        }
        
        function showBookingModal(customer_id, customer_name, customer_address, paymentSelection) {
            $('#bookingModal').modal('show');
            
            $('#booking_form').off('submit').on('submit', function(e) {
                e.preventDefault();
                
                let booking_months = $('#booking_expiry').val();
                let booking_amount = $('#booking_amount').val();
                
                if (!booking_amount || booking_amount <= 0) {
                    alert('Kérjük, adja meg a foglaló összegét!');
                    return;
                }
                
                $('#bookingModal').modal('hide');
                sendCreateRequest(
                    customer_id, 
                    customer_name, 
                    customer_address, 
                    'booking', 
                    paymentSelection, 
                    booking_months, 
                    booking_amount
                );
            });
        }
        
        async function sendCreateRequest(
            customer_id, 
            customer_name, 
            customer_address, 
            receipt_type, 
            paymentSelection, 
            booking_months = null, 
            booking_amount = null
        ) {
            $('#create').prop('disabled', true);
            
            try {
                const response = await $.ajax({
                    url: '/ajax/create_sale',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        customer_name: customer_name,
                        customer_address: customer_address,
                        receipt_type: receipt_type,
                        payment_method: paymentSelection.primaryMethod,
                        payment_methods: paymentSelection.paymentMethods,
                        split_payments: paymentSelection.splitPayments,
                        discount: $('#customer_discount').val() || 0,
                        coupon: 0,
                        coupon_code: String($('#customer_coupon').val() || '').trim(),
                        loyalty: $('#loyalty_card_balance').val() || 0,
                        giftcard: $('#gift_card_balance').val() || 0,
                        customer_card_id: selectedCustomerCard ? selectedCustomerCard.id : null,
                        customer_card_type: selectedCustomerCard ? selectedCustomerCard.type : null,
                        total_net: 0,
                        total_gross: 0,
                        final_total_net: 0,
                        final_total_gross: booking_amount ?? 0,
                        booking_months: booking_months,
                        booking_amount: booking_amount,
                        products: selectedProducts,
                        sale_package_payload: salePackageDraft ? salePackageDraft : null
                    }
                });
                
                if (response.success) {
                    // Store sale data for potential printing
                    if (receipt_type === 'receipt') {
                        lastSaleData = {
                            sale_id: response.sale_id,
                            products: selectedProducts,
                            total: response.final_total || booking_amount || calculateTotal(),
                            payment_method: paymentSelection.primaryMethod,
                            split_payments: paymentSelection.splitPayments
                        };
                        // 1. Send to the Serial Port via WebSocket
                        if (response.serial_port_text) {
                            printToCashMachine(response.serial_port_text);
                        } else {
                            console.warn("No serial_port_text found in response.");
                        }
                        resetModalContent();
                    } else{
                        // Show PDF
                        let iframeHTML = `<iframe src="${response.pdf_url}" width="100%" height="680" frameborder="0" style="border:0"></iframe>`;
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').html('Bizonylat nyomtatási kép');
                        $('#printmodal').modal('show');
                    }
                    
                    showNotification('Bizonylat sikeresen létrehozva!', 'success');
                } else {
                    alert(response.message);
                }
            } catch (error) {
                console.error('Error creating sale:', error);
                showNotification('Hiba történt a bizonylat létrehozásakor!', 'danger');
            } finally {
                $('#create').prop('disabled', false);
            }
        }

        function saveSalePackageDraft() {
            let shippingServiceId = $('#sale_package_shipping_service').val();
            if (!shippingServiceId) {
                alert('Válasszon szállítási szolgáltatót!');
                return;
            }

            let selectedOption = $('#sale_package_shipping_service option:selected');
            salePackageDraft = {
                type: 'package',
                shipping_service_id: parseInt(shippingServiceId, 10),
                shipping_service_name: selectedOption.text().trim(),
                shipping_price: parseFloat($('#sale_package_shipping_price').val() || 0),
                tracking_number: $('#sale_package_tracking_number').val() || '',
                weight: $('#sale_package_weight').val() || null,
                notes: $('#sale_package_notes').val() || ''
            };

            saleCodDraft = null;
            $('#sale_cod_info').text('');
            $('#sale_package_info').text('Csomag (pending): ' + salePackageDraft.shipping_service_name + ', ' + formatMoney(salePackageDraft.shipping_price));
            $('#remove_sale_shipping_btn').show();
            $('#salePackageModal').modal('hide');
            showNotification('Csomag feladás adatok rögzítve (pending).', 'success');
            refreshSummary();
        }

        function saveSaleCodDraft() {
            let shippingServiceId = $('#sale_cod_shipping_service').val();
            if (!shippingServiceId) {
                alert('Válasszon utánvétet támogató szállítási szolgáltatót!');
                return;
            }

            let selectedOption = $('#sale_cod_shipping_service option:selected');
            saleCodDraft = {
                type: 'cod',
                shipping_service_id: parseInt(shippingServiceId, 10),
                shipping_service_name: selectedOption.text().trim(),
                shipping_price: parseFloat($('#sale_cod_shipping_price').val() || 0),
                cash_on_delivery_amount: parseFloat($('#sale_cod_amount').val() || 0),
                cash_on_delivery_recipient: $('#sale_cod_recipient').val() || '',
                cash_on_delivery_bank_account: $('#sale_cod_bank_account').val() || '',
                cash_on_delivery_bank_name: $('#sale_cod_bank_name').val() || '',
                tracking_number: $('#sale_cod_tracking_number').val() || '',
                cash_on_delivery_notes: $('#sale_cod_notes').val() || '',
                notes: $('#sale_cod_notes').val() || ''
            };

            salePackageDraft = saleCodDraft;
            $('#sale_package_info').text('Csomag (pending): ' + saleCodDraft.shipping_service_name + ', ' + formatMoney(saleCodDraft.shipping_price));
            $('#sale_cod_info').text('Utánvét (pending): ' + formatMoney(saleCodDraft.cash_on_delivery_amount));
            $('#remove_sale_shipping_btn').show();
            $('#saleCodModal').modal('hide');
            showNotification('Utánvétes csomag adatok rögzítve (pending).', 'success');
            refreshSummary();
        }

        function clearSaleShippingDraft() {
            salePackageDraft = null;
            saleCodDraft = null;
            $('#sale_package_info').text('');
            $('#sale_cod_info').text('');
            $('#remove_sale_shipping_btn').hide();
            refreshSummary();
            showNotification('Szállítási adatok törölve.', 'success');
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('hu-HU', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(parseFloat(amount || 0)) + ' Ft';
        }

        function toggleSplitPaymentSection() {
            let selectedMethods = $('input[name="payment_methods[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            $('.payment-method-option').removeClass('active');
            $('input[name="payment_methods[]"]:checked').each(function() {
                $(this).closest('.payment-method-option').addClass('active');
            });

            if (selectedMethods.length > 1) {
                $('#split_payment_section').show();
                autoFillRemainingSplitAmount();
            } else {
                $('#split_payment_section').hide();
                $('#split_cash_amount, #split_card_amount, #split_transfer_amount').val(0);
            }
        }

        function autoFillRemainingSplitAmount() {
            let selectedMethods = $('input[name="payment_methods[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedMethods.length !== 2 || selectedMethods.indexOf('cash') === -1) {
                return;
            }

            let payableAmount = getCurrentPayableAmount();
            if (payableAmount <= 0) {
                return;
            }

            let cashAmount = parseFloat($('#split_cash_amount').val() || 0);
            let remaining = payableAmount - cashAmount;
            if (remaining < 0) {
                remaining = 0;
            }

            let otherMethod = selectedMethods.find(function(method) {
                return method !== 'cash';
            });

            if (otherMethod === 'card') {
                $('#split_card_amount').val(Math.round(remaining));
            } else if (otherMethod === 'transfer') {
                $('#split_transfer_amount').val(Math.round(remaining));
            }
        }

        function getCurrentPayableAmount() {
            let payable = 0;

            $('#prod_table tbody tr').each(function() {
                let rowLabel = $(this).find('td').eq(0).text().trim();
                if (rowLabel.indexOf('Fizetendő végösszeg') !== -1) {
                    let grossText = $(this).find('td').eq(3).text().trim();
                    payable = parseHuMoney(grossText);
                }
            });

            if (payable <= 0) {
                payable = calculateTotal();
            }

            return payable;
        }

        function parseHuMoney(text) {
            let normalized = String(text || '')
                .replace(/Ft/gi, '')
                .replace(/\s/g, '')
                .replace(/\./g, '')
                .replace(',', '.');

            let parsed = parseFloat(normalized);
            return isNaN(parsed) ? 0 : parsed;
        }

        function collectPaymentSelection() {
            let selectedMethods = $('input[name="payment_methods[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (!selectedMethods.length) {
                return {
                    valid: false,
                    message: 'Kérjük, válasszon fizetési módot!'
                };
            }

            let splitPayments = [];
            if (selectedMethods.length > 1) {
                let amountMap = {
                    cash: parseFloat($('#split_cash_amount').val() || 0),
                    card: parseFloat($('#split_card_amount').val() || 0),
                    transfer: parseFloat($('#split_transfer_amount').val() || 0),
                };

                for (let i = 0; i < selectedMethods.length; i++) {
                    let method = selectedMethods[i];
                    let amount = amountMap[method] || 0;
                    if (amount <= 0) {
                        return {
                            valid: false,
                            message: 'Vegyes fizetésnél minden kiválasztott módhoz adj meg pozitív összeget!'
                        };
                    }
                    splitPayments.push({ method: method, amount: amount });
                }
            } else {
                splitPayments.push({ method: selectedMethods[0], amount: null });
            }

            let primaryMethod = selectedMethods[0];
            if (splitPayments.length > 1) {
                let sorted = splitPayments.slice().sort(function(a, b) {
                    return (parseFloat(b.amount || 0) - parseFloat(a.amount || 0));
                });
                primaryMethod = sorted[0].method;
            }

            return {
                valid: true,
                primaryMethod: primaryMethod,
                paymentMethods: selectedMethods,
                splitPayments: splitPayments
            };
        }
        
        // ==================== UTILITY FUNCTIONS ====================
    /**
        * Sends a string to the local Python WebSocket server to be printed to the Serial Port
        * @param {string} serialText - The formatted text from the server response
        */
        function printToCashMachine(serialText) {
            //const socket = new WebSocket("ws://localhost:8080");
            const socket = new WebSocket("ws://127.0.0.1:8080");
            
            socket.onopen = function() {
                // Fontos: JSON objektummá alakítjuk a szöveget
                const payload = JSON.stringify({ "text": serialText });
                socket.send(payload);
                console.log("Adat elküldve JSON formátumban:", payload);
            };

            socket.onmessage = function(event) {
                const response = JSON.parse(event.data);
                if (response.status === "OK") {
                    console.log("Sikeres nyomtatás!");
                } else {
                    console.error("Szerver hiba:", response.status);
                }
                socket.close();
            };

            socket.onerror = function(err) {
                console.error("Nem sikerült csatlakozni a Python szerverhez.");
            };
        }
        function handleCustomerSelectChange() {
            let customer_id = $(this).val();
            
            if (customer_id > 0) {
                $.ajax({
                    url: '/ajax/get_customer',
                    type: "POST",
                    data: { customer_id: customer_id },
                    success: function(data) {
                        let datas = JSON.parse(data);
                        $('#customer_address').val(
                            datas.address.zip + ' ' + 
                            datas.address.city + ' ' + 
                            datas.address.address
                        );
                        $('#customer_born_place').val(datas.customer.birth_place);
                        loadCustomerCards(customer_id);
                    }
                });
            } else {
                $('#customer_address').val('');
                $('#customer_born_place').val('');
                selectedCustomerCard = null;
                $('#customer_discount').val('');
                $('#loyalty_card_balance').val('');
            }

            refreshSummary();
        }

        function loadCustomerCards(customerId) {
            selectedCustomerCard = null;
            $('#customer_card_select').html('<option value="">- válasszon -</option>');

            $.ajax({
                url: '/ajax/sale/customer-cards',
                type: 'POST',
                data: {
                    customer_id: customerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (!resp || !resp.success || !Array.isArray(resp.cards) || !resp.cards.length) {
                        return;
                    }

                    $.each(resp.cards, function(_, card) {
                        let label = card.name + ' (' + card.code + ') - ' + card.type + ' - ' + card.value;
                        let option = new Option(label, card.id, false, false);
                        $(option).data('card', card);
                        $('#customer_card_select').append(option);
                    });

                    $('#customerCardModal').modal('show');
                }
            });
        }

        function applySelectedCustomerCard() {
            let card = $('#customer_card_select option:selected').data('card');
            if (!card) {
                $('#customerCardModal').modal('hide');
                return;
            }

            selectedCustomerCard = card;

            if (card.type === 'percent') {
                $('#customer_discount').val(card.value || 0);
                $('#loyalty_card_balance').val('');
            } else if (card.type === 'price') {
                $('#loyalty_card_balance').val(card.value || 0);
                $('#customer_discount').val('');
            } else {
                $('#customer_discount').val('');
                $('#loyalty_card_balance').val('');
            }

            $('#customerCardModal').modal('hide');
            refreshSummary();
        }

        function handleCouponChange() {
            let code = String($('#customer_coupon').val() || '').trim();
            if (!code) {
                selectedCoupon = null;
                refreshSummary();
                return;
            }

            $.ajax({
                url: '/ajax/sale/validate-coupon',
                type: 'POST',
                data: {
                    code: code,
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (!resp || !resp.success || !resp.coupon) {
                        selectedCoupon = null;
                        showNotification((resp && resp.message) ? resp.message : 'Érvénytelen kuponkód.', 'warning');
                        refreshSummary();
                        return;
                    }

                    selectedCoupon = resp.coupon;
                    if (selectedCoupon.type === 'percentage') {
                        $('#customer_discount').val(selectedCoupon.value || 0);
                    } else if (selectedCoupon.type === 'fixed') {
                        $('#loyalty_card_balance').val(selectedCoupon.value || 0);
                    }
                    refreshSummary();
                }
            });
        }
        
        function showItemListSelect() {
            $('#item_list_add_btn').hide();
            $('#product_select_div').hide();
            $('#product_list_add_btn').show();
            $('#item_list_select_div').show();
            loadAllItemOptions();
        }
        
        function showProductSelect() {
            $('#product_list_add_btn').hide();
            $('#item_list_select_div').hide();
            $('#item_list_add_btn').show();
            $('#product_select_div').show();
            loadAllProductOptions();
        }
        
        function resetModalContent() {
            $(this).find('.modal-body').html('');
            $('#customer_discount').val('');
            $('#customer_coupon').val('');
            $('#loyalty_card_balance').val('');
            $('#gift_card_balance').val('');
            $('#product_select').val(0).trigger('change');
            selectedProducts = [];
            $('#prod_first_table_tbody').html('');
            $('#prod_table tbody').html('');
            $('#customer_select').val(0).trigger('change');
            $('#customer_address').val('');
            $('#prod_status').val('');
            lastSaleData = null;
            salePackageDraft = null;
            saleCodDraft = null;
            $('input[name="payment_methods[]"]').prop('checked', false);
            $('.payment-method-option').removeClass('active');
            $('#split_cash_amount, #split_card_amount, #split_transfer_amount').val(0);
            $('#split_payment_section').hide();
            $('#sale_package_info').text('');
            $('#sale_cod_info').text('');
            $('#remove_sale_shipping_btn').hide();
        }
        
        function showNotification(message, type = 'info') {
            $.notify(
                { icon: "done", message: message }, 
                {
                    type: type,
                    timer: 3000, 
                    placement: {from: 'top', align: 'right'}
                }
            );
        }
        
        function calculateTotal() {
            return selectedProducts.reduce((sum, product) => {
                let gross = parseFloat(product.price_gross || (parseFloat(product.price_net || 0) * 1.27) || 0);
                return sum + (gross * parseInt(product.quantity || 1));
            }, 0);
        }
        
        // ==================== PRODUCT DATA LOADING ====================
        function refreshSummary() {
            $.ajax({
                url: '/ajax/get_sale_summary',
                type: 'POST',
                data: {
                    products: selectedProducts,
                    discount: $('#customer_discount').val() || 0,
                    coupon: 0,
                    coupon_code: String($('#customer_coupon').val() || '').trim(),
                    loyalty: $('#loyalty_card_balance').val() || 0,
                    giftcard: $('#gift_card_balance').val() || 0,
                    customer_id: $('#customer_select').val() || 0,
                    customer_card_id: selectedCustomerCard ? selectedCustomerCard.id : null,
                    customer_card_type: selectedCustomerCard ? selectedCustomerCard.type : null,
                    receipt_type: $('input[name="receipt_type"]:checked').val() || '',
                    sale_package_payload: salePackageDraft ? salePackageDraft : null,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#prod_first_table_tbody').html(response.left_html);
                    $('#prod_table tbody').html(response.right_html);
                    autoFillRemainingSplitAmount();
                }
            });
        }
        
        function loadAllProductOptions() {
            $.ajax({
                url: '{{ route('product.getall') }}',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#product_select').html('<option value="0">Terméklista betöltése...</option>');
                },
                success: function(response) {
                    if (response.success && response.rows && response.rows.length > 0) {
                        $('#product_select').html('<option value="0">Válasszon...</option>');
                        
                        $.each(response.rows, function(index, row) {
                            if (row && row.name) {
                                var option = new Option(
                                    '#' + (row.product_id || row.id) + ' - ' + row.name,
                                    row.id || row.product_id,
                                    false,
                                    false
                                );
                                
                                $(option).data({
                                    product_name: row.name || '',
                                    warehouse_id: row.warehouse_id || '',
                                    warehouse_name: row.warehouse_name || '',
                                    short_description: row.short_description || '',
                                    price: row.real_price || row.price || 0,
                                    status: row.status || '',
                                    product_id: row.product_id || row.id
                                });
                                
                                option.setAttribute('data-product_name', row.name || '');
                                option.setAttribute('data-warehouse_id', row.warehouse_id || '');
                                option.setAttribute('data-warehouse_name', row.warehouse_name || '');
                                option.setAttribute('data-short_description', row.short_description || '');
                                option.setAttribute('data-price', row.real_price || row.price || 0);
                                option.setAttribute('data-status', row.status || '');
                                option.setAttribute('data-product_id', row.product_id || row.id);
                                
                                $('#product_select').append(option);
                            }
                        });
                        
                        if ($.fn.select2 && $('#product_select').hasClass('select2-hidden-accessible')) {
                            $('#product_select').select2();
                        }
                    } else {
                        $('#product_select').html('<option value="0">Nincs termék</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading products:', error);
                    $('#product_select').html('<option value="0">Hiba a betöltés során</option>');
                    showNotification('Hiba történt a terméklista betöltésekor', 'danger');
                }
            });
        }
        
        function loadAllItemOptions() {
            $.ajax({
                url: '/ajax/get-itemlist-rows',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#item_list_select').html('<option value="0">Kényszerlista betöltése...</option>');
                },
                success: function(response) {
                    if (response.success && response.rows && response.rows.length > 0) {
                        $('#item_list_select').html('<option value="0">Válasszon...</option>');
                        
                        $.each(response.rows, function(index, row) {
                            if (row && row.row1) {
                                var option = new Option(
                                    row.row1 + ' - ' + (row.row13 || ''),
                                    row.row1,
                                    false,
                                    false
                                );
                                
                                $(option).data({
                                    product_name: row.row13 || '',
                                    warehouse_id: "64",
                                    warehouse_name: "Teszt",
                                    short_description: row.row13 || '',
                                    price: row.row10 || '0',
                                    status: "Feldolgozás alatt",
                                    source_item_list_id: row.source_item_list_id || ''
                                });
                                
                                option.setAttribute('data-product_name', row.row13 || '');
                                option.setAttribute('data-warehouse_id', "64");
                                option.setAttribute('data-warehouse_name', "Teszt");
                                option.setAttribute('data-short_description', row.row13 || '');
                                option.setAttribute('data-price', row.row10 || '0');
                                option.setAttribute('data-status', "Feldolgozás alatt");
                                option.setAttribute('data-source_item_list_id', row.source_item_list_id || '');
                                
                                $('#item_list_select').append(option);
                            }
                        });
                    } else {
                        $('#item_list_select').html('<option value="0">Nincs elem</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading item list:', error);
                    $('#item_list_select').html('<option value="0">Hiba a betöltés során</option>');
                    showNotification('Hiba történt a kényszerlista betöltésekor', 'danger');
                }
            });
        }
        
        // ==================== INITIALIZE ====================
        initializePage();
    });
</script>
@endpush