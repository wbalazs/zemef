@extends('layouts.app', [
    'activePage' => 'billingo-bills',
    'activeButton' => 'billingo-bills',
    'title' => 'Zemef ERP - Szinkronizálás megerősítése',
    'navName' => 'Szinkronizálás'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Szinkronizálás megerősítése</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fa fa-sync fa-4x text-info mb-3"></i>
                            <h4>Biztosan szinkronizálja a számlát?</h4>
                            <p class="text-muted">
                                Számlaszám: <strong>{{ $billingoBill->bill_number }}</strong><br>
                                Partner: {{ $billingoBill->partner_name }}
                            </p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('billingo-bills.show', $billingoBill) }}" 
                                   class="btn btn-secondary btn-block">
                                    <i class="fa fa-times"></i> Mégse
                                </a>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('billingo-bills.sync', $billingoBill) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-check"></i> Igen, szinkronizálás
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection