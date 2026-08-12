@extends('layouts.app', ['activePage' => 'actual_stock', 'activeButton' => 'actual_stock', 'title' => 'Zemef ERP', 'navName' => 'Terméktörténet' ])

@section('content')


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Terméktörténet - {{ $product->name." (".$product->code.")" }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="product_history_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Rögzítette</th>
                                        <th>Dátum</th>
                                        <th>Típus</th>
                                        <th>Mennyiség</th>
                                        <th>Súly</th>
                                        <th>Ár</th>
                                        <th>Innen</th>
                                        <th>Ide</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($history as $item)
                                        <tr>
                                            <td>
                                                {{$item->employee_id}}
                                                @if (!empty($item->employee_id))
                                                    {{ \App\Models\Employee::find($item->employee_id)?->lastname.' '.\App\Models\Employee::find($item->employee_id)?->firstname ?? '-' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d H:i') }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->quantity }} db</td>
                                            <td>
                                                @if (!empty($item->weight))
                                                    {{ number_format($item->weight, 1, '.', ' ') }} g
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($item->price))
                                                    {{ number_format($item->price, 0, '.', ' ') }} Ft
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($item->warehouse_from))
                                                    {{ \App\Models\Warehouse::find($item->warehouse_from)->name ?? '-' }}
                                                
                                                @elseif (!empty($item->customer_id))
                                                    {{ \App\Models\Customer::find($item->customer_id)->name." (Ügyfél)" ?? '-' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($item->warehouse_to))
                                                    {{ \App\Models\Warehouse::find($item->warehouse_to)->name ?? '-' }}
                                                @else
                                                    -
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

            let table = $('#datatables').DataTable();
        });
    </script>
@endpush