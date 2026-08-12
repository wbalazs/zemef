@extends('layouts.app', ['activePage' => 'doc_type', 'activeButton' => 'doc_type', 'title' => 'Zemef ERP', 'navName' => 'Dokumentum típusok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Dokumentum típusok</h3>
                                </div>
                                @can('manage-masters', App\User::class)
                                    <div class="col-4 text-right">
                                        <a href="/master/doc_type" class="btn btn-sm btn-primary">Új dokumentum típus</a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_doc_types_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Létrehozás dátuma</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Létrehozás dátuma</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($doc_types as $doc_type)
                                        <tr>
                                            <td>{{ $doc_type->name }}</td>
                                            <td>{{ $doc_type->created_at }}</td>
                                            @can('manage-masters', App\User::class)
                                                <td class="text-right">
                                                    <a href="/master/doc_type/?doc_type_id={{ $doc_type->doc_type_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    <form class="d-inline-block" action="/master/delete_doc_type/?doc_type_id={{ $doc_type->doc_type_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli a dokumentum típust?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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