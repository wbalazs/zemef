@extends('layouts.app', ['activePage' => 'product', 'activeButton' => 'product', 'title' => 'Zemef ERP', 'navName' => 'Termékek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Termékek</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Product::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('product.create') }}" class="btn btn-sm btn-primary">Új termék</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="products_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Kód</th>
                                        <th>Megnevezés</th>
                                        <th>Márka</th>
                                        <th>Egyedi?</th>
                                        <th>Állapot</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                        
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Kód</th>
                                        <th>Megnevezés</th>
                                        <th>Márka</th>
                                        <th>Egyedi?</th>
                                        <th>Állapot</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->code }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->brand }}</td>
                                            <td>{{ $product->is_unique == 1 ? 'Igen' : 'Nem' }}</td>
                                            <td>{{ $product->status_text }}</td>
                                            <td class="text-right">
                                                    
                                                @if(Auth::user()->hasPermission('edit', App\Models\Product::class))
                                                    <a href="{{ route('product.edit', $product->product_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                @endif
                                                @if(Auth::user()->hasPermission('delete', App\Models\Product::class))
                                                    <form class="d-inline-block" action="{{ route('product.destroy', $product->product_id) }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a terméket?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                    </form>
                                                @endif
                                                <!-- Nyomtatási lehetőségek -->
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" id="printDropdown{{ $product->product_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Cimke <i class="fas fa-print"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="printDropdown{{ $product->product_id }}">
                                                        <a target="_blank" class="dropdown-item" href="{{ route('product.printLabel', ['product_id' => $product->product_id, 'label_type' => 1]) }}">1-es típus</a>
                                                        <a target="_blank" class="dropdown-item" href="{{ route('product.printLabel', ['product_id' => $product->product_id, 'label_type' => 2]) }}">2-es típus</a>
                                                        <a target="_blank" class="dropdown-item" href="{{ route('product.printLabel', ['product_id' => $product->product_id, 'label_type' => 3]) }}">3-as típus</a>
                                                    </div>
                                                </div>
                                        
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

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
            
        });
    </script>
@endpush