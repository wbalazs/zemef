@extends('layouts.app', ['activePage' => 'calendar', 'activeButton' => 'calendar', 'title' => 'Zemef ERP', 'navName' => 'Naptár' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Naptár</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="master_calendars_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Dátum</th>
                                        <th>Munkanap?</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Dátum</th>
                                        <th>Munkanap?</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($calendars as $day)
                                        <tr>
                                            <td>{{ $day->formattedDate }}</td>
                                            <td>
                                                <select class="form-control is_working_day" style="max-width:250px;" data-id="{{ $day->calendar_id }}" name="is_working_day">
                                                    <option value="1" {{ $day->is_workday == 1 ? 'selected' : '' }}>Igen</option>
                                                    <option value="0" {{ $day->is_workday == 0 ? 'selected' : '' }}>Nem</option>
                                                </select>
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


            $(document).on('change', '.is_working_day', function() {
                var id = $(this).data('id');
                var is_working_day = $(this).val();
                $.ajax({
                    url: "/ajax/calendar_update",
                    method: "POST",
                    data: {
                        id: id,
                        is_working_day: is_working_day
                    },
                    success: function(data) {
                        $.notify({
                            icon: "done",
                            message: "Sikeresen naptár módosítás!"
                        }, {
                            type: 'success',
                            timer: 3000,
                            placement: {
                                from: 'top',
                                align: 'right'
                            }
                        });
                    },fail: function(data) {
                        $.notify({
                            icon: "error",
                            message: "Hiba történt a naptár módosítása közben!"
                        }, {
                            type: 'danger',
                            timer: 3000,
                            placement: {
                                from: 'top',
                                align: 'right'
                            }
                        });
                    }
                });
            });


            

        });
    </script>
@endpush