@extends('layouts.app', ['activePage' => 'repair', 'activeButton' => 'repair', 'title' => 'Zemef ERP', 'navName' => 'Javítandó tételek' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Javítandó tételek</th></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="repairs_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Termék</th>
                                        <th>Mennyiség</th>
                                        <th>Megjegyzés</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Termék</th>
                                        <th>Mennyiség</th>
                                        <th>Megjegyzés</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($repair_list as $item)
                                        <tr>
                                            <td>{{ $item->type == 'delivery_note' ? $item->delivery_note_number : $item->warehouse_transfer_number }}</td>                                          
                                            <td>{{ $all_warehouse[$item->warehouse_from]->name ?? '' }}</td>
                                            <td>{{ $all_warehouse[$item->warehouse_to]->name ?? '' }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }} db</td>
                                            <td>
                                                <textarea data-id="{{ $item->item_id }}" class="form-control item_note" rows="3">{{ $item->notes }}</textarea>
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

            $('.edit_comment').click(function(){

                var pti_id = $(this).attr('data-pti_id');
                $('textarea[name=comment_'+ pti_id +']').prop('disabled', false);
                $('textarea[name=comment_'+ pti_id +']').focus();

                $(this).removeClass('d-inline-block');
                $(this).hide();
                $('.save_comment').addClass('d-inline-block');

            });

            $('.save_comment').click(function(){

                var pti_id = $(this).attr('data-pti_id');
                var pti_comment = $('textarea[name=comment_'+pti_id+']').val();

                $(this).removeClass('d-inline-block');
                $(this).hide();
                $('.edit_comment').addClass('d-inline-block');

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_pti_comment",
                    data: { pti_id: pti_id, pti_comment: pti_comment }
                }).done(function( resp ) {
                    $('textarea[name=comment_'+ pti_id +']').prop('disabled', true);
                    $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                });

            });

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
            
        });
    </script>
@endpush