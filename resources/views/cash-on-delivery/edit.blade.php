@extends('layouts.app', [
    'activePage' => 'cash-on-delivery', 
    'activeButton' => 'cash-on-delivery', 
    'title' => 'Zemef ERP', 
    'navName' => 'Utánvét szerkesztése'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Utánvét szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('cash-on-delivery.show', $cashOnDelivery->id) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i> Megtekintés
                            </a>
                            <a href="{{ route('cash-on-delivery.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="{{ route('cash-on-delivery.update', $cashOnDelivery->id) }}">
                    @csrf
                    @method('patch')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Alap információk</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Csomag</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $cashOnDelivery->package->package_number ?? 'Ismeretlen' }} - {{ $cashOnDelivery->package->order->order_id ?? 'Ismeretlen' }}" 
                                                   readonly>
                                            <small class="form-text text-muted">A csomag nem módosítható</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('amount') ? ' has-danger' : '' }}">
                                                Összeg *
                                            </label>
                                            <input type="number" step="0.01" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" 
                                                   name="amount" value="{{ old('amount', $cashOnDelivery->amount) }}" 
                                                   placeholder="0.00" required>
                                            @include('alerts.feedback', ['field' => 'amount'])
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('currency') ? ' has-danger' : '' }}">
                                                Pénznem *
                                            </label>
                                            <select class="form-control{{ $errors->has('currency') ? ' is-invalid' : '' }}" 
                                                    name="currency" required>
                                                <option value="HUF" {{ old('currency', $cashOnDelivery->currency) == 'HUF' ? 'selected' : '' }}>HUF - Forint</option>
                                                <option value="EUR" {{ old('currency', $cashOnDelivery->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="USD" {{ old('currency', $cashOnDelivery->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'currency'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('bank_account_number') ? ' has-danger' : '' }}">
                                                Bankszámlaszám *
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('bank_account_number') ? ' is-invalid' : '' }}" 
                                                   name="bank_account_number" value="{{ old('bank_account_number', $cashOnDelivery->bank_account_number) }}" 
                                                   placeholder="12345678-12345678-12345678" required>
                                            @include('alerts.feedback', ['field' => 'bank_account_number'])
                                            <small class="form-text text-muted">A számla formátuma: 8-8-8 számjegy</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">Kedvezményezett adatai</h6>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('recipient_name') ? ' has-danger' : '' }}">
                                                Kedvezményezett neve *
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('recipient_name') ? ' is-invalid' : '' }}" 
                                                   name="recipient_name" value="{{ old('recipient_name', $cashOnDelivery->recipient_name) }}" 
                                                   placeholder="Kedvezményezett teljes neve" required>
                                            @include('alerts.feedback', ['field' => 'recipient_name'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('bank_name') ? ' has-danger' : '' }}">
                                                Bank neve
                                            </label>
                                            <input type="text" class="form-control{{ $errors->has('bank_name') ? ' is-invalid' : '' }}" 
                                                   name="bank_name" value="{{ old('bank_name', $cashOnDelivery->bank_name) }}" 
                                                   placeholder="Bank neve">
                                            @include('alerts.feedback', ['field' => 'bank_name'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('status') ? ' has-danger' : '' }}">
                                                Státusz *
                                            </label>
                                            <select class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" 
                                                    name="status" required>
                                                <option value="pending" {{ old('status', $cashOnDelivery->status) == 'pending' ? 'selected' : '' }}>Függőben</option>
                                                <option value="collected" {{ old('status', $cashOnDelivery->status) == 'collected' ? 'selected' : '' }}>Beszedve</option>
                                                <option value="transferred" {{ old('status', $cashOnDelivery->status) == 'transferred' ? 'selected' : '' }}>Átutalva</option>
                                                <option value="cancelled" {{ old('status', $cashOnDelivery->status) == 'cancelled' ? 'selected' : '' }}>Törölve</option>
                                            </select>
                                            @include('alerts.feedback', ['field' => 'status'])
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('notes') ? ' has-danger' : '' }}">
                                                Megjegyzés
                                            </label>
                                            <textarea class="form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" 
                                                      name="notes" rows="4" 
                                                      placeholder="Egyéb megjegyzések...">{{ old('notes', $cashOnDelivery->notes) }}</textarea>
                                            @include('alerts.feedback', ['field' => 'notes'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($cashOnDelivery->collected_at || $cashOnDelivery->transferred_at)
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Időbélyegek</h6>
                                <div class="alert alert-info">
                                    @if($cashOnDelivery->collected_at)
                                        <strong>Beszedve:</strong> {{ $cashOnDelivery->collected_at->format('Y.m.d H:i') }}<br>
                                    @endif
                                    @if($cashOnDelivery->transferred_at)
                                        <strong>Átutalva:</strong> {{ $cashOnDelivery->transferred_at->format('Y.m.d H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Módosítások mentése
                                </button>
                                <a href="{{ route('cash-on-delivery.show', $cashOnDelivery->id) }}" class="btn btn-warning">
                                    <i class="fa fa-times"></i> Mégse
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
            // Bank account number formatting
            $('input[name="bank_account_number"]').on('input', function() {
                var value = $(this).val().replace(/-/g, '').replace(/\s/g, '');
                if (value.length > 0) {
                    value = value.match(new RegExp('.{1,8}', 'g')).join('-');
                }
                $(this).val(value);
            });
        });
    </script>
@endpush