{{-- resources/views/customercard/index.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'customercard', 
    'activeButton' => 'customercard', 
    'title' => 'Zemef ERP', 
    'navName' => 'Ügyfél Kártyák'
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
                                    <h3 class="mb-0">Ügyfél Kártyák</h3>
                                    <p class="text-sm mb-0">
                                        Kedvezmény kártyák kezelése
                                    </p>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\CustomerCard::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('customercard.create') }}" class="btn btn-sm btn-primary">Új kártya</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="col-12 mt-3" id="customer-card-filters">
                            <div class="row">
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-business-filter">Üzlet neve</label>
                                    <input type="search" id="card-business-filter" class="form-control">
                                </div>
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-customer-filter">Ügyfél neve</label>
                                    <input type="search" id="card-customer-filter" class="form-control">
                                </div>
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-start-filter">Kezdő dátum (ettől)</label>
                                    <input type="date" id="card-start-filter" class="form-control">
                                </div>
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-end-filter">Vég dátum (eddig)</label>
                                    <input type="date" id="card-end-filter" class="form-control">
                                </div>
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-status-filter">Státusz</label>
                                    <select id="card-status-filter" class="form-control">
                                        <option value="">Összes</option>
                                        <option value="active">Aktív</option>
                                        <option value="upcoming">Előjegyzett</option>
                                        <option value="expired">Lejárt</option>
                                        <option value="inactive">Inaktív</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-2 form-group">
                                    <label for="card-type-filter">Típus</label>
                                    <select id="card-type-filter" class="form-control">
                                        <option value="">Összes</option>
                                        <option value="percent">Százalék</option>
                                        <option value="price">Fix összeg</option>
                                        <option value="product_item">Termék alapú</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" id="card-clear-filters" class="btn btn-sm btn-secondary">
                                <i class="fa fa-times"></i> Szűrők törlése
                            </button>
                        </div>

                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="customer_cards_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Típus</th>
                                        <th>Érték</th>
                                        <th>Kezdő dátum</th>
                                        <th>Vég dátum</th>
                                        <th>Státusz</th>
                                        <th>Üzletek</th>
                                        <th>Ügyfelek</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Típus</th>
                                        <th>Érték</th>
                                        <th>Kezdő dátum</th>
                                        <th>Vég dátum</th>
                                        <th>Státusz</th>
                                        <th>Üzletek</th>
                                        <th>Ügyfelek</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($customerCards as $card)
                                        <tr data-type="{{ $card->type }}"
                                            data-status="{{ !$card->is_active ? 'inactive' : ($card->isCurrentlyActive ? 'active' : ($card->isUpcoming ? 'upcoming' : 'expired')) }}"
                                            data-start-date="{{ $card->start_date?->format('Y-m-d') }}"
                                            data-end-date="{{ $card->end_date?->format('Y-m-d') }}">
                                            <td>{{ $card->name }}</td>
                                            <td><span class="badge badge-info">{{ $card->code }}</span></td>
                                            <td>
                                                @switch($card->type)
                                                    @case('percent')
                                                        <span class="badge badge-success">Százalék</span>
                                                        @break
                                                    @case('price')
                                                        <span class="badge badge-warning">Fix összeg</span>
                                                        @break
                                                    @case('product_item')
                                                        <span class="badge badge-primary">Termék alapú</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $card->getDiscountTypeLabel() }}</td>
                                            <td>{{ $card->start_date->format('Y.m.d') }}</td>
                                            <td>{{ $card->end_date->format('Y.m.d') }}</td>
                                            <td>
                                                @if($card->is_active)
                                                    @if($card->isCurrentlyActive)
                                                        <span class="badge badge-success">Aktív</span>
                                                    @elseif($card->isUpcoming)
                                                        <span class="badge badge-info">Előjegyzett</span>
                                                    @else
                                                        <span class="badge badge-danger">Lejárt</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>{{ $card->businesses->pluck('name')->implode(', ') ?: 'Nincs korlátozás' }}</td>
                                            <td>{{ $card->customers->pluck('name')->implode(', ') ?: 'Nincs korlátozás' }}</td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                    @if(Auth::user()->hasPermission('edit', App\Models\CustomerCard::class))
                                                        <a href="{{ route('customercard.show', $card->id) }}" 
                                                           class="btn btn-link btn-info btn-sm" 
                                                           title="Megtekintés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('edit', App\Models\CustomerCard::class))
                                                        <a href="{{ route('customercard.edit', $card->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('delete', App\Models\CustomerCard::class))
                                                        <form class="d-inline-block" 
                                                              action="{{ route('customercard.destroy', $card->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt a kártyát?');">
                                                           @method('DELETE')
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

@push('js')
<script>
    $(function () {
        var table = $('#datatables').DataTable();
        var filters = $('#customer-card-filters').find('input, select');

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable !== table.table().node()) {
                return true;
            }

            var row = table.row(dataIndex).node();
            var business = $('#card-business-filter').val().trim().toLocaleLowerCase('hu');
            var customer = $('#card-customer-filter').val().trim().toLocaleLowerCase('hu');
            var startDate = $('#card-start-filter').val();
            var endDate = $('#card-end-filter').val();
            var status = $('#card-status-filter').val();
            var type = $('#card-type-filter').val();

            return data[7].toLocaleLowerCase('hu').includes(business)
                && data[8].toLocaleLowerCase('hu').includes(customer)
                && (!startDate || (row.dataset.startDate && row.dataset.startDate >= startDate))
                && (!endDate || (row.dataset.endDate && row.dataset.endDate <= endDate))
                && (!status || row.dataset.status === status)
                && (!type || row.dataset.type === type);
        });

        filters.on('input change', function () {
            table.draw();
        });

        $('#card-clear-filters').on('click', function () {
            filters.val('');
            table.search('').columns().search('').draw();
        });
    });
</script>
@endpush

