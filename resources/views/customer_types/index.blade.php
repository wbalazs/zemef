@extends('layouts.app', ['activePage' => 'customer_type', 'activeButton' => 'customer_type', 'title' => 'Zemef ERP', 'navName' => 'Ügyfél típus'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Ügyfél típus</h3>
                                    <p class="text-sm mb-0">
                                    </p>
                                </div>
                                
                                @if(Auth::user()->hasPermission('create', App\Models\Bank::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('customer_type.create') }}" class="btn btn-sm btn-primary">Új kategória</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="customer_types-table">
                            <table id="datatables" data-name="customer_types_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th data-orderable="false">Név</th>
                                        <th data-orderable="false">Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th data-orderable="false" class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Név</th>
                                        <th>Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @include('customer_types.customer_type_children', ['customer_types' => $customer_types])

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