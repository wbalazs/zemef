@extends('layouts.app', ['activePage' => 'wait_approve', 'activeButton' => 'wait_approve', 'title' => 'Zemef ERP', 'navName' => 'Jóváhagyásra váró szabadságok' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Jóváhagyásra váró szabadságok</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Munkavállaló</th>
                                        <th>Dátum-tól</th>
                                        <th>Dátum-ig</th>
                                        <th>Nap(ok)</th>
                                        <th>Státusz</th>
                                        <th>Művelet</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Munkavállaló</th>
                                        <th>Dátum-tól</th>
                                        <th>Dátum-ig</th>
                                        <th>Nap(ok)</th>
                                        <th>Státusz</th>
                                        <th>Művelet</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($holidays as $holiday)
                                        <tr>
                                            <td>{{ $holiday->employee->lastname.' '.$holiday->employee->firstname }}</td>
                                            <td>{{ $holiday->start_date }}</td>
                                            <td>{{ $holiday->end_date }}</td>
                                            <td>{{ $holiday->days_taken }} nap</td>
                                            <td>{{ $holiday->status->status_name }}</td>
                                            <td>
                                                <a data-id="{{ $holiday->id }}" class="btn btn-sm btn-success approve"><i class="fa fa-check"></i></a>
                                                <a data-id="{{ $holiday->id }}" class="btn btn-sm btn-danger reject"><i class="fa fa-times"></i></a>
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
            
            var table2 = $('#datatables').DataTable();

            $(document).on('click', '.approve', function() {
                let id = $(this).data('id');
                let row = table2.row($(this).closest('tr'));
                swal({
                    text: "Biztosan jóváhagyja a szabadságot?",
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '/ajax/approve_holiday',
                            method: 'POST',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.data && response.data.success) {
                                    row.remove().draw();
                                    $.notify({ icon: "done", message: "A szabadság sikeresen jóváhagyva." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                                } else if (response.data && response.data.error) {
                                    $.notify({ icon: "done", message: "Hiba:"+response.data.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                }
                            },
                            error: function() {
                                $.notify({ icon: "done", message: "Hiba történt a szabadság jóváhagyása közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.reject', function() {
                let id = $(this).data('id');
                let row = table2.row($(this).closest('tr'));
                swal({
                    text: "Biztosan elutasítja a szabadságot?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '/ajax/decline_holiday',
                            method: 'POST',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.data && response.data.success) {
                                    row.remove().draw();
                                    $.notify({ icon: "done", message: "A szabadság sikeresen elutasítva." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                                } else if (response.data && response.data.error) {
                                    $.notify({ icon: "done", message: "Hiba:"+response.data.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                }
                            },
                            error: function() {
                                $.notify({ icon: "done", message: "Hiba történt a szabadság elutasítása közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                            }
                        });
                    }
                });
            });


            

        });
    </script>
@endpush