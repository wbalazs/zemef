@extends('layouts.app', [
    'activePage' => 'vat-reports',
    'activeButton' => 'reports',
    'title' => 'Zemef ERP - Részletes ÁFA Kimutatás',
    'navName' => 'Részletes ÁFA Kimutatás'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Szűrők</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('vat-reports.detailed') }}" id="filter-form">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dátum tól</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ request('start_date', date('Y-m-01')) }}">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dátum ig</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ request('end_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>ÁFA kulcs</label>
                                <select name="vat_rate" class="form-control">
                                    <option value="">Összes</option>
                                    <option value="5%" {{ request('vat_rate') == '5%' ? 'selected' : '' }}>5%</option>
                                    <option value="18%" {{ request('vat_rate') == '18%' ? 'selected' : '' }}>18%</option>
                                    <option value="27%" {{ request('vat_rate') == '27%' ? 'selected' : '' }}>27%</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Keresés</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Számlaszám, partner, termék..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Szűrés
                                    </button>
                                    <a href="{{ route('vat-reports.detailed') }}" class="btn btn-secondary">
                                        <i class="fa fa-refresh"></i> Alaphelyzet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        @if($summary)
        <div class="row">
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-file-invoice"></i>
                        </div>
                        <p class="card-category">Számlák száma</p>
                        <h3 class="card-title">{{ $summary->bill_count ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-money-bill"></i>
                        </div>
                        <p class="card-category">Nettó összeg</p>
                        <h3 class="card-title">{{ number_format($summary->total_net ?? 0, 0, ',', ' ') }} Ft</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-percentage"></i>
                        </div>
                        <p class="card-category">ÁFA összeg</p>
                        <h3 class="card-title">{{ number_format($summary->total_vat ?? 0, 0, ',', ' ') }} Ft</h3>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Table -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">Részletes ÁFA kimutatás</h3>
                        <p class="text-sm mb-0">
                            Összesen {{ $items->total() }} tétel
                        </p>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('vat-reports.export') }}?{{ http_build_query(request()->query()) }}" 
                           class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> Excel export
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="detailed-table">
                        <thead>
                            <tr>
                                <th>Sorszám</th>
                                <th>Dátum</th>
                                <th>Partner</th>
                                <th>Tétel megnevezése</th>
                                <th class="text-right">Mennyiség</th>
                                <th class="text-right">Nettó egységár</th>
                                <th class="text-right">Nettó összeg</th>
                                <th class="text-right">ÁFA kulcs</th>
                                <th class="text-right">ÁFA összeg</th>
                                <th class="text-right">Bruttó összeg</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $grossTotal = $item->total_net + $item->vat_amount;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('billingo-bills.show', ['billingoBill' => $item->billingo_bill_id]) }}" 
                                           target="_blank">
                                            {{ $item->bill_number }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->fulfillment_date)->format('Y.m.d') }}</td>
                                    <td>{{ $item->partner_name }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-right">{{ number_format($item->quantity, 3, ',', ' ') }}</td>
                                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} Ft</td>
                                    <td class="text-right">{{ number_format($item->total_net, 0, ',', ' ') }} Ft</td>
                                    <td class="text-right">{{ $item->vat }}</td>
                                    <td class="text-right">{{ number_format($item->vat_amount, 0, ',', ' ') }} Ft</td>
                                    <td class="text-right">{{ number_format($grossTotal, 0, ',', ' ') }} Ft</td>
                                    <td>
                                        <a href="{{ route('billingo-bills.show', ['billingoBill' => $item->billingo_bill_id]) }}" 
                                           class="btn btn-sm btn-info" 
                                           target="_blank"
                                           title="Számla megtekintése">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $items->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#detailed-table').DataTable({
                "pageLength": 50,
                "order": [[1, 'desc']],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Hungarian.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 10 } // Actions column
                ]
            });
        }
    });
</script>
@endpush