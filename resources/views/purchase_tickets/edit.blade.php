@extends('layouts.app', ['activePage' => 'purchase_ticket', 'activeButton' => 'purchase_ticket', 'title' => 'Zemef ERP', 'navName' => 'Vételi jegy szerkesztése' ])

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
                            <a href="{{ route('purchase_ticket.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body ">

                                            <form id="purchase_ticket_form" method="post" action="{{ route('purchase_ticket.update', $purchase_ticket->purchase_ticket_id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('patch')

                                            <h6 class="heading-small text-muted mb-4">Vételi jegy alap adatai</h6>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">
                                                                Ügyfél</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif required name="customer_id" class="select2 form-control{{ $errors->has('customer_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($customers as $customer)
                                                                    <option @if($customer->customer_id == $purchase_ticket->customer_id) selected @endif value="{{ $customer->customer_id }}" >{{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $zip = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->addresses->where('address_type', 'Lakcím')->first()->zip : '';
                                                                $zip_id = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->addresses->where('address_type', 'Lakcím')->first()->zip_id : '0';
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
                                                                $city = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->addresses->where('address_type', 'Lakcím')->first()->city : '';
                                                                $city_id = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->addresses->where('address_type', 'Lakcím')->first()->city_id : '0';
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
                                                                $address = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->addresses->where('address_type', 'Lakcím')->first()->address : '';
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
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $identity_card_number = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->identity_card_number : '';
                                                            ?>
                                                            <label class="form-control-label{{ $errors->has('customer_identity_card_number') ? ' has-danger' : '' }}">
                                                                Személyi igazolvány szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('customer_identity_card_number') ? ' is-invalid' : '' }}" name="customer_identity_card_number" value="{{ old('customer_identity_card_number', $identity_card_number) }}" placeholder="" disabled>
                                                            @include('alerts.feedback', ['field' => 'customer_identity_card_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $tax_number = (isset($purchase_ticket->customer)) ? $purchase_ticket->customer->tax_number : '';
                                                            ?>
                                                            <label class="form-control-label{{ $errors->has('customer_tax_number') ? ' has-danger' : '' }}">
                                                                Adószám</label>
                                                            <input type="text" class="form-control{{ $errors->has('customer_tax_number') ? ' is-invalid' : '' }}" name="customer_tax_number" value="{{ old('customer_tax_number', $tax_number) }}" placeholder="" disabled>
                                                            @include('alerts.feedback', ['field' => 'customer_tax_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('currency_id') ? ' has-danger' : '' }}">
                                                                Deviza</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif required name="currency_id" class="select2 form-control{{ $errors->has('currency_id') ? ' is-invalid' : '' }}">
                                                                @foreach($currencies as $currency)
                                                                    <option 
                                                                        @if($currency->currency_id == $purchase_ticket->currency_id) 
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
                                                            <?php $currency_value = (isset($purchase_ticket->currency_value)) ? $purchase_ticket->currency_value : ''; ?>
                                                            <label class="form-control-label{{ $errors->has('currency_value') ? ' has-danger' : '' }}">
                                                                Árfolyam</label>
                                                            <input type="number" @if($purchase_ticket->status == 2) disabled @endif step=".01" class="form-control{{ $errors->has('currency_value') ? ' is-invalid' : '' }}" name="currency_value" value="{{ old('currency_value', $selected_currency_value ?? '') }}" placeholder="" @if($selected_currency_editable?? null == '0') disabled @endif required>
                                                            @include('alerts.feedback', ['field' => 'currency_value'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('payment_id') ? ' has-danger' : '' }}">
                                                                Fizetés módja</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif name="payment_id" class="select2 form-control{{ $errors->has('payment_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($payments ?? [] as $payment)
                                                                    <option value="{{ $payment->payment_id }}" @if((int)$payment->payment_id === (int)old('payment_id', $purchase_ticket->payment_id)) selected @endif>{{ $payment->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'payment_id'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                Sorszám</label>
                                                            <input type="text" class="form-control" name="ticket_number" value="{{ $purchase_ticket->ticket_number }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                                                                Üzlet</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif required name="business_id" id="business_id" class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($businesses as $business)
                                                                    <option @if($business->business_id == $purchase_ticket->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label @if($purchase_ticket->status == 2) disabled @endif class="form-control-label{{ $errors->has('storage_id') ? ' has-danger' : '' }}">
                                                                Raktár</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif title="- válasszon -" required name="storage_id" id="storage_id" class="select2 form-control{{ $errors->has('storage_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @if(isset($warehouse))
                                                                    <option value="{{ $warehouse->warehouse_id }}" selected>{{ $warehouse->name }}</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label @if($purchase_ticket->status == 2) disabled @endif class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">
                                                                Státusz</label>
                                                            <select @if($purchase_ticket->status == 2) disabled @endif title="- válasszon -" name="status" disabled class="select2 form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
                                                                <option @if($purchase_ticket->status != "1") selected @endif value="1">Folyamatban</option>
                                                                <option @if($purchase_ticket->status == "2") selected @endif value="2">Feldolgozva</option>
                                                                <option @if($purchase_ticket->status == "3") selected @endif value="3">Sztornó ?</option>
                                                                <option @if($purchase_ticket->status == "4") selected @endif value="4">Sztornó</option>
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                                
                                            @if($purchase_ticket->is_storno || $purchase_ticket->storno_pt_id)
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                @if($purchase_ticket->is_storno) Sztornózott @else Sztornó @endif vételi jegy</label>
                                                            <input type="text" class="form-control" name="ticket_number" value="{{ $purchase_ticket->relatedticketnumber }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('ticket_number') ? ' has-danger' : '' }}">
                                                                Sztornó oka</label>
                                                            <input type="text" class="form-control" name="storno_reason" value="{{ $purchase_ticket->storno_reason }}" disabled>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            @endif

                                            <hr>
                                            </form>

                                            <h6 class="heading-small text-muted mb-4">Vételi jegy tételek</h6>

                                            @foreach($inventoryItems as $item)
                                                @php
                                                    $itemData = is_array($item->item_data)
                                                        ? $item->item_data
                                                        : (json_decode($item->item_data ?? '[]', true) ?: []);
                                                @endphp

                                                <hr>
                                                <div class="pti_item_wrapper @if($item->status != 2) orange_bg @else green_bg @endif">
                                                    <input type="hidden" name="pti_id" class="pti_id" value="{{ $item->inventory_id }}">
                                                    <div class="row">

                                                        <!-- <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check pti_check">
                                                                        <label class="form-check-label">
                                                                        <input @if($item->status == 2) checked @endif class="form-check-input" type="checkbox" value="2" data-gtm-form-interact-field-id="1" disabled name="status">
                                                                        <span class="form-check-sign"></span>
                                                                        Feldolgozva
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>-->
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label{{ $errors->has('termek_tipus_id') ? ' has-danger' : '' }}">
                                                                        Termék típus</label>
                                                                    <select disabled title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" name="termek_tipus_id" class="selectpicker termek_tipus_id form-control{{ $errors->has('termek_tipus_id') ? ' is-invalid' : '' }}">
                                                                        @php
                                                                            $selectedTermekTipusId = $item->termek_tipus_id
                                                                                ?? ($itemData['termek_tipus_id'] ?? null);
                                                                        @endphp
                                                                        <option value="">- válasszon -</option>
                                                                        @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedTermekTipusId])
                                                                    </select>
                                                                   
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <label class="form-check-label">
                                                                        <input @if((int)$item->quantity === 1) checked @endif class="form-check-input pti_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" disabled name="pti_is_unique[]">
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
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="text" class="form-control pti_description" name="pti_description[]" value="{{ $item->description }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Mennyiség</label>
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="number" disabled class="form-control pti_quantity" name="pti_quantity[]" value="{{ $item->quantity }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Egységár</label>
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="number" class="form-control pti_oneprice" name="pti_oneprice[]" value="{{ $item->oneprice }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Ár</label>
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="number" class="form-control pti_price" name="pti_price[]" value="{{ $item->price }}">
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
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="text" class="form-control pti_factory_number" name="pti_factory_number[]" value="{{ $item->factory_number }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">
                                                                        Súly</label>
                                                                    <input @if($purchase_ticket->status == 2) disabled @endif type="number" class="form-control pti_weight" name="pti_weight[]" value="{{ $item->weight }}">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">Kategória</label>
                                                                    <select @if($purchase_ticket->status == 2) disabled @endif name="new_category_id" class="selectpicker form-control new_category_id">
                                                                        @include('categories.category_options', ['categories' => $categories, 'category_id' => old('new_category_id', $itemData['category_id'] ?? 1)])
                                                                    </select>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <fieldset>
                                                                <div class="form-group">
                                                                    <label class="form-control-label">Karát</label>
                                                                    <select @if($purchase_ticket->status == 2) disabled @endif name="new_karat_id" class="selectpicker form-control new_karat_id">
                                                                        <option value="">- válasszon -</option>
                                                                        @foreach($karats ?? [] as $karat)
                                                                            <option value="{{ $karat->karat_id }}" @if((int)($itemData['karat_id'] ?? 0) === (int)$karat->karat_id) selected @endif>{{ $karat->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        
                                                    </div>
                                                </div>


                                                <div class="modal fade" id="pti_images_modal_{{ $item->inventory_id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                      
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Vételi jegy tétel képek</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <!-- Modal body -->
                                                            <div class="modal-body">
                                                                
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="dropzone_area" >
                                                                            <form action="/ajax/upload_pti_image" id="pti_image_form_{{ $item->inventory_id }}" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                                <div class="dz-message needsclick">
                                                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                                                    <input type="hidden" name="pti_id" value="{{ $item->inventory_id }}">
                                                                                    @csrf
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                

                                                                <div class="row">
                                                                    <div class="col-lg-12">

                                                                        <div class="gallery_container pti_gallery_container_{{ $item->inventory_id }}">
                                                                            
                                                                            <div class="delete_buttons">
                                                                                @if($item->pti_images)
                                                                                    @foreach($item->pti_images as $img)
                                                                                        <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                                                            <button data-mod="" type="button" data-pti_id="{{ $item->inventory_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                                                                <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @endif
                                                                            </div>
                                                                            
                                                                            <div data-lightGallery="1" id="lightgallery_pti_images_{{ $item->inventory_id }}" class="">
                                                                                @if($item->pti_images)
                                                                                    @foreach($item->pti_images as $img)
                                                                                        <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="pti_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>">
                                                                                            <img data-img_id="{{ $img->image_id }}" class="img-responsive pti_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>" alt="">
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


                                                <div class="modal fade" id="pti_inv_modal_{{ $item->inventory_id }}" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                      
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Vételi jegy tétel vizsgálata</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <!-- Modal body -->
                                                            <div class="modal-body">

                                                                <h5>Képek</h5>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="dropzone_area" >
                                                                            <form action="/ajax/upload_pti_image" id="pti_image_form2_{{ $item->inventory_id }}" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                                <div class="dz-message needsclick">
                                                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                                                    <input type="hidden" name="pti_id" value="{{ $item->inventory_id }}">
                                                                                    @csrf
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">

                                                                        <div class="gallery_container pti_gallery_container2_{{ $item->inventory_id }}">
                                                                            
                                                                            <div class="delete_buttons">
                                                                                @if($item->pti_images2)
                                                                                @foreach($item->pti_images2 as $img)
                                                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                                                        <button data-mod="2" type="button" data-pti_id="{{ $item->inventory_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                @endforeach
                                                                                @endif
                                                                            </div>
                                                                            
                                                                            <div data-lightGallery="1" id="lightgallery_pti_images2_{{ $item->inventory_id }}">
                                                                                @if($item->pti_images2)
                                                                                @foreach($item->pti_images2 as $img)
                                                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="pti_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>">
                                                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive pti_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>" alt="">
                                                                                    </div>
                                                                                @endforeach
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if($item->quantity > 1)
                                                                <form id="investigation_form_{{ $item->inventory_id }}" method="GET" action="">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 text-center">
                                                                            <h5>Mennyiség: {{ $item->quantity }} db</h5>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <fieldset>
                                                                                @foreach([1 => 'Értékesíthető', 2 => 'Belső javításra', 3 => 'Fémjelzésre', 4 => 'Tört arany', 5 => 'Tört ezüst', 6 => 'Selejt', 7 => 'Ötvösnek'] as $key => $label)
                                                                                <div class="row mb-2">
                                                                                    <div class="col-8">
                                                                                        <div class="form-check">
                                                                                            <label class="form-check-label">
                                                                                            <input class="form-check-input inv_checkbox" data-pti_id="{{ $item->inventory_id }}" type="checkbox" name="investigation_{{ $item->inventory_id }}[]" value="{{ $key }}" data-gtm-form-interact-field-id="{{ $key }}">
                                                                                            <span class="form-check-sign"></span>
                                                                                            {{ $label }}
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-4">
                                                                                        <input type="number" disabled value="0" class="form-control inv_number" name="number_investigation_{{ $item->inventory_id }}_{{ $key }}">
                                                                                    </div>
                                                                                </div>
                                                                                @endforeach
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row further_data_{{ $item->inventory_id }} hidden_element">
                                                                        <div class="col-lg-12">
                                                                            <fieldset>
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label{{ $errors->has('goldsmith_id') ? ' has-danger' : '' }}">
                                                                                        Ötvös</label>
                                                                                    <select name="goldsmith_id_{{ $item->inventory_id }}" class="select2 form-control{{ $errors->has('goldsmith_id') ? ' is-invalid' : '' }}">
                                                                                        <option value="">- válasszon -</option>
                                                                                        @foreach($goldsmithes as $goldsmith)
                                                                                        <option value="{{ $goldsmith->id }}">{{ $goldsmith->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                            <fieldset>
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label{{ $errors->has('inv_comment') ? ' has-danger' : '' }}">
                                                                                        Megjegyzés</label>
                                                                                    <textarea class="form-control zm_textarea" name="inv_comment_{{ $item->inventory_id }}">{{ $item->inv_comment }}</textarea>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>

                                                                    @if($item->investigated_at)
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <fieldset>
                                                                                    <div class="form-group">
                                                                                        <label class="form-control-label">
                                                                                            Vizsgálat időpontja</label>
                                                                                        <input disabled type="text" class="form-control" name="investigated_at" value="{{ date('Y. m. d. H:i:s', strtotime($item->investigated_at)) }}">
                                                                                    </div>
                                                                                </fieldset>
                                                                            </div>
                                                                            <div class="col-lg-12">
                                                                                <fieldset>
                                                                                    <div class="form-group">
                                                                                        <label class="form-control-label">
                                                                                            Felhasználó</label>
                                                                                        <input disabled type="text" class="form-control" name="investigated_at" value="{{ $item->investigation_user->name }}">
                                                                                    </div>
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </form>
                                                                @else
                                                                <form id="investigation_form_{{ $item->inventory_id }}" method="GET" action="">
                                                                    <div class="row">
                                                                        <div class="col-lg-3">
                                                                            <fieldset>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input type="hidden" id="validate_{{ $item->inventory_id }}" value="">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="1" data-gtm-form-interact-field-id="1">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Értékesíthető
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="2" data-gtm-form-interact-field-id="2">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Belső javításra
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="3" data-gtm-form-interact-field-id="3">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Fémjelzésre
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="4" data-gtm-form-interact-field-id="4">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Tört arany
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="5" data-gtm-form-interact-field-id="5">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Tört ezüst
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="6" data-gtm-form-interact-field-id="6">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Selejt
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check form-check-radio">
                                                                                    <label class="form-check-label">
                                                                                    <input class="form-check-input inv_radio" data-pti_id="{{ $item->inventory_id }}" type="radio" name="investigation_{{ $item->inventory_id }}" value="7" data-gtm-form-interact-field-id="7">
                                                                                    <span class="form-check-sign"></span>
                                                                                    Ötvősnek
                                                                                    </label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row further_data_{{ $item->inventory_id }} hidden_element">
                                                                        <div class="col-lg-12">
                                                                            <fieldset>
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label{{ $errors->has('goldsmith_id') ? ' has-danger' : '' }}">
                                                                                        Ötvös</label>
                                                                                    <select name="goldsmith_id_{{ $item->inventory_id }}" class="select2 form-control{{ $errors->has('goldsmith_id') ? ' is-invalid' : '' }}">
                                                                                        <option value="">- válasszon -</option>
                                                                                        @foreach($goldsmithes as $goldsmith)
                                                                                            <option value="{{ $goldsmith->id }}" >{{ $goldsmith->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </fieldset>
                                                                            <fieldset>
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label{{ $errors->has('inv_comment') ? ' has-danger' : '' }}">
                                                                                        Megjegyzés</label>
                                                                                    <textarea class="form-control zm_textarea" name="inv_comment_{{ $item->inventory_id }}">{{ $item->inv_comment }}</textarea>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>

                                                                    @if($item->investigated_at)
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <fieldset>
                                                                                    <div class="form-group">
                                                                                        <label class="form-control-label">
                                                                                            Vizsgálat időpontja</label>
                                                                                        <input disabled type="text" class="form-control" name="investigated_at" value="{{ date('Y. m. d. H:i:s', strtotime($item->investigated_at)) }}">
                                                                                    </div>
                                                                                </fieldset>
                                                                            </div>
                                                                            <div class="col-lg-12">
                                                                                <fieldset>
                                                                                    <div class="form-group">
                                                                                        <label class="form-control-label">
                                                                                            Felhasználó</label>
                                                                                        <input disabled type="text" class="form-control" name="investigated_at" value="{{ $item->investigation_user->name }}">
                                                                                    </div>
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </form>
                                                                @endif


                                                            </div>
                                                            
                                                            <!-- Modal footer -->
                                                            <hr>
                                                            <div class="modal-footer">
                                                                @if($item->quantity > 1)
                                                                    <button data-pti_id="{{ $item->inventory_id }}" data-quantity="{{ $item->quantity }}" type="button" class="btn btn-success fr save_investigation_more">Mentés</button>
                                                                @else
                                                                    <button data-pti_id="{{ $item->inventory_id }}" type="button" class="btn btn-success fr save_investigation">Mentés</button>
                                                                @endif

                                                                <button type="button" class="btn btn-danger fr" data-dismiss="modal">Bezárás</button>
                                                            </div>
                                                        
                                                        </div>
                                                    </div>
                                                </div>



                                            @endforeach

                                            @if($purchase_ticket->status != 2)
                                                <div class="purchase_ticket_items_rows">
                                                    <hr>
                                                    <div class="orange_bg pti_empty_row">
                                                        <input type="hidden" name="pti_id" class="pti_id">
                                                        <div class="row">
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label{{ $errors->has('termek_tipus_id') ? ' has-danger' : '' }}">
                                                                            Termék típus
                                                                        </label>
                                                                        <select @if($purchase_ticket->status == 2) disabled @endif required title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" name="termek_tipus_id" class="selectpicker termek_tipus_id form-control{{ $errors->has('termek_tipus_id') ? ' is-invalid' : '' }}">
                                                                            <option value="">- válasszon -</option>
                                                                            @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => old('termek_tipus_id')])
                                                                        </select>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <div class="form-check">
                                                                            <label class="form-check-label">
                                                                                <input @if($purchase_ticket->status == 2) disabled @endif checked class="form-check-input pti_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="pti_is_unique[]">
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
                                                                            Leírás
                                                                        </label>
                                                                        <input type="text" class="form-control pti_description" name="pti_description[]">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">
                                                                            Mennyiség
                                                                        </label>
                                                                        <input type="number" class="form-control pti_quantity" name="pti_quantity[]" value="1">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">
                                                                            Egységár
                                                                        </label>
                                                                        <input type="number" class="form-control pti_oneprice" name="pti_oneprice[]">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">
                                                                            Ár
                                                                        </label>
                                                                        <input type="number" disabled class="form-control pti_price" name="pti_price[]">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">
                                                                            Gyártási szám
                                                                        </label>
                                                                        <input type="text" class="form-control pti_factory_number" name="pti_factory_number[]">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">
                                                                            Súly
                                                                        </label>
                                                                        <input type="number" class="form-control pti_weight" name="pti_weight[]">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Kategória</label>
                                                                        <select name="new_category_id" class="selectpicker form-control new_category_id">
                                                                            @include('categories.category_options', ['categories' => $categories, 'category_id' => old('new_category_id', 1)])
                                                                        </select>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">Karát</label>
                                                                        <select name="new_karat_id" class="selectpicker form-control new_karat_id">
                                                                            <option value="">- válasszon -</option>
                                                                            @foreach($karats ?? [] as $karat)
                                                                                <option value="{{ $karat->karat_id }}">{{ $karat->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                    </div>
                                                </div>
                                            @endif



                                            <hr>
                                        </div>
                                    </div>
                                
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        @php $hasInventoryItems = isset($inventoryItems) && $inventoryItems->count() > 0; @endphp
                                        <button @if($purchase_ticket->status == 2) disabled @endif type="button" class="btn btn-success save_purchase_ticket">Mentés</button>
                                        <button @if($purchase_ticket->status == 2 || !$hasInventoryItems) disabled @endif type="button" class="btn btn-success save_and_close_purchase_ticket">Mentés és lezárás</button>
                                        <button @if($purchase_ticket->status == 2 || !$hasInventoryItems) disabled @endif type="button" class="btn btn-success print_purchase_ticket">@if($purchase_ticket->status == 2) Nyomtatva: {{ date('Y. m. d. H:i', strtotime($purchase_ticket->printed_at)) }} @else Mentés és nyomtatás @endif</button>

                                        @if($purchase_ticket->wait_to_transfer == '1' && $purchase_ticket->status == 2)
                                            @if(!$purchase_ticket->is_storno)
                                                <button @if($purchase_ticket->cancelled_at) disabled @endif type="button" class="btn btn-warning cancel_purchase_ticket">@if($purchase_ticket->cancelled_at) Sztornózva: {{ date('Y. m. d. H:i', strtotime($purchase_ticket->cancelled_at)) }} @else Sztornó @endif</button>
                                                <button type="button" class="btn btn-info copy_purchase_ticket">Másolás</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

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


        function buildPTIGallery($initScope)
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
                                $(this).width($('.pti_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                                $(this).height($('.pti_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                                $(this).css('top', $('.pti_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                                $(this).css('left', $('.pti_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
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


        function initPTIGallery(pti_id, mod="") {
            var $initScope_implementation = $('#lightgallery_pti_images'+mod+'_' + pti_id);

            console.log('init #lightgallery_pti_images'+mod+'_' + pti_id);

            var images = $initScope_implementation.find('img').get();
            var allImagesLoaded = images.every(function(img) {
                return img.complete;
            });

            if (allImagesLoaded) {
                console.log('allImagesLoaded');
                buildPTIGallery($initScope_implementation, mod);
            } else {
                $(images).on('load', function() {
                    var allLoaded = images.every(function(img) {
                        return img.complete;
                    });
                    if (allLoaded) {
                        buildPTIGallery($initScope_implementation, mod);
                    }
                });
            }
        }


        function checkImages(pit_id, mod){
            var totalImages = $('#lightgallery_pti_images'+mod+'_'+pit_id+' img').length;
            console.log('totalImages');
            console.log(totalImages);
            var loadedImages = 0;
            $('#lightgallery_pti_images'+mod+'_'+pit_id+' img').each(function(i, v) {
                loadedImages++;
                console.log((i+1));
                console.log(loadedImages);
                if ((i+1) == totalImages){
                    if (loadedImages == totalImages) {
                        console.log('LOADED');
                        $('#lightgallery_pti_images'+mod+'_'+pit_id).show();
                        $('#lightgallery_pti_images'+mod+'_'+pit_id).animate({ opacity: 1 }, 'slow');
                        initPTIGallery(pit_id, mod);
                    }
                }
            });
        }



        $(document).ready(function () {
            let valnow = $('input[name=currency_id]').val();



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

            $(document).on('input', '.pti_weight, .pti_oneprice', function() {
                let row = $(this).closest('.row');
                let weight = row.find('.pti_weight').val();
                let onePrice = row.find('.pti_oneprice').val();

                if(weight && onePrice) {
                    let totalPrice = weight * onePrice;
                    row.find('.pti_price').val(totalPrice);
                }
            });


            $('.captureButton').on('click', function() {
                $(this).next('.imageInput').click();
            });

            $('.imageInput').on('change', function() {
                let formData = new FormData();
                formData.append('pti_image', $(this)[0].files[0]);
                let pti_id = $(this).prev('.captureButton').data('pti_id');
                formData.append('pti_id', pti_id);

                $.ajax({
                    url: '/ajax/saveAndUploadPTIImage',
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
                                url: "/ajax/load_pti_gallery",
                                data: { pti_id: pti_id, mod: '' }
                            })
                            .done(function(html) {
                                $('.pti_gallery_container_' + pti_id).html(html);
                                checkImages(pti_id, '');
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

            $('.termek_tipus_id').selectpicker();
            $('.new_category_id').selectpicker();
            $('.new_karat_id').selectpicker();

            //Investigate_more modalhoz
            $(document).on("change", ".inv_checkbox", function(){
                let relatedInput = $(this).closest('.row').find('input[type="number"]');
                if ($(this).prop('checked')) {
                    relatedInput.removeAttr('disabled');
                } else {
                    relatedInput.attr('disabled', true);
                    relatedInput.val(0);
                }
            });

            // A beviteli mező értékének megváltozásakor
            $('body').on('change', '.inv_number', function() {
                if ($(this).val() < 0) {
                    $(this).val(0);
                }
            });

            $('body').on('change', 'input.form-check-input.inv_checkbox', function() {
                let associatedInput = $(this).closest('.row').find('input[type="number"]');
                
                if ($(this).is(':checked')) {
                    associatedInput.removeAttr('disabled');
                    if($(this).val() == "7") {
                        $('.further_data_' + $(this).data('pti_id')).removeClass('hidden_element');
                    }
                } else {
                    associatedInput.attr('disabled', 'disabled').val(0);
                    if($(this).val() == "7") {
                        $('.further_data_' + $(this).data('pti_id')).addClass('hidden_element');
                    }
                }
            });

            @foreach($purchase_ticket->purchase_ticket_items as $item)

                $('#lightgallery_pti_images_{{ $item->inventory_id }}').hide();
                $('#lightgallery_pti_images_{{ $item->inventory_id }}').css('opacity', 0);
                $('#pti_images_modal_{{ $item->inventory_id }}').on('shown.bs.modal', function (e) {
                    console.log('Modal shown');
                    $("#pti_image_form_{{ $item->inventory_id }}").dropzone({
                        maxFilesize: 50, // MB
                        parallelUploads: 1,
                        paramName: 'pti_image',
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
                                url: "/ajax/auto_save_pti_images",
                                data: { pti_id: {{ $item->inventory_id }}, image: response }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_pti_gallery",
                                data: { pti_id: {{ $item->inventory_id }}, mod: '' }
                            })
                            .done(function( html ) {
                                $('.pti_gallery_container_{{ $item->inventory_id }}').html(html);
                                checkImages({{ $item->inventory_id }}, '');
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        }
                    });

                    checkImages({{ $item->inventory_id }}, '');
                });


                $('#lightgallery_pti_images2_{{ $item->inventory_id }}').hide();
                $('#lightgallery_pti_images2_{{ $item->inventory_id }}').css('opacity', 0);
                $('#pti_inv_modal_{{ $item->inventory_id }}').on('shown.bs.modal', function (e) {

                    $("#pti_image_form2_{{ $item->inventory_id }}").dropzone({
                        maxFilesize: 50, // MB
                        parallelUploads: 1,
                        paramName: 'pti_image',
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
                                url: "/ajax/auto_save_pti_images",
                                data: { pti_id: {{ $item->inventory_id }}, image: response, module: "pti2" }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_pti_gallery",
                                data: { pti_id: {{ $item->inventory_id }}, mod: '2' }
                            })
                            .done(function( html ) {
                                $('.pti_gallery_container2_{{ $item->inventory_id }}').html(html);
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
                var pti_id = $(this).attr('data-pti_id');
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
                        url: "/ajax/delete_pti_image",
                        data: { img_id: img_id }
                    })
                    .done(function( msg ) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_pti_gallery",
                            data: { pti_id: pti_id, mod: mod }
                        })
                        .done(function( html ) {
                            $('.pti_gallery_container'+mod+'_' + pti_id).html(html);
                            checkImages(pti_id, mod);
                        });
                    });

                });

            });



            $("body").on("mouseenter", ".pti_img_wrapper", function(e){
                $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
            });
            $("body").on("mouseleave", ".pti_img_wrapper", function(e){
                if( !$(e.toElement).hasClass('delete_img') ) {
                    $('.delete_img').stop(true).hide(200);
                }
            });


            
            $("body").on("change", ".inv_radio", function(e){

                var pti_id = $(this).attr('data-pti_id');
                if($(this).val() == '7'){
                    $('.further_data_'+pti_id).removeClass('hidden_element');
                } else {
                    $('.further_data_'+pti_id).addClass('hidden_element');
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



            // Termék típus kiválasztók inicializálása
            $('.termek_tipus_id').selectpicker('refresh');


            $('select[name=customer_id]').change(function(){

                var customer_id = $(this).val();

                if(onlyNumbers(customer_id)){
                    //ügyfél adatok lekérése
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_customer",
                        data: { customer_id: customer_id }
                    })
                    .done(function( resp ) {

                        var o = $.parseJSON(resp);

                        $('select[name=customer_zip]').prop('disabled', true);
                        $('select[name=customer_city]').prop('disabled', true);
                        $('input[name=customer_address]').prop('disabled', true);
                        $('input[name=customer_identity_card_number]').prop('disabled', true);
                        $('input[name=customer_tax_number]').prop('disabled', true);

                        $('select[name=customer_zip]').append('<option selected value="'+o.address.zip_id+'">'+o.address.zip+'</option>').trigger('change');
                        $('select[name=customer_city]').append('<option selected value="'+o.address.city_id+'">'+o.address.city+'</option>').trigger('change');
                        $('input[name=customer_address]').val(o.address.address);
                        $('input[name=customer_identity_card_number]').val(o.customer.identity_card_number);
                        $('input[name=customer_tax_number]').val(o.customer.tax_number);    
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

                    $('input[name=customer_zip]').focus();
                }


            });


            $('select[name=business_id]').change(function(){

            let business_id = $(this).val();

                if(onlyNumbers(business_id)){
                    //Raktárak lekérése üzlet azonosító alapján a munkavállalóhoz
                    $.ajax({
                        method: "POST",
                        url: "/ajax/get_storages_by_business_id_employee",
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

            function isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            }

            $(document).on('change', '.termek_tipus_id', function() {

                if($(this).val().length && $(this).parents('.orange_bg').hasClass('pti_empty_row')){
                    $(this).parents('.orange_bg').removeClass('pti_empty_row').addClass('pti_item_wrapper');

                    var pti_row_html = `
                    <hr>
                    <div class="orange_bg pti_empty_row">
                        <input type="hidden" name="pti_id" class="pti_id">
                        <div class="row">
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Termék típus</label>
                                        <select title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" class="selectpicker form-control termek_tipus_id" name="termek_tipus_id">
                                            <option value="">- válasszon -</option>
                                            @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => old('termek_tipus_id')])
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input pti_is_unique" checked type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="pti_is_unique[]">
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
                                        <label class="form-control-label">Leírás</label>
                                        <input type="text" class="form-control pti_description" name="pti_description[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Mennyiség</label>
                                        <input type="number" value="1" class="form-control pti_quantity" name="pti_quantity[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Egységár</label>
                                        <input type="number" class="form-control pti_oneprice" name="pti_oneprice[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Ár</label>
                                        <input type="number" disabled class="form-control pti_price" name="pti_price[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Gyártási szám</label>
                                        <input type="text" class="form-control pti_factory_number" name="pti_factory_number[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Súly</label>
                                        <input type="number" class="form-control pti_weight" name="pti_weight[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Kategória</label>
                                        <select name="new_category_id" class="selectpicker form-control new_category_id">
                                            @include('categories.category_options', ['categories' => $categories, 'category_id' => old('new_category_id', 1)])
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Karát</label>
                                        <select name="new_karat_id" class="selectpicker form-control new_karat_id">
                                            <option value="">- válasszon -</option>
                                            @foreach($karats ?? [] as $karat)
                                                <option value="{{ $karat->karat_id }}">{{ $karat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                `;


                    $('.purchase_ticket_items_rows').append(pti_row_html);
                    $('.termek_tipus_id').selectpicker('refresh');
                    $('.new_category_id').selectpicker('refresh');
                    $('.new_karat_id').selectpicker('refresh');

                } else if(!$(this).val() && $('.pti_empty_row').length < 1) {
                    $(this).parents('.orange_bg').addClass('pti_empty_row');
                }


            });


            function savePurchaseTicket(callback, print = false, close = false) {
                if(!$("#purchase_ticket_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('#purchase_ticket_form')).click().remove();
                    return false;
                }

                function hasSavablePTIItem() {
                    var hasItem = false;
                    $('.pti_item_wrapper').each(function() {
                        var wrapper = $(this);
                        var ptiId = wrapper.find('.pti_id').first().val();
                        var termekTipusId = wrapper.find('.termek_tipus_id').val() || wrapper.find('.termek_tipus_id option:selected').val();
                        if ((ptiId && String(ptiId).trim() !== '') || (termekTipusId && String(termekTipusId).trim() !== '')) {
                            hasItem = true;
                            return false;
                        }
                    });
                    return hasItem;
                }

                var customer_id = $('select[name=customer_id]').val();
                var storage_id = $('select[name=storage_id]').val();
                var business_id = $('select[name=business_id]').val();
                var payment_id = $('select[name=payment_id]').val();
                let currency_id = $('select[name=currency_id]').val();
                let currency_value = $('input[name=currency_value]').val();

                var zip = "";
                var city = "";
                var address = "";
                var identity_card_number = "";
                var tax_number = "";

                if(!onlyNumbers(customer_id)){
                    zip = $('select[name=customer_zip]').val();
                    city = $('select[name=customer_city]').val();
                    address = $('input[name=customer_address]').val();
                    identity_card_number = $('input[name=customer_identity_card_number]').val();
                    tax_number = $('input[name=customer_tax_number]').val();
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_purchase_ticket",
                    data: { 
                        print: print,
                        close: false,
                        purchase_ticket_id: '{{ $purchase_ticket->purchase_ticket_id }}', 
                        customer_id: customer_id, 
                        storage_id: storage_id, 
                        business_id: business_id, 
                        payment_id: payment_id,
                        zip: zip, 
                        city: city, 
                        address: address, 
                        identity_card_number: identity_card_number, 
                        tax_number: tax_number,
                        currency_id: currency_id,
                        currency_value: currency_value
                    }
                })
                .done(function(resp) {
                    var purchase_ticket_id = '{{ $purchase_ticket->purchase_ticket_id }}';
                    var ajaxRequests = [];
                    $('.pti_item_wrapper').each(function() {
                        var wrapper = $(this);
                        var ptiId = wrapper.find('.pti_id').first().val();
                        var termekTipusId = wrapper.find('.termek_tipus_id').val() || wrapper.find('.termek_tipus_id option:selected').val();
                        var newCategoryId = wrapper.find('select.new_category_id option:selected').val();
                        var newKaratId = wrapper.find('select.new_karat_id option:selected').val();

                        if ((newCategoryId === undefined || newCategoryId === null) && wrapper.find('select.new_category_id').length) {
                            newCategoryId = wrapper.find('select.new_category_id').val();
                        }
                        if ((newKaratId === undefined || newKaratId === null) && wrapper.find('select.new_karat_id').length) {
                            newKaratId = wrapper.find('select.new_karat_id').val();
                        }

                        if (newCategoryId === undefined || newCategoryId === null) {
                            newCategoryId = '';
                        }
                        if (newKaratId === undefined || newKaratId === null) {
                            newKaratId = '';
                        }

                        if(ptiId || termekTipusId){
                            var pti_data = {
                                print: print,
                                purchase_ticket_id: purchase_ticket_id,
                                pti_id: ptiId,
                                storage_id: storage_id,
                                termek_tipus_id: termekTipusId,
                                new_category_id: newCategoryId,
                                new_karat_id: newKaratId,
                                pti_is_unique: wrapper.find('.pti_is_unique').is(':checked'),
                                pti_description: wrapper.find('.pti_description').val(),
                                pti_quantity: wrapper.find('.pti_quantity').val(),
                                pti_oneprice: wrapper.find('.pti_oneprice').val(),
                                pti_price: wrapper.find('.pti_price').val(),
                                pti_factory_number: wrapper.find('.pti_factory_number').val(),
                                pti_weight: wrapper.find('.pti_weight').val(),
                            };

                            var request = $.ajax({
                                method: "POST",
                                url: "/ajax/save_purchase_ticket_item",
                                data: pti_data
                            }).done(function(respItem) {
                                wrapper.find('.pti_id').first().val(respItem);
                            });

                            ajaxRequests.push(request);
                        }
                    });

                    $.when.apply($, ajaxRequests).done(function() {
                        if (close) {
                            if (!hasSavablePTIItem()) {
                                swal('Nincs mentett tétel', 'Lezárás csak akkor lehetséges, ha legalább egy tétel szerepel a vételi jegyen.', 'warning');
                                return;
                            }

                            $.ajax({
                                method: "POST",
                                url: "/ajax/save_purchase_ticket",
                                data: {
                                    print: false,
                                    close: true,
                                    purchase_ticket_id: '{{ $purchase_ticket->purchase_ticket_id }}',
                                    customer_id: customer_id,
                                    storage_id: storage_id,
                                    business_id: business_id,
                                    payment_id: payment_id,
                                    zip: zip,
                                    city: city,
                                    address: address,
                                    identity_card_number: identity_card_number,
                                    tax_number: tax_number,
                                    currency_id: currency_id,
                                    currency_value: currency_value
                                }
                            }).done(function() {
                                $.notify({
                                    icon: "done",
                                    message: "Az adatok sikeresen mentve és lezárva."
                                }, {
                                    type: 'success',
                                    timer: 3000,
                                    placement: {
                                        from: 'top',
                                        align: 'right'
                                    }
                                });

                                if(callback) callback();
                                location.href = "/purchase_ticket/{{ $purchase_ticket->purchase_ticket_id }}/edit";
                            }).fail(function(xhr) {
                                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                    ? xhr.responseJSON.message
                                    : 'A lezárás nem sikerült.';
                                swal('Lezárás sikertelen', msg, 'error');
                            });
                            return;
                        }

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
                        location.href = "/purchase_ticket/{{ $purchase_ticket->purchase_ticket_id }}/edit";
                    });
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Mentés közben hiba történt.';
                    swal('Mentés sikertelen', msg, 'error');
                });
            }

            @if (session('print'))
                // Nyomtatás
                $.ajax({
                    method: "POST",
                    url: "/ajax/pdf/purchase_ticket",
                    data: { purchase_ticket_id: '{{ $purchase_ticket->purchase_ticket_id }}' }
                })
                .done(function( msg ) {
                    var file_path = "/pdf/purchase_tickets/" + msg + "&timestamp=" + new Date().getTime();
                    printJS(file_path);
                });
            @endif




            $('.save_purchase_ticket').click(function(){
                savePurchaseTicket();
            });

            $('.save_and_close_purchase_ticket').click(function(){
                var hasItem = false;
                $('.pti_item_wrapper').each(function() {
                    var wrapper = $(this);
                    var ptiId = wrapper.find('.pti_id').first().val();
                    var termekTipusId = wrapper.find('.termek_tipus_id').val() || wrapper.find('.termek_tipus_id option:selected').val();
                    if ((ptiId && String(ptiId).trim() !== '') || (termekTipusId && String(termekTipusId).trim() !== '')) {
                        hasItem = true;
                        return false;
                    }
                });

                if (!hasItem) {
                    swal('Nincs tétel', 'Lezárás csak akkor lehetséges, ha legalább egy tétel szerepel a vételi jegyen.', 'warning');
                    return;
                }

                swal({
                    text: "Biztosan menti és lezárja a vételi jegyet?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(result) {
                    if (result) {
                        savePurchaseTicket(null, false, true);
                    }
                });
            });


            $('.print_purchase_ticket').click(function(){
                var hasItem = false;
                $('.pti_item_wrapper').each(function() {
                    var wrapper = $(this);
                    var ptiId = wrapper.find('.pti_id').first().val();
                    var termekTipusId = wrapper.find('.termek_tipus_id').val() || wrapper.find('.termek_tipus_id option:selected').val();
                    if ((ptiId && String(ptiId).trim() !== '') || (termekTipusId && String(termekTipusId).trim() !== '')) {
                        hasItem = true;
                        return false;
                    }
                });

                if (!hasItem) {
                    swal('Nincs tétel', 'Nyomtatás csak akkor lehetséges, ha legalább egy tétel szerepel a vételi jegyen.', 'warning');
                    return;
                }

                swal({
                    text: "Biztosan nyomtatja a vételi jegyet?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(result) {
                    if (result) {
                        if(!$("#purchase_ticket_form")[0].checkValidity()) {
                            $('<input type="submit">').hide().appendTo($('#purchase_ticket_form')).click().remove();
                            return false;
                        }
                        savePurchaseTicket(function() {
                            setTimeout(function() {
                                @if(!$purchase_ticket->is_storno)
                                swal({
                                    text: "Szeretne ügyfélkártyát regisztrálni?",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonClass: 'btn btn-success',
                                    cancelButtonClass: 'btn btn-danger',
                                    confirmButtonText: 'Igen',
                                    cancelButtonText: 'Nem',
                                    buttonsStyling: false
                                }, function(cardResult) {
                                    if (cardResult) {
                                        window.open('/customer_card_reg', '_blank');
                                    }else{
                                        window.location.href = "/purchase_ticket";
                                    }
                                });
                                @endif
                            }, 500);
                        }, true);
                    }
                });
            });



            $('.copy_purchase_ticket').click(function(){

                swal({
                    text: "Biztosan másolja ezt a vételi jegyet?",
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
                        url: "/ajax/copy_purchase_ticket",
                        data: { purchase_ticket_id: '{{ $purchase_ticket->purchase_ticket_id }}' }
                    })
                    .done(function( msg ) {
                        window.location.href = "/purchase_ticket/" + msg + "/edit";
                    });

                });

            });


            $('.cancel_purchase_ticket').click(function(){

                swal({
                    text: "Biztosan sztornózza ezt a vételi jegyet?",
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
                                url: "/ajax/cancel_purchase_ticket",
                                data: { purchase_ticket_id: '{{ $purchase_ticket->purchase_ticket_id }}', storno_reason: $('input[name="reason"]').val() }
                            })
                            .done(function(response) {
                                if(response.data.success == 'success'){
                                    window.location.href = "/purchase_ticket/" + response.data.purchase_ticket_id + "/edit";
                                }else{
                                    $.notify({ icon: "done", message: response.data.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                }
                            });
                        });
                    }, 500);
                });

            });

            $('.save_investigation').click(function(){

                var pti_id = $(this).attr('data-pti_id');
                

                if(!$("#investigation_form_" + pti_id)[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('#investigation_form_' + pti_id)).click().remove();
                    return false;
                }

                let investigation = $('input[name=investigation_'+pti_id+']:checked').val();
                let inv_comment = $('textarea[name=inv_comment_'+pti_id+']').val();
                let goldsmith_id = $('select[name=goldsmith_id_'+pti_id+']').val();

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_investigation",
                    data: { pti_id: pti_id, investigation: investigation, goldsmith_id: goldsmith_id, inv_comment: inv_comment }
                })
                .done(function( response ) {
                    if(response.data.success == 'success'){
                        $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000,z_index: 1060, placement: {from: 'top',align: 'center'}});
                        $('#examine_'+pti_id).addClass('hidden_element');
                        $('#pti_inv_modal_' + pti_id).modal('hide');
                    }else{
                        $.notify({ icon: "done", message: response.data.message }, {type: 'danger',timer: 3000,z_index: 1060, placement: {from: 'top',align: 'center'}});
                    }
                });
            });


            $('.save_investigation_more').click(function(){
                let pti_id = $(this).data('pti_id');
                let quantity = $(this).data('quantity');
                let inv_comment = $('textarea[name=inv_comment_'+pti_id+']').val();
                let goldsmith_id = $('select[name=goldsmith_id_'+pti_id+']').val();
                let totalValue = 0;
                let checkboxData = [];
                let isOption7Checked = false;

                $('.inv_checkbox[data-pti_id="' + pti_id + '"]:checked').each(function(){
                    let currentCheckboxValue = $(this).val();
                    let inputValue = $('input[name="number_investigation_' + pti_id + '_' + currentCheckboxValue + '"]').val();
                    
                    totalValue += parseInt(inputValue);

                    if(currentCheckboxValue == "7") {
                        isOption7Checked = true;
                    }
                    
                    // Adatok gyűjtése egy tömbbe
                    checkboxData.push({
                        id: currentCheckboxValue,
                        value: inputValue
                    });
                });

                if (isOption7Checked) {
                    if (!goldsmith_id || goldsmith_id <= 0) {
                        $.notify({ icon: "done", message: "Ha az ötvösnek opció ki van választva, akkor kötelező megadni az ötvös nevét!" },{
                            type: 'danger',
                            timer: 3000,
                            z_index: 1060,
                            placement: {
                                from: 'top',
                                align: 'center'
                            }
                        });
                        return false;
                    }
                }

                if (totalValue != quantity) {
                    $.notify({ icon: "done", message: "A beírt mennyiségek összege nem egyezik meg az elvárt mennyiséggel!" },{
                        type: 'danger',
                        timer: 3000,
                        z_index: 1060,
                        placement: {
                            from: 'top',
                            align: 'center'
                        }
                    });
                    return false;
                }



                $.ajax({
                    method: "POST",
                    url: "/ajax/save_investigation_more",
                    data: { pti_id: pti_id, goldsmith_id: goldsmith_id, inv_comment: inv_comment,checkboxData: checkboxData,totalValue: totalValue }
                })
                .done(function( response ) {
                    if(response.data.success == 'success'){
                        $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000,z_index: 1060, placement: {from: 'top',align: 'center'}});
                        $('#examine_'+pti_id).addClass('hidden_element');
                        $('#pti_inv_modal_' + pti_id).modal('hide');
                    }else{
                        $.notify({ icon: "done", message: response.data.message }, {type: 'danger',timer: 3000,z_index: 1060, placement: {from: 'top',align: 'center'}});
                    }
                });
            });


        });
    </script>
@endpush