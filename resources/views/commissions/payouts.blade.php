{{-- resources/views/commissions/payouts.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'payouts',
    'activeButton' => 'laravel',
    'title' => 'Jutalék kifizetések',
    'navName' => 'Kifizetések'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Jutalék kifizetések</h4>
                        <p class="card-category">Alkalmazottak jutalékainak kifizetése</p>
                    </div>
                    
                    <div class="card-body">
                        {{-- Kifizetési űrlap --}}
                        <form method="POST" action="{{ route('commissions.pay') }}" class="mb-4">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Bolt <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="business_id" required>
                                            <option value="">Válassz boltot</option>
                                            @foreach($businesses as $business)
                                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Alkalmazott <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="employee_id" required>
                                            <option value="">Válassz alkalmazottat</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->lastname }} {{ $employee->firstname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Hónap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control monthpicker" name="month" 
                                               value="{{ now()->format('Y-m') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Fizetési mód</label>
                                        <select class="form-control" name="payment_method">
                                            <option value="cash">Készpénz</option>
                                            <option value="bank_transfer">Banki átutalás</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-money-bill"></i> Kifizetés
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        {{-- Kifizetések listája --}}
                        <div class="table-responsive">
                            <table class="table table-striped" id="payouts-table">
                                <thead>
                                    <tr>
                                        <th>Dátum</th>
                                        <th>Alkalmazott</th>
                                        <th>Bolt</th>
                                        <th>Összeg</th>
                                        <th>Fizetési mód</th>
                                        <th>Státusz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payouts ?? [] as $payout)
                                        <tr>
                                            <td>{{ $payout->paid_date->format('Y.m.d') }}</td>
                                            <td>{{ $payout->employee->lastname }} {{ $payout->employee->firstname }}</td>
                                            <td>{{ $payout->business->name }}</td>
                                            <td class="text-right">{{ number_format($payout->amount, 0, ',', ' ') }} Ft</td>
                                            <td>
                                                @if($payout->payment_method == 'cash')
                                                    Készpénz
                                                @else
                                                    Banki átutalás
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-success">Kifizetve</span>
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
        
        // DataTable
        $('#payouts-table').DataTable({
            "language": {
                "search": "Keresés:",
                "zeroRecords": "Nincs találat",
                "info": "_START_ - _END_ / _TOTAL_ találat",
                "infoEmpty": "Nincs találat",
                "infoFiltered": "(_MAX_ összes rekordból)",
            },
            "order": [[0, "desc"]]
        });
    });
</script>
@endpush