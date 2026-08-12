{{-- resources/views/denominations/index.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'denominations',
    'activeButton' => 'denominations',
    'title' => 'Zemef ERP',
    'navName' => 'Pénztár tranzakciók'
])


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Címletek</h3>
                    <div class="card-tools">
                        @if(Auth::user()->hasPermission('create', App\Models\Denomination::class))
                            <a href="{{ route('denominations.create', ['currency_id' => $currencyId]) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Új címlet
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Szűrő deviza szerint -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="currency_filter" onchange="window.location.href='{{ route('denominations.index') }}?currency_id='+this.value">
                                <option value="">Összes deviza</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ $currencyId == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->code }} - {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Deviza</th>
                                    <th>Címlet neve</th>
                                    <th>Érték</th>
                                    <th>Típus</th>
                                    <th>Aktív</th>
                                    <th>Műveletek</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($denominations as $denomination)
                                <tr>
                                    <td>{{ $denomination->denomination_id }}</td>
                                    <td>{{ $denomination->currency->code ?? 'N/A' }}</td>
                                    <td>{{ $denomination->name }}</td>
                                    <td class="text-right">{{ number_format($denomination->value, 2, ',', ' ') }}</td>
                                    <td>
                                        @if($denomination->type === 'coin')
                                            <span class="badge bg-info">Érme</span>
                                        @else
                                            <span class="badge bg-primary">Bankjegy</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($denomination->is_active)
                                            <span class="badge bg-success">Aktív</span>
                                        @else
                                            <span class="badge bg-danger">Inaktív</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Denomination::class))
                                            <a href="{{ route('denominations.edit', $denomination) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(Auth::user()->hasPermission('delete', App\Models\Denomination::class))
                                            <form action="{{ route('denominations.destroy', $denomination) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törölni szeretnéd?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nincsenek címletek rögzítve</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection