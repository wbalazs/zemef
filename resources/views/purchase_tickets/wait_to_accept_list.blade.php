@extends('layouts.app', ['activePage' => 'wait_to_accept_list', 'activeButton' => 'wait_to_accept_list', 'title' => 'Zemef ERP', 'navName' => 'Átvételre váró tételek' ])

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
                                    <h3 class="mb-0"><th>Átvételre váró tételek</th></h3>
                                    <a style="display: none;" class="btn btn-sm btn-success accept_selected"><i class="fa fa-transfer"></i> Kijelöltek átvétele</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="wait_to_accept_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Bizonylat típusa</th>
                                        <th>Bizonylatszám</th>
                                        <th>Kapcs. bizonylat</th>
                                        <th>Fuvarcimke</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Megnevezés</th>
                                        <th>Mennyiség</th>
                                        <th>Ügyfél</th>
                                        <th>Művelet</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Kijelölés</th>
                                        <th>Bizonylat típusa</th>
                                        <th>Bizonylatszám</th>
                                        <th>Kapcs. bizonylat</th>
                                        <th>Fuvarcimke</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Megnevezés</th>
                                        <th>Mennyiség</th>
                                        <th>Ügyfél</th>
                                        <th>Művelet</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    {{-- Server-side DataTables tölti be AJAX-ból --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="waitToAcceptDetailsModal" tabindex="-1" aria-labelledby="waitToAcceptDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="waitToAcceptDetailsModalLabel">Bizonylat adatlap</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="waitToAcceptDetailsContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezár</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">

        function handlePasswordInput(inputElement) {
            inputElement.type = 'password';
        }
        
        $(document).ready(function() {

            if ($.fn.dataTable.isDataTable('#datatables')) {
                $('#datatables').DataTable().destroy();
            }

            let table2 = $('#datatables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/ajax/wait_to_accept_list_data',
                    type: 'GET'
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                    { data: 'type_text', name: 'type_text' },
                    { data: 'document_number', name: 'document_number' },
                    { data: 'foreign_receipt_name', name: 'foreign_receipt_name' },
                    { data: 'shipping_label', name: 'shipping_label' },
                    { data: 'warehouse_from_name', name: 'warehouse_from_name' },
                    { data: 'warehouse_to_name', name: 'warehouse_to_name' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'quantity_text', name: 'quantity', searchable: false },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                initComplete: function() {
                    let api = this.api();
                    // Kért oszlopok: Bizonylat típusa, Bizonylatszám, Kapcs. bizonylat,
                    // Fuvarcimke, Átadó, Fogadó, Megnevezés, Ügyfél
                    let filterColumns = [1, 2, 3, 4, 5, 6, 7, 9];

                    filterColumns.forEach(function(colIdx) {
                        let headerText = $(api.column(colIdx).header()).text().trim();
                        let $cell = $(api.column(colIdx).footer()).empty();

                        // A bizonylat típusához legördülő lista kényelmesebb.
                        if (colIdx === 1) {
                            let $select = $('<select class="form-control form-control-sm"><option value="">Mind</option></select>');
                            $select.append('<option value="Szállítólevél">Szállítólevél</option>');
                            $select.append('<option value="Raktárközi">Raktárközi</option>');

                            $select.appendTo($cell).on('change', function() {
                                api.column(colIdx).search(this.value).draw();
                            });
                        } else {
                            $('<input type="text" class="form-control form-control-sm" placeholder="Szűrés...">')
                                .attr('aria-label', headerText + ' szűrés')
                                .appendTo($cell)
                                .on('keyup change clear', function() {
                                    if (api.column(colIdx).search() !== this.value) {
                                        api.column(colIdx).search(this.value).draw();
                                    }
                                });
                        }
                    });
                }
            });

            $('#datatables tbody').on('change', '.note-checkbox', updateTransferButtonVisibility);
            table2.on('draw', updateTransferButtonVisibility);
                

            // Elrejtjük a megerősítés gombot kezdetben
            $('#confirmTransfer').hide();

            function updateTransferButtonVisibility() {
                let hasCheckedCheckbox = $('.note-checkbox:checked').length > 0;
                if (hasCheckedCheckbox) {
                    $('.accept_selected').show();
                } else {
                    $('.accept_selected').hide();
                }
            }

            $(document).on('click', '.accept_selected', function() {
                let itemsData = [];

                swal({
                    title: 'Aláíró kód szükséges a tételek átvételéhez!',
                    html: '<p><input type="text" class="form-control" id="sign_code_request" oninput="handlePasswordInput(this)" autocomplete="one-time-code"></p>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Mégsem',
                    buttonsStyling: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        $('.note-checkbox:checked').each(function() {
                            let item = {
                                type: $(this).data('type'),
                                id: $(this).data('id'),
                                item_id: $(this).data('item_id')
                            };

                            itemsData.push(item);
                        });

                        $.ajax({
                            method: "POST",
                            url: "/ajax/accept_items",
                            data: { sign_code : $('#sign_code_request').val() ,items: itemsData }
                        }).done(function(resp) {
                            if(!resp.success){
                                $.notify({ icon: "error", message: resp.message }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                                return;
                            }else{
                                $.notify({ icon: "done", message: resp.message }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                                $('.accept_selected').hide();
                                table2.ajax.reload(null, false);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.open-transfer-details', function() {
                let type = $(this).data('type');
                let id = $(this).data('id');

                $('#waitToAcceptDetailsContent').html('<div class="text-muted">Betöltés...</div>');
                $('#waitToAcceptDetailsModal').modal('show');

                $.ajax({
                    method: 'GET',
                    url: '/ajax/wait_to_accept_item_details',
                    data: { type: type, id: id }
                }).done(function(resp) {
                    if (resp.success) {
                        $('#waitToAcceptDetailsContent').html(resp.html);
                    } else {
                        $('#waitToAcceptDetailsContent').html('<div class="text-danger">Nem sikerült betölteni az adatlapot.</div>');
                    }
                }).fail(function(xhr) {
                    let message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Hiba történt az adatlap betöltése közben.';
                    $('#waitToAcceptDetailsContent').html('<div class="text-danger">' + message + '</div>');
                });
            });

        });
    </script>
@endpush