@extends('layouts.app', ['activePage' => 'own_list', 'activeButton' => 'own_list', 'title' => 'Zemef ERP', 'navName' => 'Nálam lévő tételek' ])

@section('content')
<style>
    .selectable-card.selected {
        padding:15px;
        border: 2px solid #007bff;
        box-shadow: 0px 0px 4px blue;
    }
</style>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-12" style="display:grid;">
                                    <h3 class="mb-0"><th>Nálam lévő tételek</th></h3>
                                    <a style="display: none;" class="btn btn-sm btn-success accept_selected"><i class="fa fa-transfer"></i> Kijelöltek átvétele</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="own_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Művelet</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Termék</th>
                                        <th>Mennyiség</th>
                                        <th>Megjegyzés</th>
                                        <th>Jav. díj</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Művelet</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Termék</th>
                                        <th>Mennyiség</th>
                                        <th>Megjegyzés</th>
                                        <th>Jav. díj</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($accept_list as $item)
                                        <tr>
                                            <td>{{ $item->type == 'delivery_note' ? $item->delivery_note_number : $item->warehouse_transfer_number }}</td>
                                            <td>
                                                @if($item->accepted_at === null)
                                                    <a data-id="{{ $item->item_id }}" class="btn btn-primary accept">Átvétel</a>
                                                    <a style="display:none;" data-id="{{ $item->item_id }}" class="btn btn-success return">Visszaadás</a>
                                                @elseif($item->accepted_at !== null && $item->back_to_storage_at === null)
                                                    <a data-id="{{ $item->item_id }}" class="btn btn-success return">Visszaadás</a>
                                                @endif
                                            </td>                                            
                                            <td>{{ $all_warehouse[$item->warehouse_from]->name ?? '' }}</td>
                                            <td>{{ $all_warehouse[$item->warehouse_to]->name ?? '' }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }} db</td>
                                            <td>
                                                <textarea data-id="{{ $item->item_id }}" class="form-control item_note" rows="3">{{ $item->notes }}</textarea>
                                            </td>
                                            <td>
                                                <input data-id="{{ $item->item_id }}" type="number" value="{{ $item->repair_price }}" class="form-control repair_price">
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
            
            let table2 = $('#datatables').DataTable();

            $(document).on('focusout', '.item_note', function() {
                let textarea = $(this);
                    if (textarea.length) {
                        let itemId = textarea.data('id');
                        let newNote = textarea.val();
                        saveNote(itemId, newNote);
                    }
            });


            $(document).on('change', '.repair_price', function() {
                saveRepairPrice($(this).data('id'), $(this).val());
            });

            $(document).on('click', '.accept', function() {
                let id = $(this).data('id');
                let that = this;
                $.ajax({
                    url: '/ajax/accept_item',
                    method: 'POST',
                    data: {
                        transfer_items_id: id
                    },
                    success: function(response) {
                        if (response.data && response.data.success) {
                            $(that).hide();
                            $(that).siblings('.return').show();
                            $.notify({ icon: "done", message: "A terméket sikeresen átvette." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        } else if (response.data && response.data.error) {
                            $.notify({ icon: "done", message: "Hiba:"+response.data.error }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                    },
                    error: function() {
                        $.notify({ icon: "done", message: "Hiba történt az átvétel közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    }
                });
            });


            $(document).on('click', '.return', function() {
                let id = $(this).data('id');
                let that = this;
                let row = table2.row($(that).closest('tr'));
                $.ajax({
                    url: '/ajax/return_item',
                    method: 'POST',
                    data: {
                        transfer_items_id: id
                    },
                    success: function(response) {
                        if (response.data && response.data.success) {
                            row.remove().draw();
                            $.notify({ icon: "done", message: "A terméket sikeresen visszaadta!." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        } else if (response.data && response.data.error) {
                            $.notify({ icon: "done", message: "Hiba:"+response.data.error }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                    },
                    error: function() {
                        $.notify({ icon: "done", message: "Hiba történt az átadás közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    }
                });
            });

            function saveNote(id, note) {
                $.ajax({
                    url: '/ajax/save_pti_comment',
                    method: 'POST',
                    data: {
                        transfer_items_id: id,
                        transfer_items_note: note
                    },
                    success: function(response) {
                        if (response.data && response.data.success) {
                            $.notify({ icon: "done", message: "A megjegyzés sikeresen módosítva." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        } else if (response.data && response.data.error) {
                            $.notify({ icon: "done", message: "Hiba:"+response.data.error }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                    },
                    error: function() {
                        $.notify({ icon: "done", message: "Hiba történt a mentés közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    }
                });
            }

            function saveRepairPrice(id, price) {
                $.ajax({
                    url: '/ajax/save_pti_repair_price',
                    method: 'POST',
                    data: {
                        transfer_items_id: id,
                        repair_price: price
                    },
                    success: function(response) {
                        if (response.data && response.data.success) {
                            $.notify({ icon: "done", message: "A javítási ár sikeresen módosítva." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        } else if (response.data && response.data.error) {
                            $.notify({ icon: "done", message: "Hiba:"+response.data.error }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                        }
                    },
                    error: function() {
                        $.notify({ icon: "done", message: "Hiba történt a mentés közben!" }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    }
                });
            }

        });
    </script>
@endpush