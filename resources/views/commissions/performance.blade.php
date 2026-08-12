{{-- resources/views/commissions/performance.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'performance',
    'activeButton' => 'laravel',
    'title' => 'Teljesítmény',
    'navName' => 'Teljesítmény'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Napi teljesítmény</h4>
                        <p class="card-category">Boltok és alkalmazottak napi forgalma</p>
                    </div>
                    
                    <div class="card-body">
                        {{-- Szűrők --}}
                        <form method="GET" action="{{ route('commissions.performance') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bolt</label>
                                        <select class="form-control select2" name="business_id" onchange="this.form.submit()">
                                            <option value="">Válassz boltot</option>
                                            @foreach($businesses as $business)
                                                <option value="{{ $business->id }}" 
                                                    {{ request('business_id') == $business->id ? 'selected' : '' }}>
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Alkalmazott</label>
                                        <select class="form-control select2" name="employee_id" onchange="this.form.submit()">
                                            <option value="">Válassz alkalmazottat</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->lastname }} {{ $employee->firstname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Dátum</label>
                                        <input type="text" class="form-control monthpicker" name="date" 
                                               value="{{ $date->format('Y-m') }}" onchange="this.form.submit()">
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        @if(request('business_id'))
                            {{-- Bolt teljesítmény --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>
                                        <i class="fas fa-store"></i> 
                                        {{ $businesses->find(request('business_id'))->name }} - havi teljesítmény
                                    </h5>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Dátum</th>
                                                    <th class="text-right">Napi forgalom</th>
                                                    <th class="text-right">Halmozott</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $cumulative = 0; @endphp
                                                @foreach($businessPerformance as $perf)
                                                    @php $cumulative += $perf->total_sales; @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($perf->performance_date)->format('Y.m.d') }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($perf->total_sales, 0, ',', ' ') }} Ft
                                                        </td>
                                                        <td class="text-right">
                                                            {{ number_format($cumulative, 0, ',', ' ') }} Ft
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td>Összesen:</td>
                                                    <td class="text-right">
                                                        {{ number_format($businessPerformance->sum('total_sales'), 0, ',', ' ') }} Ft
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(request('employee_id') && request('business_id'))
                            {{-- Alkalmazott teljesítmény --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>
                                        <i class="fas fa-user"></i> 
                                        {{ $employees->find(request('employee_id'))->lastname }} 
                                        {{ $employees->find(request('employee_id'))->firstname }} - havi teljesítmény
                                    </h5>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Dátum</th>
                                                    <th class="text-right">Napi forgalom</th>
                                                    <th class="text-right">Halmozott</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $cumulative = 0; @endphp
                                                @foreach($employeePerformance as $perf)
                                                    @php $cumulative += $perf->total_sales; @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($perf->performance_date)->format('Y.m.d') }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($perf->total_sales, 0, ',', ' ') }} Ft
                                                        </td>
                                                        <td class="text-right">
                                                            {{ number_format($cumulative, 0, ',', ' ') }} Ft
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td>Összesen:</td>
                                                    <td class="text-right">
                                                        {{ number_format($employeePerformance->sum('total_sales'), 0, ',', ' ') }} Ft
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
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
<script>
    $(document).ready(function() {
        // Month picker
        $('.monthpicker').datetimepicker({
            format: 'YYYY-MM',
            viewMode: 'months',
            useCurrent: false,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            }
        });
        
        // Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Válassz...'
        });
    });
</script>
@endpush