@extends('layouts.app', [
    'activePage' => 'cash-on-delivery', 
    'activeButton' => 'cash-on-delivery', 
    'title' => 'Zemef ERP', 
    'navName' => $title ?? 'Utánvétek'
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ $title ?? 'Utánvétek' }}</h3>
                                    <p class="text-sm mb-0">
                                        Utánvétes fizetések kezelése
                                    </p>
                                </div>
                                <div class="col-4 text-right">
                                    @if(Auth::user()->hasPermission('create', App\Models\Package::class))
                                        <a href="{{ route('cash-on-delivery.create') }}" class="btn btn-sm btn-primary">Új utánvét</a>
                                    @endif
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Szűrés
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('cash-on-delivery.index') }}">Összes</a>
                                            <a class="dropdown-item" href="{{ route('cash-on-delivery.pending') }}">Függőben</a>
                                            <a class="dropdown-item" href="{{ route('cash-on-delivery.collected') }}">Beszedve</a>
                                            <a class="dropdown-item" href="{{ route('cash-on-delivery.transferred') }}">Átutalva</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('cash-on-delivery.statistics') }}">
                                                <i class="fa fa-chart-bar"></i> Statisztika
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Csomag</th>
                                        <th>Rendelés</th>
                                        <th>Összeg</th>
                                        <th>Bankszámla</th>
                                        <th>Kedvezményezett</th>
                                        <th>Státusz</th>
                                        <th>Létrehozva</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cashOnDeliveries as $cod)
                                        <tr>
                                            <td>
                                                <a href="{{ route('cash-on-delivery.edit', $cod->package_id) }}" class="text-info">
                                                    {{ $cod->package->package_number ?? 'Ismeretlen' }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $cod->package->order->order_id ?? 'Ismeretlen' }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ number_format($cod->amount, 0, ',', ' ') }} {{ $cod->currency }}
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $cod->bank_account_number }}</small>
                                                @if($cod->bank_name)
                                                    <br><small class="text-muted">{{ $cod->bank_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $cod->recipient_name }}</td>
                                            <td>
                                                @switch($cod->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">{{ $cod->status_label }}</span>
                                                        @break
                                                    @case('collected')
                                                        <span class="badge badge-success">{{ $cod->status_label }}</span>
                                                        @if($cod->collected_at)
                                                            <br><small class="text-muted">{{ $cod->collected_at->format('m.d') }}</small>
                                                        @endif
                                                        @break
                                                    @case('transferred')
                                                        <span class="badge badge-info">{{ $cod->status_label }}</span>
                                                        @if($cod->transferred_at)
                                                            <br><small class="text-muted">{{ $cod->transferred_at->format('m.d') }}</small>
                                                        @endif
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">{{ $cod->status_label }}</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $cod->created_at->format('Y.m.d H:i') }}</td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                    @can('view', $cod)
                                                        <a href="{{ route('cash-on-delivery.show', $cod->id) }}" 
                                                           class="btn btn-link btn-info btn-sm" 
                                                           title="Megtekintés">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('update', $cod)
                                                        <a href="{{ route('cash-on-delivery.edit', $cod->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @if($cod->status === 'pending')
                                                        <form class="d-inline-block" 
                                                              action="{{ route('cash-on-delivery.mark-collected', $cod->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-success btn-sm"
                                                                    title="Beszedve"
                                                                    onclick="return confirm('Biztosan beszedettként jelöli?')">
                                                                <i class="fa fa-money-bill-wave"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($cod->status === 'collected')
                                                        <form class="d-inline-block" 
                                                              action="{{ route('cash-on-delivery.mark-transferred', $cod->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-info btn-sm"
                                                                    title="Átutalva"
                                                                    onclick="return confirm('Biztosan átutaltként jelöli?')">
                                                                <i class="fa fa-exchange-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @can('delete', $cod)
                                                        <form class="d-inline-block" 
                                                              action="{{ route('cash-on-delivery.destroy', $cod->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt az utánvétet?');">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-danger btn-sm remove" 
                                                                    title="Törlés">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    {{ $cashOnDeliveries->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection