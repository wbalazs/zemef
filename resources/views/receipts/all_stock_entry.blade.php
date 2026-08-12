@extends('layouts.app', ['activePage' => 'all_stock_entry', 'activeButton' => 'all_stock_entry', 'title' => 'Zemef ERP', 'navName' => 'Összes ékszer vásárlás' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Összes ékszer vásárlás</th></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="all_stock_entries_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Üzlet</th>
                                        <th>Összeg</th>
                                        <th>Rögzítő</th>
                                        <th>Ügyfél</th>
                                        <th>Létrehozás dátuma</th>
                                        <th>Kapcsolódó bizonylat</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($stock_entries as $stock_entry)
                                        <tr>
                                            <td>{{ $stock_entry->ticket_number }}</td>
                                            <td>{{ optional($stock_entry->business)->name }}</td>
                                            <td>{{ number_format($stock_entry->stock_entry_items->map(function($item) use ($stock_entry) {
                                                return $item->price*$stock_entry->currency_value;
                                            })->sum(), 0, ',', ' ') }} {{ optional($stock_entry->currency)->sign }}</td>
                                            <td>{{ optional($stock_entry->employee)->lastname . ' ' . optional($stock_entry->employee)->firstname }}</td>
                                            <td>{{ optional($stock_entry->customer)->name }}</td>
                                            <td>{{ $stock_entry->created_at }}</td>
                                            <td>{{ $stock_entry->relatedticketnumber }}</td>
                                            <td>
                                                <a data-id="{{ $stock_entry->stock_entry_id }}" class="btn btn-sm btn-primary print"><i class="fa fa-print"></i> Nyomtatás</a>
                                                <!--<a data-id="{{ $stock_entry->stock_entry_id }}" class="btn btn-sm btn-warning get_pictures"><i class="fa fa-files-o"></i> Fájlok ({{ $stock_entry->stock_entry_images->count() }})</a>-->
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


            $(document).on('click', '.get_pictures', function(e) {
                let stock_entry_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/get_stock_entry_images',
                    data: {
                        "stock_entry_id": stock_entry_id
                    },
                    success: function(response) {
                        let html = '<div class="row gallery-items">';
                        $.each(response.images, function(index, value) {
                            html += '<div class="col-md-3 mb-3 position-relative">';
                            html += '<a href="/document_storage/stock_entry_images/' + value.image_path + '" class="lightgallery-item">';
                            html += '<img src="/document_storage/stock_entry_images/' + value.image_path + '" class="img-fluid">';
                            html += '</a>';
                            html += '<i data-id="'+value.image_id+'" class="fa fa-trash position-absolute delete_picture" style="color:red;top: 5px; right: 15px; cursor: pointer;font-size:20px;"></i>';
                            html += '</div>';
                        });
                        html += '</div>';
                        $('#printmodal .modal-body').html(html);
                        $('#printmodalLabel').empty().html('Ékszervásárlás fájlok');
                        $('#printmodal').modal('show');
                        $(".gallery-items").lightGallery({
                            selector: '.lightgallery-item'
                        });
                    }
                });
            });


            $('body').on('click', '.print', function(e) {
                let stock_entry_id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: '/ajax/print_stock_entry',
                    data: {
                        "stock_entry_id": stock_entry_id
                    },
                    success: function(response) {
                        let iframeHTML = '<iframe src="'+response+'" width="100%" height="680" frameborder="0" style="border:0"></iframe>';
                        $('#printmodal .modal-body').html(iframeHTML);
                        $('#printmodalLabel').empty().html('Vételi jegy nyomtatási kép');
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