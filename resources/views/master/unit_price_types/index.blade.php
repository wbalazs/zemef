@extends('layouts.app', ['activePage' => 'unit_price_types', 'activeButton' => 'unit_price_types', 'title' => 'Zemef ERP', 'navName' => 'Egységár típusok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Egységár típusok</h3>
                                </div>
                                @can('manage-masters', App\User::class)
                                    <div class="col-4 text-right">
                                        <a href="/master/unit_price_type" class="btn btn-sm btn-primary">Új egységár típus</a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_unit_price_types_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Érvényesség kezdete</th>
                                        <th>Ár</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Érvényesség kezdete</th>
                                        <th>Ár</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($unit_price_types as $unit_price_type)
                                        <tr>
                                            <td>{{ $unit_price_type->name }}</td>
                                            <td>{{ $unit_price_type->from_date }}</td>
                                            <td>{{ $unit_price_type->price }}</td>
                                            @can('manage-masters', App\User::class)
                                                <td class="text-right">
                                                    <a href="/master/unit_price_type/?unit_price_type_id={{ $unit_price_type->unit_price_type_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    <form class="d-inline-block" action="/master/delete_unit_price_type/?unit_price_type_id={{ $unit_price_type->unit_price_type_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a devizát?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                    </form>
                                                </td>
                                            @endcan
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