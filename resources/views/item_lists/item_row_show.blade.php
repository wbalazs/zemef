@extends('layouts.app', ['activePage' => 'item-list', 'activeButton' => 'item-list', 'title' => 'Zemef ERP', 'navName' => 'Kényszerlista tétel megtekintés' ])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Tétel megtekintés</h4>
                        <div>
                            <a href="{{ route('item_list.subindex', $itemList->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                            @if((int)($item['status'] ?? 0) !== \App\Enums\ItemListStatus::BOUGHT_BACK->value)
                                <a href="{{ route('item_list.row.edit', ['itemList' => $itemList->id, 'sor' => $item['sor']]) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Szerkesztés
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Sor:</strong> {{ $item['sor'] ?? '-' }}</div>
                            <div class="col-md-4"><strong>Státusz:</strong> {{ $item['status'] ?? '-' }}</div>
                            <div class="col-md-4"><strong>Zálogjegy:</strong> {{ $item['zalogjegy_szama'] ?? '-' }}</div>
                        </div>

                        <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
