@extends('layouts.app', ['activePage' => 'index', 'activeButton' => 'webshop', 'title' => 'Zemef ERP', 'navName' => 'Megrendelések' ])

@section('content')

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Webshop megrendelések</th></h3>
                                </div>
                            </div>
                        </div>
                        <!-- Alerts -->
                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="webshop_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Azonosító</th>
                                        <th>Státusz</th>
                                        <th>Ügyfél neve</th>
                                        <th>Dátum</th>
                                        <th>Összesen</th>
                                        <th>Termékek</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Azonosító</th>
                                        <th>Státusz</th>
                                        <th>Ügyfél neve</th>
                                        <th>Dátum</th>
                                        <th>Összesen</th>
                                        <th>Termékek</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->order_id }}</td>
                                            <td>
                                                <i class="{{ $order->order_status?->icon }}"></i> {{ $order->order_status?->name }}
                                            </td>
                                            <td>
                                                <a href="{{ route('customer.edit', ['customer' => $order->customer->customer_id]) }}">{{ $order->customer->name }}</a>
                                            </td>
                                            <td>{{ $order->order_date }}</td>
                                            <td>{{ number_format($order->total,0,'',' ') }} Ft</td>
                                            <td>
                                                <ul>
                                                    @foreach($order->items as $item)
                                                        <li>#{{ $item->shop_product_id }} - {{ $item->product_name }}: {{ $item->quantity }} x {{ number_format($item->price,0,'',' ') }} Ft</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="text-right">
                                                @if(Auth::user()->hasPermission('edit', App\Models\WebshopOrder::class))
                                                    <a href="{{ route('webshop.edit', ['webshop' => $order->id]) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i>
                                                    </a>
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
                                "dir": "delivery_note_images"
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
                                "dir": "delivery_note_docs"
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
                let delivery_note_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/get_delivery_note_images',
                    data: {
                        "delivery_note_id": delivery_note_id
                    },
                    success: function(response) {
                        let html = '<div class="row gallery-items">';
                        $.each(response.images, function(index, value) {
                            html += '<div class="col-md-3 mb-3 position-relative">';
                            html += '<a href="/document_storage/delivery_note_images/' + value.image_path + '" class="lightgallery-item">';
                            html += '<img src="/document_storage/delivery_note_images/' + value.image_path + '" class="img-fluid">';
                            html += '</a>';
                            html += '<i data-id="'+value.image_id+'" class="fa fa-trash position-absolute delete_picture" style="color:red;top: 5px; right: 15px; cursor: pointer;font-size:20px;"></i>';
                            html += '</div>';
                        });
                        html += '</div>';
                        $('#printmodal .modal-body').html(html);
                        $('#printmodalLabel').empty().html('Szállítólevél fájlok');
                        $('#printmodal').modal('show');
                        $(".gallery-items").lightGallery({
                            selector: '.lightgallery-item'
                        });
                        get_files(delivery_note_id);
                    }
                });
            });

            function get_files(delivery_note_id) {
                $.ajax({
                    type: "POST",
                    url: '/ajax/get_delivery_note_files',
                    data: {
                        "delivery_note_id": delivery_note_id
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
                            html += '<td><a href="/document_storage/delivery_note_docs/' + value.document_path + '">' + value.document_comment + '</a></td>';
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
                let delivery_note_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/print_delivery_note',
                    data: {
                        "delivery_note_id": delivery_note_id
                    },
                    success: function(response) {
                        let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').empty().html('Szállítólevél nyomtatási kép');
                        $('#printmodal').modal('show');
                    }
                });
            });

        });
    </script>
@endpush