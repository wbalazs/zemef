@extends('layouts.app', [
    'activePage' => 'vat-reports',
    'activeButton' => 'reports',
    'title' => 'Zemef ERP - ÁFA Fizetés Kimutatás',
    'navName' => 'ÁFA Fizetés Kimutatás'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Period Selection Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Időszak kiválasztása</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('vat-reports.payment') }}" id="period-form">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Időszak típusa</label>
                                <select name="period" class="form-control" id="period-select">
                                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Havi</option>
                                    <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Negyedéves</option>
                                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Éves</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Év</label>
                                <select name="year" class="form-control" id="year-select">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="month-container">
                            <div class="form-group">
                                <label>Hónap</label>
                                <select name="month" class="form-control">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="quarter-container" style="display: {{ $period == 'quarter' ? 'block' : 'none' }}">
                            <div class="form-group">
                                <label>Negyedév</label>
                                <select name="quarter" class="form-control">
                                    <option value="1" {{ (isset($quarter) && $quarter == 1) ? 'selected' : '' }}>1. negyedév</option>
                                    <option value="2" {{ (isset($quarter) && $quarter == 2) ? 'selected' : '' }}>2. negyedév</option>
                                    <option value="3" {{ (isset($quarter) && $quarter == 3) ? 'selected' : '' }}>3. negyedév</option>
                                    <option value="4" {{ (isset($quarter) && $quarter == 4) ? 'selected' : '' }}>4. negyedév</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-calculator"></i> Kimutatás generálása
                                    </button>
                                    <button type="button" class="btn btn-info" id="print-report">
                                        <i class="fa fa-print"></i> Nyomtatás
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> 
                                Kimutatási időszak: 
                                <strong>{{ $startDate->format('Y. m. d.') }}</strong> - 
                                <strong>{{ $endDate->format('Y. m. d.') }}</strong>
                                ({{ $period == 'month' ? 'Havi' : ($period == 'quarter' ? 'Negyedéves' : 'Éves') }} kimutatás)
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- VAT Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-arrow-up"></i>
                        </div>
                        <p class="card-category">Kimenő ÁFA</p>
                        <h3 class="card-title">{{ number_format($totalOutputVat, 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fa fa-calendar"></i> 
                            Értékesítésekből származó ÁFA
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-arrow-down"></i>
                        </div>
                        <p class="card-category">Bejövő ÁFA</p>
                        <h3 class="card-title">{{ number_format($totalInputVat, 0, ',', ' ') }} Ft</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fa fa-calendar"></i> 
                            Beszerzésekből levonható ÁFA
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-money-check-alt"></i>
                        </div>
                        <p class="card-category">Fizetendő ÁFA</p>
                        @php
                            $vatToPayTotal = $totalOutputVat - $totalInputVat;
                        @endphp
                        <h3 class="card-title {{ $vatToPayTotal >= 0 ? 'text-warning' : 'text-success' }}">
                            {{ number_format(abs($vatToPayTotal), 0, ',', ' ') }} Ft
                        </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fa {{ $vatToPayTotal >= 0 ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> 
                            {{ $vatToPayTotal >= 0 ? 'NAV felé fizetendő' : 'Visszaigényelhető' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- VAT Calculation Details -->
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">ÁFA számítás részletei</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="vat-calculation-table">
                <thead>
                    <tr>
                        <th>ÁFA kulcs</th>
                        <th class="text-right">Kimenő ÁFA (értékesítés)</th>
                        <th class="text-right">Bejövő ÁFA (beszerzés)</th>
                        <th class="text-right">ÁFA különbözet</th>
                        <th class="text-right">Fizetendő/ visszaigényelhető</th>
                        <th class="text-center">Műveletek</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($vatToPay) > 0)
                        @foreach($vatToPay as $vatRate => $data)
                            @php
                                $difference = $data['output_vat'] - $data['input_vat'];
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $vatRate }}%</strong>
                                    @if($data['output_vat'] > 0 || $data['input_vat'] > 0)
                                        <br>
                                        <small class="text-muted">
                                            @if($data['output_vat'] > 0)
                                                <span class="text-danger">Kimenő: {{ number_format($data['output_vat'], 0, ',', ' ') }} Ft</span>
                                            @endif
                                            @if($data['input_vat'] > 0)
                                                <span class="text-success"> / Bejövő: {{ number_format($data['input_vat'], 0, ',', ' ') }} Ft</span>
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="text-danger">
                                        {{ number_format($data['output_vat'], 0, ',', ' ') }} Ft
                                    </span>
                                </td>
                                <td class="text-right">
                                    <span class="text-success">
                                        {{ number_format($data['input_vat'], 0, ',', ' ') }} Ft
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($difference != 0)
                                        <span class="{{ $difference >= 0 ? 'text-warning' : 'text-info' }}">
                                            {{ number_format(abs($difference), 0, ',', ' ') }} Ft
                                        </span>
                                    @else
                                        <span class="text-muted">0 Ft</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($difference > 0)
                                        <span class="badge badge-warning">
                                            Fizetendő: {{ number_format($difference, 0, ',', ' ') }} Ft
                                        </span>
                                    @elseif($difference < 0)
                                        <span class="badge badge-info">
                                            Visszaigényelhető: {{ number_format(abs($difference), 0, ',', ' ') }} Ft
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">0 Ft</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($data['output_vat'] > 0 || $data['input_vat'] > 0)
                                        <a href="{{ route('vat-reports.detailed') }}?vat_rate={{ $vatRate }}%&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Részletek" 
                                           data-toggle="tooltip">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Nincsenek adatok a kiválasztott időszakra.
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th>Összesítés:</th>
                        <th class="text-right">{{ number_format($totalOutputVat, 0, ',', ' ') }} Ft</th>
                        <th class="text-right">{{ number_format($totalInputVat, 0, ',', ' ') }} Ft</th>
                        <th class="text-right">
                            @php
                                $totalDifference = $totalOutputVat - $totalInputVat;
                            @endphp
                            {{ number_format(abs($totalDifference), 0, ',', ' ') }} Ft
                        </th>
                        <th class="text-right">
                            @if($totalDifference > 0)
                                <span class="badge badge-warning badge-lg">
                                    Összes fizetendő: {{ number_format($totalDifference, 0, ',', ' ') }} Ft
                                </span>
                            @elseif($totalDifference < 0)
                                <span class="badge badge-info badge-lg">
                                    Összes visszaigényelhető: {{ number_format(abs($totalDifference), 0, ',', ' ') }} Ft
                                </span>
                            @else
                                <span class="badge badge-secondary badge-lg">0 Ft</span>
                            @endif
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

        <!-- ssssssssssBreakdown by Bill Types -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Kimenő számlák összesítése</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $outgoingSummary = App\Models\BillingoBillItem::selectRaw('
                                    COUNT(DISTINCT billingo_bills.id) as bill_count,
                                    SUM(billingo_bill_items.net_total) as total_net,
                                    SUM(billingo_bill_items.vat_amount) as total_vat,
                                    SUM(billingo_bill_items.gross_total) as total_gross
                                ')
                                ->join('billingo_bills', 'billingo_bill_items.billingo_bill_id', '=', 'billingo_bills.id')
                                ->where('billingo_bills.type', 'outgoing')
                                ->where('billingo_bills.paid', true)
                                ->whereBetween('billingo_bills.fulfillment_date', [$startDate, $endDate])
                                ->whereNotIn('billingo_bill_items.vat', ['0%', ''])
                                ->first();
                        @endphp
                        
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Sorszámlák száma:</th>
                                    <td class="text-right">{{ $outgoingSummary->bill_count ?? 0 }} db</td>
                                </tr>
                                <tr>
                                    <th>Nettó összeg:</th>
                                    <td class="text-right">{{ number_format($outgoingSummary->total_net ?? 0, 0, ',', ' ') }} Ft</td>
                                </tr>
                                <tr>
                                    <th>ÁFA összeg:</th>
                                    <td class="text-right">{{ number_format($outgoingSummary->total_vat ?? 0, 0, ',', ' ') }} Ft</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Bruttó összeg:</th>
                                    <td class="text-right"><strong>{{ number_format($outgoingSummary->total_gross ?? 0, 0, ',', ' ') }} Ft</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <a href="{{ route('billingo-bills.index') }}?type=outgoing&date_from={{ $startDate->format('Y-m-d') }}&date_to={{ $endDate->format('Y-m-d') }}&payment_status=paid" 
                               class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-list"></i> Kimenő számlák megtekintése
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Bejövő számlák összesítése</h4>
                    </div>
                    <div class="card-body">
                       @php
                            $incomingSummary = App\Models\BillingoBillItem::selectRaw('
                                    COUNT(DISTINCT billingo_bills.id) as bill_count,
                                    SUM(billingo_bill_items.net_total) as total_net,
                                    SUM(billingo_bill_items.vat_amount) as total_vat,
                                    SUM(billingo_bill_items.gross_total) as total_gross
                                ')
                                ->join('billingo_bills', 'billingo_bill_items.billingo_bill_id', '=', 'billingo_bills.id')
                                ->where('billingo_bills.type', 'incoming')
                                ->where('billingo_bills.paid', true)
                                ->whereBetween('billingo_bills.fulfillment_date', [$startDate, $endDate])
                                ->whereNotIn('billingo_bill_items.vat', ['0%', ''])
                                ->first();
                        @endphp
                        
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Bevételezett számlák száma:</th>
                                    <td class="text-right">{{ $incomingSummary->bill_count ?? 0 }} db</td>
                                </tr>
                                <tr>
                                    <th>Nettó összeg:</th>
                                    <td class="text-right">{{ number_format($incomingSummary->total_net ?? 0, 0, ',', ' ') }} Ft</td>
                                </tr>
                                <tr>
                                    <th>ÁFA összeg:</th>
                                    <td class="text-right">{{ number_format($incomingSummary->total_vat ?? 0, 0, ',', ' ') }} Ft</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Bruttó összeg:</th>
                                    <td class="text-right"><strong>{{ number_format($incomingSummary->total_gross ?? 0, 0, ',', ' ') }} Ft</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <a href="{{ route('billingo-bills.index') }}?type=incoming&date_from={{ $startDate->format('Y-m-d') }}&date_to={{ $endDate->format('Y-m-d') }}&payment_status=paid" 
                               class="btn btn-sm btn-info btn-block">
                                <i class="fa fa-list"></i> Bejövő számlák megtekintése
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export and Print Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Export és nyomtatás</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('vat-reports.export') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" 
                           class="btn btn-success btn-block">
                            <i class="fa fa-file-excel"></i> Excel export
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-block" id="print-details">
                            <i class="fa fa-print"></i> Részletes nyomtatás
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info btn-block" id="print-summary">
                            <i class="fa fa-print"></i> Összesítő nyomtatás
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('vat-reports.index') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" 
                           class="btn btn-secondary btn-block">
                            <i class="fa fa-arrow-left"></i> Vissza a kimutatáshoz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Template (hidden) -->
        <div id="print-template" style="display: none;">
            <div class="print-header">
                <h2>ÁFA Fizetés Kimutatás</h2>
                <p><strong>Cég:</strong> Zemef Kft.</p>
                <p><strong>Időszak:</strong> {{ $startDate->format('Y. m. d.') }} - {{ $endDate->format('Y. m. d.') }}</p>
                <p><strong>Nyomtatás dátuma:</strong> {{ now()->format('Y. m. d. H:i') }}</p>
                <hr>
            </div>
            
            <div class="print-summary">
                <h3>Összesítés</h3>
                <table class="print-table">
                    <tr>
                        <th>Kimenő ÁFA (értékesítés):</th>
                        <td>{{ number_format($totalOutputVat, 0, ',', ' ') }} Ft</td>
                    </tr>
                    <tr>
                        <th>Bejövő ÁFA (beszerzés):</th>
                        <td>{{ number_format($totalInputVat, 0, ',', ' ') }} Ft</td>
                    </tr>
                    <tr>
                        <th>ÁFA különbözet:</th>
                        <td>{{ number_format(abs($totalOutputVat - $totalInputVat), 0, ',', ' ') }} Ft</td>
                    </tr>
                    <tr>
                        <th><strong>Fizetendő/Visszaigényelhető:</strong></th>
                        <td><strong>{{ number_format(abs($totalOutputVat - $totalInputVat), 0, ',', ' ') }} Ft</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="print-details">
                <h3>Részletes bontás</h3>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>ÁFA kulcs</th>
                            <th>Kimenő ÁFA</th>
                            <th>Bejövő ÁFA</th>
                            <th>Különbözet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vatToPay as $vatRate => $data)
                            <tr>
                                <td>{{ $vatRate }}%</td>
                                <td>{{ number_format($data['output_vat'], 0, ',', ' ') }} Ft</td>
                                <td>{{ number_format($data['input_vat'], 0, ',', ' ') }} Ft</td>
                                <td>{{ number_format($data['output_vat'] - $data['input_vat'], 0, ',', ' ') }} Ft</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="print-footer">
                <hr>
                <p><strong>Aláírás:</strong> _________________________________</p>
                <p><strong>Dátum:</strong> _______________________________</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card-stats .card-header {
        padding: 15px;
    }
    
    .card-stats .card-title {
        font-size: 1.5rem;
        margin-bottom: 0;
    }
    
    .badge-lg {
        font-size: 1rem;
        padding: 8px 12px;
    }
    
    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #print-template, #print-template * {
            visibility: visible;
        }
        #print-template {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: white;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .print-table th,
        .print-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .print-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .print-header, .print-summary, .print-details, .print-footer {
            margin-bottom: 30px;
        }
        .print-header h2 {
            margin-top: 0;
        }
        hr {
            border-top: 2px solid #000;
        }
    }
</style>
@endpush

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        // Handle period type change
        $('#period-select').change(function() {
            const period = $(this).val();
            
            if (period === 'quarter') {
                $('#month-container').hide();
                $('#quarter-container').show();
            } else if (period === 'year') {
                $('#month-container').hide();
                $('#quarter-container').hide();
            } else {
                $('#month-container').show();
                $('#quarter-container').hide();
            }
        });

        // Initialize DataTable
if ($.fn.DataTable) {
    $('#vat-calculation-table').DataTable({
        "pageLength": 10,
        "order": [[0, 'asc']],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Hungarian.json"
        },
        "columnDefs": [
            { 
                "orderable": false, 
                "targets": [4, 5] // Actions and status columns not sortable
            },
            { 
                "className": "text-right", 
                "targets": [1, 2, 3, 4] // Right align numeric columns
            },
            { 
                "className": "text-center", 
                "targets": [5] // Center align actions column
            }
        ],
        "drawCallback": function(settings) {
            // Initialize tooltips after table is drawn
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

        // Print functionality
        $('#print-report').click(function() {
            window.print();
        });

        $('#print-details').click(function() {
            // Show the print template and print
            const printContent = $('#print-template').html();
            const originalContent = $('body').html();
            
            $('body').html(printContent);
            window.print();
            $('body').html(originalContent);
            location.reload();
        });

        $('#print-summary').click(function() {
            // Create simplified summary for printing
            const printWindow = window.open('', '_blank');
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>ÁFA Fizetés Kimutatás - Összesítő</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .summary-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                        .summary-table th { background-color: #f2f2f2; font-weight: bold; }
                        .total-row { background-color: #f8f9fa; font-weight: bold; }
                        .company-info { margin-bottom: 30px; }
                        .print-date { margin-top: 30px; font-style: italic; }
                        @media print {
                            body { margin: 0; padding: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="company-info">
                        <h1>Zemef Kft. - ÁFA Fizetés Kimutatás</h1>
                        <p><strong>Időszak:</strong> ${$('#period-select option:selected').text()} - ${$('#year-select').val()}. év</p>
                        <p><strong>Kimutatási időszak:</strong> {{ $startDate->format('Y. m. d.') }} - {{ $endDate->format('Y. m. d.') }}</p>
                    </div>
                    
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Leírás</th>
                                <th>Összeg (Ft)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Kimenő ÁFA (értékesítések)</td>
                                <td>${formatNumber({{ $totalOutputVat }})} Ft</td>
                            </tr>
                            <tr>
                                <td>Bejövő ÁFA (beszerzések)</td>
                                <td>${formatNumber({{ $totalInputVat }})} Ft</td>
                            </tr>
                            <tr class="total-row">
                                <td>ÁFA különbözet</td>
                                <td>${formatNumber({{ abs($totalOutputVat - $totalInputVat) }})} Ft</td>
                            </tr>
                            <tr class="total-row">
                                <td>
                                    <strong>
                                        ${ {{ $totalOutputVat - $totalInputVat }} >= 0 ? 'NAV felé fizetendő' : 'Visszaigényelhető' }
                                    </strong>
                                </td>
                                <td><strong>${formatNumber({{ abs($totalOutputVat - $totalInputVat) }})} Ft</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="print-date">
                        <p>Nyomtatva: {{ now()->format('Y. m. d. H:i') }}</p>
                    </div>
                    
                    <div class="no-print" style="margin-top: 20px;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
                            Nyomtatás
                        </button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer;">
                            Bezárás
                        </button>
                    </div>
                    
                    <script>
                        function formatNumber(num) {
                            return num.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, " ");
                        }
                    <\/script>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
        });

        // Auto-calculate when year changes
        $('#year-select, #period-select').change(function() {
            // Optional: Auto-submit form after short delay
            setTimeout(function() {
                $('#period-form').submit();
            }, 500);
        });

        // Add confirmation for Excel export
        $('a[href*="export"]').on('click', function(e) {
            if (!confirm('Biztosan exportálja az ÁFA fizetés kimutatást Excel formátumba?')) {
                e.preventDefault();
                return false;
            }
        });

        // Add keyboard shortcuts
        $(document).keydown(function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                $('#print-report').click();
            }
            
            // Ctrl+E for Excel export
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                $('a[href*="export"]').click();
            }
        });

        // Show keyboard shortcuts help
        $(document).tooltip({
            items: '[title]',
            content: function() {
                const element = $(this);
                const title = element.attr('title');
                const shortcut = element.data('shortcut');
                
                if (shortcut) {
                    return title + ' (' + shortcut + ')';
                }
                
                return title;
            }
        });
    });

    // Helper function for number formatting
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }
</script>
@endpush