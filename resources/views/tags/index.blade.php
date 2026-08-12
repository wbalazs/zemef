@extends('layouts.app', ['activePage' => 'tag', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Címkék' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Címkék</h3>
                                </div>
                                @can('create', App\Tag::class)
                                    <div class="col-4 text-right">
                                        <a href="{{ route('tag.create') }}" class="btn btn-sm btn-primary">Új címke</a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="tags-table">
                            <table id="datatables" data-name="tags_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Címke</th>
                                        <th>Szín</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Tag::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Címke</th>
                                        <th>Szín</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($tags as $tag)
                                        <tr>
                                            <td>{{ $tag->name }}</td>
                                            <td><span class="badge badge-default" style="background-color:{{ $tag->color }}">{{ $tag->name }}</span></td>
                                           
                                            <td class="text-right">
                                                @if (auth()->user()->can('update', $tag) || auth()->user()->can('delete', $tag))
                                                    @can('update', $tag)
                                                        <a href="{{ route('tag.edit', $tag->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    @endcan
                                                    @if ($tag->items->isEmpty() && auth()->user()->can('delete', $tag))
                                                        <form class="d-inline-block" action="{{ route('tag.destroy', $tag->id) }}" method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a címkét?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                        </form>
                                                    @endif
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