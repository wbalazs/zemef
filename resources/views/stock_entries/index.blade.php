@extends('layouts.app', ['activePage' => 'stock_entry', 'activeButton' => 'stock_entry', 'title' => 'Zemef ERP', 'navName' => 'Ékszer vásárlások' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Ékszer vásárlások</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\StockEntry::class))
                                     <div class="col-4 text-right">
                                        <a href="{{ route('stock_entry.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új ékszer vásárlás</a>
                                        
                                    </div>
                                @endif
                                
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="stock_entries_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Számla sorszáma</th>
                                        <th>Teljesítés dátuma</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Beszállító</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Számla sorszáma</th>
                                        <th>Teljesítés dátuma</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Beszállító</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Státusz</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($stock_entries as $stock_entry)
                                        <tr>
                                            <td>{{ $stock_entry->ticket_number }}</td>
                                            <td>{{ $stock_entry->invoice_number }}</td>
                                            <td>{{ $stock_entry->fulfillment_date }}</td>
                                            <td>{{ $stock_entry->business?->name }}</td>
                                            <td>{{ number_format($stock_entry->stock_entry_items->map(function($item) use ($stock_entry) {
                                                return $item->price*$stock_entry->currency_value;
                                            })->sum(), 0, ',', ' ') }} {{ $stock_entry->currency?->sign }}</td>
                                            <td>{{ $stock_entry->employee?->lastname . ' ' . $stock_entry->employee?->firstname }}</td>
                                            <td>{{ $stock_entry->customer?->name }}</td>
                                            <td>{{ $stock_entry->created_at }}</td>
                                            <td>{{ $stock_entry->relatedticketnumber }}</td>
                                            <td>{{ $stock_entry->status_text }}</td>
                                            
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\StockEntry::class))
                                                    <a href="{{ route('stock_entry.edit', $stock_entry->stock_entry_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
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