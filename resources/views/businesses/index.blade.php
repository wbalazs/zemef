@extends('layouts.app', ['activePage' => 'business', 'activeButton' => 'business', 'title' => 'Zemef ERP', 'navName' => 'Üzletek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Üzletek</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Business::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('business.create') }}" class="btn btn-sm btn-primary">Új üzlet</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        

                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="businesses_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Név</th>
                                        <th>Irányítószám</th>
                                        <th>Város</th>
                                        <th>Cím</th>
                                        <th>Emelet</th>
                                        <th>Ajtó</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Business::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Név</th>
                                        <th>Irányítószám</th>
                                        <th>Város</th>
                                        <th>Cím</th>
                                        <th>Emelet</th>
                                        <th>Ajtó</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Business::class))
                                            <th class="text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($businesses as $business)
                                        <tr>
                                            <td>{{ $business->name }}</td>
                                            <td>{{ $business->zip }}</td>
                                            <td>{{ $business->city }}</td>
                                            <td>{{ $business->address }}</td>
                                            <td>{{ $business->floor }}</td>
                                            <td>{{ $business->door }}</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\Business::class))
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $business) || auth()->user()->can('delete', $business))
                                                            @if(Auth::user()->hasPermission('edit', App\Models\Business::class))
                                                                <a href="{{ route('business.edit', $business->business_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                            @endif
                                                            @if(Auth::user()->hasPermission('delete', App\Models\Business::class))
                                                                <form class="d-inline-block" action="{{ route('business.destroy', $business->business_id) }}" method="POST">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt az üzletet?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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