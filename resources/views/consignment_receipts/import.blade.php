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
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <?php
                                                                $tax_number = (isset($consignment_receipt->customer)) ? $consignment_receipt->customer->tax_number : '';
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

                                            @foreach($consignment_receipt->consignment_receipt_items as $item)

                                                <hr>
                                                <div class="cri_item_wrapper @if($item->status != 2) orange_bg @else green_bg @endif">
                                                    <input type="hidden" name="cri_id" class="cri_id" value="{{ $item->consignment_receipt_item_id }}">
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
                                                                        <input @if($item->product->is_unique == 1) checked @endif class="form-check-input cri_is_unique" type="checkbox" value="2" data-gtm-form-interact-field-id="1" disabled name="cri_is_unique[]">
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
                                                            <button data-cri_id="{{ $item->consignment_receipt_item_id }}" data-toggle="modal" data-target="#cri_images_modal_{{ $item->consignment_receipt_item_id }}" type="button" class="btn btn-success cri_button consignment_receipt_item_images">Képek <i class="fa fa-image"></i></button>
                                                            <button data-cri_id="{{ $item->consignment_receipt_item_id }}" class="btn btn-success cri_button captureButton"><i class="fa fa-camera"></i> Kép készítése</button>
                                                            <input type="file" accept="image/*" capture="camera" name="image" class="imageInput" style="display: none;">
                                                            @if(!$item->investigation && $consignment_receipt->printed_at)
                                                                <button id="examine_{{ $item->consignment_receipt_item_id }}" data-cri_id="{{ $item->consignment_receipt_item_id }}" data-toggle="modal" data-target="#cri_product_modal_{{ $item->consignment_receipt_item_id }}" type="button" class="btn btn-success cri_button consignment_receipt_item_inv">Vizsgálat<i class="fa fa-search"></i></button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="modal fade" id="cri_images_modal_{{ $item->consignment_receipt_item_id }}">
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
                                                                            <form action="/ajax/upload_cri_image" id="cri_image_form_{{ $item->consignment_receipt_item_id }}" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                                <div class="dz-message needsclick">
                                                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                                                    <input type="hidden" name="cri_id" value="{{ $item->consignment_receipt_item_id }}">
                                                                                    @csrf
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                

                                                                <div class="row">
                                                                    <div class="col-lg-12">

                                                                        <div class="gallery_container cri_gallery_container_{{ $item->consignment_receipt_item_id }}">
                                                                            
                                                                            <div class="delete_buttons">
                                                                                @foreach($item->consignment_receipt_images as $img)
                                                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                                                        <button data-mod="" type="button" data-cri_id="{{ $item->consignment_receipt_item_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            
                                                                            <div data-lightGallery="1" id="lightgallery_cri_images_{{ $item->consignment_receipt_item_id }}" class="">
                                                                                @foreach($item->consignment_receipt_images as $img)
                                                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="cri_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path; ?>">
                                                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive cri_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path; ?>" alt="">
                                                                                    </div>
                                                                                @endforeach
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

                                                
                                                <div class="modal fade" id="cri_product_modal_{{ $item->consignment_receipt_item_id }}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                      
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Termék adatlap</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <!-- Modal body -->
                                                            <div class="modal-body nomargin">
                                                                
                                                                <form id="product_form_{{ $item->consignment_receipt_item_id }}" method="post" action="">
                                                                    @include('products.edit_body', ['product' => $item->product, 'categories' => $categories, 'hallmarks' => $hallmarks, 'product_colors' => $product_colors, 'product_statuses' => $product_statuses, 'product_signs' => $product_signs])
                                                                </form>

                                                            </div>
                                                            
                                                            <!-- Modal footer -->
                                                            <hr>
                                                            <div class="modal-footer">
                                                                <button data-cri_id="{{ $item->consignment_receipt_item_id }}" type="button" class="btn btn-success fr save_product">Mentés</button>
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


        function initCRIGallery(cri_id, mod="") {
            var $initScope_implementation = $('#lightgallery_cri_images'+mod+'_' + cri_id);

            console.log('init #lightgallery_cri_images'+mod+'_' + cri_id);

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
                let = consignment_receipt_id = '{{ $consignment_receipt->consignment_receipt_id }}';
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
                let cri_id = $(this).prev('.captureButton').data('cri_id');
                formData.append('cri_id', cri_id);

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
                                data: { cri_id: cri_id, mod: '' }
                            })
                            .done(function(html) {
                                $('.cri_gallery_container_' + cri_id).html(html);
                                checkImages(cri_id, '');
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


            @foreach($consignment_receipt->consignment_receipt_items as $item)
                $('#lightgallery_cri_images_{{ $item->consignment_receipt_item_id }}').hide();
                $('#lightgallery_cri_images_{{ $item->consignment_receipt_item_id }}').css('opacity', 0);
                $('#cri_images_modal_{{ $item->consignment_receipt_item_id }}').on('shown.bs.modal', function (e) {
                    $("#cri_image_form_{{ $item->consignment_receipt_item_id }}").dropzone({
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
                                data: { cri_id: {{ $item->consignment_receipt_item_id }}, image: response }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_cri_gallery",
                                data: { cri_id: {{ $item->consignment_receipt_item_id }}, mod: '' }
                            })
                            .done(function( html ) {
                                $('.cri_gallery_container_{{ $item->consignment_receipt_item_id }}').html(html);
                                checkImages({{ $item->consignment_receipt_item_id }}, '');
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        }
                    });

                    checkImages({{ $item->consignment_receipt_item_id }}, '');
                });


                $('#lightgallery_cri_images2_{{ $item->consignment_receipt_item_id }}').hide();
                $('#lightgallery_cri_images2_{{ $item->consignment_receipt_item_id }}').css('opacity', 0);
                $('#cri_inv_modal_{{ $item->consignment_receipt_item_id }}').on('shown.bs.modal', function (e) {

                    $("#cri_image_form2_{{ $item->consignment_receipt_item_id }}").dropzone({
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
                                data: { cri_id: {{ $item->consignment_receipt_item_id }}, image: response, module: "sei2" }
                            });
                        },
                        queuecomplete: function(file, response) {
                            $.ajax({
                                method: "POST",
                                url: "/ajax/load_cri_gallery",
                                data: { cri_id: {{ $item->consignment_receipt_item_id }}, mod: '2' }
                            })
                            .done(function( html ) {
                                $('.cri_gallery_container2_{{ $item->consignment_receipt_item_id }}').html(html);
                                checkImages({{ $item->consignment_receipt_item_id }}, 2);
                                $('.dz-complete').remove();
                                $('.dz-message').show();
                            });
                        }
                    });

                    checkImages({{ $item->consignment_receipt_item_id }}, 2);

                });
            @endforeach




            $("body").on("click", ".delete_img", function(e){
                var img_id = $(this).attr('data-img_id');
                var cri_id = $(this).attr('data-cri_id');
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
                            data: { cri_id: cri_id, mod: mod }
                        })
                        .done(function( html ) {
                            $('.cri_gallery_container'+mod+'_' + cri_id).html(html);
                            checkImages(cri_id, mod);
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

                    var cri_row_html = `
                    <hr>
                    <div class="orange_bg cri_empty_row">
                        <input type="hidden" name="cri_id" class="cri_id">
                        <div class="row">
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Megnevezés</label>
                                        <select class="select2 form-control cri_name" name="cri_name[]">
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
                                                <input class="form-check-input cri_is_unique" checked type="checkbox" value="2" data-gtm-form-interact-field-id="1" name="cri_is_unique[]">
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
                                        <input type="text" class="form-control cri_description" name="cri_description[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Mennyiség</label>
                                        <input type="number" value="1" class="form-control cri_quantity" name="cri_quantity[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Egységár</label>
                                        <input type="number" class="form-control cri_oneprice" name="cri_oneprice[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Ár</label>
                                        <input type="number" disabled class="form-control cri_price" name="cri_price[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Gyártási szám</label>
                                        <input type="text" class="form-control cri_factory_number" name="cri_factory_number[]">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-2">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="form-control-label">Súly</label>
                                        <input type="number" class="form-control cri_weight" name="cri_weight[]">
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                `;

                    $('.consignment_receipt_items_rows').append(cri_row_html);
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
                    url: "/ajax/save_consignment_receipt",
                    data: { 
                        consignment_receipt_id: '{{ $consignment_receipt->consignment_receipt_id }}', 
                        customer_id: customer_id, 
                        business_id: business_id, 
                        zip: zip, 
                        city: city, 
                        address: address, 
                        identity_card_number: identity_card_number, 
                        tax_number: tax_number,
                        currency_id: currency_id,
                        currency_value: currency_value,
                        expire_date: expire_date,
                        is_hallmark_requested: is_hallmark_requested,
                        invoice_number: invoice_number,
                        storage_id: storage_id
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
                                cri_id: sei.parents('.cri_item_wrapper').find('.cri_id').val(),
                                cri_name: sei.val(),
                                cri_is_unique: sei.parents('.cri_item_wrapper').find('.cri_is_unique').is(':checked'),
                                cri_description: sei.parents('.cri_item_wrapper').find('.cri_description').val(),
                                cri_quantity: sei.parents('.cri_item_wrapper').find('.cri_quantity').val(),
                                cri_oneprice: sei.parents('.cri_item_wrapper').find('.cri_oneprice').val(),
                                cri_price: sei.parents('.cri_item_wrapper').find('.cri_price').val(),
                                cri_factory_number: sei.parents('.cri_item_wrapper').find('.cri_factory_number').val(),
                                cri_weight: sei.parents('.cri_item_wrapper').find('.cri_weight').val(),
                                storage_id: storage_id,
                            };

                            console.log(cri_data);

                            var request = $.ajax({
                                method: "POST",
                                url: "/ajax/save_consignment_receipt_item",
                                data: cri_data
                            }).done(function(respItem) {
                                sei.parents('.cri_item_wrapper').find('.cri_id').val(respItem);
                            });

                            ajaxRequests.push(request);
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
                var cri_id = $(this).attr('data-cri_id');
                $('#cri_inv_modal_' + cri_id).modal('hide');

                setTimeout(function() { 
                    $('#cri_product_modal_' + cri_id).modal('show');
                }, 500);
            });


            $('.save_product').click(function(){

                var cri_id = $(this).attr('data-cri_id');
                var storage_id = $('select[name=storage_id]').val();
                var formData = $("#product_form_"+cri_id).serialize();
                

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_cri_product",
                    data: { cri_id: cri_id,storage_id:storage_id, form_data: formData }
                })
                .done(function( resp ) {
                    $('#examine_'+cri_id).addClass('hidden_element');
                    $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                }).fail(function() {
                    $.notify({ icon: "done", message: "Hiba a mentés közben." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                });


            });


        });
    </script>
@endpush