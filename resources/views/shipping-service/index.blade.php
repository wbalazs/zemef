@extends('layouts.app', [
    'activePage' => 'shipping-service',
    'activeButton' => 'package',
    'title' => 'Zemef ERP',
    'navName' => 'Szállítási szolgáltatók'
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
                                    <h3 class="mb-0">Szállítási szolgáltatók</h3>
                                    <p class="text-sm mb-0">Szolgáltatók és alap szállítási díjak kezelése</p>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('shipping-service.create') }}" class="btn btn-sm btn-primary">Új szolgáltató</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Alapár</th>
                                        <th>Utánvét</th>
                                        <th>Státusz</th>
                                        <th>Sorrend</th>
                                        <th>Leírás</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shippingServices as $service)
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td><span class="badge badge-info">{{ $service->code }}</span></td>
                                            <td>{{ number_format($service->base_price, 0, ',', ' ') }} Ft</td>
                                            <td>
                                                @if($service->supports_cod)
                                                    <span class="badge badge-success">Igen</span>
                                                @else
                                                    <span class="badge badge-secondary">Nem</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($service->status)
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>{{ $service->sort_order }}</td>
                                            <td>{{ Str::limit($service->description, 50) }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('shipping-service.edit', $service->id) }}" class="btn btn-link btn-warning btn-sm" title="Szerkesztés">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form class="d-inline-block" method="POST" action="{{ route('shipping-service.toggle-status', $service->id) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <button type="submit" class="btn btn-link btn-{{ $service->status ? 'warning' : 'success' }} btn-sm" title="{{ $service->status ? 'Deaktiválás' : 'Aktiválás' }}">
                                                        <i class="fa fa-{{ $service->status ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form class="d-inline-block" method="POST" action="{{ route('shipping-service.destroy', $service->id) }}" onsubmit="return confirm('Biztosan törli ezt a szolgáltatót?');">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-link btn-danger btn-sm" title="Törlés">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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
