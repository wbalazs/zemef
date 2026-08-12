@extends('layouts.app', [
    'activePage' => 'shipping-service',
    'activeButton' => 'package',
    'title' => 'Zemef ERP',
    'navName' => 'Szállítási szolgáltató szerkesztése'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Szállítási szolgáltató szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('shipping-service.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('shipping-service.update', $shippingService->id) }}">
                    @csrf
                    @method('patch')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Név *</label>
                                    <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name', $shippingService->name) }}" required>
                                    @include('alerts.feedback', ['field' => 'name'])
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Kód *</label>
                                    <input type="text" name="code" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" value="{{ old('code', $shippingService->code) }}" required>
                                    @include('alerts.feedback', ['field' => 'code'])
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Alap szállítási díj (Ft) *</label>
                                    <input type="number" step="0.01" min="0" name="base_price" class="form-control{{ $errors->has('base_price') ? ' is-invalid' : '' }}" value="{{ old('base_price', $shippingService->base_price) }}" required>
                                    @include('alerts.feedback', ['field' => 'base_price'])
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Rendezési sorrend</label>
                                    <input type="number" min="0" name="sort_order" class="form-control{{ $errors->has('sort_order') ? ' is-invalid' : '' }}" value="{{ old('sort_order', $shippingService->sort_order) }}">
                                    @include('alerts.feedback', ['field' => 'sort_order'])
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" value="1" name="supports_cod" {{ old('supports_cod', $shippingService->supports_cod) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        Utánvét támogatott
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Leírás</label>
                                    <textarea name="description" rows="4" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description', $shippingService->description) }}</textarea>
                                    @include('alerts.feedback', ['field' => 'description'])
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status', $shippingService->status) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        Aktív
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Mentés</button>
                                <a href="{{ route('shipping-service.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Mégse</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
