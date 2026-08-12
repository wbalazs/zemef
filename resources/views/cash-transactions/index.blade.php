@extends('layouts.app', [
    'activePage' => 'cash-transactions',
    'activeButton' => 'cash-transactions',
    'title' => 'Zemef ERP',
    'navName' => 'Pénztár tranzakciók'
])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Pénztár</h3>
                </div>
                <div class="card-body">
                    <!-- Pénztár választó -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label>Pénztár választása</label>
                            <select class="form-control" id="cash_register_select" onchange="window.location.href='{{ route('cash-transactions.index') }}?cash_register_id='+this.value">
                                <option value="">Válasszon pénztárt...</option>
                                @foreach($cashRegisters as $register)
                                    <option value="{{ $register?->cash_register_id }}" {{ $selectedRegisterId == $register?->cash_register_id ? 'selected' : '' }}>
                                        {{ $register?->name }} ({{ $register?->currency->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    @if($cashRegister)
                        <!-- Pénztár információk -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="alert small-box alert-info" role="alert">
                                    <div class="inner">
                                        <h3>{{ number_format($cashRegister->current_balance, $cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashRegister->currency->code }}</h3>
                                        <p>{{ $cashRegister->name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert small-box alert-warning" role="alert">
                                    <div class="inner">
                                        <h3>{{ number_format($previousDayBalance, $cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashRegister->currency->code }}</h3>
                                        <p>Előző napi záró</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert small-box alert-success">
                                    <div class="inner">
                                        <h3>{{ number_format($cashRegister->current_balance - $previousDayBalance, $cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashRegister->currency->code }}</h3>
                                        <p>Napi változás</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Új tranzakció form -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Új tranzakció rögzítése</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('cash-transactions.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="cash_register_id" value="{{ $cashRegister->cash_register_id }}">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Dátum *</label>
                                                <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                                       value="{{ old('transaction_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                                @error('transaction_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Irány *</label>
                                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Bevétel</option>
                                                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Kiadás</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Összeg *</label>
                                                <input type="text" name="amount" class="form-control amount-input @error('amount') is-invalid @enderror" 
                                                       value="{{ old('amount') }}" required>
                                                <small class="text-muted">{{ $cashRegister->currency->code === 'HUF' ? 'Csak egész szám' : 'Két tizedes' }}</small>
                                                @error('amount')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kinek/Kitől *</label>
                                                <input type="text" name="payer_payee" class="form-control @error('payer_payee') is-invalid @enderror" 
                                                       value="{{ old('payer_payee') }}" required>
                                                @error('payer_payee')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Jogcím *</label>
                                                <select name="reason" class="form-control @error('reason') is-invalid @enderror" required>
                                                    <option value="">Válasszon...</option>
                                                    <option value="sales">Értékesítés</option>
                                                    <option value="purchase">Beszerzés</option>
                                                    <option value="other">Egyéb</option>
                                                </select>
                                                @error('reason')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Egyéb jogcím</label>
                                                <input type="text" name="reason_custom" class="form-control" value="{{ old('reason_custom') }}">
                                            </div>
                                        </div>
                                        
                                        @if($cashRegister->currency->code !== 'HUF')
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>EUR árfolyam</label>
                                                <input type="text" name="exchange_rate" class="form-control" value="{{ old('exchange_rate') }}">
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Címletek -->
                                    @if($denominations->count() > 0)
                                    <div class="row">
                                        <div class="col-12">
                                            <label>Címletek</label>
                                            <div class="denominations-container">
                                                @foreach($denominations as $denom)
                                                <div class="form-group d-inline-block mr-3 mb-2">
                                                    <label>{{ $denom->name }} ({{ number_format($denom->value, 0, ',', ' ') }} {{ $cashRegister->currency->code }})</label>
                                                    <div class="input-group" style="width: 150px;">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-outline-secondary denomination-minus" data-target="denom_{{ $denom->denomination_id }}">-</button>
                                                        </div>
                                                        <input type="number" name="denominations[{{ $denom->denomination_id }}]" 
                                                               id="denom_{{ $denom->denomination_id }}" class="form-control denomination-input" 
                                                               value="0" min="0" data-value="{{ $denom->value }}" style="text-align: center;">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary denomination-plus" data-target="denom_{{ $denom->denomination_id }}">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="mt-2">
                                                <strong>Összesen: <span id="denomination_total">0</span> {{ $cashRegister->currency->code }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Megjegyzés</label>
                                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mellékletek</label>
                                                <input type="file" name="attachments[]" class="form-control-file" multiple accept=".jpg,.jpeg,.png,.pdf">
                                                <small class="text-muted">Több fájl is kiválasztható (kép, PDF)</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="print_voucher" value="1" class="custom-control-input" id="print_voucher" checked>
                                                    <label class="custom-control-label" for="print_voucher">
                                                        <strong>Pénztárbizonylat nyomtatása</strong>
                                                    </label>
                                                    <small class="text-muted d-block">(két példányban egy oldalon)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-success">Tranzakció rögzítése</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Tranzakciók listája -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tranzakciók</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Bizonylatszám</th>
                                            <th>Dátum</th>
                                            <th>Típus</th>
                                            <th>Összeg</th>
                                            <th>Kinek/Kitől</th>
                                            <th>Jogcím</th>
                                            <th>Egyenleg</th>
                                            <th>Műveletek</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->document_number }}</td>
                                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($transaction->type === 'income')
                                                    <span class="badge badge-success">Bevétel</span>
                                                @else
                                                    <span class="badge badge-danger">Kiadás</span>
                                                @endif
                                            </td>
                                            <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($transaction->amount, $cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashRegister->currency->code }}
                                            </td>
                                            <td>{{ $transaction->payer_payee }}</td>
                                            <td>
                                                {{ $transaction->reason }}
                                                @if($transaction->reason_custom)
                                                    <br><small>{{ $transaction->reason_custom }}</small>
                                                @endif
                                            </td>
                                            <td>{{ number_format($transaction->balance_after, $cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashRegister->currency->code }}</td>
                                            <td>
                                                <a href="{{ route('cash-transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Összeg formázás
    $('.amount-input').on('input', function() {
        let value = $(this).val().replace(/\s/g, '').replace(/,/g, '.');
        if (!isNaN(value) && value !== '') {
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            $(this).val(parts.join('.'));
        }
    });
    
    // Címletek számolás
    function calculateDenominationTotal() {
        let total = 0;
        $('.denomination-input').each(function() {
            let count = parseInt($(this).val()) || 0;
            let value = parseFloat($(this).data('value')) || 0;
            total += count * value;
        });
        
        let currencyCode = '{{ $cashRegister->currency->code ?? 'HUF' }}';
        let decimals = currencyCode === 'HUF' ? 0 : 2;
        $('#denomination_total').text(total.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
    }
    
    $('.denomination-plus').click(function() {
        let target = $(this).data('target');
        let input = $('#' + target);
        input.val(parseInt(input.val()) + 1);
        calculateDenominationTotal();
    });
    
    $('.denomination-minus').click(function() {
        let target = $(this).data('target');
        let input = $('#' + target);
        let val = parseInt(input.val()) - 1;
        if (val >= 0) {
            input.val(val);
            calculateDenominationTotal();
        }
    });
    
    $('.denomination-input').on('input', calculateDenominationTotal);
    
    // Dátum figyelmeztetés
    $('input[name="transaction_date"]').on('change', function() {
        let selectedDate = new Date($(this).val());
        let today = new Date();
        today.setHours(0,0,0,0);
        
        if (selectedDate < today) {
            alert('Figyelem! Visszadátumozott tranzakciót rögzítesz!');
        }
    });
</script>
@endpush
@endsection