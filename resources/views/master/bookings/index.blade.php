@extends('layouts.app', ['activePage' => 'booking', 'activeButton' => 'booking', 'title' => 'Zemef ERP', 'navName' => 'Foglalási szabályok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Foglalási szabályok</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_bookings_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Minimum összeg</th>
                                        <th>Maximum összeg</th>
                                        <th>Időtartam (hónap)</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Minimum összeg</th>
                                        <th>Maximum összeg</th>
                                        <th>Időtartam (hónap)</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->name }}</td>
                                            <td>{{ number_format($booking->min_amount,0,"."," ") }} Ft</td>
                                            <td>
                                                @if(is_null($booking->max_amount))
                                                    -
                                                @else
                                                    {{ number_format($booking->max_amount, 0, ".", " ") }} Ft
                                                @endif
                                            </td>
                                            <td>{{ $booking->duration_months }} hónap</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\Booking::class))
                                                <td class="text-right">
                                                    <a href="/master/booking/?booking_id={{ $booking->booking_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                </td>
                                            @endif
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

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });

            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
@endpush