@extends('layouts.app', ['activePage' => 'inventory_control', 'activeButton' => 'inventory_control', 'title' => 'Zemef ERP', 'navName' => 'Leltározás' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Leltározás</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\InventoryControl::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('inventory_control.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Új leltárív</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="inventory_controls_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Raktár</th>
                                        <th>Leltár dátum</th>
                                        <th>Zárt?</th>
                                       
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                                                           </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Raktár</th>
                                        <th>Leltár dátum</th>
                                        <th>Zárt?</th>
                                        
                                            <th class="text-right">Műveletek</th>
                                        
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($inventory_controls as $inventory_control)
                                        <tr>
                                            <td>{{ $inventory_control->inventory_control_number }}</td>
                                            <td>{{ $inventory_control->business->name }}</td>
                                            <td>{{ $inventory_control->warehouse->name }}</td>
                                            <td>{{ $inventory_control->inventory_control_date }}</td>
                                            <td>@if($inventory_control->is_closed == 0) <a class="btn btn-sm btn-danger">Nem</a> @else <a class="btn btn-sm btn-success">Igen</a>  @endif</td>
                                            
                                                <td class="text-center">
                                                    @if(Auth::user()->hasPermission('edit', App\Models\InventoryControl::class))
                                                        @if($inventory_control->is_closed == 1)
                                                                
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Nyomtatás
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a target="_blank" class="dropdown-item" href="{{ route('inventory_control.print', $inventory_control->inventory_control_id) }}">Leltárív Nyomtatása</a>
                                                                    <a target="_blank" class="dropdown-item" href="{{ route('inventory_control.discrepancies', $inventory_control->inventory_control_id) }}">Leltár Eltérések Nyomtatása</a>
                                                                    <a target="_blank" class="dropdown-item" href="{{ route('inventory_control.report', $inventory_control->inventory_control_id) }}">Jegyzőkönyv Nyomtatása</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    
                                                        <a href="{{ route('inventory_control.edit', $inventory_control->inventory_control_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
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

            $('body').on('click', '.applyBtn', function(e) {
                var hlpr = $('input[placeholder=Sorszám]').val();
                $('input[placeholder=Sorszám]').val(Math.random());
                $('input[placeholder=Sorszám]').trigger('change');
                $('input[placeholder=Sorszám]').val(hlpr);
                $('input[placeholder=Sorszám]').trigger('change');
            });

        });
    </script>
@endpush