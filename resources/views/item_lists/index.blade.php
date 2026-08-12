@extends('layouts.app', ['activePage' => 'item-list', 'activeButton' => 'stock_entry', 'title' => 'Zemef ERP', 'navName' => 'Ékszer vásárlások' ])
<style>
    .modal-content {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .modal-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            padding: 12px 20px;
        }
    #modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 12px 20px;
}
</style>
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Kényszerlista átvételek</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\ItemList::class))
                                
                                    <div class="col-4 text-right">
                                         <form id="importForm" method="POST" action="{{ route('item_list.import') }}" enctype="multipart/form-data" class="d-inline">
                                            <div class="d-inline-flex align-items-center">
                                                @csrf
                                                <select class="form-control mr-2" name="import_source" id="importSourceSelect" style="width: 140px;">
                                                    <option value="pdf" selected>PDF (Gemini)</option>
                                                    <option value="json">JSON</option>
                                                </select>
                                                <input class="form-control" type="file" name="file" id="fileInput">
                                                <button type="submit" class="btn btn-sm btn-primary" id="uploadBtn">{{ __('pages.itemlist.import_btn') }}</button>
                                        </div>
                                        </form>   
                                    </div>
                                @endif
                                
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="stock_entries_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Átadás napja</th>
                                        <th>Bizonylat száma 1</th>
                                        <th>Bizonylat száma 2</th>
                                        <th>Kiemelt közvetítő</th>
                                        <th>Listázás azonosító</th>
                                        <th>Rögzítő</th>
                                        <th>Átvétel helye</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sorszám</th>
                                        <th>Átadás napja</th>
                                        <th>Bizonylat száma 1</th>
                                        <th>Bizonylat száma 2</th>
                                        <th>Kiemelt közvetítő</th>
                                        <th>Listázás azonosító</th>
                                        <th>Rögzítő</th>
                                        <th>Átvétel helye</th>
                                        <th>Státusz</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($items as $item)
                                        @php
                                            $header = $item->json['header'] ?? [];
                                            $statusLabel = \App\Enums\ItemListStatus::tryFrom((int) $item->status)?->label() ?? (string) $item->status;
                                        @endphp
                                        <tr class="@if($item->status == 1) table-hover @else table-warning @endif">
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $header['atadas_napja'] ?? '-' }}</td>
                                            <td>{{ $header['bizonylat_szama_1'] ?? '-' }}</td>
                                            <td>{{ $header['bizonylat_szama_2'] ?? '-' }}</td>
                                            <td>{{ $header['kiemelt_kozvetito'] ?? '-' }}</td>
                                            <td>{{ $header['listazas_azonosito'] ?? '-' }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td>{{ $item->business->name ?? '-' }}</td>
                                            <td>{{ $statusLabel }}</td>
                                            <td class="text-right">
                                                @if($item->status == 0)
                                                    @if(Auth::user()->hasPermission('edit', App\Models\ItemList::class))
                                                        <a href="{{ route('item_list.subindex', $item->id) }}" class="fa fa-pencil"></a>
                                                        <a href="#" onclick="closeItemList({{ $item->id }});" class="fa fa-check text-info"></a>
                                                    @endif
                                                @else
                                                    @if(Auth::user()->hasPermission('edit', App\Models\ItemList::class))
                                                        <a href="{{ route('item_list.subindex', $item->id) }}" class="fa fa-eye text-success"></a>
                                                        <a href="{{ route('item-list.print-receipt', $item->id) }}" 
                                                            class="fa fa-print text-primary" 
                                                            target="_blank"
                                                            title="Átvételi jegyzőkönyv nyomtatás"></a>
                                                    @endif
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

@push('modals')
    {{-- multi route modal --}}
    @include('item_lists.modals.item_list_status')
@endpush


@push('js')
    <script type="text/javascript">
    
        $(document).ready(function() {

            var table = $('#datatables').DataTable();

            $('body').on('click', '.applyBtn', function(e) {
                var hlpr = $('input[placeholder=Sorszám]').val();
                $('input[placeholder=Sorszám]').val(Math.random());
                $('input[placeholder=Sorszám]').trigger('change');
                $('input[placeholder=Sorszám]').val(hlpr);
                $('input[placeholder=Sorszám]').trigger('change');
            });

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });

            function refreshUploadMode() {
                var selectedSource = $('#importSourceSelect').val();
                if (selectedSource === 'json') {
                    $('#fileInput').attr('accept', '.json,application/json');
                    $('#uploadBtn').text('JSON feltoltes');
                } else {
                    $('#fileInput').attr('accept', '.pdf,application/pdf');
                    $('#uploadBtn').text('PDF feltoltes');
                }
                $('#fileInput').val('');
            }

            $('#importSourceSelect').on('change', refreshUploadMode);
            refreshUploadMode();
            
            $('#itemlistStatusModalYes').click(function() {
                // Close the modal
                $('#itemlistStatusModal').modal('hide');
                const item_list_id = $('#modal_itemlist_id').val();

                $.ajax({
                    url: "{{ route('item_list.status') }}", // Use the dynamically generated URL
                    method: "POST",
                    delay: 250,
                    data: {
                        item_list_id: item_list_id,
                        status: 1,
                    }
                })
                .done(function( msg ) {
                    location.reload();
                })
            });
        });

        function closeItemList(id){
            $('#itemlistStatusModal').modal('show');
            $('#modal_itemlist_id').val(id);
        }
    </script>
@endpush