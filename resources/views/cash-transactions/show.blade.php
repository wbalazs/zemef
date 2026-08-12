{{-- resources/views/cash-transactions/show.blade.php --}}
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
                    <h3 class="card-title">Tranzakció részletei</h3>
                    <div class="card-tools">
                        <a href="{{ route('cash-transactions.index', ['cash_register_id' => $cashTransaction->cash_register_id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Vissza
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Bizonylatszám:</th>
                                    <td><strong>{{ $cashTransaction->document_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Pénztár:</th>
                                    <td>{{ $cashTransaction->cashRegister->name }} ({{ $cashTransaction->cashRegister->currency->code }})</td>
                                </tr>
                                <tr>
                                    <th>Dátum:</th>
                                    <td>{{ $cashTransaction->transaction_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Irány:</th>
                                    <td>
                                        @if($cashTransaction->type === 'income')
                                            <span class="badge bg-success">Bevétel</span>
                                        @else
                                            <span class="badge bg-danger">Kiadás</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Összeg:</th>
                                    <td class="{{ $cashTransaction->type === 'income' ? 'text-success' : 'text-danger' }} font-weight-bold">
                                        {{ number_format($cashTransaction->amount, $cashTransaction->cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kinek/Kitől:</th>
                                    <td>{{ $cashTransaction->payer_payee }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Jogcím:</th>
                                    <td>
                                        {{ $cashTransaction->reason }}
                                        @if($cashTransaction->reason_custom)
                                            <br><small class="text-muted">({{ $cashTransaction->reason_custom }})</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rögzítette:</th>
                                    <td>{{ $cashTransaction->createdBy->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Rögzítés dátuma:</th>
                                    <td>{{ $cashTransaction->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if($cashTransaction->exchange_rate)
                                <tr>
                                    <th>Árfolyam:</th>
                                    <td>{{ number_format($cashTransaction->exchange_rate, 6, ',', ' ') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Egyenleg előtte:</th>
                                    <td>{{ number_format($cashTransaction->balance_before, $cashTransaction->cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}</td>
                                </tr>
                                <tr>
                                    <th>Egyenleg utána:</th>
                                    <td>{{ number_format($cashTransaction->balance_after, $cashTransaction->cashRegister->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($cashTransaction->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Megjegyzés</h4>
                                </div>
                                <div class="card-body">
                                    {{ $cashTransaction->description }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cashTransaction->denominations)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Címletek</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Címlet</th>
                                                <th>Darab</th>
                                                <th>Érték</th>
                                                <th>Összesen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach($cashTransaction->denominations as $denomId => $count)
                                                @if($count > 0)
                                                    @php
                                                        $denomination = \App\Models\Denomination::find($denomId);
                                                        $subtotal = $denomination->value * $count;
                                                        $total += $subtotal;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $denomination->name }}</td>
                                                        <td>{{ $count }} db</td>
                                                        <td>{{ number_format($denomination->value, 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}</td>
                                                        <td>{{ number_format($subtotal, 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Összesen:</td>
                                                <td>{{ number_format($total, 2, ',', ' ') }} {{ $cashTransaction->cashRegister->currency->code }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cashTransaction->attachments->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Mellékletek ({{ $cashTransaction->attachments->count() }})</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($cashTransaction->attachments as $attachment)
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        @if(in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}" class="img-fluid mb-2" style="max-height: 120px; object-fit: cover;">
                                                        @elseif($attachment->mime_type === 'application/pdf')
                                                            <div class="pdf-preview mb-2">
                                                                <i class="fal fa-file-pdf fa-4x text-danger"></i>
                                                            </div>
                                                        @else
                                                            <div class="file-preview mb-2">
                                                                <i class="fal fa-file fa-4x text-secondary"></i>
                                                            </div>
                                                        @endif
                                                        
                                                        <p class="mb-1 text-truncate small" title="{{ $attachment->file_name }}">
                                                            {{ $attachment->file_name }}
                                                        </p>
                                                        
                                                        <small class="text-muted d-block mb-2">
                                                            {{ $attachment->formatted_size }}
                                                        </small>
                                                        
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ $attachment->url }}" target="_blank" class="btn btn-info" title="Megnyitás">
                                                                <i class="fal fa-eye"></i>
                                                            </a>
                                                            <a href="{{ $attachment->url }}" download="{{ $attachment->file_name }}" class="btn btn-success" title="Letöltés">
                                                                <i class="fal fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection