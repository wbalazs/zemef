@extends('layouts.app', ['activePage' => 'photography', 'activeButton' => 'photography', 'title' => 'Zemef ERP', 'navName' => 'Fotózás' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Fotózás</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Photography::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('photography.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új fotózási lista</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="photographies_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Lista elnevezés</th>
                                        <th>Készítette</th>
                                        <th>Készültség</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Photography::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Lista elnevezés</th>
                                        <th>Készítette</th>
                                        <th>Készültség</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Photography::class))
                                            <th class="text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($photo_list as $list)
                                        <tr>
                                            <td>{{ $list->list_name }}</td>
                                            <td>{{ $list->employee?->lastname.' '.$list->employee?->firstname }}</td>
                                            <td>{{ $list->created_at }}</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\Photography::class))
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $list) || auth()->user()->can('delete', $list))
                                                            @if(Auth::user()->hasPermission('edit', App\Models\Photography::class))
                                                                <a href="{{ route('photography.edit', $list->photography_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                            @endif
                                                            @if(Auth::user()->hasPermission('delete', App\Models\Photography::class))
                                                                <form class="d-inline-block" action="{{ route('photography.destroy', $list->photography_id) }}" method="POST">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt az listát?') ? this.parentElement.submit() : ''"><i class="fa fa-times"></i></a>
                                                                </form>
                                                            @endif
                                                    @endif
                                                </td>
                                            @endif
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