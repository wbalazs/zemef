@extends('layouts.app', ['activePage' => 'consignment_receipt', 'activeButton' => 'consignment_receipt', 'title' => 'Zemef ERP', 'navName' => 'Tétel szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                        <li class="nav-item gallery_tab">
                            <a class="nav-link " id="images_data-tab" data-toggle="pill" href="#images_data" role="tab" aria-controls="images_data" aria-selected="true">Képek</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="/repair_list" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body ">

                                            <form id="consignment_receipt_form" method="post" action="/save_cri" enctype="multipart/form-data">
                                                <input type="hidden" name="cri_id" value="{{ $pti->consignment_receipt_item_id }}">
                                                @csrf
                                                @method('patch')

                                                <h6 class="heading-small text-muted mb-4">Tétel adatai</h6>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                                    Megnevezés</label>
                                                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $pti->name) }}">
                                                                @include('alerts.feedback', ['field' => 'name'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                                                    Leírás</label>
                                                                <input type="text" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" value="{{ old('description', $pti->description) }}">
                                                                @include('alerts.feedback', ['field' => 'description'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('quantity') ? ' has-danger' : '' }}">
                                                                    Mennyiség</label>
                                                                <input type="number" class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}" name="quantity" value="{{ old('quantity', $pti->quantity) }}">
                                                                @include('alerts.feedback', ['field' => 'quantity'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('price') ? ' has-danger' : '' }}">
                                                                    Ár</label>
                                                                <input type="number" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" value="{{ old('price', $pti->price) }}">
                                                                @include('alerts.feedback', ['field' => 'price'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('factory_number') ? ' has-danger' : '' }}">
                                                                    Gyártási szám</label>
                                                                <input type="text" class="form-control{{ $errors->has('factory_number') ? ' is-invalid' : '' }}" name="factory_number" value="{{ old('factory_number', $pti->factory_number) }}">
                                                                @include('alerts.feedback', ['field' => 'factory_number'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('weight') ? ' has-danger' : '' }}">
                                                                    Súly</label>
                                                                <input type="number" class="form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}" name="weight" value="{{ old('weight', $pti->weight) }}">
                                                                @include('alerts.feedback', ['field' => 'weight'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label{{ $errors->has('comment') ? ' has-danger' : '' }}">
                                                                    Megjegyzés</label>
                                                                <textarea class="zm_textarea form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment">{{ old('comment', $pti->comment) }}</textarea>
                                                                @include('alerts.feedback', ['field' => 'comment'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group{{ $errors->has('tag') ? ' has-danger' : '' }}">
                                                                <label class="form-control-label">Címkék</label>
                                                                <select multiple name="tags[]" class="select2 tags_select form-control">
                                                                    @foreach ($tags as $tag)
                                                                        <option value="{{ $tag->id }}" @if (in_array($tag->id, old('tags', $pti->tags->pluck('id')->toArray()) ?? []) ) selected @endif> {{ $tag->name }} 
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @include('alerts.feedback', ['field' => 'tags'])
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                                @if($pti->repair)
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label">
                                                                    Ötvös</label>
                                                                <input disabled type="text" class="form-control" name="goldsmith" value="@if($pti->goldsmith->first()) {{ $pti->goldsmith->first()->name }} @endif">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label">
                                                                    Ötvösnek átadva</label>
                                                                <input disabled type="text" class="form-control" name="goldsmith" value="@if($pti->goldsmith->first()) {{ $pti->goldsmith->first()->pivot->delivered_at }} @endif">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label">
                                                                    Ötvös átvette</label>
                                                                <input disabled type="text" class="form-control" name="goldsmith" value="@if($pti->goldsmith->first()) {{ $pti->goldsmith->first()->pivot->received_at }} @else - @endif">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label">
                                                                    Ötvös visszaadta</label>
                                                                <input disabled type="text" class="form-control" name="goldsmith" value="@if($pti->goldsmith->first()) {{ $pti->goldsmith->first()->pivot->back_to_storage_at }} @else - @endif">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                @if($pti->goldsmith->first() && $pti->goldsmith->first()->pivot->back_to_storage_at && !$pti->goldsmith->first()->pivot->accepted_at)
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <button type="button" data-cri_id="{{ $pti->consignment_receipt_item_id }}" class="btn btn-info accept_pti">Átvétel</button>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($pti->goldsmith->first() && $pti->goldsmith->first()->pivot->back_to_storage_at && $pti->goldsmith->first()->pivot->accepted_at)
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <fieldset>
                                                            <div class="form-group">
                                                                <label class="form-control-label">
                                                                    Visszavétel</label>
                                                                <input disabled type="text" class="form-control" name="goldsmith" value="@if($pti->goldsmith->first()->pivot->accepted_at) {{ $pti->goldsmith->first()->pivot->accepted_at }} @else - @endif">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                @endif

                                                @endif

                                            </form>
                                        </div>
                                    </div>
                                
                                </div>

                                <button type="button" class="btn btn-success save_cri">Mentés</button>

                            </div>



                            <div class="tab-pane fade" id="images_data" role="tabpanel" aria-labelledby="images_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="dropzone_area" >
                                            <form action="/ajax/upload_cri_image" id="cri_image_form_{{ $pti->consignment_receipt_item_id }}" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                <div class="dz-message needsclick">
                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                    <input type="hidden" name="cri_id" value="{{ $pti->consignment_receipt_item_id }}">
                                                    @csrf
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="cri_gallery_container_{{ $pti->consignment_receipt_item_id }}">
                                            
                                            <div class="delete_buttons">
                                                @foreach($pti->cri_images as $img)
                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                        <button type="button" data-cri_id="{{ $pti->consignment_receipt_item_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div data-lightGallery="1" id="lightgallery_cri_images_{{ $pti->consignment_receipt_item_id }}" class="">
                                                @foreach($pti->cri_images as $img)
                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="cri_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/cri_images/' . $img->image_path; ?>">
                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive cri_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/cri_images/' . $img->image_path; ?>" alt="">
                                                    </div>
                                                @endforeach
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
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>

        Dropzone.autoDiscover = false;

        function onlyNumbers(str) {
            return /^\d+$/.test(str);
        }


        function buildPTIGallery($initScope)
        {   
            console.log('build');
            if (typeof $initScope != 'undefined' && $initScope.length)
            {
                $initScope.justifiedGallery(
                {
                    border: 1,
                    rowHeight: 150,
                    margins: 2,
                    waitThumbnailsLoad: true,
                    randomize: false,
                }).on('jg.complete', function()
                {

                    $initScope.lightGallery(
                    {
                        thumbnail: true,
                        animateThumb: true,
                        showThumbByDefault: true,
                    });

                    setTimeout(function(){
                        $('.hidden_image_wrapper').each(function(){
                                $(this).width($('.cri_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                                $(this).height($('.cri_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                                $(this).css('top', $('.cri_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                                $(this).css('left', $('.cri_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
                        });
                    }, 2000);

                });
                $initScope.on('onAfterOpen.lg', function(event)
                {
                    $('body').addClass("overflow-hidden");
                });
                $initScope.on('onCloseAfter.lg', function(event)
                {
                    $('body').removeClass("overflow-hidden");
                });
            }
        }


        function initPTIGallery(cri_id) {
            var $initScope_implementation = $('#lightgallery_cri_images_' + cri_id);

            var images = $initScope_implementation.find('img').get();
            var allImagesLoaded = images.every(function(img) {
                return img.complete;
            });
            console.log('allImagesLoaded');
            console.log(allImagesLoaded);
            if (allImagesLoaded) {
                buildPTIGallery($initScope_implementation);
            } else {
                $(images).on('load', function() {
                    var allLoaded = images.every(function(img) {
                        return img.complete;
                    });
                    if (allLoaded) {
                        buildPTIGallery($initScope_implementation);
                    }
                });
            }
        }



        $(document).ready(function () {
            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );

            // Init Sliders
            $('.datepicker').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-screenshot',
                    clear: 'fa fa-trash',
                    close: 'fa fa-remove'
                }
            });


            $('.gallery_tab').click(function(){

                $("#cri_image_form_{{ $cri->consignment_receipt_item_id }}").dropzone({
                    maxFilesize: 50, // MB
                    parallelUploads: 1,
                    paramName: 'cri_image',
                    uploadMultiple: false,
                    addRemoveLinks: true,
                    maxFiles: 100,
                    acceptedFiles: '.jpg,.jpeg,.png',
                    dictRemoveFile: 'Törlés',
                    dictCancelUpload: 'mégse',
                    dictDefaultMessage: 'Kattintson vagy húzza ide a fájlokat! <br /> (max 50MB)',
                    renameFilename: cleanImageNameFn,
                    success: function(file, response) {
                        //automatikus mentés feltöltés után
                        $.ajax({
                            method: "POST",
                            url: "/ajax/auto_save_cri_images",
                            data: { cri_id: {{ $cri->consignment_receipt_item_id }}, image: response }
                        });
                    },
                    queuecomplete: function(file, response) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_cri_gallery",
                            data: { cri_id: {{ $cri->consignment_receipt_item_id }} }
                        })
                        .done(function( html ) {
                            $('.cri_gallery_container_{{ $cri->consignment_receipt_item_id }}').html(html);
                            initPTIGallery('{{ $cri->consignment_receipt_item_id }}');
                            $('.dz-complete').remove();
                            $('.dz-message').show();
                        });
                    }
                });

                initPTIGallery('{{ $cri->consignment_receipt_item_id }}');

            });


            $('select[name=customer_id]').select2({
                theme:"bootstrap4",
                tags: true
            });


            $("body").on("click", ".delete_img", function(e){
                var img_id = $(this).attr('data-img_id');
                var cri_id = $(this).attr('data-cri_id');

                swal({
                    text: "Biztosan törli ezt az képet?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Igen, törlöm',
                    cancelButtonText: 'Nem',
                    buttonsStyling: false
                },function() {

                    $.ajax({
                        method: "POST",
                        url: "/ajax/delete_cri_image",
                        data: { img_id: img_id }
                    })
                    .done(function( msg ) {
                        //kép hozzáadása a listához
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_cri_gallery",
                            data: { cri_id: cri_id }
                        })
                        .done(function( html ) {
                            $('.cri_gallery_container_' + cri_id).html(html);

                            var totalImages = $('#cri_images_modal_' + cri_id + ' img').length;

                            var loadedImages = 0;

                            $('#cri_images_modal_' + cri_id + ' img').each(function() {
                                loadedImages++;
                                if (loadedImages === totalImages) {
                                    initPTIGallery(cri_id);
                                }
                            });
                        });
                    });


                });

            });


            $("body").on("mouseenter", ".cri_img_wrapper", function(e){
                $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
            });
            $("body").on("mouseleave", ".cri_img_wrapper", function(e){
                if( !$(e.toElement).hasClass('delete_img') ) {
                    $('.delete_img').stop(true).hide(200);
                }
            });


            $('.save_cri').click(function(){
                var consignment_receipt_id = '{{ $pti->consignment_receipt->consignment_receipt_id }}';
                var cri_name = $('input[name=name]').val();
                var cri_id = $('input[name=cri_id]').val();
                var cri_description = $('input[name=description]').val();
                var cri_quantity = $('input[name=quantity]').val();
                var cri_price = $('input[name=price]').val();
                var cri_factory_number = $('input[name=factory_number]').val();
                var cri_weight = $('input[name=weight]').val();
                var cri_comment = $('textarea[name=comment]').val();
                var tags = $('select.tags_select').val();
                
                $.ajax({
                    method: "POST",
                    url: "/ajax/save_consignment_receipt_item",
                    data: { consignment_receipt_id: consignment_receipt_id, cri_id: cri_id, cri_name: cri_name, cri_description: cri_description, cri_quantity: cri_quantity, cri_price: cri_price, cri_factory_number: cri_factory_number, cri_weight: cri_weight, cri_comment: cri_comment, tags: tags }
                }).done(function( resp ) {
                    $.notify({ icon: "done", message: "Az adatok sikeresen mentve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                });
            });


            $('select.tags_select').select2();
            $('select.tags_select').change(function(){

                //Belső és ügyfél nem lehet együtt
                if($.inArray('5', $(this).val()) !== -1 && $.inArray('6', $(this).val()) !== -1){
                    $.notify({ icon: "done", message: "Belső javítás és ügyfél nem lehet egyszerre. Kérem, ellenőrizze!" }, {type: 'warning',timer: 4000, placement: {from: 'top',align: 'right'}});

                    var valuesToRemove = ['5','6'];

                    var new_val = $.grep($(this).val(), function(value) {
                        return !valuesToRemove.includes(value);
                    });

                    $(this).val(new_val);
                    $('select.tags_select').trigger('change');


                }

            });

        });
    </script>
@endpush