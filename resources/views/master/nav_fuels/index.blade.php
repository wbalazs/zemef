@extends('layouts.app', ['activePage' => 'nav_fuels', 'activeButton' => 'nav_fuels', 'title' => 'Zemef ERP', 'navName' => 'Üzemanyagárak' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Üzemanyagárak</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\NAVFuel::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/nav_fuel" class="btn btn-sm btn-primary">Új ár</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_nav_fuels_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Hónap</th>
                                        <th>Benzin</th>
                                        <th>Gázolaj</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Hónap</th>
                                        <th>Benzin</th>
                                        <th>Gázolaj</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($nav_fuels as $nav_fuel)                                        
                                        <tr>
                                            <td>{{ $nav_fuel->year.'. '.Carbon\Carbon::create(null, $nav_fuel->month)->locale('hu')->monthName }}</td>
                                            <td>{{ $nav_fuel->benzin95 }}</td>
                                            <td>{{ $nav_fuel->dizel }}</td>
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\NAVFuel::class))
                                                <a href="/master/nav_fuel/?standard_id={{ $nav_fuel->standard_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                @endif
                                                @if(Auth::user()->hasPermission('delete', App\Models\NAVFuel::class))
                                                    <form class="d-inline-block" action="/master/delete_nav_fuel/?standard_id={{ $nav_fuel->standard_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt az adatot?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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

            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
@endpush