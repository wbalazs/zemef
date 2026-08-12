@extends('layouts.app', ['activePage' => 'holiday', 'activeButton' => 'holiday', 'title' => 'Zemef ERP', 'navName' => 'Összes szabadság' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Összes szabadság</h3>
                                </div>
                                <div class="col-4 text-right">
                                        @if(Auth::user()->hasPermission('create', App\Models\Holiday::class))
                                            <a href="{{ route('holiday.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új szabadság</a>
                                        @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="col-12 mt-2">
                            <h6 class="heading-small text-muted mb-1">Tárgyévi szabadság: {{ $employeeHoliday->holiday_days ?? '--' }} nap</h6>
                            <h6 class="heading-small text-muted mb-4">Igényelhető szabadság: {{ $employeeHoliday->holiday_remainings ?? '--' }} nap</h6>
                        </div>

                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="holidays_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Dátum-tól</th>
                                        <th>Dátum-ig</th>
                                        <th>Nap(ok)</th>
                                        <th>Státusz</th>
                                        <th>Felülvizsgálta</th>
                                        <th>Megjegyzés</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Dátum-tól</th>
                                        <th>Dátum-ig</th>
                                        <th>Nap(ok)</th>
                                        <th>Státusz</th>
                                        <th>Felülvizsgálta</th>
                                        <th>Megjegyzés</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($holidays as $holiday)
                                        <tr style="{{ $holiday->status_id == 3 ? 'color: red;' : '' }}">
                                            <td>{{ $holiday->start_date }}</td>
                                            <td>{{ $holiday->end_date }}</td>
                                            <td>{{ $holiday->days_taken }} nap</td>
                                            <td>{{ $holiday->status->status_name }}</td>
                                            <td>
                                                @if($holiday->approvedBy)
                                                    {{ $holiday->approvedBy->lastname }} {{ $holiday->approvedBy->firstname }}
                                                @elseif($holiday->declinedBy)
                                                    {{ $holiday->declinedBy->lastname }} {{ $holiday->declinedBy->firstname }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $holiday->notes }}</td>
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
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#datatables').DataTable();
            
        });
    </script>
@endpush