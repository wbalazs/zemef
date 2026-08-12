@extends('layouts.app', ['activePage' => 'property', 'activeButton' => 'property', 'title' => 'Zemef ERP', 'navName' => 'Ingatlanok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Ingatlanok</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Address::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('property.create') }}" class="btn btn-sm btn-primary">Új ingatlan</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="properties_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Irányítószám</th>
                                        <th>Település</th>
                                        <th>Cím</th>
                                        @can('manage-properties', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Irányítószám</th>
                                        <th>Település</th>
                                        <th>Cím</th>
                                        @can('manage-properties', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($properties as $property)
                                        <tr>
                                            <td>{{ $property->name }}</td>
                                            <td>{{ $property->zip }}</td>
                                            <td>{{ $property->city }}</td>
                                            <td>{{ $property->address }}</td>
                                            @can('manage-properties', App\User::class)
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $property) || auth()->user()->can('delete', $property))
                                                            @can('update', $property)
                                                                <a href="{{ route('property.edit', $property->property_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                            @endcan
                                                            @can('delete', $property)
                                                                <form class="d-inline-block" action="{{ route('property.destroy', $property->property_id) }}" method="POST">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt az ingatlant?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                                </form>
                                                            @endcan
                                                    @endif
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