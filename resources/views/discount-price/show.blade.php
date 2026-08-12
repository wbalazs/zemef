@extends('layouts.app', [
    'activePage' => 'discount-price', 
    'activeButton' => 'discount-price', 
    'title' => 'Zemef ERP', 
    'navName' => 'Kedvezmény részletei'
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
                                    <h3 class="mb-0">Kedvezmény részletei</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('discount-price.index') }}" class="btn btn-sm btn-warning">
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
                                            <th width="40%">Név:</th>
                                            <td>{{ $discountPrice->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kód:</th>
                                            <td><code>{{ $discountPrice->code }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Típus:</th>
                                            <td>
                                                <span class="badge badge-{{ $discountPrice->type === 'percentage' ? 'success' : 'warning' }}">
                                                    {{ $discountPrice->type_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Érték:</th>
                                            <td>{{ $discountPrice->display_value }}</td>
                                        </tr>
                                        <tr>
                                            <th>Státusz:</th>
                                            <td>
                                                @if($discountPrice->isValid())
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-lg-6">
                                    <h6 class="heading-small text-muted mb-4">Korlátozások</h6>
                                    
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Min. rendelési érték:</th>
                                            <td>
                                                @if($discountPrice->min_order_amount)
                                                    {{ number_format($discountPrice->min_order_amount, 0, ',', ' ') }} Ft
                                                @else
                                                    <span class="text-muted">Nincs</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Max. kedvezmény:</th>
                                            <td>
                                                @if($discountPrice->max_discount_amount)
                                                    {{ number_format($discountPrice->max_discount_amount, 0, ',', ' ') }} Ft
                                                @else
                                                    <span class="text-muted">Nincs</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Érvényesség:</th>
                                            <td>
                                                @if($discountPrice->valid_from && $discountPrice->valid_to)
                                                    {{ $discountPrice->valid_from->format('Y.m.d') }} - {{ $discountPrice->valid_to->format('Y.m.d') }}
                                                @elseif($discountPrice->valid_from)
                                                    {{ $discountPrice->valid_from->format('Y.m.d') }} - ...
                                                @else
                                                    <span class="text-muted">Korlátlan</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Felhasználási korlát:</th>
                                            <td>
                                                @if($discountPrice->usage_limit)
                                                    {{ $discountPrice->used_count }} / {{ $discountPrice->usage_limit }}
                                                @else
                                                    {{ $discountPrice->used_count }} (korlátlan)
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($discountPrice->description)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Leírás</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            {{ $discountPrice->description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($discountPrice->packages->count() > 0)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Felhasználások ({{ $discountPrice->packages->count() }})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Csomag szám</th>
                                                    <th>Rendelés</th>
                                                    <th>Létrehozva</th>
                                                    <th>Végső ár</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($discountPrice->packages as $package)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('package.edit', $package->id) }}" class="text-info">
                                                            {{ $package->package_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $package->order->order_id ?? 'Ismeretlen' }}</td>
                                                    <td>{{ $package->created_at->format('Y.m.d H:i') }}</td>
                                                    <td>{{ number_format($package->final_price, 0, ',', ' ') }} Ft</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection