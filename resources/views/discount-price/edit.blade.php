@extends('layouts.app', [
    'activePage' => 'discount-price', 
    'activeButton' => 'discount-price', 
    'title' => 'Zemef ERP', 
    'navName' => 'Kedvezmény szerkesztése'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Kedvezmény szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('discount-price.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="{{ route('discount-price.update', $discountPrice->id) }}">
                    @csrf
                    @method('patch')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Alap információk</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                Név *
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                                   name="name" value="{{ old('name', $discountPrice->name) }}" 
                                                   placeholder="Kedvezmény neve" required autofocus>
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
                                                   name="code" value="{{ old('code', $discountPrice->code) }}" 
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
                                                <option value="percentage" {{ old('type', $discountPrice->type) == 'percentage' ? 'selected' : '' }}>Százalékos</option>
                                                <option value="fixed" {{ old('type', $discountPrice->type) == 'fixed' ? 'selected' : '' }}>Fix összeg</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'type'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('value') ? ' has-danger' : '' }}">
                                                Érték *
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}" 
                                                   name="value" value="{{ old('value', $discountPrice->value) }}" 
                                                   placeholder="" id="value_input" required>
                                            @include('alerts.feedback', ['field' => 'value'])
                                            <small class="form-text text-muted" id="value_help">
                                                Adja meg a kedvezmény értékét
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('min_order_amount') ? ' has-danger' : '' }}">
                                                Minimális rendelési érték
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('min_order_amount') ? ' is-invalid' : '' }}" 
                                                   name="min_order_amount" value="{{ old('min_order_amount', $discountPrice->min_order_amount) }}" 
                                                   placeholder="0.00">
                                            @include('alerts.feedback', ['field' => 'min_order_amount'])
                                            <small class="form-text text-muted">Minimum rendelési érték a kedvezmény érvényesítéséhez</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('max_discount_amount') ? ' has-danger' : '' }}">
                                                Maximális kedvezmény
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('max_discount_amount') ? ' is-invalid' : '' }}" 
                                                   name="max_discount_amount" value="{{ old('max_discount_amount', $discountPrice->max_discount_amount) }}" 
                                                   placeholder="0.00">
                                            @include('alerts.feedback', ['field' => 'max_discount_amount'])
                                            <small class="form-text text-muted">Maximális kedvezmény összeg (százalékos típusnál)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Korlátozások és beállítások</h6>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('valid_from') ? ' has-danger' : '' }}">
                                                Érvényesség kezdete
                                            </label>
                                            <input type="date" class="form-control{{ $errors->has('valid_from') ? ' is-invalid' : '' }}" 
                                                   name="valid_from" value="{{ old('valid_from', $discountPrice->valid_from ? $discountPrice->valid_from->format('Y-m-d') : '') }}">
                                            @include('alerts.feedback', ['field' => 'valid_from'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('valid_to') ? ' has-danger' : '' }}">
                                                Érvényesség vége
                                            </label>
                                            <input type="date" class="form-control{{ $errors->has('valid_to') ? ' is-invalid' : '' }}" 
                                                   name="valid_to" value="{{ old('valid_to', $discountPrice->valid_to ? $discountPrice->valid_to->format('Y-m-d') : '') }}">
                                            @include('alerts.feedback', ['field' => 'valid_to'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('usage_limit') ? ' has-danger' : '' }}">
                                                Felhasználási korlát
                                            </label>
                                            <input type="number" class="form-control{{ $errors->has('usage_limit') ? ' is-invalid' : '' }}" 
                                                   name="usage_limit" value="{{ old('usage_limit', $discountPrice->usage_limit) }}" 
                                                   placeholder="Korlátlan">
                                            @include('alerts.feedback', ['field' => 'usage_limit'])
                                            <small class="form-text text-muted">Hányszor használható fel a kedvezmény (üresen hagyva korlátlan)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                                Leírás
                                            </label>
                                            <textarea class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" 
                                                      name="description" rows="3" 
                                                      placeholder="Kedvezmény leírása...">{{ old('description', $discountPrice->description) }}</textarea>
                                            @include('alerts.feedback', ['field' => 'description'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="1" 
                                                       name="status" {{ old('status', $discountPrice->status) ? 'checked' : '' }}>
                                                <span class="form-check-sign"></span>
                                                Aktív
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                        <div class="alert alert-info">
                                            <strong>Felhasználási statisztika:</strong><br>
                                            Eddig felhasználva: <strong>{{ $discountPrice->used_count }}</strong> alkalommal
                                            @if($discountPrice->usage_limit)
                                                ({{ $discountPrice->usage_limit - $discountPrice->used_count }} maradt)
                                            @endif
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
                                <a href="{{ route('discount-price.index') }}" class="btn btn-warning">
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
            // Type selector change
            $('#type_selector').change(function(){
                var type = $(this).val();
                updateValueField(type);
            });

            function updateValueField(type) {
                var valueInput = $('#value_input');
                var valueHelp = $('#value_help');

                if (type === 'percentage') {
                    valueInput.attr('placeholder', '25');
                    valueInput.attr('min', '0');
                    valueInput.attr('max', '100');
                    valueHelp.text('Százalékos érték (0-100%)');
                } else if (type === 'fixed') {
                    valueInput.attr('placeholder', '1000');
                    valueInput.attr('min', '0');
                    valueInput.removeAttr('max');
                    valueHelp.text('Fix összeg forintban');
                } else {
                    valueInput.attr('placeholder', '');
                    valueHelp.text('Adja meg a kedvezmény értékét');
                }
            }

            // Initialize fields on page load
            var initialType = $('#type_selector').val();
            updateValueField(initialType);

            // Date validation
            $('input[name="valid_from"], input[name="valid_to"]').change(function(){
                var startDate = new Date($('input[name="valid_from"]').val());
                var endDate = new Date($('input[name="valid_to"]').val());
                
                if (startDate && endDate && startDate > endDate) {
                    alert('A kezdő dátum nem lehet későbbi, mint a vég dátum!');
                    $('input[name="valid_to"]').val('');
                }
            });
        });
    </script>
@endpush