@extends('layouts.app', ['activePage' => 'all_booking', 'activeButton' => 'all_booking', 'title' => 'Zemef ERP', 'navName' => 'Összes foglalós bizonylat' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Összes foglaló</th></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="all_booking_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Ügyfél név</th>
                                        <th>Ügyfél cím</th>
                                        <th>Rögzítő</th>
                                        <th>Foglalás összege</th>
                                        <th>Kedvezmények (kupon, hűség, ajándék)</th>
                                        <th>Végösszeg</th>
                                        <th>Fizetési mód</th>
                                        <th>Foglalási határidő</th>
                                        <th>Létrehozva</th>
                                        <th>Nyomtatva</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Ügyfél név</th>
                                        <th>Ügyfél cím</th>
                                        <th>Rögzítő</th>
                                        <th>Foglalás összege</th>
                                        <th>Kedvezmények (kupon, hűség, ajándék)</th>
                                        <th>Végösszeg</th>
                                        <th>Fizetési mód</th>
                                        <th>Foglalási határidő</th>
                                        <th>Létrehozva</th>
                                        <th>Nyomtatva</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ $booking->customer_name }}</td>
                                            <td>{{ $booking->customer_address }}</td>
                                            <td>{{ $booking->employee_name }}</td>
                                            <td>{{ number_format($booking->booking_price ?? 0, 0, ',', ' ') }} Ft</td>
                                            <td>
                                                Kupon: {{ number_format($booking->coupon ?? 0, 0, ',', ' ') }} Ft<br>
                                                Hűség: {{ number_format($booking->loyalty ?? 0, 0, ',', ' ') }} Ft<br>
                                                Ajándék: {{ number_format($booking->giftcard ?? 0, 0, ',', ' ') }} Ft
                                            </td>
                                            <td>{{ number_format($booking->final_total_gross, 0, ',', ' ') }} Ft</td>
                                            <td>
                                                {{ $booking->payment_method == 'cash' ? 'Készpénz' : ($booking->payment_method == 'card' ? 'Bankkártya' : 'Átutalás') }}
                                            </td>
                                            <td>{{ $booking->booking_max_date ?? '-' }}</td>
                                            <td>{{ $booking->created_at }}</td>
                                            <td>
                                                @if ($booking->is_printed)
                                                    ✔ {{ $booking->printed_at }}
                                                @else
                                                    ✖
                                                @endif
                                            </td>
                                            <td>
                                                <a data-id="{{ $booking->id }}" class="btn btn-sm btn-primary print">
                                                    <i class="fa fa-print"></i> Nyomtatás
                                                </a>
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

    @include('receipts.print_modal')
@endsection

@push('js')
    <script type="text/javascript">

        $(document).ready(function() {


            $('body').on('click', '.print', function(e) {
                let booking_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/print_booking',
                    data: {
                        "booking_id": booking_id
                    },
                    success: function(response) {
                        let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').empty().html('Foglaló nyomtatási kép');
                        $('#printmodal').modal('show');
                    }
                });
            });

            let table = $('#datatables').DataTable();
            
        });
    </script>
@endpush