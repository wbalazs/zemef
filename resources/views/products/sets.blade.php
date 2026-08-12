@extends('layouts.app', ['activePage' => 'product_set', 'activeButton' => 'product_set', 'title' => 'Zemef ERP', 'navName' => 'Szettek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Szettek</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Product::class))
                                    <div class="col-4 text-right">
                                        <a href="/product_set/create" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új szett</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="sets_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Márka</th>
                                        <th>Állapot</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Product::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Márka</th>
                                        <th>Állapot</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Product::class))
                                            <th class="text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->brand }}</td>
                                            <td>{{ $product->status_text }}</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\Product::class))
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $product) || auth()->user()->can('delete', $product))
                                                            @can('update', $product)
                                                                <a href="{{ route('product_set.edit', $product->product_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                            @endcan
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

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });

        });
    </script>
@endpush