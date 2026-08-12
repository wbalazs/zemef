@extends('layouts.app', [
    'activePage' => 'package-size', 
    'activeButton' => 'package-size', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomagméret létrehozása'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Csomagméret létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('package-size.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="{{ route('package-size.store') }}">
                    @csrf

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
                                                   name="name" value="{{ old('name') }}" 
                                                   placeholder="Csomagméret neve" required autofocus>
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
                                                   name="code" value="{{ old('code') }}" 
                                                   placeholder="S, M, L, XL..." required>
                                            @include('alerts.feedback', ['field' => 'code'])
                                            <small class="form-text text-muted">Rövid kód a méret azonosításához</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('sort_order') ? ' has-danger' : '' }}">
                                                Rendezési sorrend
                                            </label>
                                            <input type="number" class="form-control{{ $errors->has('sort_order') ? ' is-invalid' : '' }}" 
                                                   name="sort_order" value="{{ old('sort_order', 0) }}" 
                                                   placeholder="0">
                                            @include('alerts.feedback', ['field' => 'sort_order'])
                                            <small class="form-text text-muted">Alacsonyabb szám = előrébb a listában</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('length') ? ' has-danger' : '' }}">
                                                Hosszúság (cm)
                                            </label>
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('length') ? ' is-invalid' : '' }}" 
                                                   name="length" value="{{ old('length') }}" 
                                                   placeholder="0.0">
                                            @include('alerts.feedback', ['field' => 'length'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('width') ? ' has-danger' : '' }}">
                                                Szélesség (cm)
                                            </label>
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('width') ? ' is-invalid' : '' }}" 
                                                   name="width" value="{{ old('width') }}" 
                                                   placeholder="0.0">
                                            @include('alerts.feedback', ['field' => 'width'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('height') ? ' has-danger' : '' }}">
                                                Magasság (cm)
                                            </label>
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('height') ? ' is-invalid' : '' }}" 
                                                   name="height" value="{{ old('height') }}" 
                                                   placeholder="0.0">
                                            @include('alerts.feedback', ['field' => 'height'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('weight_limit') ? ' has-danger' : '' }}">
                                                Súly limit (kg)
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('weight_limit') ? ' is-invalid' : '' }}" 
                                                   name="weight_limit" value="{{ old('weight_limit') }}" 
                                                   placeholder="0.00">
                                            @include('alerts.feedback', ['field' => 'weight_limit'])
                                            <small class="form-text text-muted">Maximális megengedett súly</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('price') ? ' has-danger' : '' }}">
                                                Alapár (Ft)
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" 
                                                   name="price" value="{{ old('price', 0) }}" 
                                                   placeholder="0.00">
                                            @include('alerts.feedback', ['field' => 'price'])
                                            <small class="form-text text-muted">Alap szállítási költség</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">További beállítások</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                                Leírás
                                            </label>
                                            <textarea class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" 
                                                      name="description" rows="5" 
                                                      placeholder="Részletes leírás a csomagméretről...">{{ old('description') }}</textarea>
                                            @include('alerts.feedback', ['field' => 'description'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="1" 
                                                       name="status" {{ old('status', true) ? 'checked' : '' }}>
                                                <span class="form-check-sign"></span>
                                                Aktív
                                            </label>
                                            <small class="form-text text-muted">Inaktív csomagméretek nem választhatók új csomagoknál</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Section -->
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h6 class="heading-small text-muted mb-2">Előnézet</h6>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div id="size_preview">
                                                    <p class="text-muted">Adja meg az adatokat az előnézet megjelenítéséhez</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Csomagméret létrehozása
                                </button>
                                <a href="{{ route('package-size.index') }}" class="btn btn-warning">
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
            // Update preview on input change
            function updatePreview() {
                var name = $('input[name="name"]').val() || 'Név';
                var code = $('input[name="code"]').val() || 'KÓD';
                var length = $('input[name="length"]').val();
                var width = $('input[name="width"]').val();
                var height = $('input[name="height"]').val();
                var weightLimit = $('input[name="weight_limit"]').val();
                var price = $('input[name="price"]').val() || '0';
                
                var dimensions = 'Nincs megadva';
                if (length && width && height) {
                    dimensions = length + ' × ' + width + ' × ' + height + ' cm';
                }
                
                var weightText = weightLimit ? weightLimit + ' kg' : 'Nincs korlát';
                var priceText = parseFloat(price).toLocaleString('hu-HU') + ' Ft';
                
                var previewHtml = `
                    <div class="row">
                        <div class="col-4">
                            <strong>${name}</strong><br>
                            <span class="badge badge-info">${code}</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Méretek:</small><br>
                            ${dimensions}
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Súly limit:</small><br>
                            ${weightText}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Alapár:</small>
                            <strong>${priceText}</strong>
                        </div>
                    </div>
                `;
                
                $('#size_preview').html(previewHtml);
            }

            // Bind input events
            $('input[name="name"], input[name="code"], input[name="length"], input[name="width"], input[name="height"], input[name="weight_limit"], input[name="price"]').on('input', updatePreview);

            // Initial preview
            updatePreview();
        });
    </script>
@endpush