@extends('layouts.app', [
    'activePage' => 'cash-register',
    'activeButton' => 'cash-register',
    'title' => 'Zemef ERP',
    'navName' => 'Pénztár tranzakciók'
])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pénztár szerkesztése: {{ $cashRegister->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('cash-registers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Vissza
                        </a>
                    </div>
                </div>
                <form action="{{ route('cash-registers.update', $cashRegister) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Megnevezés *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $cashRegister->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prefix">Prefix * (egyedi bizonylat előtag)</label>
                                    <input type="text" name="prefix" id="prefix" class="form-control @error('prefix') is-invalid @enderror" 
                                           value="{{ old('prefix', $cashRegister->prefix) }}" required maxlength="10">
                                    @error('prefix')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="currency_id">Deviza *</label>
                                    <select name="currency_id" id="currency_id" class="form-control @error('currency_id') is-invalid @enderror" required>
                                        <option value="">Válasszon...</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->currency_id }}" {{ old('currency_id', $cashRegister->currency_id) == $currency->currency_id ? 'selected' : '' }}>
                                                {{ $currency->sign }} - {{ $currency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="opening_balance">Induló érték *</label>
                                    <input type="number" step="0.01" name="opening_balance" id="opening_balance" 
                                           class="form-control @error('opening_balance') is-invalid @enderror" 
                                           value="{{ old('opening_balance', $cashRegister->opening_balance) }}" required>
                                    @error('opening_balance')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="opening_balance_date">Induló érték dátuma *</label>
                                    <input type="date" name="opening_balance_date" id="opening_balance_date" 
                                           class="form-control @error('opening_balance_date') is-invalid @enderror" 
                                           value="{{ old('opening_balance_date', $cashRegister->opening_balance_date->format('Y-m-d')) }}" required>
                                    @error('opening_balance_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="business_id">Üzlet *</label>
                                    <select name="business_id" id="business_id" class="form-control @error('business_id') is-invalid @enderror" required>
                                        <option value="">Válasszon...</option>
                                        @foreach($businesses as $business)
                                            <option value="{{ $business->business_id }}" {{ old('business_id', $cashRegister->business_id) == $business->business_id ? 'selected' : '' }}>
                                                {{ $business->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('business_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee_id">Munkavállaló</label>
                                    <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror">
                                        <option value="">Nincs hozzárendelve</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->employee_id }}" {{ old('employee_id', $cashRegister->employee_id) == $employee->employee_id ? 'selected' : '' }}>
                                                {{ $employee->firstname. ' ' . $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Aktív</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', $cashRegister->is_active) == 1 ? 'selected' : '' }}>Igen</option>
                                        <option value="0" {{ old('is_active', $cashRegister->is_active) == 0 ? 'selected' : '' }}>Nem</option>
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