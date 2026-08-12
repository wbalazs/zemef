@extends('layouts.app', ['activePage' => 'all_purchase_ticket', 'activeButton' => 'all_purchase_ticket', 'title' => 'Zemef ERP', 'navName' => 'Összes vételi jegy' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Összes vételi jegy</th></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="all_purchase_tickets_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($purchase_tickets as $purchase_ticket)
                                        <tr>
                                            <td>{{ $purchase_ticket->ticket_number }}</td>
                                            <td>{{ $purchase_ticket->business->name }}</td>
                                            <td>{{ number_format($purchase_ticket->purchase_ticket_items->map(function($item) use ($purchase_ticket) {
                                                return $item->price*$purchase_ticket->currency_value;
                                            })->sum(), 0, ',', ' ') }} {{ $purchase_ticket->currency->sign }}</td>
                                            <td>{{ $purchase_ticket->employee->lastname . ' ' . $purchase_ticket->employee->firstname }}</td>
                                            <td>{{ $purchase_ticket->customer->name }}</td>
                                            <td>{{ $purchase_ticket->created_at }}</td>
                                            <td>{{ $purchase_ticket->relatedticketnumber }}</td>
                                            <td>
                                                <a data-id="{{ $purchase_ticket->purchase_ticket_id }}" class="btn btn-sm btn-primary print"><i class="fa fa-print"></i> Nyomtatás</a>
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
                let purchase_ticket_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/print_purchase_ticket',
                    data: {
                        "purchase_ticket_id": purchase_ticket_id
                    },
                    success: function(response) {
                        let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').empty().html('Vételi jegy nyomtatási kép');
                        $('#printmodal').modal('show');
                    }
                });
            });

            var table = $('#datatables').DataTable();

            $('body').on('click', '.applyBtn', function(e) {
                var hlpr = $('input[placeholder=Sorszám]').val();
                $('input[placeholder=Sorszám]').val(Math.random());
                $('input[placeholder=Sorszám]').trigger('change');
                $('input[placeholder=Sorszám]').val(hlpr);
                $('input[placeholder=Sorszám]').trigger('change');
            });
            
        });
    </script>
@endpush