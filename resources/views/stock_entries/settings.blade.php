@extends('layouts.app', ['activePage' => 'stock_entry', 'activeButton' => 'stock_entry', 'title' => 'Zemef ERP', 'navName' => 'Ékszer bevételezés beállítások'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ $stock_entry->ticket_number ?? 'Új bevételezés' }} - Tételek beállítása</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('stock_entry.edit', $stock_entry->stock_entry_id) }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        @include('alerts.errors')

                        @if($inventoryItems->isEmpty())
                            <div class="alert alert-info">
                                Nincsenek még generált inventory tételek ehhez a bevételezéshez. Előbb hozza létre a tételeket a bevételezés szerkesztésén.
                            </div>
                        @else
                            @php $item = $inventoryItems->first(); @endphp
                            <div class="settings-section">
                                <form method="post" action="{{ route('stock_entry.inventory.settings.save', $item->inventory_id) }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <input type="hidden" name="status" value="{{ $item->status }}">
                                    @php
                                        $productCategories2Value = $item->item_data['product_categories_2'] ?? null;
                                        if (is_array($productCategories2Value)) {
                                            $productCategories2Value = $productCategories2Value[0] ?? null;
                                        }
                                    @endphp

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>VZJ</label>
                                                <input type="text" name="vzj" class="form-control" value="{{ old('vzj', $item->item_data['vzj'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kategória</label>
                                                <select name="category_id" class="form-control">
                                                    @foreach($categories ?? [] as $category)
                                                        <option value="{{ $category->category_id }}" {{ ($item->item_data['category_id'] ?? '') == $category->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Beszerzési ár</label>
                                                <input type="number" step="0.01" name="beszerzesi_ar" class="form-control" value="{{ old('beszerzesi_ar', $item->item_data['beszerzesi_ar'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Eladási ár</label>
                                                <input type="number" step="0.01" name="eladasi_ar" class="form-control" value="{{ old('eladasi_ar', $item->item_data['eladasi_ar'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>ÁFA típus</label>
                                                <select name="vat_type" class="form-control">
                                                    @foreach($vat_types ?? [] as $vat)
                                                        <option value="{{ $vat->vat_type_id }}" {{ ($item->item_data['vat_type_id'] ?? '') == $vat->vat_type_id ? 'selected' : '' }}>{{ $vat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Fémjelzések</label>
                                                <select name="hallmarks[]" class="form-control selectpicker" multiple data-live-search="true">
                                                    @foreach($hallmarks ?? [] as $hallmark)
                                                        <option value="{{ $hallmark->hallmark_id }}" {{ in_array($hallmark->hallmark_id, $item->item_data['hallmarks'] ?? []) ? 'selected' : '' }}>{{ $hallmark->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Státuszok</label>
                                                <select name="statuses[]" class="form-control selectpicker" multiple data-live-search="true">
                                                    @foreach($product_statuses ?? [] as $status)
                                                        <option value="{{ $status->product_status_id }}" {{ in_array($status->product_status_id, $item->item_data['statuses'] ?? []) ? 'selected' : '' }}>{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Karat</label>
                                                <select name="karat_id" class="form-control">
                                                    <option value="">Válasszon</option>
                                                    @for($i = 14; $i <= 24; $i++)
                                                        <option value="{{ $i }}" {{ ($item->item_data['karat_id'] ?? '') == $i ? 'selected' : '' }}>{{ $i }}K</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Színek</label>
                                                <select name="colors[]" class="form-control selectpicker" multiple data-live-search="true">
                                                    @foreach($product_colors ?? [] as $color)
                                                        <option value="{{ $color->product_color_id }}" {{ in_array($color->product_color_id, $item->item_data['colors'] ?? []) ? 'selected' : '' }}>{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kategória II</label>
                                                <select name="product_categories_2" class="form-control">
                                                    <option value="">Válasszon</option>
                                                    <option value="1" {{ $productCategories2Value == 1 ? 'selected' : '' }}>Gyerek</option>
                                                    <option value="2" {{ $productCategories2Value == 2 ? 'selected' : '' }}>Női</option>
                                                    <option value="3" {{ $productCategories2Value == 3 ? 'selected' : '' }}>Férfi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Méret</label>
                                                <input type="text" name="size" class="form-control" value="{{ old('size', $item->item_data['size'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Címkeválasztás</label>
                                                <select id="label_type_{{ $item->inventory_id }}" name="label_type" class="form-control label-type" data-inventory-id="{{ $item->inventory_id }}">
                                                    <option value="1" {{ ($item->item_data['label_type'] ?? 1) == 1 ? 'selected' : '' }}>1-es típus</option>
                                                    <option value="2" {{ ($item->item_data['label_type'] ?? 1) == 2 ? 'selected' : '' }}>2-es típus</option>
                                                    <option value="3" {{ ($item->item_data['label_type'] ?? 1) == 3 ? 'selected' : '' }}>3-as típus</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Mennyiség</label>
                                                <input type="number" class="form-control" value="{{ $item->quantity }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Súly (g)</label>
                                                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight', $item->weight) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Egységár</label>
                                                <input type="number" step="1" name="oneprice" class="form-control" value="{{ old('oneprice', $item->oneprice) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Ár</label>
                                                <input type="number" step="1" name="price" class="form-control" value="{{ old('price', $item->price) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Gyári szám</label>
                                                <input type="text" name="factory_number" class="form-control" value="{{ old('factory_number', $item->factory_number) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Leírás</label>
                                                <textarea name="description" class="form-control" rows="2">{{ old('description', $item->description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Raktári megjegyzés</label>
                                                <textarea name="inv_comment" class="form-control" rows="2">{{ old('inv_comment', $item->inv_comment) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Ügyfél megjegyzés</label>
                                                <textarea name="comment" class="form-control" rows="2">{{ old('comment', $item->comment) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" id="investigation_{{ $item->inventory_id }}" name="investigation" value="1" class="form-check-input" {{ $item->investigation ? 'checked' : '' }}>
                                                <label class="form-check-label" for="investigation_{{ $item->inventory_id }}">Vizsgálat</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" id="repair_{{ $item->inventory_id }}" name="repair" value="1" class="form-check-input" {{ $item->repair ? 'checked' : '' }}>
                                                <label class="form-check-label" for="repair_{{ $item->inventory_id }}">Javítás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" id="hallmark_{{ $item->inventory_id }}" name="hallmark" value="1" class="form-check-input" {{ $item->hallmark ? 'checked' : '' }}>
                                                <label class="form-check-label" for="hallmark_{{ $item->inventory_id }}">Fémjelzés</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" id="is_fake_{{ $item->inventory_id }}" name="is_fake" value="1" class="form-check-input" {{ $item->is_fake ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_fake_{{ $item->inventory_id }}">Hamís</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-end mb-3">
                                        <div class="col-md-6">
                                            <a id="print_label_btn_{{ $item->inventory_id }}" target="_blank" class="btn btn-primary mt-4" data-product-id="{{ $item->product_id }}" href="{{ route('product.printLabel', ['product_id' => $item->product_id, 'label_type' => 1]) }}">Cimke nyomtatása</a>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="submit" class="btn btn-success mt-4">Mentés</button>
                                        </div>
                                    </div>
                                </form>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Képkezelés</h5>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Képfeltöltés</label>
                                            <input type="file" class="form-control inventory_images" multiple accept="image/*" data-inventory-id="{{ $item->inventory_id }}">
                                            <input type="hidden" class="inventory_id_input" value="{{ $item->inventory_id }}">
                                        </div>
                                    </div>
                                </div>
                                <div id="inventory_gallery_{{ $item->inventory_id }}" class="row"></div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function refreshInventoryGallery(inventoryId) {
        if (!inventoryId) return;
        $.post('/load_inventory_gallery', { inventory_id: inventoryId, _token: '{{ csrf_token() }}' }, function(html) {
            $('#inventory_gallery_' + inventoryId).html(html);
        }).fail(function() {
            console.error('Hiba a galéria betöltésekor: ' + inventoryId);
        });
    }

    function uploadInventoryImages(inventoryId, files) {
        if (!inventoryId || !files.length) return;

        let formData = new FormData();
        formData.append('inventory_id', inventoryId);
        formData.append('_token', '{{ csrf_token() }}');

        $.each(files, function(index, file) {
            formData.append('inventory_images[]', file);
        });

        $.ajax({
            url: '/upload_inventory_image',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                refreshInventoryGallery(inventoryId);
            },
            error: function() {
                alert('Hiba a képfeltöltés során.');
            }
        });
    }

    $(document).ready(function() {
        @foreach($inventoryItems as $item)
            refreshInventoryGallery({{ $item->inventory_id }});
        @endforeach

        $(document).on('change', '.inventory_images', function() {
            let inventoryId = $(this).data('inventoryId');
            uploadInventoryImages(inventoryId, this.files);
            $(this).val('');
        });

        $(document).on('change', '.label-type', function() {
            let inventoryId = $(this).attr('id').replace('label_type_', '');
            let productId = $('#print_label_btn_' + inventoryId).data('product-id');
            let type = $(this).val();
            if (productId) {
                $('#print_label_btn_' + inventoryId).attr('href', '/product/printLabel?product_id=' + productId + '&label_type=' + type);
            }
        });

        $('.label-type').trigger('change');

        // Initialize selectpickers
        $('.selectpicker').selectpicker();
    });
</script>
@endpush
