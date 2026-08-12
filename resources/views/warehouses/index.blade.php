@extends('layouts.app', ['activePage' => 'warehouse', 'activeButton' => 'warehouse', 'title' => 'Zemef ERP', 'navName' => 'Raktárak' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Raktárak</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Warehouse::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('warehouse.create') }}" class="btn btn-sm btn-primary">Új raktár</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="warehouses_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                       
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                       
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($warehouses as $warehouse)
                                        <tr>
                                            <td>{{ $warehouse->name }}</td>
                                            <td class="text-right">
                                                @if (auth()->user()->can('update', $warehouse))
                                                           
                                                        <a href="{{ route('warehouse.edit', $warehouse->warehouse_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                @endif    
                                                @if (auth()->user()->can('delete', $warehouse) || auth()->user()->can('delete', $warehouse))    
                                                        <form class="d-inline-block" action="{{ route('warehouse.destroy', $warehouse->warehouse_id) }}" method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a raktárat?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                        </form>
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

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });



        });
    </script>
@endpush