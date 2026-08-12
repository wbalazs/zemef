@extends('layouts.app', [
    'activePage' => 'package', 
    'activeButton' => 'package', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomagok'
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
                                    <h3 class="mb-0">Csomagok</h3>
                                    <p class="text-sm mb-0">
                                        Csomagok kezelése
                                    </p>
                                </div>
                               @if(Auth::user()->hasPermission('create', App\Models\Package::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('package.create') }}" class="btn btn-sm btn-primary">Új csomag</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="packages_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Csomag szám</th>
                                        <th>Rendelés</th>
                                        <th>Szállítási szolgáltató</th>
                                        <th>Nyomkövetés</th>
                                        <th>Státusz</th>
                                        <th>Tételek</th>
                                        <th>Súly</th>
                                        <th>Létrehozva</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Csomag szám</th>
                                        <th>Rendelés</th>
                                        <th>Szállítási szolgáltató</th>
                                        <th>Nyomkövetés</th>
                                        <th>Státusz</th>
                                        <th>Tételek</th>
                                        <th>Súly</th>
                                        <th>Létrehozva</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($packages as $package)
                                        <tr>
                                            <td>{{ $package->package_number }}</td>
                                            <td>
                                                @php
                                                    $billing = $package->order ? ($package->order->getJsonField('billing') ?? []) : [];
                                                @endphp
                                                {{ trim(($billing['last_name'] ?? '') . ' ' . ($billing['first_name'] ?? '')) ?: '-' }}
                                                @if(!empty($billing['address_1']))
                                                    {{ $billing['address_1'] }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $package->shipping_service_name }}<br>
                                                <small class="text-muted">{{ number_format($package->shipping_price ?? 0, 0, ',', ' ') }} Ft</small>
                                            </td>
                                            <td>
                                                @if($package->tracking_number)
                                                    <span class="badge badge-info">{{ $package->tracking_number }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($package->status)
                                                    @case('created')
                                                        <span class="badge badge-primary">{{ $package->status_label }}</span>
                                                        @break
                                                    @case('shipped')
                                                        <span class="badge badge-warning">{{ $package->status_label }}</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge badge-success">{{ $package->status_label }}</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">{{ $package->status_label }}</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $package->total_items }} db</td>
                                            <td>{{ $package->weight ? $package->weight . ' kg' : '-' }}</td>
                                            <td>{{ $package->created_at->format('Y.m.d H:i') }}</td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                   @if(Auth::user()->hasPermission('view', App\Models\Package::class))
                                                        <a href="#" 
                                                           class="btn btn-link btn-info btn-sm" 
                                                           title="Megtekintés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('edit', App\Models\Package::class))
                                                        <a href="{{ route('package.edit', $package->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if($package->status === 'created')
                                                        <form class="d-inline-block" 
                                                              action="{{ route('package.mark-as-shipped', $package->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-success btn-sm" 
                                                                    title="Kiszállítva"
                                                                    data-toggle="tooltip">
                                                                <i class="fa fa-shipping-fast"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($package->status === 'shipped')
                                                        <form class="d-inline-block" 
                                                              action="{{ route('package.mark-as-delivered', $package->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-success btn-sm" 
                                                                    title="Kézbesítve"
                                                                    data-toggle="tooltip">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('delete', App\Models\Package::class))
                                                        <form class="d-inline-block" 
                                                              action="{{ route('package.destroy', $package->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt a csomagot?');">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-danger btn-sm remove" 
                                                                    title="Törlés"
                                                                    data-toggle="tooltip">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection