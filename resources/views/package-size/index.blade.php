@extends('layouts.app', [
    'activePage' => 'package-size', 
    'activeButton' => 'package-size', 
    'title' => 'Zemef ERP', 
    'navName' => 'Csomag Méretek'
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
                                    <h3 class="mb-0">Csomag Méretek</h3>
                                    <p class="text-sm mb-0">
                                        Csomag méretek kezelése
                                    </p>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Package::class))

                                    <div class="col-4 text-right">
                                        <a href="{{ route('package-size.create') }}" class="btn btn-sm btn-primary">Új méret</a>
                                    </div>
                                @endif
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
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Méretek</th>
                                        <th>Súly limit</th>
                                        <th>Alapár</th>
                                        <th>Státusz</th>
                                        <th>Leírás</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packageSizes as $size)
                                        <tr>
                                            <td>{{ $size->name }}</td>
                                            <td><span class="badge badge-info">{{ $size->code }}</span></td>
                                            <td>{{ $size->dimensions }}</td>
                                            <td>{{ $size->weight_limit ? $size->weight_limit . ' kg' : '-' }}</td>
                                            <td>{{ number_format($size->price, 0, ',', ' ') }} Ft</td>
                                            <td>
                                                @if($size->status)
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($size->description, 50) }}</td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                    @can('update', $size)
                                                        <a href="{{ route('package-size.edit', $size->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        
                                                        <form class="d-inline-block" 
                                                              action="{{ route('package-size.toggle-status', $size->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-{{ $size->status ? 'warning' : 'success' }} btn-sm"
                                                                    title="{{ $size->status ? 'Deaktiválás' : 'Aktiválás' }}">
                                                                <i class="fa fa-{{ $size->status ? 'times' : 'check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('delete', $size)
                                                        <form class="d-inline-block" 
                                                              action="{{ route('package-size.destroy', $size->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt a csomagméretet?');">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection