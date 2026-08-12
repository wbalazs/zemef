@extends('layouts.app', ['activePage' => 'attribute', 'activeButton' => 'attribute', 'title' => 'Zemef ERP', 'navName' => 'Ingatlan tulajdonság' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Ingatlan tulajdonság</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Attribute::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/attribute" class="btn btn-sm btn-primary">Új tulajdonság</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_attributes_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Típus</th>
                                        <th>Aktív</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Típus</th>
                                        <th>Aktív</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($attributes as $attribute)
                                        <tr>
                                            <td>{{ $attribute->name }}</td>
                                            <td>{{ $attribute->type }}</td>
                                            <td><?php echo ($attribute->status) ? 'Igen' : 'Nem'; ?></td>
                                            <td class="text-right">
                                            @if(Auth::user()->hasPermission('create', App\Models\Attribute::class))
                                                <a href="/master/attribute/?attribute_id={{ $attribute->attribute_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                            @endif
                                            @if(Auth::user()->hasPermission('delete', App\Models\Attribute::class))
                                                <form class="d-inline-block" action="/master/delete_attribute/?attribute_id={{ $attribute->attribute_id }}" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a fémjelzést?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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