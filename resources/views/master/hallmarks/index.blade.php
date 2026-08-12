@extends('layouts.app', ['activePage' => 'hallmark', 'activeButton' => 'hallmark', 'title' => 'Zemef ERP', 'navName' => 'Fémjelzés' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Fémjelzés</h3>
                                </div>
                                @if(Auth::user()->hasPermission('delete', App\Models\Hallmark::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/hallmark" class="btn btn-sm btn-primary">Új fémjelzés</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_hallmarks_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Típus</th>
                                        <th>Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Típus</th>
                                        <th>Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($hallmarks as $hallmark)
                                        <tr>
                                            <td>{{ $hallmark->name }}</td>
                                            <td>{{ $hallmark->type }}</td>
                                            <td><?php echo ($hallmark->status) ? 'Igen' : 'Nem'; ?></td>
                                            @can('manage-masters', App\User::class)
                                                <td class="text-right">
                                                    <a href="/master/hallmark/?hallmark_id={{ $hallmark->hallmark_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    <form class="d-inline-block" action="/master/delete_hallmark/?hallmark_id={{ $hallmark->hallmark_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a fémjelzést?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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

            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
@endpush