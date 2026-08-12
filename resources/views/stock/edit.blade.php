@extends('layouts.app', ['activePage' => 'actual_stock', 'activeButton' => 'actual_stock', 'title' => 'Zemef ERP', 'navName' => 'Készlet szerkesztése'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">PRD-{{ $inventory->inventory_id ?? 'Új tétel' }} szerkesztése</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('actual_stock') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('alerts.success')
                        @include('alerts.errors')

                        <ul class="nav nav-pills mb-4" id="inventory-edit-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="base-data-tab" data-toggle="pill" href="#base-data" role="tab">Alap adatok</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="images-tab" data-toggle="pill" href="#images" role="tab">Képek</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="inventory-edit-tabs-content">
                            <div class="tab-pane fade show active" id="base-data" role="tabpanel">
                                <form method="post" action="{{ route('actual_stock.update', $inventory->inventory_id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Termék neve</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $inventory->name) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kód</label>
                                                <input type="text" class="form-control" value="{{ $inventory->code }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>SKU</label>
                                                <input type="text" class="form-control" value="{{ $inventory->sku }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Raktár</label>
                                                <select name="storage_id" class="form-control" disabled>
                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->warehouse_id }}" {{ $inventory->storage_id == $warehouse->warehouse_id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Státusz</label>
                                                <select name="status" class="form-control">
                                                    @foreach(\App\Http\Controllers\StockController::STATUSES as $key => $value)
                                                        <option value="{{ $key }}" {{ $inventory->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mennyiség</label>
                                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $inventory->quantity) }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Súly (g)</label>
                                                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight', $inventory->weight) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Beszerzési ár (db)</label>
                                                <input type="number" step="1" name="oneprice" class="form-control" value="{{ old('oneprice', $inventory->oneprice) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Eladási ár (db)</label>
                                                <input type="number" step="1" name="price" class="form-control" value="{{ old('price', $inventory->price) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Gyári szám</label>
                                                <input type="text" name="factory_number" class="form-control" value="{{ old('factory_number', $inventory->factory_number) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Leírás</label>
                                                <textarea name="description" class="form-control" rows="2">{{ old('description', $inventory->description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h6 class="heading-small text-muted mb-3">Tétel adatok (item_data)</h6>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Szállító (VZJ)</label>
                                                @php
                                                    $selectedCustomerMarkingId = old('customer_marking_id', $itemData['customer_marking_id'] ?? '');
                                                @endphp
                                                <select id="vzj" name="customer_marking_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($customer_markings as $customer_marking)
                                                        <option
                                                            value="{{ $customer_marking->id }}"
                                                            data-price="{{ optional($customer_marking->productStatus)->price }}"
                                                            data-vat-type-id="{{ optional($customer_marking->productStatus)->vat_type_id }}"
                                                            data-product-status-id="{{ optional($customer_marking->productStatus)->product_status_id }}"
                                                            {{ (string)$selectedCustomerMarkingId === (string)$customer_marking->id ? 'selected' : '' }}
                                                        >
                                                            {{ $customer_marking->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kategória</label>
                                                <select name="category_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->category_id }}" {{ (string)old('category_id', $itemData['category_id'] ?? '') === (string)$category->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Beszerzési ár</label>
                                                <input type="number" step="1" name="beszerzesi_ar" class="form-control" value="{{ old('beszerzesi_ar', $itemData['beszerzesi_ar'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Eladási ár</label>
                                                <input type="number" step="1" name="eladasi_ar" class="form-control" value="{{ old('eladasi_ar', $itemData['eladasi_ar'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Áfa típus</label>
                                                <select name="vat_type_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($vat_types as $vatType)
                                                        <option value="{{ $vatType->vat_type_id }}" {{ (string)old('vat_type_id', $itemData['vat_type_id'] ?? '') === (string)$vatType->vat_type_id ? 'selected' : '' }}>{{ $vatType->name }} - {{ $vatType->value }}%</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Karát</label>
                                                <select name="karat_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($karats as $karat)
                                                        <option value="{{ $karat->karat_id }}" {{ (string)old('karat_id', $itemData['karat_id'] ?? '') === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kategória II</label>
                                                <select name="category2_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @foreach($category2s as $category2)
                                                        <option value="{{ $category2->category2_id }}" {{ (string)old('category2_id', $itemData['category2_id'] ?? '') === (string)$category2->category2_id ? 'selected' : '' }}>{{ $category2->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Méret</label>
                                                <input type="text" name="size" class="form-control" value="{{ old('size', $itemData['size'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Állapot</label>
                                                @php
                                                    $rawSelectedStatus = old('statuses.0', old('statuses', $itemData['statuses'][0] ?? ($itemData['statuses'] ?? '')));
                                                    $selectedStatusId = is_array($rawSelectedStatus) ? ($rawSelectedStatus[0] ?? '') : $rawSelectedStatus;
                                                @endphp
                                                <select name="statuses[]" class="form-control">
                                                    @foreach($product_statuses as $productStatus)
                                                        <option value="{{ $productStatus->product_status_id }}" {{ (string)$selectedStatusId === (string)$productStatus->product_status_id ? 'selected' : '' }}>{{ $productStatus->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Színek</label>
                                                <select name="colors[]" class="form-control" multiple>
                                                    @foreach($product_colors as $productColor)
                                                        <option value="{{ $productColor->product_color_id }}" {{ in_array($productColor->product_color_id, old('colors', $itemData['colors'] ?? [])) ? 'selected' : '' }}>{{ $productColor->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mt-4 pt-2">
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" class="custom-control-input" id="hallmarks" name="hallmarks" value="1" {{ old('hallmarks', $itemData['hallmarks'] ?? 0) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="hallmarks">Fémjelzés</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Termék típus</label>
                                                @php
                                                    $selectedTermekTipusId = old('termek_tipus_id', $itemData['termek_tipus_id'] ?? ($inventory->termek_tipus_id ?? ''));
                                                @endphp
                                                <select name="termek_tipus_id" class="form-control">
                                                    <option value="">- válasszon -</option>
                                                    @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $selectedTermekTipusId])
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Címke típus</label>
                                                <div class="d-flex align-items-center" style="gap:6px;">
                                                    <select id="label_type" name="label_type" class="form-control">
                                                        <option value="">- válasszon -</option>
                                                        <option value="1" {{ (string)old('label_type', $itemData['label_type'] ?? '') === '1' ? 'selected' : '' }}>1-es típus</option>
                                                        <option value="2" {{ (string)old('label_type', $itemData['label_type'] ?? '') === '2' ? 'selected' : '' }}>2-es típus</option>
                                                        <option value="3" {{ (string)old('label_type', $itemData['label_type'] ?? '') === '3' ? 'selected' : '' }}>3-as típus</option>
                                                    </select>
                                                    <button type="button" class="btn btn-sm btn-info item-label-print-btn" data-inventory-id="{{ $inventory->inventory_id }}" title="Címke nyomtatás">
                                                        <i class="fa fa-print"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Megjegyzés (ügyfélnek)</label>
                                                <textarea name="comment" class="form-control" rows="2">{{ old('comment', $inventory->comment) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Raktári megjegyzés</label>
                                                <textarea name="inv_comment" class="form-control" rows="2">{{ old('inv_comment', $inventory->inv_comment) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label class="d-block mb-2">Egyéb jelzők</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" class="custom-control-input" id="investigation" name="investigation" value="1" {{ $inventory->investigation ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="investigation">Vizsgálat megtörtént</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" class="custom-control-input" id="repair" name="repair" value="1" {{ $inventory->repair ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="repair">Javítás alatt</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" class="custom-control-input" id="hallmark" name="hallmark" value="1" {{ $inventory->hallmark ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="hallmark">Fémjelzés szükséges</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" class="custom-control-input" id="is_fake" name="is_fake" value="1" {{ $inventory->is_fake ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_fake">Hamisítvány</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-success">Mentés</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="images" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Képfeltöltés</label>
                                            <input type="file" id="inventory_images" class="form-control" multiple accept="image/*">
                                            <input type="hidden" id="inventory_id" value="{{ $inventory->inventory_id }}">
                                            <small class="form-text text-muted">Több kép is feltölthető (jpg, png, gif).</small>
                                        </div>
                                    </div>
                                </div>

                                <div id="inventory_gallery" class="row">
                                    <!-- A képek betöltése AJAX-szal -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function recalculatePriceFields() {
        let beszerzesiAr = parseFloat($('input[name="beszerzesi_ar"]').val()) || 0;
        let eladasiAr = parseFloat($('input[name="eladasi_ar"]').val()) || 0;
        let weight = parseFloat($('input[name="weight"]').val()) || 0;

        let onePrice = beszerzesiAr * weight;
        let totalPrice = eladasiAr * weight;

        $('input[name="oneprice"]').val(onePrice > 0 ? onePrice : '');
        $('input[name="price"]').val(totalPrice > 0 ? totalPrice : '');
    }

    function applySelectedMarkingData() {
        let $selected = $('#vzj option:selected');
        let selectedPrice = $selected.data('price');
        let selectedVatTypeId = $selected.data('vat-type-id');
        let selectedProductStatusId = $selected.data('product-status-id');

        if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
            $('input[name="eladasi_ar"]').val(selectedPrice);
        } else if (!$('#vzj').val()) {
            $('input[name="eladasi_ar"]').val('');
        }

        if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
            $('select[name="vat_type_id"]').val(String(selectedVatTypeId));
        } else if (!$('#vzj').val()) {
            $('select[name="vat_type_id"]').val('');
        }

        if (selectedProductStatusId !== undefined && selectedProductStatusId !== null && selectedProductStatusId !== '') {
            $('select[name="statuses[]"]').val(String(selectedProductStatusId));
        } else if (!$('#vzj').val()) {
            $('select[name="statuses[]"]').val('');
        }

        recalculatePriceFields();
    }

    function refreshInventoryGallery() {
        let inventoryId = $('#inventory_id').val();
        if (!inventoryId) return;

        $.post('/load_inventory_gallery', { inventory_id: inventoryId, _token: '{{ csrf_token() }}' }, function(html) {
            $('#inventory_gallery').html(html);
        }).fail(function() {
            alert('Hiba a galéria betöltésekor.');
        });
    }

    function uploadInventoryImages(files) {
        let inventoryId = $('#inventory_id').val();
        if (!inventoryId || !files.length) return;

        let formData = new FormData();
        formData.append('inventory_id', inventoryId);
        formData.append('_token', '{{ csrf_token() }}');

        $.each(files, function(index, file) {
            formData.append('images[]', file);
        });

        $.ajax({
            url: '/upload_inventory_image',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                refreshInventoryGallery();
            },
            error: function(xhr) {
                alert('Hiba a képfeltöltés során.');
            }
        });
    }

    $(document).ready(function() {
        refreshInventoryGallery();

        applySelectedMarkingData();

        $('#vzj').on('change', function() {
            applySelectedMarkingData();
        });

        $(document).on('input', 'input[name="beszerzesi_ar"], input[name="eladasi_ar"], input[name="weight"]', function() {
            recalculatePriceFields();
        });

        $(document).on('click', '.item-label-print-btn', function() {
            let inventoryId = $(this).data('inventory-id');
            let labelType = $('#label_type').val();

            if (!labelType) {
                alert('Válassz címke típust.');
                return;
            }

            let url = '/product/printLabel?inventory_id=' + inventoryId + '&label_type=' + labelType;
            window.open(url, '_blank');
        });

        $('#inventory_images').on('change', function() {
            uploadInventoryImages(this.files);
            $(this).val('');
        });

        $(document).on('click', '.delete_inventory_image', function() {
            let imgId = $(this).data('img_id');
            if (!imgId || !confirm('Biztosan törli a képet?')) {
                return;
            }

            $.post('/delete_inventory_image', { img_id: imgId, _token: '{{ csrf_token() }}' }, function(resp) {
                if (resp.success) {
                    refreshInventoryGallery();
                } else {
                    alert('A kép törlése nem sikerült.');
                }
            }).fail(function() {
                alert('Hálózati hiba.');
            });
        });
    });
</script>
@endpush