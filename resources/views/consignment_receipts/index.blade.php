@extends('layouts.app', ['activePage' => 'consignment_receipt', 'activeButton' => 'consignment_receipt', 'title' => 'Zemef ERP', 'navName' => 'Bizományosi átvételek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Bizományosi átvételek</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\ConsignmentReceipt::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('consignment_receipt.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új bizományosi átvétel</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="consignment_receipts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Lejárati dátum</th>
                                        <th>Fémjelzés?</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Státusz</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\ConsignmentReceipt::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Lejárati dátum</th>
                                        <th>Fémjelzés?</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Státusz</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\ConsignmentReceipt::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($consignment_receipts as $consignment_receipt)
                                        <tr>
                                            <td>{{ $consignment_receipt->ticket_number }}</td>
                                            <td>{{ $consignment_receipt->expire_date }}</td>
                                            <td>{{ $consignment_receipt->is_hallmark_requested == 1 ? 'Igen' : 'Nem' }}</td>
                                            <td>{{ $consignment_receipt->business->name }}</td>
                                            <td>{{ number_format($consignment_receipt->consignment_receipt_items->map(function($item) use ($consignment_receipt) {
                                                return $item->price*$consignment_receipt->currency_value;
                                            })->sum(), 0, ',', ' ') }} {{ $consignment_receipt->currency->sign }}</td>
                                            <td>{{ $consignment_receipt->employee->lastname . ' ' . $consignment_receipt->employee->firstname }}</td>
                                            <td>{{ $consignment_receipt->customer->name }}</td>
                                            <td>{{ $consignment_receipt->created_at }}</td>
                                            <td>{{ $consignment_receipt->relatedticketnumber }}</td>
                                            <td>{{ $consignment_receipt->status_text }}</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\ConsignmentReceipt::class))
                                                <td class="text-right">
                                                    @if(Auth::user()->hasPermission('edit', App\Models\ConsignmentReceipt::class))
                                                        <a href="{{ route('consignment_receipt.edit', $consignment_receipt->consignment_receipt_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    @endif
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