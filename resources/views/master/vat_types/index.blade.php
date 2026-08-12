@extends('layouts.app', ['activePage' => 'vat_types', 'activeButton' => 'vat_types', 'title' => 'Zemef ERP', 'navName' => 'Áfa típusok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Áfa típusok</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\VatType::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/vat_type" class="btn btn-sm btn-primary">Új áfa típus</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_vat_types_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Érték %</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Érték %</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($vat_types as $vat_type)
                                        <tr>
                                            <td>{{ $vat_type->name }}</td>
                                            <td>{{ $vat_type->value }} %</td>
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\VatType::class))
                                                        <a href="/master/vat_type/?vat_type_id={{ $vat_type->vat_type_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                @endif
                                                @if( Auth::user()->hasPermission('delete', App\Models\VatType::class))
                                                    <form class="d-inline-block" action="/master/delete_vat_type/?vat_type_id={{ $vat_type->vat_type_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt az áfa típust?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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

        });
    </script>
@endpush