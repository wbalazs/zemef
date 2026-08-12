@extends('layouts.app', ['activePage' => 'actual_stock', 'activeButton' => 'actual_stock', 'title' => 'Zemef ERP', 'navName' => 'Készlet megtekintése'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ $inventory->code ?? 'Tétel' }} megtekintése</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('actual_stock.edit', $inventory->inventory_id) }}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i> Szerkesztés</a>
                                <a href="{{ route('actual_stock') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Termék neve</label>
                                    <input type="text" class="form-control" value="{{ $inventory->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kód</label>
                                    <input type="text" class="form-control" value="{{ $inventory->code }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Raktár</label>
                                    <input type="text" class="form-control" value="{{ $inventory->storage->name ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Státusz</label>
                                    <input type="text" class="form-control" value="{{ \App\Http\Controllers\StockController::STATUSES[$inventory->status] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mennyiség</label>
                                    <input type="number" class="form-control" value="{{ $inventory->quantity }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Súly (g)</label>
                                    <input type="number" class="form-control" value="{{ $inventory->weight }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Beszerzési ár (db)</label>
                                    <input type="number" class="form-control" value="{{ $inventory->oneprice }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Eladási ár (db)</label>
                                    <input type="number" class="form-control" value="{{ $inventory->price }}" disabled>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="heading-small text-muted mb-3">Tétel adatok (item_data)</h6>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Szállító (VZJ)</label>
                                    <input type="text" class="form-control" value="{{ $itemData['vzj'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kategória</label>
                                    <input type="text" class="form-control" value="{{ optional($categories->firstWhere('category_id', $itemData['category_id'] ?? null))->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Beszerzési ár</label>
                                    <input type="text" class="form-control" value="{{ $itemData['beszerzesi_ar'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Eladási ár</label>
                                    <input type="text" class="form-control" value="{{ $itemData['eladasi_ar'] ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Áfa típus</label>
                                    <input type="text" class="form-control" value="{{ optional($vat_types->firstWhere('vat_type_id', $itemData['vat_type_id'] ?? null))->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Karát</label>
                                    <input type="text" class="form-control" value="{{ optional($karats->firstWhere('karat_id', $itemData['karat_id'] ?? null))->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kategória II</label>
                                    <input type="text" class="form-control" value="{{ optional($category2s->firstWhere('category2_id', $itemData['category2_id'] ?? null))->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Méret</label>
                                    <input type="text" class="form-control" value="{{ $itemData['size'] ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Állapotok</label>
                                    <input type="text" class="form-control" value="{{ collect($itemData['statuses'] ?? [])->map(function ($id) use ($product_statuses) { return optional($product_statuses->firstWhere('product_status_id', $id))->name; })->filter()->implode(', ') }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Színek</label>
                                    <input type="text" class="form-control" value="{{ collect($itemData['colors'] ?? [])->map(function ($id) use ($product_colors) { return optional($product_colors->firstWhere('product_color_id', $id))->name; })->filter()->implode(', ') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fémjelzés</label>
                                    <input type="text" class="form-control" value="{{ !empty($itemData['hallmarks']) ? 'Igen' : 'Nem' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Termék típus</label>
                                    <input type="text" class="form-control" value="@if(($itemData['termek_tipus_id'] ?? null) == 1) Nyaklánc @elseif(($itemData['termek_tipus_id'] ?? null) == 2) Fülbevaló @elseif(($itemData['termek_tipus_id'] ?? null) == 3) Gyűrű @elseif(($itemData['termek_tipus_id'] ?? null) == 4) Karkötő @endif" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Címke típus</label>
                                    <input type="text" class="form-control" value="{{ $itemData['label_type'] ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>

                        @if($inventory->inventory_pictures && $inventory->inventory_pictures->count())
                            <hr>
                            <h6 class="heading-small text-muted mb-3">Képek</h6>
                            <div class="row">
                                @foreach($inventory->inventory_pictures as $picture)
                                    <div class="col-md-3 mb-3">
                                        <img src="{{ env('PUBLIC_URL') }}/document_storage/inventory_images/{{ $inventory->inventory_id }}/{{ $picture->image_path }}" class="img-fluid rounded border" alt="inventory-image">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
