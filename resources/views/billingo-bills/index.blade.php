@extends('layouts.app', [
    'activePage' => 'billingo-bills',
    'activeButton' => 'billingo-bills',
    'title' => 'Zemef ERP',
    'navName' => 'Billingo Számlák'
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats">
                        <div class="card-header card-header-success card-header-icon">
                            <div class="card-icon">
                                <i class="fa fa-file-invoice"></i>
                            </div>
                            <p class="card-category">Összes számla</p>
                            <h3 class="card-title">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="fa fa-arrow-up"></i>
                                {{ $stats['outgoing'] }} kimenő, {{ $stats['incoming'] }} bejövő
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="fa fa-clock"></i>
                            </div>
                            <p class="card-category">Függőben</p>
                            <h3 class="card-title">{{ $stats['unpaid'] }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ $stats['overdue'] }} lejárt számla
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <p class="card-category">Kifizetve</p>
                            <h3 class="card-title">{{ $stats['paid'] }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="fa fa-calendar"></i>
                                {{ now()->format('Y.m.d') }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats">
                        <div class="card-header card-header-warning card-header-icon">
                            <div class="card-icon">
                                <i class="fa fa-money-bill"></i>
                            </div>
                            <p class="card-category">Összeg</p>
                            <h3 class="card-title">{{ number_format($stats['total_amount'], 0, ',', ' ') }} Ft</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="fa fa-chart-line"></i>
                                Összesített érték
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Szűrők</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('billingo-bills.index') }}" id="filter-form">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Típus</label>
                                    <select name="type" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Összes</option>
                                        <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Kimenő</option>
                                        <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Bejövő</option>
                                        <option value="draft" {{ request('type') == 'draft' ? 'selected' : '' }}>Piszkozat</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fizetés</label>
                                    <select name="payment_status" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>Összes</option>
                                        <option value="not_paid" {{ request('payment_status') == 'not_paid' ? 'selected' : '' }}>Fizetésre vár</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Kifizetve</option>
                                        <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Lejárt</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Dátum tól</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}" 
                                           onchange="document.getElementById('filter-form').submit()">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Dátum ig</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}" 
                                           onchange="document.getElementById('filter-form').submit()">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Keresés</label>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Számlaszám, partner..." 
                                               value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-primary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('billingo-bills.index') }}" class="btn btn-sm btn-default btn-block">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Billingo Számlák</h3>
                                    <p class="text-sm mb-0">
                                        Összesen {{ $bills->total() }} számla
                                    </p>
                                </div>
                                @can('create', App\Models\BillingoBill::class)
                                    <div class="col-4 text-right">
                                        
                                        <a href="{{ route('billingo-bills.create') }}" class="btn btn-sm btn-info ml-2">
                                            <i class="fa fa-file-alt"></i> Új számla
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="bills-table">
                            <table id="datatables" data-name="billingo_bills_table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Partner</th>
                                        <th>Típus</th>
                                        <th>Teljesítés</th>
                                        <th>Esedékesség</th>
                                        <th>Nettó</th>
                                        <th>Bruttó</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bills as $bill)
                                        <tr class="{{ $bill->isOverdue() ? 'table-danger' : '' }}">
                                            <td>
                                                <strong>{{ $bill->bill_number ?? 'N/A' }}</strong>
                                                @if($bill->isOverdue())
                                                    <span class="badge badge-danger ml-1">LEJÁRT</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($bill->partner_name, 30) }}</td>
                                            <td>
                                                @php
                                                    $typeBadges = [
                                                        'outgoing' => ['class' => 'success', 'text' => 'Kimenő'],
                                                        'incoming' => ['class' => 'info', 'text' => 'Bejövő'],
                                                        'draft' => ['class' => 'secondary', 'text' => 'Piszkozat'],
                                                        'proforma' => ['class' => 'warning', 'text' => 'Proforma'],
                                                        'advance' => ['class' => 'primary', 'text' => 'Előleg'],
                                                    ];
                                                    $type = $typeBadges[$bill->type] ?? ['class' => 'secondary', 'text' => $bill->type];
                                                @endphp
                                                <span class="badge badge-{{ $type['class'] }}">{{ $type['text'] }}</span>
                                            </td>
                                            <td>{{ $bill->fulfillment_date ? $bill->fulfillment_date->format('Y.m.d') : '-' }}</td>
                                            <td>
                                                {{ $bill->due_date ? $bill->due_date->format('Y.m.d') : '-' }}
                                                @if($bill->due_date && $bill->due_date->isPast() && !$bill->paid)
                                                    <br><small class="text-danger">{{ $bill->due_date->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($bill->net_total, 0, ',', ' ') }} {{ $bill->currency }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($bill->gross_total, 0, ',', ' ') }} {{ $bill->currency }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $status = $bill->status_badge;
                                                @endphp
                                                <span class="badge badge-{{ $status['color'] }}">
                                                    {{ $status['text'] }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                @can('view', $bill)
                                                    <a href="{{ route('billingo-bills.show', $bill) }}" 
                                                       class="btn btn-link btn-info btn-sm" 
                                                       title="Megtekintés">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('update', $bill)
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if(!$bill->paid)
                                                                <a class="dropdown-item" href="#" onclick="markAsPaid({{ $bill->id }})">
                                                                    <i class="fa fa-check text-success"></i> Megjelölés fizetettnek
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item" href="#" onclick="markAsUnpaid({{ $bill->id }})">
                                                                    <i class="fa fa-times text-danger"></i> Megjelölés fizetetlennek
                                                                </a>
                                                            @endif
                                                            
                                                            @if($bill->billingo_id)
                                                                <a class="dropdown-item" href="{{ route('billingo-bills.sync', $bill) }}">
                                                                    <i class="fa fa-sync text-info"></i> Szinkronizálás
                                                                </a>
                                                            @endif
                                                            
                                                            @if($bill->download_url || $bill->pdf_path)
                                                                <a class="dropdown-item" href="{{ asset($bill->pdf_path) }}">
                                                                    <i class="fa fa-download text-primary"></i> PDF letöltés
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="m-0 text-muted">
                                        Megjelenítve {{ $bills->firstItem() }} - {{ $bills->lastItem() }} / {{ $bills->total() }} számla
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right">
                                        {{ $bills->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Bill Modal -->
    <div class="modal fade" id="createBillModal" tabindex="-1" role="dialog" aria-labelledby="createBillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('billingo-bills.store') }}" id="quick-bill-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createBillModalLabel">Gyors számla létrehozás</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Számla típusa *</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="outgoing">Kimenő számla</option>
                                        <option value="incoming">Bejövő számla</option>
                                        <option value="proforma">Proforma számla</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Pénznem *</label>
                                    <select name="currency" id="currency" class="form-control" required>
                                        <option value="HUF" selected>HUF - Magyar Forint</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="USD">USD - Amerikai dollár</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="partner_name">Partner neve *</label>
                                    <input type="text" name="partner_name" id="partner_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="partner_tax_number">Adószám</label>
                                    <input type="text" name="partner_tax_number" id="partner_tax_number" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fulfillment_date">Teljesítés dátuma *</label>
                                    <input type="date" name="fulfillment_date" id="fulfillment_date" 
                                           class="form-control" 
                                           value="{{ date('Y-m-d') }}" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Fizetési határidő *</label>
                                    <input type="date" name="due_date" id="due_date" 
                                           class="form-control" 
                                           value="{{ date('Y-m-d', strtotime('+8 days')) }}" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <h5 class="mt-4">Tételek</h5>
                        <div id="items-container">
                            <div class="item-row row mb-3">
                                <div class="col-md-5">
                                    <input type="text" name="items[0][name]" class="form-control" placeholder="Termék/szolgáltatás neve *" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[0][quantity]" class="form-control" placeholder="Menny." value="1" min="0.001" step="0.001" required>
                                </div>
                                <div class="col-md-2">
                                    <select name="items[0][unit]" class="form-control">
                                        <option value="db" selected>db</option>
                                        <option value="h">óra</option>
                                        <option value="kg">kg</option>
                                        <option value="m">m</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[0][unit_price]" class="form-control" placeholder="Nettó ár" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-danger remove-item" disabled>
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-item" class="btn btn-sm btn-secondary">
                            <i class="fa fa-plus"></i> Új tétel
                        </button>

                        <div class="form-group mt-4">
                            <label for="comment">Megjegyzés</label>
                            <textarea name="comment" id="comment" class="form-control" rows="2" placeholder="Opcionális megjegyzés..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Számla létrehozása
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#datatables')) {
            var table = $('#datatables').DataTable({
                "pageLength": 25,
                "order": [[3, 'desc']], // Default sort by fulfillment date
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Hungarian.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // Actions column not sortable
                ],
                "initComplete": function() {
                    console.log('DataTable initialized successfully');
                }
            });
        } else {
            console.log('DataTable already initialized');
            // If already initialized, you might want to redraw it
            $('#datatables').DataTable().draw();
        }

        // Add item row in modal
        let itemIndex = 1;
        $('#add-item').click(function() {
            const newRow = `
                <div class="item-row row mb-3">
                    <div class="col-md-5">
                        <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Termék/szolgáltatás neve *" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Menny." value="1" min="0.001" step="0.001" required>
                    </div>
                    <div class="col-md-2">
                        <select name="items[${itemIndex}][unit]" class="form-control">
                            <option value="db" selected>db</option>
                            <option value="h">óra</option>
                            <option value="kg">kg</option>
                            <option value="m">m</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" placeholder="Nettó ár" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>`;
            $('#items-container').append(newRow);
            itemIndex++;
            
            // Enable remove button for all items except first
            $('.remove-item').prop('disabled', false);
        });

        // Remove item row
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
            }
            
            // Disable remove button if only one item left
            if ($('.item-row').length === 1) {
                $('.remove-item').prop('disabled', true);
            }
        });

        // Quick action functions
        window.markAsPaid = function(billId) {
            if (confirm('Biztosan megjelöli ezt a számlát fizetettként?')) {
                $.ajax({
                    url: `/billingo-bills/${billId}/quick-action`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: 'mark_paid'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Hiba történt a művelet során.');
                    }
                });
            }
        };

        window.markAsUnpaid = function(billId) {
            if (confirm('Biztosan megjelöli ezt a számlát fizetetlenként?')) {
                $.ajax({
                    url: `/billingo-bills/${billId}/quick-action`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: 'mark_unpaid'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Hiba történt a művelet során.');
                    }
                });
            }
        };

        // Auto-calculate due date based on type
        $('#type').change(function() {
            const type = $(this).val();
            const fulfillmentDate = $('#fulfillment_date').val();
            
            if (fulfillmentDate) {
                const date = new Date(fulfillmentDate);
                if (type === 'outgoing') {
                    date.setDate(date.getDate() + 8); // 8 days for outgoing
                } else if (type === 'incoming') {
                    date.setDate(date.getDate() + 30); // 30 days for incoming
                }
                $('#due_date').val(date.toISOString().split('T')[0]);
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .table-danger {
        background-color: #f8d7da !important;
    }
    .badge-overdue {
        background-color: #dc3545;
        color: white;
        font-size: 0.7em;
        padding: 2px 5px;
        border-radius: 3px;
    }
    .item-row {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
</style>
@endpush