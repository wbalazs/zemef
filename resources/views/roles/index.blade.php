@extends('layouts.app', ['activePage' => 'role', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Csoportok'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Csoportok</h3>
                                </div>
                                <div class="col-4 text-right">
                                    @can('manage-users', App\User::class)
                                        <a href="{{ route('role.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új csoport</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
 
                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="roles-table">
                            <table id="datatables" data-name="roles_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Leírás</th>
                                        @can('manage-users', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Leírás</th>
                                        @can('manage-users', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->description }}</td>
                                            @can('manage-users', App\User::class)
                                                <td class="text-right">
                                                    
                                                       
                                                            <a href="{{ route('role.edit', $role->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                       
                                                       
                                                            <form class="d-inline-block" action="{{ route('role.destroy', $role->id) }}" method="POST">
                                                                @method('delete')
                                                                @csrf
                                                                <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a csoportot?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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

            let table = $('#datatables').DataTable();

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
            
        });
    </script>
@endpush
