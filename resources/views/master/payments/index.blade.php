@extends('layouts.app', ['activePage' => 'payment', 'activeButton' => 'payment', 'title' => 'Zemef ERP', 'navName' => 'Fizetési mód' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Fizetési mód</h3>
                                </div>
                                 @if(Auth::user()->hasPermission('create', App\Models\Payment::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/payment" class="btn btn-sm btn-primary">Új fizetési mód</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_payments_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Napok száma</th>
                                        <th>Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Megnevezés</th>
                                        <th>Napok száma</th>
                                        <th>Aktív</th>
                                        @can('manage-masters', App\User::class)
                                            <th class="text-right">Műveletek</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->name }}</td>
                                            <td>{{ $payment->days }}</td>
                                            <td><?php echo ($payment->status) ? 'Igen' : 'Nem'; ?></td>
                                            @can('manage-masters', App\User::class)
                                                <td class="text-right">
                                                    <a href="/master/payment/?payment_id={{ $payment->payment_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                    <form class="d-inline-block" action="/master/delete_payment/?payment_id={{ $payment->payment_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a fizetési módot?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                    </form>
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

            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
@endpush