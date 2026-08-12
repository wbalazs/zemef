@extends('layouts.app', ['activePage' => 'extension_list', 'activeButton' => 'extension_list', 'title' => 'Zemef ERP', 'navName' => 'Kiegészítendő vételi jegy szerkesztése'])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Kiegészítendő vételi jegy szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('purchase_tickets.extension_list') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="post" action="{{ route('purchase_tickets.extension_update', ['purchase_ticket' => $purchase_ticket->purchase_ticket_id]) }}">
                        @csrf
                        @method('patch')

                        <h6 class="heading-small text-muted mb-4">Fejléc adatok</h6>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Vételi jegy sorszáma</label>
                                    <input type="text" class="form-control" value="{{ $purchase_ticket->ticket_number }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Üzlet</label>
                                    <select name="business_id" id="business_id" class="form-control" required>
                                        <option value="">- válasszon -</option>
                                        @foreach($businesses as $business)
                                            <option
                                                value="{{ $business->business_id }}"
                                                @if((string) old('business_id', $purchase_ticket->business_id) === (string) $business->business_id) selected @endif
                                            >
                                                {{ $business->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Ahonnan jött</label>
                                    <input type="text" class="form-control" value="{{ optional($purchase_ticket->customer)->name }}" disabled>
                                </div>
                            </div>
                        </div>

                        @php
                            $selectedMarkingId = old('business_marking_id', $headerData['business_marking_id'] ?? '');
                        @endphp

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Szállító kód</label>
                                    <select name="business_marking_id" id="business_marking_id" class="form-control">
                                        <option value="">- válasszon -</option>
                                        @foreach($businessMarkings as $marking)
                                            <option
                                                value="{{ $marking->id }}"
                                                data-sale-price="{{ optional($marking->productStatus)->price }}"
                                                data-vat-name="{{ optional(optional($marking->productStatus)->vatType)->name }}"
                                                @if((string) $selectedMarkingId === (string) $marking->id) selected @endif
                                            >
                                                {{ $marking->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Szállítólevél sorszáma</label>
                                    <input type="text" name="delivery_note_number" class="form-control" value="{{ old('delivery_note_number', $headerData['delivery_note_number'] ?? '') }}">
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Teljesítés dátum</label>
                                    <input type="date" name="fulfillment_date" class="form-control" value="{{ old('fulfillment_date', $headerData['fulfillment_date'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Eladási ár</label>
                                    <input type="text" id="sale_price" class="form-control" value="{{ old('sale_price', $headerData['sale_price'] ?? '') }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Áfa típus</label>
                                    <input type="text" id="vat_name" class="form-control" value="{{ old('vat_name', $headerData['vat_name'] ?? '') }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">Beszerzési ár</label>
                                    <input type="number" step="0.01" min="0" name="purchase_price" class="form-control" value="{{ old('purchase_price', $headerData['purchase_price'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="heading-small text-muted mb-4 mt-4">Ékszer vásárlás tételek</h6>

                        @if(!empty($brokenGoldWarehouseName))
                            <div class="alert alert-info">
                                A <strong>Törtarany</strong> jelölésű tételek mentéskor automatikusan a(z) <strong>{{ $brokenGoldWarehouseName }}</strong> raktárba kerülnek.
                            </div>
                        @endif

                        @forelse($groupedInventoryItems as $inventoryId => $items)
                            @php
                                $firstItem = $items->first();
                                $itemData = is_array($firstItem->item_data) ? $firstItem->item_data : [];
                                $defaultItemMarkingId = old('items.' . $firstItem->inventory_id . '.business_marking_id', $itemData['customer_marking_id'] ?? ($headerData['business_marking_id'] ?? ''));
                                $defaultBrokenGold = old('items.' . $firstItem->inventory_id . '.is_broken_gold', !empty($itemData['is_broken_gold']) ? 1 : 0);
                                $selectedStatusId = $itemData['statuses'][0] ?? ($itemData['statuses'] ?? '');
                                $selectedKaratId = $itemData['karat_id'] ?? ($itemData['karats'][0] ?? '');
                                $selectedCategory2Id = $itemData['category2_id']
                                    ?? (is_array($itemData['product_categories_2'] ?? null)
                                        ? ($itemData['product_categories_2'][0] ?? '')
                                        : ($itemData['product_categories_2'] ?? ''));
                            @endphp

                            <div class="mb-3">
                                <div class="card-body p-3 item-summary" data-target="item-detail-{{ $firstItem->inventory_id }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Kód:</strong> {{ $firstItem->code ?? '–' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Megnevezés:</strong> {{ $firstItem->description ?? '' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Súly:</strong> {{ number_format($items->sum('weight'), 2) }} g
                                            <i class="fa fa-chevron-down float-right"></i>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div id="item-detail-{{ $firstItem->inventory_id }}" class="item-detail">
                                    <div class="sei_item_wrapper green_bg" data-inventory-id="{{ $firstItem->inventory_id }}">
                                        <form class="sei_item_form">
                                            @csrf
                                            <b>{{ $firstItem->code ?? '' }}</b>

                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Szállító (VZJ)</label>
                                                        <select disabled name="business_marking_id" class="selectpicker form-control item-business-marking-inline" data-live-search="true" data-selected-id="{{ $defaultItemMarkingId }}">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($businessMarkings as $marking)
                                                                <option value="{{ $marking->id }}" data-price="{{ optional($marking->productStatus)->price }}" data-vat-type-id="{{ optional($marking->productStatus)->vat_type_id }}" data-product-status-id="{{ optional($marking->productStatus)->product_status_id }}" @if((string)$defaultItemMarkingId === (string)$marking->id) selected @endif>
                                                                    {{ $marking->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Kategória</label>
                                                        <select disabled name="category_id" class="selectpicker form-control">
                                                            @include('categories.category_options', ['categories' => $categories, 'category_id' => old('category_id', $itemData['category_id'] ?? 1)])
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Beszerzési ár</label>
                                                        <input disabled type="number" class="form-control" name="beszerzesi_ar" value="{{ $itemData['beszerzesi_ar'] ?? ($headerData['purchase_price'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Eladási ár</label>
                                                        <input disabled type="number" class="form-control" name="eladasi_ar" value="{{ $itemData['eladasi_ar'] ?? ($headerData['sale_price'] ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Áfa típus</label>
                                                        <select disabled name="vat_type_id" class="selectpicker form-control">
                                                            @foreach($vat_types as $vat_type)
                                                                <option value="{{ $vat_type->vat_type_id }}" {{ (string)($itemData['vat_type_id'] ?? ($headerData['vat_type_id'] ?? '')) === (string)$vat_type->vat_type_id ? 'selected' : '' }}>
                                                                    {{ $vat_type->name.' - '.$vat_type->value }} %
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group" style="margin-left:20px;">
                                                        <br />
                                                        <input disabled type="checkbox" class="form-check-input" name="hallmarks" value="1" {{ old('hallmarks', $itemData['hallmarks'] ?? 0) == 1 ? 'checked' : '' }}>
                                                        <label class="form-control-label">Fémjelzés</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Állapot</label>
                                                        <select disabled name="statuses[]" class="selectpicker product_statuses_select form-control">
                                                            @foreach($product_statuses as $product_status)
                                                                <option value="{{ $product_status->product_status_id }}" {{ (string)$selectedStatusId === (string)$product_status->product_status_id ? 'selected' : '' }}>
                                                                    {{ $product_status->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Karát</label>
                                                        <select disabled name="karat_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($karats ?? [] as $karat)
                                                                <option value="{{ $karat->karat_id }}" {{ (string)$selectedKaratId === (string)$karat->karat_id ? 'selected' : '' }}>{{ $karat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Szín</label>
                                                        <select disabled multiple name="colors[]" class="selectpicker product_colors_select form-control">
                                                            @foreach($product_colors ?? [] as $color)
                                                                <option value="{{ $color->product_color_id }}" {{ in_array($color->product_color_id, old('colors', $itemData['colors'] ?? [])) ? 'selected' : '' }}>
                                                                    {{ $color->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Kategória II</label>
                                                        <select disabled name="category2_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @foreach($category2s ?? [] as $category2)
                                                                <option value="{{ $category2->category2_id }}" {{ (string)$selectedCategory2Id === (string)$category2->category2_id ? 'selected' : '' }}>{{ $category2->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Méret</label>
                                                        <input disabled type="text" class="form-control" name="size" value="{{ $itemData['size'] ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group" style="margin-left:20px;">
                                                        <br />
                                                        <input disabled type="checkbox" class="form-check-input" name="is_broken_gold" value="1" @if((int)$defaultBrokenGold === 1) checked @endif>
                                                        <label class="form-control-label">Törtarany</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Termék típus</label>
                                                        <select disabled name="termek_tipus_id" class="selectpicker form-control">
                                                            <option value="">- válasszon -</option>
                                                            @include('products.product_types_options', ['product_types' => $product_types, 'product_type_id' => $firstItem->termek_tipus_id])
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Leírás</label>
                                                        <input disabled type="text" class="form-control" name="description" value="{{ $firstItem->description }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Mennyiség</label>
                                                        <input disabled type="number" class="form-control" name="quantity" value="{{ $items->sum('quantity') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Egységár</label>
                                                        <input disabled type="number" class="form-control" name="oneprice" value="{{ $firstItem->oneprice }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Ár</label>
                                                        <input disabled type="number" class="form-control" name="price" value="{{ $items->sum('price') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Súly</label>
                                                        <input disabled type="number" class="form-control" name="weight" value="{{ $items->sum('weight') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Gyártási szám</label>
                                                        <input disabled type="text" class="form-control" name="factory_number" value="{{ $firstItem->factory_number }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-10 d-flex justify-content-end align-items-end">
                                                    <button type="button" class="btn btn-warning btn-sm edit_items_button me-1" data-inventory-id="{{ $firstItem->inventory_id }}">
                                                        Tétel szerkesztése
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-sm save_items_button d-none me-1" data-inventory-id="{{ $firstItem->inventory_id }}">
                                                        Mentés
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">Nincs rögzített tétel a vételi jegyhez.</div>
                        @endforelse

                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Mentés</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            const initialHeaderMarkingId = '{{ (string) old('business_marking_id', $headerData['business_marking_id'] ?? '') }}';

            function recalculateItemPrice($container) {
                var beszerzesiAr = parseFloat($container.find('[name="beszerzesi_ar"]').val()) || 0;
                var eladasiAr = parseFloat($container.find('[name="eladasi_ar"]').val()) || 0;
                var weight = parseFloat($container.find('[name="weight"]').val()) || 0;
                var onePrice = beszerzesiAr * weight;
                var totalPrice = eladasiAr * weight;
                $container.find('[name="oneprice"]').val(onePrice > 0 ? onePrice : '');
                $container.find('[name="price"]').val(totalPrice > 0 ? totalPrice : '');
            }

            function updateMarkingInfo() {
                let selected = $('#business_marking_id option:selected');
                $('#sale_price').val(selected.data('sale-price') || '');
                $('#vat_name').val(selected.data('vat-name') || '');
            }

            function syncItemMarkingsWithHeader(forceAll) {
                let headerMarkingId = $('#business_marking_id').val() || '';
                $('.item-business-marking').each(function() {
                    if (forceAll || !$(this).val()) {
                        $(this).val(headerMarkingId);
                    }
                });

                $('.item-business-marking-inline').each(function() {
                    if (forceAll || !$(this).val()) {
                        $(this).val(headerMarkingId);
                    }
                });
            }

            function refreshBusinessMarkingPickers() {
                if ($.fn.selectpicker) {
                    $('#business_marking_id').selectpicker('refresh');
                    $('.item-business-marking-inline').selectpicker('refresh');
                }
            }

            function rebuildBusinessMarkingOptions(markings, selectedMarkingId, resetItemSelections) {
                let options = '<option value="">- válasszon -</option>';
                let itemOptions = '<option value="">- válasszon -</option>';

                $.each(markings || [], function(_, marking) {
                    options += '<option value="' + marking.id + '" data-sale-price="' + (marking.price ?? '') + '" data-vat-name="' + (marking.vat_name ?? '') + '">' + marking.name + '</option>';
                    itemOptions += '<option value="' + marking.id + '">' + marking.name + '</option>';
                });

                $('#business_marking_id').html(options);
                if (selectedMarkingId && String(selectedMarkingId).length) {
                    $('#business_marking_id').val(String(selectedMarkingId));
                }

                $('.item-business-marking').each(function() {
                    const previous = $(this).val() || $(this).data('selected-id') || '';
                    $(this).html(itemOptions);
                    if (!resetItemSelections && String(previous).length) {
                        $(this).val(String(previous));
                    }
                });

                $('.item-business-marking-inline').each(function() {
                    const previous = $(this).val() || $(this).data('selected-id') || '';
                    $(this).html(itemOptions);
                    if (!resetItemSelections && String(previous).length) {
                        $(this).val(String(previous));
                    }
                });

                syncItemMarkingsWithHeader(resetItemSelections);
                refreshBusinessMarkingPickers();
                updateMarkingInfo();
            }

            function loadBusinessMarkings(businessId, clearSelection) {
                if (!businessId) {
                    $('#business_marking_id').html('<option value="">- válasszon -</option>');
                    $('.item-business-marking').html('<option value="">- válasszon -</option>').val('');
                    $('.item-business-marking-inline').html('<option value="">- válasszon -</option>').val('');
                    updateMarkingInfo();
                    return;
                }

                $.ajax({
                    method: 'POST',
                    url: '/ajax/get_business',
                    data: { business_id: businessId }
                }).done(function(response) {
                    let markings = response && response.business_markings ? response.business_markings : [];
                    let selectedMarkingId = clearSelection ? '' : ($('#business_marking_id').val() || initialHeaderMarkingId || '');
                    rebuildBusinessMarkingOptions(markings, selectedMarkingId, clearSelection);
                    if (clearSelection) {
                        $('#business_marking_id').val('');
                        syncItemMarkingsWithHeader(true);
                        updateMarkingInfo();
                    }
                });
            }

            $('#business_marking_id').on('change', function() {
                updateMarkingInfo();
                syncItemMarkingsWithHeader(true);
            });

            $(document).on('change', '.item-business-marking-inline', function() {
                let $wrapper = $(this).closest('.sei_item_wrapper');
                let $selected = $(this).find('option:selected');
                let selectedPrice = $selected.data('price');
                let selectedVatTypeId = $selected.data('vat-type-id');
                let selectedProductStatusId = $selected.data('product-status-id');

                if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                    $wrapper.find('[name="eladasi_ar"]').val(selectedPrice);
                }
                if (selectedVatTypeId !== undefined && selectedVatTypeId !== null && selectedVatTypeId !== '') {
                    $wrapper.find('[name="vat_type_id"]').val(String(selectedVatTypeId));
                }
                if (selectedProductStatusId !== undefined && selectedProductStatusId !== null && selectedProductStatusId !== '') {
                    $wrapper.find('[name="statuses[]"]').val(String(selectedProductStatusId));
                }

                if ($.fn.selectpicker) {
                    $wrapper.find('select.selectpicker').selectpicker('refresh');
                }

                recalculateItemPrice($wrapper);
            });

            $(document).on('input', '.sei_item_wrapper [name="beszerzesi_ar"], .sei_item_wrapper [name="eladasi_ar"], .sei_item_wrapper [name="weight"]', function() {
                recalculateItemPrice($(this).closest('.sei_item_wrapper'));
            });

            $(document).on('click', '.item-summary', function() {
                var targetId = $(this).data('target');
                $('#' + targetId).slideToggle(300);
                $(this).find('.fa-chevron-down').toggleClass('fa-chevron-up');
            });

            $(document).on('click', '.edit_items_button', function() {
                var inventoryId = $(this).data('inventory-id');
                var $wrapper = $('.sei_item_wrapper[data-inventory-id="' + inventoryId + '"]');
                $wrapper.find('input, select, textarea').prop('disabled', false);
                $wrapper.find('.edit_items_button').addClass('d-none');
                $wrapper.find('.save_items_button').removeClass('d-none');
                if ($.fn.selectpicker) {
                    $wrapper.find('select.selectpicker').selectpicker('refresh');
                }
            });

            $(document).on('click', '.save_items_button', function(e) {
                e.preventDefault();
                var inventoryId = $(this).data('inventory-id');
                var $wrapper = $('.sei_item_wrapper[data-inventory-id="' + inventoryId + '"]');
                var $form = $wrapper.find('form.sei_item_form');
                var formData = $form.serializeArray();

                formData.push({ name: 'inventory_id', value: inventoryId });
                formData.push({ name: 'business_id', value: $('#business_id').val() || '' });
                formData.push({ name: 'is_broken_gold', value: $wrapper.find('[name="is_broken_gold"]').is(':checked') ? 1 : 0 });

                $.ajax({
                    url: '/ajax/save_extension_item_inline',
                    method: 'POST',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        $.notify({ message: 'Tétel sikeresen mentve' }, { type: 'success' });
                        $wrapper.find('input, select, textarea').prop('disabled', true);
                        $wrapper.find('.edit_items_button').removeClass('d-none');
                        $wrapper.find('.save_items_button').addClass('d-none');
                        if ($.fn.selectpicker) {
                            $wrapper.find('select.selectpicker').selectpicker('refresh');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Hiba a mentés során';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        $.notify({ message: message }, { type: 'danger' });
                    }
                });
            });

            $('#business_id').on('change', function() {
                loadBusinessMarkings($(this).val(), true);
            });

            loadBusinessMarkings($('#business_id').val(), false);
        });
    </script>
@endpush
