@extends('layouts.app', ['activePage' => 'cities', 'activeButton' => 'cities', 'title' => 'Zemef ERP', 'navName' => 'Települések' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Települések</h3>
                                </div>
                               @if(Auth::user()->hasPermission('create', App\Models\Address\Address::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('master.cities.create') }}" class="btn btn-sm btn-primary">Új település</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_cities_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Ország</th>
                                        <th>Irányítószám</th>
                                        <th>Település</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Ország</th>
                                        <th>Irányítószám</th>
                                        <th>Település</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($cities as $city)
                                        @foreach ($city->zips as $zip)
                                            <tr>
                                                <td>{{ $city->country->name }}</td>
                                                <td>{{ $zip->name }}</td>
                                                <td>{{ $city->name }}</td>
                                                <td>
                                                    @if(Auth::user()->hasPermission('delete', App\Models\Address\Address::class))
                                                        <a href="{{ route('master.cities.edit', $zip->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                        <form action="{{ route('master.cities.destroy', $city->id) }}" method="post" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a települést?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
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