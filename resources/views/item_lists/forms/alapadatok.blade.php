        <div class="row">
            <div class="col-lg-3">
                <fieldset>
                    <div class="form-group">
                        <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">
                            Ügyfél</label>
                        <select @if($stock_entry->printed_at) disabled @endif required name="customer_id" class="select2 form-control{{ $errors->has('customer_id') ? ' is-invalid' : '' }}">
                            <option value="">- válasszon -</option>
                            @foreach($customers as $customer)
                                <option @if($customer->customer_id == $stock_entry->customer_id) selected @endif value="{{ $customer->customer_id }}" >{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-2">
                <fieldset>
                    <div class="form-group">
                        <?php
                            $zip = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->zip : '';
                            $zip_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->zip_id : '0';
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
                            $city = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->city : '';
                            $city_id = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->city_id : '0';
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
                            $address = (isset($stock_entry->customer)) ? $stock_entry->customer->addresses->where('address_type', 'Lakcím')->first()->address : '';
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
                            $tax_number = (isset($stock_entry->customer)) ? $stock_entry->customer->tax_number : '';
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
                        <select @if($stock_entry->printed_at) disabled @endif required name="currency_id" class="select2 form-control{{ $errors->has('currency_id') ? ' is-invalid' : '' }}">
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
                        <?php $currency_value = (isset($stock_entry->currency_value)) ? $stock_entry->currency_value : ''; ?>
                        <label class="form-control-label{{ $errors->has('currency_value') ? ' has-danger' : '' }}">
                            Árfolyam</label>
                        <input type="number" @if($stock_entry->printed_at) disabled @endif step=".01" class="form-control{{ $errors->has('currency_value') ? ' is-invalid' : '' }}" name="currency_value" value="{{ old('currency_value', $selected_currency_value) }}" placeholder="" @if($selected_currency_editable == '0') disabled @endif required>
                        @include('alerts.feedback', ['field' => 'currency_value'])
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-2">
                <fieldset>
                    <div class="form-group">
                        <div class="form-check">
                            <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" id="has_contract" name="has_contract" {{ $stock_entry->has_contract ? 'checked' : '' }}>
                            <span class="form-check-sign"></span>
                            Szerződés
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-2">
                <fieldset>
                    <div class="form-group">
                        <label class="form-control-label{{ $errors->has('contract_number') ? ' has-danger' : '' }}">
                            Szerződés szám</label>
                        <input type="text" {{ $stock_entry->has_contract ? '' : 'disabled' }} class="form-control{{ $errors->has('contract_number') ? ' is-invalid' : '' }}" id="contract_number" name="contract_number" value="{{ old('contract_number', $stock_entry->contract_number) }}">
                        @include('alerts.feedback', ['field' => 'contract_number'])
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
                        <input type="text" class="form-control" name="ticket_number" value="{{ $stock_entry->ticket_number }}" disabled>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset>
                    <div class="form-group">
                        <label class="form-control-label{{ $errors->has('fulfillment_date') ? ' has-danger' : '' }}">
                            Teljesítés dátum</label>
                        <input type="date" class="form-control" name="fulfillment_date" id="fulfillment_date" value="{{ $stock_entry->fulfillment_date }}" required>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset>
                    <div class="form-group">
                        <label class="form-control-label{{ $errors->has('invoice_number') ? ' has-danger' : '' }}">
                            Számla sorszám</label>
                        <input type="text" @if($stock_entry->printed_at) disabled @endif class="form-control" name="invoice_number" value="{{ $stock_entry->invoice_number }}" required>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset>
                    <div class="form-group">
                        <label @if($stock_entry->printed_at) disabled @endif class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">
                            Státusz</label>
                        <select @if($stock_entry->printed_at) disabled @endif title="- válasszon -" name="status" disabled class="select2 form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
                            <option @if($stock_entry->status != "2") selected @endif value="1">Folyamatban</option>
                            <option @if($stock_entry->status == "2") selected @endif value="2">Feldolgozva</option>
                        </select>
                    </div>
                </fieldset>
            </div>
        </div>