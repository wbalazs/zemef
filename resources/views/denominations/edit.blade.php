{{-- resources/views/denominations/edit.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'denominations',
    'activeButton' => 'denominations',
    'title' => 'Zemef ERP',
    'navName' => 'Pénztár tranzakciók'
])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Címlet szerkesztése: {{ $denomination->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('denominations.index', ['currency_id' => $denomination->currency_id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Vissza
                        </a>
                    </div>
                </div>
                <form action="{{ route('denominations.update', $denomination) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency_id">Devizanem *</label>
                                    <select name="currency_id" id="currency_id" class="form-control @error('currency_id') is-invalid @enderror" required>
    <option value="">Válasszon...</option>
    @foreach($currencies as $currency)
        <option value="{{ $currency->currency_id }}" {{ old('currency_id', $denomination->currency_id) == $currency->currency_id ? 'selected' : '' }}>
            {{ $currency->code }} - {{ $currency->name }}
        </option>
    @endforeach
</select>
                                    @error('currency_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Címlet neve *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $denomination->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="value">Címlet érték *</label>
                                    <input type="number" step="0.01" name="value" id="value" 
                                           class="form-control @error('value') is-invalid @enderror" 
                                           value="{{ old('value', $denomination->value) }}" required>
                                    @error('value')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Típus *</label>
                                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="coin" {{ old('type', $denomination->type) == 'coin' ? 'selected' : '' }}>Érme</option>
                                        <option value="banknote" {{ old('type', $denomination->type) == 'banknote' ? 'selected' : '' }}>Bankjegy</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Aktív</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', $denomination->is_active) == 1 ? 'selected' : '' }}>Igen</option>
                                        <option value="0" {{ old('is_active', $denomination->is_active) == 0 ? 'selected' : '' }}>Nem</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Mentés</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection