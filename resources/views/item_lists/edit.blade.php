<form id="import_stock_entry_form" class="compact-form">
    @csrf
    <div class="sei_item_wrapper">
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" id="sei_error" style="display: none;">
            <i class="fas fa-exclamation-circle"></i> Kérem töltse ki a mezőket!
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" name="sei_factory_number" id="sei_factory_number" class="form-control" value="F-2023-001" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label required-field">Termék neve</label>
                    <input type="text" class="form-control" name="sei_name" id="sei_name" value="" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Termék leírás</label>
                    <textarea class="form-control" name="sei_description" id="sei_description" rows="2"></textarea>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="row">
                    <div class="col-6">
                        <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                            Üzlet</label>
                        <select required name="business_id" id="business_id" class="select2 form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                            <option value="">- válasszon -</option>
                            @foreach($businesses as $business)
                                <option @if($business->business_id == $itemList->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-control-label{{ $errors->has('storage_id') ? ' has-danger' : '' }}">
                            Raktár</label>
                        <select title="- válasszon -" required name="storage_id" id="storage_id" class="select2 form-control{{ $errors->has('storage_id') ? ' is-invalid' : '' }}">
                            <option value="">- válasszon -</option>
                            @if(isset($warehouse))
                                <option value="{{ $warehouse->warehouse_id }}" selected>{{ $warehouse->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-4">
                        <label class="form-label required-field">Darab</label>
                        <input type="number" class="form-control sei_quantity" name="sei_quantity" id="sei_quantity" value="1" min="1" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label required-field">Egység ár (HUF)</label>
                        <input type="number" class="form-control sei_oneprice" name="sei_oneprice" id="sei_oneprice" value="" step="0.01" required>
                    </div>                        
                    <div class="col-4">
                        <label class="form-label">Teljes ár (HUF)</label>
                        <input type="number" class="form-control sei_price" name="sei_price" id="sei_price" value="">
                    </div>                        
                </div>
                <div class="row">
                    <div class="col-4">
                        <label class="form-label">Súly (gramm)</label>
                        <input type="number" class="form-control" name="sei_weight" value="" id="sei_weight">
                    </div>
                    <div class="col-4">
                        <br /><input type="checkbox" class="form-check-input" value="1" name="sei_is_unique" id="sei_is_unique">
                        <label class="form-check-label" for="sei_is_unique">Egyedi?</label>
                    </div>
                    <div class="col-4">
                    </div>
            </div>
        
            
        </div>
    </div>
</form>