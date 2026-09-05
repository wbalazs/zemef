@extends('layouts.app', ['activePage' => 'item-list', 'activeButton' => 'item-list', 'title' => 'Zemef ERP', 'navName' => 'Kényszerlista tétel szerkesztés' ])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Tétel szerkesztés</h4>
                        <div>
                            <a href="{{ route('item_list.row.show', ['itemList' => $itemList->id, 'sor' => $item['sor']]) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Megtekintés
                            </a>
                            <a href="{{ route('item_list.subindex', $itemList->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Vissza
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('alerts.success')
                        @include('alerts.errors')

                        <div class="alert alert-light border">
                            <strong>Sor (csak olvasható):</strong> {{ $item['sor'] ?? '-' }}
                        </div>

                        <form method="POST" action="{{ route('item_list.row.update', ['itemList' => $itemList->id, 'sor' => $item['sor']]) }}">
                            @csrf
                            <div class="form-group">
                                <label for="item_json">Tétel JSON</label>
                                <textarea
                                    class="form-control @error('item_json') is-invalid @enderror"
                                    id="item_json"
                                    name="item_json"
                                    rows="26"
                                    required
                                >{{ old('item_json', $itemJson) }}</textarea>
                                @error('item_json')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Mentés
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
