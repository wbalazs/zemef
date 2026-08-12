@extends('layouts.app', ['activePage' => 'all_warehouse_transfer', 'activeButton' => 'all_warehouse_transfer', 'title' => 'Zemef ERP', 'navName' => 'Összes raktárközi bizonylat' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Raktárközi bizonylatok</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="all_warehouse_transfer_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Kiállító</th>
                                        <th>Üzlet</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Kelt</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Bizonylatszám</th>
                                        <th>Kiállító</th>
                                        <th>Üzlet</th>
                                        <th>Átadó</th>
                                        <th>Fogadó</th>
                                        <th>Kelt</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($warehouse_transfers as $item)
                                        <tr>
                                            <td>{{ $item->warehouse_transfer_number }}</td>
                                            <td>{{ $item->employee->lastname . ' ' . $item->employee->firstname }}</td>
                                            <td>{{ $item->business->name }}</td>
                                            <td>{{ $item->warehouse_from_name }}</td>
                                            <td>{{ $item->warehouse_to_name }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>
                                                <a data-id="{{ $item->warehouse_transfer_id }}" class="btn btn-sm btn-primary print"><i class="fa fa-print"></i> Nyomtatás</a>
                                                <a data-id="{{ $item->warehouse_transfer_id }}" class="btn btn-sm btn-warning get_pictures"><i class="fa fa-files-o"></i> Fájlok ({{ $item->warehouse_transfer_images->count()+$item->warehouse_transfer_documents->count() }})</a>
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
@include('receipts.print_modal')
@endsection

@push('js')
    <script type="text/javascript">

        $(document).ready(function() {

            $(document).on('click', '.delete_picture', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let image_id = $(this).data('id');
                let currentImage = $(this).closest('.col-md-3');

                swal({
                    title: "Biztos benne?",
                    text: "Ezt a műveletet nem lehet visszavonni!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Igen",
                    cancelButtonText: "Nem",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            url: "/ajax/delete_image",
                            data: {
                                "image_id": image_id,
                                "dir": "warehouse_transfer_images"
                            },
                            success: function(response) {
                                swal("Törölve!", "A kép sikeresen törölve lett.", "success");
                                currentImage.remove();
                            },
                            error: function(error) {
                                swal("Hiba!", "Nem sikerült törölni a képet. Próbálja meg újra később.", "error");
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.delete-file', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let document_id = $(this).data('id');
                let currentRow = $(this).closest('tr');

                swal({
                    title: "Biztos benne?",
                    text: "Ezt a műveletet nem lehet visszavonni!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Igen",
                    cancelButtonText: "Nem",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            url: "/ajax/delete_file",
                            data: {
                                "document_id": document_id,
                                "dir": "warehouse_transfer_docs"
                            },
                            success: function(response) {
                                swal("Törölve!", "A fájl sikeresen törölve lett.", "success");
                                currentRow.remove();
                            },
                            error: function(error) {
                                swal("Hiba!", "Nem sikerült törölni a fájlt. Próbálja meg újra később.", "error");
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.get_pictures', function(e) {
                let warehouse_transfer_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/get_warehouse_transfer_images',
                    data: {
                        "warehouse_transfer_id": warehouse_transfer_id
                    },
                    success: function(response) {
                        let html = '<div class="row gallery-items">';
                        $.each(response.images, function(index, value) {
                            html += '<div class="col-md-3 position-relative">';
                            html += '<a href="/document_storage/warehouse_transfer_images/' + value.image_path + '" class="lightgallery-item">';
                            html += '<img src="/document_storage/warehouse_transfer_images/' + value.image_path + '" class="img-fluid">';
                            html += '</a>';
                            html += '<i data-id="'+value.image_id+'" class="fa fa-trash position-absolute delete_picture" style="color:red;top: 5px; right: 15px; cursor: pointer;font-size:20px;"></i>';
                            html += '</div>';
                        });
                        html += '</div>';
                        $('#printmodal .modal-body').html(html);
                        $('#printmodalLabel').empty().html('Raktárközi bizonylat fájlok');
                        $('#printmodal').modal('show');
                        $(".gallery-items").lightGallery({
                            selector: '.lightgallery-item'
                        });
                        get_files(warehouse_transfer_id);
                    }
                });
            });

            function get_files(warehouse_transfer_id) {
                $.ajax({
                    type: "POST",
                    url: '/ajax/get_warehouse_transfer_files',
                    data: {
                        "warehouse_transfer_id": warehouse_transfer_id
                    },
                    success: function(response) {
                        if(response.files.length == 0){
                            return;
                        }
                        let html = '<hr><table class="table table-striped mt-4">';
                        html += '<thead><tr><th>Fájl név</th><th>Feltöltés ideje</th><th>Műveletek</th></tr></thead>';
                        html += '<tbody>';
                        
                        $.each(response.files, function(index, value) {
                            html += '<tr>';
                            html += '<td><a href="/document_storage/warehouse_transfer_docs/' + value.document_path + '">' + value.document_comment + '</a></td>';
                            html += '<td>' + value.created_at + '</td>';
                            html += '<td><button class="btn btn-sm btn-danger delete-file" data-id="' + value.document_id + '">Törlés</button></td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table>';
                        
                        $('#printmodal .modal-body').append(html);
                    }
                });
            }




            $('body').on('click', '.print', function(e) {
                let warehouse_transfer_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/print_warehouse_transfer',
                    data: {
                        "warehouse_transfer_id": warehouse_transfer_id
                    },
                    success: function(response) {
                        let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').empty().html('Raktárközi bizonylat nyomtatási kép');
                        $('#printmodal').modal('show');
                    }
                });
            });


            let table = $('#datatables').DataTable();

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