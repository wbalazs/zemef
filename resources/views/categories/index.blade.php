@extends('layouts.app', ['activePage' => 'category', 'activeButton' => 'category', 'title' => 'Zemef ERP', 'navName' => 'Kategóriák'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Kategóriák</h3>
                                    <p class="text-sm mb-0">
                                    </p>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Category::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('category.create') }}" class="btn btn-sm btn-primary">Új kategória</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="categories-table">
                            <table id="datatables" data-name="categories_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th data-orderable="false">Név</th>
                                        <th data-orderable="false">Vegyjel</th>
                                        <th data-orderable="false">Aktív</th>
                                        <th data-orderable="false" class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Név</th>
                                        <th>Vegyjel</th>
                                        <th>Aktív</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @include('categories.category_children', ['categories' => $categories])

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