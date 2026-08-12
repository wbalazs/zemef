                            <div class="col-lg-12">
                                <div class="row">
                                <div class="col-lg-4">
                                    <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Termék adatai</h6>  
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                            Megnevezés</label>
                                                        <input type="text" @if($show) disabled @endif class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $product->name) }}" placeholder="" required autofocus>
                                                        @include('alerts.feedback', ['field' => 'name'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-9">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                            Márka</label>
                                                        <input type="text" @if($show) disabled @endif class="form-control{{ $errors->has('brand') ? ' is-invalid' : '' }}" name="brand" maxlength="100" value="{{ old('brand', $product->brand) }}" placeholder="">
                                                        @include('alerts.feedback', ['field' => 'brand'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-3">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                            Vegyjel</label>
                                                        <input type="text" @if($show) disabled @endif class="form-control{{ $errors->has('au') ? ' is-invalid' : '' }}" name="au" maxlength="100" value="{{ old('au', $product->au) }}" placeholder="">
                                                        @include('alerts.feedback', ['field' => 'au'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('short_description') ? ' has-danger' : '' }}">
                                                            Rövid leírás</label>
                                                        <input type="text" @if($show) disabled @endif class="form-control{{ $errors->has('short_description') ? ' is-invalid' : '' }}" name="short_description" maxlength="255" value="{{ old('short_description', $product->short_description) }}" placeholder="">
                                                        @include('alerts.feedback', ['field' => 'short_description'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('long_description') ? ' has-danger' : '' }}">
                                                            Hosszú leírás</label>
                                                        <textarea rows="8" @if($show) disabled @endif class="zm_textarea form-control{{ $errors->has('long_description', $product->long_description) ? ' is-invalid' : '' }}" name="long_description">{{ old('long_description', $product->long_description) }}</textarea>
                                                        @include('alerts.feedback', ['field' => 'long_description'])
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group{{ $errors->has('category_id') ? ' has-danger' : '' }}">
                                                    <label class="form-control-label" for="input-role">Kategória</label>
                                                    <select @if($show) disabled @endif title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="category_id" id="input-role" class="selectpicker form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}" placeholder="">

                                                        @include('categories.category_options', ['categories' => $categories, 'category_id' => $product->category_id])

                                                    </select>

                                                    @include('alerts.feedback', ['field' => 'category_id'])
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group{{ $errors->has('status') ? ' has-danger' : '' }}">
                                                    <label class="form-control-label" for="input-role">Státusz</label>
                                                    <select @if($show) disabled @endif title="- válasszon státuszt -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="status" id="input-role" class="selectpicker form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" placeholder="">

                                                        <option @if($product->status == 1) selected @endif value="1">Aktív</option>
                                                        <option @if($product->status == 2) selected @endif value="2">Inaktív</option>
                                                        <option @if($product->status == 3) selected @endif value="3">Feldolgozás alatt</option>
                                                        <option @if($product->status == 4) selected @endif value="4">Sztornózva</option>

                                                    </select>

                                                    @include('alerts.feedback', ['field' => 'status'])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($product->is_rent_available) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_rent_available">
                                                            <span class="form-check-sign"></span>
                                                            Bérelhető?
                                                            </label>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card-body">
                                        <h6 class="heading-small text-muted mb-4">Termék tulajdonságai</h6>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('weight') ? ' has-danger' : '' }}">
                                                            Súly</label>
                                                        <input type="number" step="0.1" disabled class="form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}" name="weight" maxlength="100" value="{{ old('weight', $product->weight) }}" placeholder="">
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('calculated_price') ? ' has-danger' : '' }}">
                                                            Számított ár</label>
                                                        <input type="number" disabled step="0.1" disabled class="form-control{{ $errors->has('calculated_price') ? ' is-invalid' : '' }}" name="calculated_price" maxlength="100" value="{{ old('calculated_price', $product->calculated_price) }}" placeholder="">
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('real_price') ? ' has-danger' : '' }}">
                                                            Valós ár</label>
                                                        <input type="number" @if($show) disabled @endif step="0.1" class="form-control{{ $errors->has('real_price') ? ' is-invalid' : '' }}" name="real_price" maxlength="100" value="{{ old('real_price', $product->real_price) }}" placeholder="">
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('price_type') ? ' has-danger' : '' }}">
                                                            Egységár típus</label>
                                                            <select @if($show) disabled @endif title="- válasszon egységár típust -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="price_type" id="input-role" class="selectpicker form-control{{ $errors->has('price_type') ? ' is-invalid' : '' }}" placeholder="">
                                                                @foreach($price_types as $price_type)
                                                                    <option @if($price_type->unit_price_type_id == $product->price_type) selected @endif value="{{ $price_type->unit_price_type_id }}" >{{ $price_type->name.' - '.number_format($price_type->price,0,'',' ') }}</option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('vat_type') ? ' has-danger' : '' }}">
                                                            Áfa típus</label>
                                                            <select @if($show) disabled @endif title="- válasszon áfa típust -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="vat_type" id="input-role" class="selectpicker form-control{{ $errors->has('vat_type') ? ' is-invalid' : '' }}" placeholder="">
                                                                @foreach($vat_types as $vat_type)
                                                                    <option @if($vat_type->vat_type_id == $product->vat_type) selected @endif value="{{ $vat_type->vat_type_id }}" >{{ $vat_type->name.' - '.$vat_type->value }} %</option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-4">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                            <input @if($product->discount_allowed) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="discount_allowed">
                                                            <span class="form-check-sign"></span>
                                                            Kedvezmény adható?
                                                            </label>
                                                        </div>

                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('hallmarks') ? ' has-danger' : '' }}">
                                                            Fémjelzések</label>
                                                        <select @if($show) disabled @endif multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="hallmarks[]" class="selectpicker form-control{{ $errors->has('hallmarks') ? ' is-invalid' : '' }}">
                                                        @foreach($hallmarks as $hallmark)
                                                            <option @if(in_array($hallmark->hallmark_id,$product->hallmarks->pluck('hallmark_id')->toArray())) selected @endif value="{{ $hallmark->hallmark_id }}" >{{ $hallmark->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
    
                                        @foreach($product->hallmarks as $hallmark)
                                            <?php $hallmark_key = 'hallmark_' . $hallmark->hallmark_id; ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
    
                                                            @if($hallmark->type == 'checkbox')
                                                                <div class="form-check">
                                                                    <label class="form-check-label">
                                                                    <input type="hidden" data-hallmark_id="{{ $hallmark->hallmark_id }}" name="hallmark_{{ $hallmark->hallmark_id }}" value="{{$hallmark->pivot->value}}"> 
                                                                    <input @if($show) disabled @endif @if($hallmark->pivot->value) checked @endif data-hallmark_id="{{ $hallmark->hallmark_id }}" class="form-check-input hallmark_checkes" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="hallmark_check_{{ $hallmark->hallmark_id }}">
                                                                    <span class="form-check-sign"></span>
                                                                    {{$hallmark->name}}
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                                    {{$hallmark->name}}</label>
                                                                <input @if($show) disabled @endif type="{{$hallmark->type}}" class="form-control{{ $errors->has($hallmark_key) ? ' is-invalid' : '' }}" name="{{$hallmark_key}}" value="{{ old($hallmark_key, $hallmark->pivot->value) }}">
                                                                @include('alerts.feedback', ['field' => $hallmark_key])
                                                            @endif
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row product_colors_row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('product_colors') ? ' has-danger' : '' }}">
                                                            Szín típusok</label>
                                                        <select @if($show) disabled @endif multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="product_colors[]" class="selectpicker product_colors_select form-control{{ $errors->has('product_colors') ? ' is-invalid' : '' }}">
                                                        @foreach($product_colors as $product_color)
                                                            <option @if(in_array($product_color->product_color_id,$product->product_colors->pluck('product_color_id')->toArray())) selected @endif value="{{ $product_color->product_color_id }}" >{{ $product_color->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="product_colors_div">
                                            @foreach($product->product_colors as $product_color)
                                                <?php $product_color_key = 'product_color_' . $product_color->product_color_id; ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                                    {{$product_color->name}}</label>
                                                                <input @if($show) disabled @endif type="text" class="form-control{{ $errors->has($product_color_key) ? ' is-invalid' : '' }}" name="{{$product_color_key}}" value="{{ old($product_color_key, $product_color->pivot->value) }}">
                                                                @include('alerts.feedback', ['field' => $product_color_key])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
    
                                        
    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('product_statuses') ? ' has-danger' : '' }}">
                                                            Állapotok</label>
                                                        <select @if($show) disabled @endif multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="product_statuses[]" class="selectpicker product_statuses_select form-control{{ $errors->has('product_statuses') ? ' is-invalid' : '' }}">
                                                        @foreach($product_statuses as $product_status)
                                                            <option @if(in_array($product_status->product_status_id,$product->product_statuses->pluck('product_status_id')->toArray())) selected @endif value="{{ $product_status->product_status_id }}" >{{ $product_status->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
    
                                        <div class="product_statuses_div">
                                            @foreach($product->product_statuses as $product_status)
                                                <?php $product_status_key = 'product_status_' . $product_status->product_status_id; ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                                    {{$product_status->name}}</label>
                                                                <input @if($show) disabled @endif type="text" class="form-control{{ $errors->has($product_status_key) ? ' is-invalid' : '' }}" name="{{$product_status_key}}" value="{{ old($product_status_key, $product_status->pivot->comment) }}">
                                                                @include('alerts.feedback', ['field' => $product_status_key])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <div class="form-group">
                                                        <label class="form-control-label{{ $errors->has('product_signs') ? ' has-danger' : '' }}">
                                                            Jelzések</label>
                                                        <select @if($show) disabled @endif multiple title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="product_signs[]" class="selectpicker product_signs_select form-control{{ $errors->has('product_signs') ? ' is-invalid' : '' }}">
                                                        @foreach($product_signs as $product_sign)
                                                            <option @if(in_array($product_sign->product_sign_id,$product->product_signs->pluck('product_sign_id')->toArray())) selected @endif value="{{ $product_sign->product_sign_id }}" >{{ $product_sign->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
    
                                        <div class="product_signs_div">
                                            @foreach($product->product_signs as $product_sign)
                                                <?php $product_sign_key = 'product_sign_' . $product_sign->product_sign_id; ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                                    {{$product_sign->name}}</label>
                                                                <input @if($show) disabled @endif type="text" class="form-control{{ $errors->has($product_sign_key) ? ' is-invalid' : '' }}" name="{{$product_sign_key}}" value="{{ old($product_sign_key, $product_sign->pivot->comment) }}">
                                                                @include('alerts.feedback', ['field' => $product_sign_key])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            </div>
                        </div>

        <!--<script src="{{ asset('js/core/jquery.3.6.0.min.js') }}" type="text/javascript"></script>-->
        <script>
            $(document).ready(function() {
                    $('.product_colors_select').change(function(e){
                        e.stopPropagation();
                        let colors = $(this).val();
                        let product_id = '{{ $product->product_id }}';
                        $.ajax({
                                method: "POST",
                                url: "/ajax/product_colors_ajax",
                                data: { product_id: product_id, colors: colors }
                        })
                        .done(function( msg ) {
                            $('.product_colors_div').empty().html(msg);
                        });
                        

                    });

                    $('.product_statuses_select').change(function(e){
                        e.stopPropagation();
                        let statuses = $(this).val();
                        let product_id = '{{ $product->product_id }}';
                        $.ajax({
                                method: "POST",
                                url: "/ajax/product_statuses_ajax",
                                data: { product_id: product_id, statuses: statuses }
                        })
                        .done(function( msg ) {
                            $('.product_statuses_div').empty().html(msg);
                        });
                        

                    });

                    $('.product_signs_select').change(function(e){
                        e.stopPropagation();
                        let signs = $(this).val();
                        let product_id = '{{ $product->product_id }}';
                        $.ajax({
                                method: "POST",
                                url: "/ajax/product_signs_ajax",
                                data: { product_id: product_id, signs: signs }
                        })
                        .done(function( msg ) {
                            $('.product_signs_div').empty().html(msg);
                        });
                        

                    });
            });
        </script>