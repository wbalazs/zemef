@extends('layouts.app', [
    'activePage' => 'vat-reports',
    'activeButton' => 'reports',
    'title' => 'ÁFA Kimutatás',
    'navName' => 'ÁFA Kimutatás'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Szűrők -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Szűrők</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('vat-reports.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Dátum tól</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ $startDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Dátum ig</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ $endDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-filter"></i> Szűrés
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="{{ route('vat-reports.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success btn-block">
                                    <i class="fa fa-download"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Összesítő kártyák -->
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-header card-header-primary">
                        <div class="card-icon">
                            <i class="fa fa-globe"></i>
                        </div>
                        <p class="card-category">Globális nyilvántartás</p>
                        <h3 class="card-title">{{ number_format($vatSummary['global']['gross'], 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            Nettó: {{ number_format($vatSummary['global']['net'], 0, ',', ' ') }} Ft<br>
                            ÁFA: {{ number_format($vatSummary['global']['vat'], 0, ',', ' ') }} Ft
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-header card-header-info">
                        <div class="card-icon">
                            <i class="fa fa-gem"></i>
                        </div>
                        <p class="card-category">Egyedi nyilvántartás</p>
                        <h3 class="card-title">{{ number_format($vatSummary['individual']['gross'], 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            Nettó: {{ number_format($vatSummary['individual']['net'], 0, ',', ' ') }} Ft<br>
                            ÁFA: {{ number_format($vatSummary['individual']['vat'], 0, ',', ' ') }} Ft
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-header card-header-success">
                        <div class="card-icon">
                            <i class="fa fa-chart-pie"></i>
                        </div>
                        <p class="card-category">Összesítés</p>
                        <h3 class="card-title">{{ number_format($vatSummary['total']['gross'], 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            Nettó: {{ number_format($vatSummary['total']['net'], 0, ',', ' ') }} Ft<br>
                            ÁFA: {{ number_format($vatSummary['total']['vat'], 0, ',', ' ') }} Ft
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-header card-header-warning">
                        <div class="card-icon">
                            <i class="fa fa-money-bill-wave"></i>
                        </div>
                        <p class="card-category">Fizetendő ÁFA*</p>
                        <h3 class="card-title">{{ number_format($vatSummary['total']['vat_to_pay'], 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <small>*Bejövő ÁFA nélkül számolva</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fő táblázat -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="vatTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="global-tab" data-toggle="tab" href="#global" role="tab">
                            Globális nyilvántartás (≤ 50.000 Ft)
                            <span class="badge badge-primary">{{ count($globalItems) }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="individual-tab" data-toggle="tab" href="#individual" role="tab">
                            Egyedi nyilvántartás (> 50.000 Ft)
                            <span class="badge badge-info">{{ count($individualItems) }}</span>
                        </a>
                    </li>
                    @if(count($itemsWithoutPurchase) > 0)
                    <li class="nav-item">
                        <a class="nav-link" id="no-purchase-tab" data-toggle="tab" href="#no-purchase" role="tab">
                            Beszerzési előzmény nélkül
                            <span class="badge badge-warning">{{ count($itemsWithoutPurchase) }}</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content">
                    <!-- Globális nyilvántartás -->
                    <div class="tab-pane fade show active" id="global" role="tabpanel">
                        @if(count($globalItems) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Termékkód</th>
                                            <th>Megnevezés</th>
                                            <th class="text-right">Súly (g)</th>
                                            <th class="text-right">Eladási ár</th>
                                            <th class="text-right">Beszerzési ár</th>
                                            <th class="text-right">Eladási g/ár</th>
                                            <th class="text-right">Beszerzési g/ár</th>
                                            <th class="text-right">Különbözet</th>
                                            <th class="text-right">ÁFA</th>
                                            <th class="text-right">ÁFA összeg</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($globalItems as $item)
                                        <tr>
                                            <td>{{ $item['product_code'] }}</td>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td class="text-right">{{ number_format($item['weight'], 2, ',', ' ') }}</td>
                                            <td class="text-right">{{ number_format($item['sales_price'], 0, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['purchase_price'], 0, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['sales_price_per_gram'], 2, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['purchase_price_per_gram'], 2, ',', ' ') }} Ft</td>
                                            <td class="text-right {{ $item['price_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($item['price_difference'], 0, ',', ' ') }} Ft
                                            </td>
                                            <td class="text-right">{{ $item['vat_rate'] }}</td>
                                            <td class="text-right">{{ number_format($item['vat_amount'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Nincsenek adatok a globális nyilvántartásban.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Egyedi nyilvántartás -->
                    <div class="tab-pane fade" id="individual" role="tabpanel">
                        @if(count($individualItems) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Termékkód</th>
                                            <th>Megnevezés</th>
                                            <th class="text-right">Súly (g)</th>
                                            <th class="text-right">Eladási ár</th>
                                            <th class="text-right">Beszerzési ár</th>
                                            <th class="text-right">Eladási g/ár</th>
                                            <th class="text-right">Beszerzési g/ár</th>
                                            <th class="text-right">Különbözet</th>
                                            <th class="text-right">ÁFA</th>
                                            <th class="text-right">ÁFA összeg</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($individualItems as $item)
                                        <tr>
                                            <td>{{ $item['product_code'] }}</td>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td class="text-right">{{ number_format($item['weight'], 2, ',', ' ') }}</td>
                                            <td class="text-right">{{ number_format($item['sales_price'], 0, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['purchase_price'], 0, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['sales_price_per_gram'], 2, ',', ' ') }} Ft</td>
                                            <td class="text-right">{{ number_format($item['purchase_price_per_gram'], 2, ',', ' ') }} Ft</td>
                                            <td class="text-right {{ $item['price_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($item['price_difference'], 0, ',', ' ') }} Ft
                                            </td>
                                            <td class="text-right">{{ $item['vat_rate'] }}</td>
                                            <td class="text-right">{{ number_format($item['vat_amount'], 0, ',', ' ') }} Ft</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Nincsenek adatok az egyedi nyilvántartásban.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Beszerzési előzmény nélküli tételek -->
                    @if(count($itemsWithoutPurchase) > 0)
                    <div class="tab-pane fade" id="no-purchase" role="tabpanel">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> 
                            Az alábbi tételekhez nem található beszerzési előzmény:
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Termék ID</th>
                                        <th>Megnevezés</th>
                                        <th>Eladási ár</th>
                                        <th>Értékesítés dátuma</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsWithoutPurchase as $item)
                                    <tr>
                                        <td>{{ $item['product_id'] }}</td>
                                        <td>{{ $item['product_name'] }}</td>
                                        <td>{{ number_format($item['sales_price'], 0, ',', ' ') }} Ft</td>
                                        <td>{{ Carbon\Carbon::parse($item['sales_date'])->format('Y.m.d H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<style>
    .nav-tabs .badge {
        margin-left: 5px;
        font-size: 0.7em;
    }
    
    .table th.text-right,
    .table td.text-right {
        text-align: right !important;
    }
    
    .table tfoot th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>
@endpush

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTables
        if ($.fn.DataTable) {
            $('#global-table').DataTable({
                "pageLength": 25,
                "order": [[2, 'desc']],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Hungarian.json"
                }
            });
            
            $('#individual-table').DataTable({
                "pageLength": 25,
                "order": [[2, 'desc']],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Hungarian.json"
                }
            });
        }
        
        // Handle tab switching
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Redraw DataTables when tab is shown to ensure proper display
            if ($.fn.DataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
        
        // Auto-refresh page after 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes = 300,000 milliseconds
        
        // Date picker enhancements
        $('input[type="date"]').on('change', function() {
            const startDate = $('input[name="start_date"]').val();
            const endDate = $('input[name="end_date"]').val();
            
            if (startDate && endDate) {
                // Validate that end date is not before start date
                if (new Date(endDate) < new Date(startDate)) {
                    alert('A végdátum nem lehet korábbi, mint a kezdődátum!');
                    $(this).val('');
                }
                
                // If range is more than 1 year, ask for confirmation
                const start = new Date(startDate);
                const end = new Date(endDate);
                const daysDiff = Math.floor((end - start) / (1000 * 60 * 60 * 24));
                
                if (daysDiff > 365) {
                    if (!confirm('Több mint 1 éves időszakot választott ki. Biztosan folytatja?')) {
                        $(this).val('');
                    }
                }
            }
        });
        
        // Export button confirmation
        $('a[href*="export"]').on('click', function(e) {
            const startDate = $('input[name="start_date"]').val();
            const endDate = $('input[name="end_date"]').val();
            
            if (!startDate || !endDate) {
                e.preventDefault();
                alert('Kérjük, válasszon dátumtartományt az exportálás előtt!');
                return false;
            }
            
            if (confirm('Biztosan exportálja az ÁFA kimutatást Excel formátumba?')) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
        
        // Filter form validation
        $('#filter-form').on('submit', function(e) {
            const startDate = $('input[name="start_date"]').val();
            const endDate = $('input[name="end_date"]').val();
            
            if (!startDate || !endDate) {
                e.preventDefault();
                alert('Kérjük, adja meg a dátumtartományt!');
                return false;
            }
            
            if (new Date(endDate) < new Date(startDate)) {
                e.preventDefault();
                alert('A végdátum nem lehet korábbi, mint a kezdődátum!');
                return false;
            }
            
            return true;
        });
        
        // Add tooltips
        $('[title]').tooltip();
        
        // Highlight overdue dates
        $('.text-danger').each(function() {
            if ($(this).text().includes('napja') || $(this).text().includes('lejárt')) {
                $(this).closest('tr').addClass('table-danger');
            }
        });
    });
</script>
@endpush
   