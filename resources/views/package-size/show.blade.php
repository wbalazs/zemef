@extends('layouts.app', [
    'activePage' => 'package-size', 
    'activeButton' => 'package-size', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomagméret részletei'
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
                                    <h3 class="mb-0">Csomagméret részletei</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('package-size.index') }}" class="btn btn-sm btn-warning">
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
                                            <td>{{ $packageSize->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kód:</th>
                                            <td><span class="badge badge-info">{{ $packageSize->code }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Státusz:</th>
                                            <td>
                                                @if($packageSize->status)
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Rendezési sorrend:</th>
                                            <td>{{ $packageSize->sort_order }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alapár:</th>
                                            <td class="font-weight-bold">{{ number_format($packageSize->price, 0, ',', ' ') }} Ft</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-lg-6">
                                    <h6 class="heading-small text-muted mb-4">Méretek és korlátok</h6>
                                    
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Méretek:</th>
                                            <td>{{ $packageSize->dimensions }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hangerő:</th>
                                            <td>
                                                @if($packageSize->volume > 0)
                                                    {{ number_format($packageSize->volume, 0, ',', ' ') }} cm³
                                                @else
                                                    Nincs megadva
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Súly limit:</th>
                                            <td>
                                                @if($packageSize->weight_limit)
                                                    {{ $packageSize->weight_limit }} kg
                                                @else
                                                    Nincs korlát
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Létrehozva:</th>
                                            <td>{{ $packageSize->created_at->format('Y.m.d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Utolsó módosítás:</th>
                                            <td>{{ $packageSize->updated_at->format('Y.m.d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($packageSize->description)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Leírás</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            {{ $packageSize->description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($packageSize->packages->count() > 0)
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-2">Használat ({{ $packageSize->packages->count() }} csomag)</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Csomag szám</th>
                                                    <th>Rendelés</th>
                                                    <th>Státusz</th>
                                                    <th>Létrehozva</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($packageSize->packages as $package)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('package.edit', $package->id) }}" class="text-info">
                                                            {{ $package->package_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $package->order->order_id ?? 'Ismeretlen' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $package->status == 'created' ? 'primary' : ($package->status == 'shipped' ? 'warning' : 'success') }}">
                                                            {{ $package->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $package->created_at->format('Y.m.d H:i') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Ez a csomagméret még nincs használatban.
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="card-footer text-center">
                            @can('update', $packageSize)
                                <a href="{{ route('package-size.edit', $packageSize->id) }}" class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Szerkesztés
                                </a>

                                <form class="d-inline-block" 
                                      action="{{ route('package-size.toggle-status', $packageSize->id) }}" 
                                      method="POST">
                                    @method('patch')
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-{{ $packageSize->status ? 'warning' : 'success' }}"
                                            onclick="return confirm('Biztosan {{ $packageSize->status ? 'deaktiválja' : 'aktiválja' }} ezt a csomagméretet?')">
                                        <i class="fa fa-{{ $packageSize->status ? 'times' : 'check' }}"></i> 
                                        {{ $packageSize->status ? 'Deaktiválás' : 'Aktiválás' }}
                                    </button>
                                </form>
                            @endcan

                            @can('delete', $packageSize)
                                @if(!$packageSize->packages()->exists())
                                    <form class="d-inline-block" 
                                          action="{{ route('package-size.destroy', $packageSize->id) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Biztosan törli ezt a csomagméretet?');">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Törlés
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection