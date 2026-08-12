@extends('layouts.app', [
    'activePage' => 'cash-on-delivery', 
    'activeButton' => 'cash-on-delivery', 
    'title' => 'Zemef ERP', 
    'navName' => 'Utánvét statisztika'
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Utánvét statisztika</h3>
                                    <p class="text-sm mb-0">
                                        Utánvétek áttekintése és statisztikái
                                    </p>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('cash-on-delivery.index') }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-arrow-left"></i> Vissza
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Summary Cards -->
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="icon-big text-center icon-warning">
                                                        <i class="fa fa-money-bill-wave text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="col-7">
                                                    <div class="numbers">
                                                        <p class="card-category">Összes utánvét</p>
                                                        <h4 class="card-title">{{ number_format($totalAmount, 0, ',', ' ') }} Ft</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="icon-big text-center icon-warning">
                                                        <i class="fa fa-clock text-warning"></i>
                                                    </div>
                                                </div>
                                                <div class="col-7">
                                                    <div class="numbers">
                                                        <p class="card-category">Függőben</p>
                                                        <h4 class="card-title">{{ number_format($pendingAmount, 0, ',', ' ') }} Ft</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="icon-big text-center icon-warning">
                                                        <i class="fa fa-check-circle text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="col-7">
                                                    <div class="numbers">
                                                        <p class="card-category">Beszedve</p>
                                                        <h4 class="card-title">{{ number_format($collectedAmount, 0, ',', ' ') }} Ft</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="card card-stats">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="icon-big text-center icon-warning">
                                                        <i class="fa fa-exchange-alt text-info"></i>
                                                    </div>
                                                </div>
                                                <div class="col-7">
                                                    <div class="numbers">
                                                        <p class="card-category">Átutalva</p>
                                                        <h4 class="card-title">{{ number_format($transferredAmount, 0, ',', ' ') }} Ft</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Distribution -->
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Státusz eloszlás</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Státusz</th>
                                                        <th>Darabszám</th>
                                                        <th>Összeg</th>
                                                        <th>%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($stats as $stat)
                                                    <tr>
                                                        <td>
                                                            @switch($stat->status)
                                                                @case('pending')
                                                                    <span class="badge badge-warning">Függőben</span>
                                                                    @break
                                                                @case('collected')
                                                                    <span class="badge badge-success">Beszedve</span>
                                                                    @break
                                                                @case('transferred')
                                                                    <span class="badge badge-info">Átutalva</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge badge-danger">Törölve</span>
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $stat->count }}</td>
                                                        <td>{{ number_format($stat->total_amount, 0, ',', ' ') }} Ft</td>
                                                        <td>
                                                            @if($totalAmount > 0)
                                                                {{ number_format(($stat->total_amount / $totalAmount) * 100, 1) }}%
                                                            @else
                                                                0%
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Legutóbbi tevékenységek</h4>
                                        </div>
                                        <div class="card-body">
                                            @if($recentActivities->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Csomag</th>
                                                                <th>Összeg</th>
                                                                <th>Státusz</th>
                                                                <th>Frissítve</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($recentActivities as $activity)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('package.edit', $activity->package_id) }}" class="text-info">
                                                                        {{ $activity->package->package_number ?? 'N/A' }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ number_format($activity->amount, 0, ',', ' ') }} Ft</td>
                                                                <td>
                                                                    <span class="badge badge-{{ $activity->status == 'pending' ? 'warning' : ($activity->status == 'collected' ? 'success' : 'info') }}">
                                                                        {{ $activity->status_label }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $activity->updated_at->format('m.d H:i') }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted text-center">Nincs elérhető tevékenység</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="row mt-4">
                                <div class="col-lg-12 text-center">
                                    <a href="{{ route('cash-on-delivery.pending') }}" class="btn btn-warning">
                                        <i class="fa fa-clock"></i> Függőben lévő utánvétek
                                    </a>
                                    <a href="{{ route('cash-on-delivery.collected') }}" class="btn btn-success">
                                        <i class="fa fa-check-circle"></i> Beszedett utánvétek
                                    </a>
                                    <a href="{{ route('cash-on-delivery.transferred') }}" class="btn btn-info">
                                        <i class="fa fa-exchange-alt"></i> Átutalt utánvétek
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection