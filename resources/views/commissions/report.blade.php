{{-- resources/views/commissions/report.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'reports',
    'activeButton' => 'laravel',
    'title' => 'Jutalék riportok',
    'navName' => 'Jutalék riportok'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Jutalék riportok</h4>
                        <p class="card-category">Boltok és alkalmazottak jutalékainak összesítése</p>
                    </div>
                    
                    <div class="card-body">
                        {{-- Szűrők --}}
                        <form method="GET" action="{{ route('commissions.report') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kezdő dátum</label>
                                        <input type="text" class="form-control datepicker" name="start_date" 
                                               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vég dátum</label>
                                        <input type="text" class="form-control datepicker" name="end_date" 
                                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bolt</label>
                                        <select class="form-control select2" name="business_id">
                                            <option value="">Összes bolt</option>
                                            @foreach($businesses as $business)
                                                <option value="{{ $business->business_id }}" 
                                                    {{ request('business_id') == $business->business_id ? 'selected' : '' }}>
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-chart-bar"></i> Riport generálás
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(isset($reportData))
                            {{-- Összesítő kártyák --}}
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-primary">
                                                        <i class="fas fa-store"></i>
                                                    </div>
                                                    <h3 class="info-title">{{ $reportData['total_businesses'] }}</h3>
                                                    <h6 class="stats-title">Érintett boltok</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-success">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <h3 class="info-title">{{ $reportData['total_employees'] }}</h3>
                                                    <h6 class="stats-title">Alkalmazottak</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-warning">
                                                        <i class="fas fa-money-bill"></i>
                                                    </div>
                                                    <h3 class="info-title">{{ number_format($reportData['total_commission'], 0, ',', ' ') }} Ft</h3>
                                                    <h6 class="stats-title">Összes jutalék</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-danger">
                                                        <i class="fas fa-chart-line"></i>
                                                    </div>
                                                    <h3 class="info-title">{{ number_format($reportData['avg_commission'], 0, ',', ' ') }} Ft</h3>
                                                    <h6 class="stats-title">Átlag jutalék</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Diagramok --}}
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Boltok jutalékai</h4>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="reportBusinessChart" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Top 10 alkalmazott</h4>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="reportEmployeeChart" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Részletes táblázat --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Részletes jutalék lista</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="report-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Dátum</th>
                                                            <th>Alkalmazott</th>
                                                            <th>Bolt</th>
                                                            <th>Kampány</th>
                                                            <th>Összeg</th>
                                                            <th>Státusz</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($reportData['details'] as $item)
                                                            <tr>
                                                                <td>{{ $item->commission_date->format('Y.m.d') }}</td>
                                                                <td>{{ $item->employee->lastname }} {{ $item->employee->firstname }}</td>
                                                                <td>{{ $item->business->name }}</td>
                                                                <td>{{ $item->campaign->name }}</td>
                                                                <td class="text-right">{{ number_format($item->amount, 0, ',', ' ') }} Ft</td>
                                                                <td>
                                                                    @if($item->status == 'calculated')
                                                                        <span class="badge badge-warning">Számolt</span>
                                                                    @else
                                                                        <span class="badge badge-success">Kifizetve</span>
                                                                    @endif
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false
        });
        
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        @if(isset($reportData))
            // Bolt diagram
            const businessCtx = document.getElementById('reportBusinessChart').getContext('2d');
            new Chart(businessCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($reportData['business_summary'])) !!},
                    datasets: [{
                        label: 'Jutalék (Ft)',
                        data: {!! json_encode(array_values($reportData['business_summary'])) !!},
                        backgroundColor: '#36A2EB'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw.toLocaleString('hu-HU') + ' Ft';
                                }
                            }
                        }
                    }
                }
            });

            // Alkalmazott diagram
            const employeeCtx = document.getElementById('reportEmployeeChart').getContext('2d');
            new Chart(employeeCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($reportData['employee_summary'])) !!},
                    datasets: [{
                        label: 'Jutalék (Ft)',
                        data: {!! json_encode(array_values($reportData['employee_summary'])) !!},
                        backgroundColor: '#FF6384'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw.toLocaleString('hu-HU') + ' Ft';
                                }
                            }
                        }
                    }
                }
            });

            // DataTable
            $('#report-table').DataTable({
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
                "order": [[0, "desc"]]
            });
        @endif
    });
</script>
@endpush