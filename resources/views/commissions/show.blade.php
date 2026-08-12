{{-- resources/views/commissions/show.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'commissions',
    'activeButton' => 'laravel',
    'title' => 'Kampány részletek',
    'navName' => $commission->name
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('commissions.index') }}">Jutalék kampányok</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $commission->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            {{-- Kampány infók --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kampány adatok</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Név:</th>
                                <td>{{ $commission->name }}</td>
                            </tr>
                            <tr>
                                <th>Típus:</th>
                                <td>
                                    @switch($commission->campaign_type)
                                        @case('sales') Eladás @break
                                        @case('gold_purchase') Törtarany @break
                                        @case('expired_ticket') Lejárt jegy @break
                                        @case('pawn') Zálog @break
                                        @default Egyéb
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th>Időszak:</th>
                                <td>{{ $commission->start_date->format('Y.m.d') }} - {{ $commission->end_date->format('Y.m.d') }}</td>
                            </tr>
                            <tr>
                                <th>Státusz:</th>
                                <td>
                                    @if($commission->status == 'active')
                                        <span class="badge badge-success">Aktív</span>
                                    @else
                                        <span class="badge badge-secondary">Inaktív</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        <h5 class="mt-4">Érintett boltok</h5>
                        <ul class="list-group">
                            @foreach($commission->businesses as $business)
                                <li class="list-group-item">
                                    <i class="fas fa-store"></i> {{ $business->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- Jutalékszabályok --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Jutalékszabályok</h4>
                    </div>
                    <div class="card-body">
                        @foreach($commission->commissionRules as $rule)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="badge badge-primary">
                                                {{ $rule->rule_type == 'basic' ? 'Alap' : 'Cél' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Jutalék:</strong>
                                            @if($rule->commission_type == 'percentage')
                                                {{ $rule->commission_value }}%
                                            @elseif($rule->commission_type == 'fixed_amount')
                                                {{ number_format($rule->commission_value, 0, ',', ' ') }} Ft
                                            @elseif($rule->commission_type == 'per_gram')
                                                {{ number_format($rule->commission_value, 0, ',', ' ') }} Ft/gramm
                                            @elseif($rule->commission_type == 'difference_percentage')
                                                Különbözet {{ $rule->commission_value }}%
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Küszöb:</strong>
                                            @if($rule->threshold_min > 0)
                                                {{ number_format($rule->threshold_min, 0, ',', ' ') }} Ft - 
                                                @if($rule->threshold_max)
                                                    {{ number_format($rule->threshold_max, 0, ',', ' ') }} Ft
                                                @else
                                                    ∞
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Időszak:</strong>
                                            {{ $rule->period_type == 'monthly' ? 'Havi' : 'Heti' }}
                                            @if($rule->period_week)
                                                ({{ $rule->period_week }}. hét)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagramok sor --}}
        <div class="row mt-4">
            {{-- Bolt diagram --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-store"></i> Bolt jutalékok összesítése
                        </h4>
                        <p class="card-category">Boltokra jutó jutalékok megoszlása</p>
                    </div>
                    <div class="card-body">
                        <canvas id="businessChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            
            {{-- Alkalmazott diagram --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-users"></i> Alkalmazott jutalékok összesítése
                        </h4>
                        <p class="card-category">Top 10 alkalmazott jutaléka</p>
                    </div>
                    <div class="card-body">
                        <canvas id="employeeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Számolt jutalékok táblázat --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Számolt jutalékok</h4>
                            <button class="btn btn-sm btn-warning" onclick="calculateCommission({{ $commission->id }})">
                                <i class="fas fa-calculator"></i> Újraszámol
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="commissions-table">
                                <thead>
                                    <tr>
                                        <th>Alkalmazott</th>
                                        <th>Bolt</th>
                                        <th>Összeg</th>
                                        <th>Dátum</th>
                                        <th>Státusz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($commission->employeeCommissions as $ec)
                                        <tr>
                                            <td>{{ $ec->employee->lastname }} {{ $ec->employee->firstname }}</td>
                                            <td>{{ $ec->business->name ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($ec->amount, 0, ',', ' ') }} Ft</td>
                                            <td>{{ $ec->commission_date->format('Y.m.d') }}</td>
                                            <td>
                                                @if($ec->status == 'calculated')
                                                    <span class="badge badge-warning">Számolt</span>
                                                @else
                                                    <span class="badge badge-success">Kifizetve</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Még nincsenek számolt jutalékok</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="2">Összesen:</td>
                                        <td class="text-right">{{ number_format($commission->employeeCommissions->sum('amount'), 0, ',', ' ') }} Ft</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('commissions.partials.calculate-modal', ['campaign' => $commission])
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Adatok előkészítése a diagramokhoz
        @php
            // Bolt adatok összegyűjtése
            $businessData = [];
            foreach($commission->employeeCommissions as $ec) {
                $businessName = $ec->business->name ?? 'Ismeretlen';
                if (!isset($businessData[$businessName])) {
                    $businessData[$businessName] = 0;
                }
                $businessData[$businessName] += $ec->amount;
            }
            
            // Alkalmazott adatok összegyűjtése (top 10)
            $employeeData = [];
            foreach($commission->employeeCommissions as $ec) {
                $employeeName = $ec->employee->lastname . ' ' . $ec->employee->firstname;
                if (!isset($employeeData[$employeeName])) {
                    $employeeData[$employeeName] = 0;
                }
                $employeeData[$employeeName] += $ec->amount;
            }
            
            // Rendezés és top 10
            arsort($employeeData);
            $employeeData = array_slice($employeeData, 0, 10, true);
        @endphp

        // Bolt diagram
        const businessCtx = document.getElementById('businessChart').getContext('2d');
        new Chart(businessCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($businessData)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($businessData)) !!},
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#36A2EB'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return {
                                            text: `${label} (${Chart.helpers.formatNumber(value)} Ft - ${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value.toLocaleString('hu-HU')} Ft (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Alkalmazott diagram (vízszintes oszlopdiagram)
        const employeeCtx = document.getElementById('employeeChart').getContext('2d');
        new Chart(employeeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($employeeData)) !!},
                datasets: [{
                    label: 'Jutalék összeg (Ft)',
                    data: {!! json_encode(array_values($employeeData)) !!},
                    backgroundColor: '#36A2EB',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Vízszintes oszlopdiagram
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.raw.toLocaleString('hu-HU')} Ft`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('hu-HU') + ' Ft';
                            }
                        }
                    }
                }
            }
        });
         @if($commission->employeeCommissions->count() > 0)
            // DataTable inicializálása, ha van adat
            $('#commissions-table').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "language": {
                    "search": "Keresés:",
                    "zeroRecords": "Nincs találat",
                    "info": "_START_ - _END_ / _TOTAL_ találat",
                    "infoEmpty": "Nincs találat",
                    "infoFiltered": "(_MAX_ összes rekordból)",
                    "paginate": {
                        "previous": "Előző",
                        "next": "Következő"
                    }
                },
                "order": [[3, "desc"]]
            });
        @else
            console.log('Nincs adat a táblázatban, DataTables kihagyva');
        @endif
        
    });

    function calculateCommission(campaignId) {
        $('#calc_campaign_id').val(campaignId);
        $('#calculateModal').modal('show');
    }
</script>
@endpush