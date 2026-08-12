@extends('layouts.app', [
    'activePage' => 'cash-on-delivery', 
    'activeButton' => 'cash-on-delivery', 
    'title' => 'Zemef ERP', 
    'navName' => 'Utánvét részletei'
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Utánvét részletei</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('cash-on-delivery.index') }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-arrow-left"></i> Vissza
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6 class="heading-small text-muted mb-4">Alap információk</h6>
                                    
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Csomag szám:</th>
                                            <td>
                                                <a href="{{ route('package.edit', $cashOnDelivery->package_id) }}" class="text-info">
                                                    {{ $cashOnDelivery->package->package_number ?? 'Ismeretlen' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Rendelés szám:</th>
                                            <td>{{ $cashOnDelivery->package->order->order_id ?? 'Ismeretlen' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Összeg:</th>
                                            <td class="font-weight-bold">
                                                {{ number_format($cashOnDelivery->amount, 0, ',', ' ') }} {{ $cashOnDelivery->currency }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Státusz:</th>
                                            <td>
                                                @switch($cashOnDelivery->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">{{ $cashOnDelivery->status_label }}</span>
                                                        @break
                                                    @case('collected')
                                                        <span class="badge badge-success">{{ $cashOnDelivery->status_label }}</span>
                                                        @break
                                                    @case('transferred')
                                                        <span class="badge badge-info">{{ $cashOnDelivery->status_label }}</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">{{ $cashOnDelivery->status_label }}</span>
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Létrehozva:</th>
                                            <td>{{ $cashOnDelivery->created_at->format('Y.m.d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-lg-6">
                                    <h6 class="heading-small text-muted mb-4">Bankszámla adatok</h6>
                                    
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Bankszámlaszám:</th>
                                            <td><code>{{ $cashOnDelivery->bank_account_number }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Bank neve:</th>
                                            <td>{{ $cashOnDelivery->bank_name ?? 'Nincs megadva' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kedvezményezett:</th>
                                            <td>{{ $cashOnDelivery->recipient_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pénznem:</th>
                                            <td>{{ $cashOnDelivery->currency }}</td>
                                        </tr>
                                        @if($cashOnDelivery->collected_at)
                                        <tr>
                                            <th>Beszedve:</th>
                                            <td>{{ $cashOnDelivery->collected_at->format('Y.m.d H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($cashOnDelivery->transferred_at)
                                        <tr>
                                            <th>Átutalva:</th>
                                            <td>{{ $cashOnDelivery->transferred_at->format('Y.m.d H.i') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($cashOnDelivery->notes)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Megjegyzés</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            {{ $cashOnDelivery->notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Kapcsolódó csomag</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Szállítási szolgáltató:</strong> {{ $cashOnDelivery->package->shipping_service_name }}<br>
                                                    <strong>Szállítási ár:</strong> {{ number_format($cashOnDelivery->package->shipping_price ?? 0, 0, ',', ' ') }} Ft<br>
                                                    <strong>Nyomkövetési szám:</strong> {{ $cashOnDelivery->package->tracking_number ?? 'Nincs' }}<br>
                                                    <strong>Súly:</strong> {{ $cashOnDelivery->package->weight ? $cashOnDelivery->package->weight . ' kg' : 'Nincs' }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Csomag státusza:</strong> 
                                                    <span class="badge badge-{{ $cashOnDelivery->package->status == 'created' ? 'primary' : ($cashOnDelivery->package->status == 'shipped' ? 'warning' : 'success') }}">
                                                        {{ $cashOnDelivery->package->status_label }}
                                                    </span><br>
                                                    @if($cashOnDelivery->package->shipped_at)
                                                        <strong>Kiszküldve:</strong> {{ $cashOnDelivery->package->shipped_at->format('Y.m.d H:i') }}<br>
                                                    @endif
                                                    @if($cashOnDelivery->package->delivered_at)
                                                        <strong>Kézbesítve:</strong> {{ $cashOnDelivery->package->delivered_at->format('Y.m.d H:i') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($cashOnDelivery->package->items->count() > 0)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Csomag tartalma</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Termék neve</th>
                                                    <th>SKU</th>
                                                    <th>Mennyiség</th>
                                                    <th>Egységár</th>
                                                    <th>Összesen</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cashOnDelivery->package->items as $item)
                                                <tr>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td>{{ $item->quantity }} db</td>
                                                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} Ft</td>
                                                    <td>{{ number_format($item->unit_price * $item->quantity, 0, ',', ' ') }} Ft</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="card-footer text-center">
                            @can('update', $cashOnDelivery)
                                <a href="{{ route('cash-on-delivery.edit', $cashOnDelivery->id) }}" class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Szerkesztés
                                </a>
                            @endcan

                            @if($cashOnDelivery->status === 'pending')
                                <form class="d-inline-block" 
                                      action="{{ route('cash-on-delivery.mark-collected', $cashOnDelivery->id) }}" 
                                      method="POST">
                                    @method('patch')
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-success"
                                            onclick="return confirm('Biztosan beszedettként jelöli?')">
                                        <i class="fa fa-money-bill-wave"></i> Beszedve
                                    </button>
                                </form>
                            @endif

                            @if($cashOnDelivery->status === 'collected')
                                <form class="d-inline-block" 
                                      action="{{ route('cash-on-delivery.mark-transferred', $cashOnDelivery->id) }}" 
                                      method="POST">
                                    @method('patch')
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-info"
                                            onclick="return confirm('Biztosan átutaltként jelöli?')">
                                        <i class="fa fa-exchange-alt"></i> Átutalva
                                    </button>
                                </form>
                            @endif

                            @can('delete', $cashOnDelivery)
                                <form class="d-inline-block" 
                                      action="{{ route('cash-on-delivery.destroy', $cashOnDelivery->id) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Biztosan törli ezt az utánvétet?');">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-danger">
                                        <i class="fa fa-trash"></i> Törlés
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection