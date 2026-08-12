@extends('layouts.app', [
    'activePage' => 'discount-price', 
    'activeButton' => 'discount-price', 
    'title' => 'Zemef ERP', 
    'navName' => 'Kedvezmények'
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
                                    <h3 class="mb-0">Kedvezmények</h3>
                                    <p class="text-sm mb-0">
                                        Kedvezmények és akciók kezelése
                                    </p>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\DiscountPrice::class))

                                    <div class="col-4 text-right">
                                        <a href="{{ route('discount-price.create') }}" class="btn btn-sm btn-primary">Új kedvezmény</a>
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
                                        <th>Típus</th>
                                        <th>Érték</th>
                                        <th>Min. rendelés</th>
                                        <th>Max. kedvezmény</th>
                                        <th>Érvényes</th>
                                        <th>Felhasználva</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($discountPrices as $discount)
                                        <tr>
                                            <td>{{ $discount->name }}</td>
                                            <td><code>{{ $discount->code }}</code></td>
                                            <td>
                                                <span class="badge badge-{{ $discount->type === 'percentage' ? 'success' : 'warning' }}">
                                                    {{ $discount->type_label }}
                                                </span>
                                            </td>
                                            <td>{{ $discount->display_value }}</td>
                                            <td>
                                                @if($discount->min_order_amount)
                                                    {{ number_format($discount->min_order_amount, 0, ',', ' ') }} Ft
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discount->max_discount_amount)
                                                    {{ number_format($discount->max_discount_amount, 0, ',', ' ') }} Ft
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discount->valid_from && $discount->valid_to)
                                                    {{ $discount->valid_from->format('Y.m.d') }} - {{ $discount->valid_to->format('Y.m.d') }}
                                                @elseif($discount->valid_from)
                                                    {{ $discount->valid_from->format('Y.m.d') }} - ...
                                                @else
                                                    <span class="text-muted">Korlátlan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discount->usage_limit)
                                                    {{ $discount->used_count }} / {{ $discount->usage_limit }}
                                                @else
                                                    {{ $discount->used_count }} <span class="text-muted">(korlátlan)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discount->isValid())
                                                    <span class="badge badge-success">Aktív</span>
                                                @else
                                                    <span class="badge badge-danger">Inaktív</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                    @can('view', $discount)
                                                        <a href="{{ route('discount-price.show', $discount->id) }}" 
                                                           class="btn btn-link btn-info btn-sm" 
                                                           title="Megtekintés">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('update', $discount)
                                                        <a href="{{ route('discount-price.edit', $discount->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        
                                                        <form class="d-inline-block" 
                                                              action="{{ route('discount-price.toggle-status', $discount->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-{{ $discount->status ? 'warning' : 'success' }} btn-sm"
                                                                    title="{{ $discount->status ? 'Deaktiválás' : 'Aktiválás' }}">
                                                                <i class="fa fa-{{ $discount->status ? 'times' : 'check' }}"></i>
                                                            </button>
                                                        </form>

                                                        <form class="d-inline-block" 
                                                              action="{{ route('discount-price.reset-usage', $discount->id) }}" 
                                                              method="POST">
                                                            @method('patch')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-secondary btn-sm"
                                                                    title="Felhasználás visszaállítása"
                                                                    onclick="return confirm('Biztosan visszaállítja a felhasználási számlálót?')">
                                                                <i class="fa fa-redo"></i>
                                                            </button>
                                                        </form>

                                                        <form class="d-inline-block" 
                                                              action="{{ route('discount-price.duplicate', $discount->id) }}" 
                                                              method="POST">
                                                            @method('post')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-info btn-sm"
                                                                    title="Másolás">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('delete', $discount)
                                                        <form class="d-inline-block" 
                                                              action="{{ route('discount-price.destroy', $discount->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt a kedvezményt?');">
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