@extends('layouts.app', ['activePage' => 'purchase_ticket', 'activeButton' => 'purchase_ticket', 'title' => 'Zemef ERP', 'navName' => 'Vételi jegyek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Vételi jegyek</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\PurchaseTicket::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('purchase_ticket.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új vételi jegy</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="purchase_tickets_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
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
                                        <th>Státusz</th>
                                        <th class="text-right">Műveletek</th>
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
                                            <td>{{ $purchase_ticket->status_text }}</td>
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\PurchaseTicket::class))
                                                    <a href="{{ route('purchase_ticket.edit', $purchase_ticket->purchase_ticket_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
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
    </div>
@endsection

@push('js')
    <script type="text/javascript">

        $(document).ready(function() {

            var table = $('#datatables').DataTable();

            $('body').on('click', '.applyBtn', function(e) {
                var hlpr = $('input[placeholder=Sorszám]').val();
                $('input[placeholder=Sorszám]').val(Math.random());
                $('input[placeholder=Sorszám]').trigger('change');
                $('input[placeholder=Sorszám]').val(hlpr);
                $('input[placeholder=Sorszám]').trigger('change');
            });


            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
            
        });
    </script>
@endpush