{{-- resources/views/customercard/show.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'customercard', 
    'activeButton' => 'customercard', 
    'title' => 'Zemef ERP', 
    'navName' => 'Ügyfél Kártya részletei'
])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Ügyfél Kártya részletei</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('customercard.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="heading-small text-muted mb-4">Kártya alap adatai</h6>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Név</label>
                                        <p class="form-control-static">{{ $customercard->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Kód</label>
                                        <p class="form-control-static">{{ $customercard->code }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Típus</label>
                                        <p class="form-control-static">
                                            @switch($customercard->type)
                                                @case('percent')
                                                    Százalékos
                                                    @break
                                                @case('price')
                                                    Fix összeg
                                                    @break
                                                @case('product_item')
                                                    Termék alapú
                                                    @break
                                                @default
                                                    {{ $customercard->type }}
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Érték</label>
                                        <p class="form-control-static">
                                            @if($customercard->type == 'percent')
                                                {{ number_format($customercard->value, 2) }} %
                                            @elseif($customercard->type == 'price')
                                                {{ number_format($customercard->value, 0, ',', ' ') }} Ft
                                            @else
                                                –
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Kezdő dátum</label>
                                        <p class="form-control-static">{{ $customercard->start_date?->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Vég dátum</label>
                                        <p class="form-control-static">{{ $customercard->end_date?->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" disabled
                                                   {{ $customercard->is_active ? 'checked' : '' }}>
                                            <span class="form-check-sign"></span>
                                            Aktív
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="heading-small text-muted mb-4">Szabályok és korlátozások</h6>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Üzletek</label>
                                        <p class="form-control-static">
                                            @if($customercard->businesses->count())
                                                {{ $customercard->businesses->pluck('name')->implode(', ') }}
                                            @else
                                                <span class="text-muted">Nincs korlátozás</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Ügyfelek</label>
                                        <p class="form-control-static">
                                            @if($customercard->customers->count())
                                                {{ $customercard->customers->pluck('name')->implode(', ') }}
                                            @else
                                                <span class="text-muted">Nincs korlátozás</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($customercard->type == 'product_item')
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Termékek</label>
                                            <p class="form-control-static">
                                                @if($customercard->products->count())
                                                    {{ $customercard->products->pluck('name')->implode(', ') }}
                                                @else
                                                    <span class="text-muted">Nincs korlátozás</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <h6 class="heading-small text-muted mb-4 mt-4">Termék szabályok</h6>

                            @if($customercard->productTypeRules->count())
                                <div class="form-group">
                                    <label class="form-control-label">Termék típusok</label>
                                    @foreach($customercard->productTypeRules as $rule)
                                        <div class="row mb-2">
                                            <div class="col-8">{{ $rule->productType->name ?? '–' }}</div>
                                            <div class="col-4">{{ number_format($rule->percent, 2) }} %</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Nincsenek termék típus szabályok</p>
                            @endif

                            @if($customercard->productGroupRules->count())
                                <div class="form-group">
                                    <label class="form-control-label">Termék csoportok</label>
                                    @foreach($customercard->productGroupRules as $rule)
                                        <div class="row mb-2">
                                            <div class="col-8">{{ $rule->productGroup->name ?? '–' }}</div>
                                            <div class="col-4">{{ number_format($rule->percent, 2) }} %</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Nincsenek termék csoport szabályok</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12 text-center">
                            <a href="{{ route('customercard.index') }}" class="btn btn-warning">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection