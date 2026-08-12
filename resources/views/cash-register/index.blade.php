@extends('layouts.app', [
    'activePage' => 'cash-registers',
    'activeButton' => 'cash-registers',
    'title' => 'Zemef ERP',
    'navName' => 'Pénztár tranzakciók'
])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pénztárak</h3>
                    <div class="card-tools">
                        @if(Auth::user()->hasPermission('create', App\Models\CashTransaction::class))
                            <a href="{{ route('cash-registers.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Új pénztár
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Megnevezés</th>
                                <th>Prefix</th>
                                <th>Deviza</th>
                                <th>Induló érték</th>
                                <th>Dátum</th>
                                <th>Üzlet</th>
                                <th>Munkavállaló</th>
                                <th>Aktív</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashRegisters as $register)
                            <tr>
                                <td>{{ $register?->cash_register_id }}</td>
                                <td>{{ $register?->name }}</td>
                                <td><span class="badge bg-info">{{ $register?->prefix }}</span></td>
                                <td>{{ $register?->currency?->code ?? 'N/A' }} ({{ $register?->currency->name ?? 'N/A' }})</td>
                                <td class="text-right">{{ number_format($register?->opening_balance, $register?->currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $register?->currency->code }}</td>
                                <td>{{ $register?->opening_balance_date?->format('Y-m-d') }}</td>
                                <td>{{ $register?->business->name ?? 'N/A' }}</td>
                                <td>{{ $register?->employee?->firstname . ' ' . $register?->employee?->lastname ?? 'Nincs hozzárendelve' }}</td>
                                <td>
                                    @if($register?->is_active)
                                        <span class="badge bg-success">Aktív</span>
                                    @else
                                        <span class="badge bg-danger">Inaktív</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->hasPermission('edit', App\Models\CashTransaction::class))
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cash-transactions.index', ['cash_register_id' => $register?->cash_register_id]) }}" 
                                            class="btn btn-sm btn-info" title="Tranzakciók">
                                                <i class="fal fa-list"></i>
                                            </a>
                                            
                                                {{-- Bevételi bizonylat nyomtatás --}}
                                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" 
                                                        data-toggle="dropdown" aria-expanded="false" title="Bizonylat nyomtatás">
                                                    <i class="fal fa-print"></i>
                                                </button>
                                            
                                            <div class="dropdown-menu dropdown-menu-right" style="width: 280px;">
                                            {{-- Bevételi bizonylat --}}
                                            <form action="{{ route('cash-registers.print-voucher', $register) }}" method="GET" target="_blank">
                                                <input type="hidden" name="type" value="income">
                                                <div class="px-3 py-2">
                                                    <label class="text-success"><i class="fal fa-arrow-up"></i> Bevételi bizonylat</label>
                                                    <input type="date" name="date" class="form-control form-control-sm mb-2" 
                                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                                    <button type="submit" class="btn btn-sm btn-success btn-block">
                                                        <i class="fal fa-print"></i> Nyomtatás
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            {{-- Kiadási bizonylat --}}
                                            <form action="{{ route('cash-registers.print-voucher', $register) }}" method="GET" target="_blank">
                                                <input type="hidden" name="type" value="expense">
                                                <div class="px-3 py-2">
                                                    <label class="text-danger"><i class="fal fa-arrow-down"></i> Kiadási bizonylat</label>
                                                    <input type="date" name="date" class="form-control form-control-sm mb-2" 
                                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                                    <button type="submit" class="btn btn-sm btn-danger btn-block">
                                                        <i class="fal fa-print"></i> Nyomtatás
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            {{-- Kombinált bizonylat --}}
                                            <form action="{{ route('cash-registers.print-voucher', $register) }}" method="GET" target="_blank">
                                                <input type="hidden" name="type" value="both">
                                                <div class="px-3 py-2">
                                                    <label class="text-primary"><i class="fal fa-chart-pie"></i> Bevételi és Kiadási</label>
                                                    <input type="date" name="date" class="form-control form-control-sm mb-2" 
                                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                                                        <i class="fal fa-print"></i> Összesítő nyomtatás
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="dropdown-divider"></div>
                                            
                                            {{-- Időszaki pénztárjelentés --}}
                                            <form action="{{ route('cash-registers.print-voucher', $register) }}" method="GET" target="_blank">
                                                <input type="hidden" name="type" value="period">
                                                <div class="px-3 py-2">
                                                    <label class="text-info"><i class="fal fa-calendar-alt"></i> Időszaki pénztárjelentés</label>
                                                    <div class="row no-gutters mb-2">
                                                        <div class="col-6 pr-1">
                                                            <input type="date" name="start_date" class="form-control form-control-sm" 
                                                                value="{{ now()->startOfMonth()->format('Y-m-d') }}" 
                                                                max="{{ now()->format('Y-m-d') }}" title="Kezdő dátum">
                                                        </div>
                                                        <div class="col-6 pl-1">
                                                            <input type="date" name="end_date" class="form-control form-control-sm" 
                                                                value="{{ now()->format('Y-m-d') }}" 
                                                                max="{{ now()->format('Y-m-d') }}" title="Záró dátum">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted d-block mb-2">Alapértelmezett: egy hónap</small>
                                                    <button type="submit" class="btn btn-sm btn-info btn-block">
                                                        <i class="fal fa-file-pdf"></i> Jelentés készítés
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        @if(Auth::user()->hasPermission('edit', App\Models\CashTransaction::class))                                        
                                            <a href="{{ route('cash-registers.edit', $register) }}" class="btn btn-sm btn-warning" title="Szerkesztés">
                                            <i class="fal fa-edit"></i>
                                        </a>
                                        @endif
                                        @if(Auth::user()->hasPermission('delete', App\Models\CashTransaction::class))                                        
                                            <form action="{{ route('cash-registers.destroy', $register) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Törlés" 
                                                        onclick="return confirm('Biztosan törölni szeretnéd?')">
                                                    <i class="fal fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Nincsenek pénztárak rögzítve</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dátum figyelmeztetés a dropdownokban
    $('input[name="date"]').on('change', function() {
        let selectedDate = new Date($(this).val());
        let today = new Date();
        today.setHours(0,0,0,0);
        
        if (selectedDate < today) {
            alert('Figyelem! Visszadátumozott bizonylatot nyomtatsz!');
        }
    });
    // Időszakos jelentés dátum validáció
    $('form:has(input[name="type"][value="period"])').on('submit', function(e) {
        let startDate = new Date($(this).find('input[name="start_date"]').val());
        let endDate = new Date($(this).find('input[name="end_date"]').val());
        
        if (endDate < startDate) {
            e.preventDefault();
            alert('A záró dátumnak a kezdő dátum után kell lennie!');
        }
    });
</script>
@endpush