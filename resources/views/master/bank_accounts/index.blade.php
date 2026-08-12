@extends('layouts.app', ['activePage' => 'bank_account', 'activeButton' => 'bank_account', 'title' => 'Zemef ERP', 'navName' => 'Bankszámlák' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Bankszámlák</h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\BankAccount::class))
                                    <div class="col-4 text-right">
                                        <a href="/master/bank_account" class="btn btn-sm btn-primary">Új bankszámla</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="bank_accounts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Bank</th>
                                        <th>Bankszámlaszám</th>
                                        <th>Aktív</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Bank</th>
                                        <th>Bankszámlaszám</th>
                                        <th>Aktív</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($bank_accounts as $bank_account)
                                        <tr>
                                            <td>{{ $bank_account->bank ? $bank_account->bank->name : 'Ismeretlen bank' }}</td>
                                            <td>{{ $bank_account->bank_account_number }}</td>
                                            <td><?php echo ($bank_account->status) ? 'Igen' : 'Nem'; ?></td>
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\BankAccount::class))
                                                <a href="/master/bank_account/?bank_account_id={{ $bank_account->bank_account_id }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                @endif
                                                @if(Auth::user()->hasPermission('delete', App\Models\BankAccount::class))
                                                    <form class="d-inline-block" action="/master/delete_bank_account/?bank_account_id={{ $bank_account->bank_account_id }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a bankszámlát?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
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