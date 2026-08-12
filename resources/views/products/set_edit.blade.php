@extends('layouts.app', ['activePage' => 'product_set', 'activeButton' => 'product_set', 'title' => 'Zemef ERP', 'navName' => 'Szett szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                            </li>
                            <li class="nav-item gallery_tab">
                                <a class="nav-link" id="images_data-tab" data-toggle="pill" href="#images_data" role="tab" aria-controls="images_data" aria-selected="true">Képek</a>
                            </li>
                        </ul>
                        <div class="col-10 text-right">
                            <a href="/product_set" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">
                                <form id="product_form" method="post" action="{{ route('product_set.update',$product_set->product_id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')
                                
                                    <div class="row">
                                        @include('products.set_edit_body')
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="images_data" role="tabpanel" aria-labelledby="images_data-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="dropzone_area" >
                                            <form action="/ajax/upload_product_image" id="product_image_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                <div class="dz-message needsclick">
                                                    <span class="">Húzza ide a képeket vagy kattintson a feltöltéshez!</span>
                                                    <input type="hidden" name="product_id" value="{{ $product_set->product_id }}">
                                                    @csrf
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="product_gallery_container">
                                            
                                            <div class="delete_buttons">
                                                @foreach($product_set->product_images as $img)
                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                        <button type="button" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                                <?php //$pti_images = $product_set->pti->pti_images->merge($product_set->pti->pti_images2); ?>
                                                @foreach($pti_images as $img)
                                                    <div class="hidden_image_wrapper" data-img_id="{{ $img->image_id }}">
                                                        <?php //TODO ezeket is lehessen törölni? ?>
                                                        <?php /* ?>
                                                        <button type="button" data-pti_id="{{ $product_set->pti->purchase_ticket_item_id }}" data-img_id="{{ $img->image_id }}" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed">
                                                            <i data-img_id="{{ $img->image_id }}" class="fa fa-times"></i>
                                                        </button>
                                                        <?php */ ?>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div data-lightGallery="1" id="lightgallery_product_images" class="">
                                                @foreach($product_set->product_images as $img)
                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="product_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/product_images/' . $img->image_path; ?>">
                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive product_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/product_images/' . $img->image_path; ?>" alt="">
                                                    </div>
                                                @endforeach
                                                    <hr>
                                                @foreach($pti_images as $img)
                                                    <div data-sub-html="" data-img_id="{{ $img->image_id }}" class="product_img_wrapper" data-src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>">
                                                        <img data-img_id="{{ $img->image_id }}" class="img-responsive product_image_img" src="<?php echo env('PUBLIC_URL') . '/document_storage/pti_images/' . $img->image_path; ?>" alt="">
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                    
                            </div>



                        </div>


                        <button @if($show) disabled @endif type="button" class="btn btn-success save_product">Mentés</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        $("#product_doc_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'product_doc',
            uploadMultiple: false,
            addRemoveLinks: true,
            maxFiles: 1,
            acceptedFiles: '.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf,.txt,.csv',
            dictRemoveFile: 'Törlés',
            dictCancelUpload: 'mégse',
            dictDefaultMessage: 'Kattintson vagy húzza ide a fájlt! <br /> (max 50MB)',
            renameFilename: cleanImageNameFn,
            success: function(file, response) {
                //add the file name as hidden input
                $('input[name=product_doc_input]').val(response);
            },
            removedfile: function(file) {
                $('input[name=product_doc_input]').val('');

                var class_name = file.name;
                class_name = class_name.replace(/[. ]/g,'');
                jQuery('.' + class_name).remove();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
        });


        function buildProductGallery($initScope)
        {
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
                                $(this).width($('.product_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').width());
                                $(this).height($('.product_image_img[data-img_id=' + $(this).attr('data-img_id') + ']').height());
                                $(this).css('top', $('.product_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('top'));
                                $(this).css('left', $('.product_img_wrapper[data-img_id=' + $(this).attr('data-img_id') + ']').css('left'));
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


        function initProductGallery() {
            var $initScope_implementation = $('#lightgallery_product_images');

            var images = $initScope_implementation.find('img').get();
            var allImagesLoaded = images.every(function(img) {
                return img.complete;
            });

            if (allImagesLoaded) {
                buildProductGallery($initScope_implementation);
            } else {
                $(images).on('load', function() {
                    var allLoaded = images.every(function(img) {
                        return img.complete;
                    });
                    if (allLoaded) {
                        buildProductGallery($initScope_implementation);
                    }
                });
            }
        }


        $("#product_image_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'product_image',
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
                    url: "/ajax/auto_save_product_images",
                    data: { product_id: '{{ $product_set->product_id }}', image: response, image_date: $('input[name=image_date]').val() }
                })
                .done(function( msg ) {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_product_gallery",
                        data: { product_id: '{{ $product_set->product_id }}' }
                    })
                    .done(function( html ) {
                        $('.product_gallery_container').html(html);
                        initProductGallery();
                        $('.dz-complete').remove();
                        $('.dz-message').show();
                    });
                });
            }
        });

        $(document).ready(function() {
            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );


            $('.save_product').click(function(){
                $('#product_form').submit();
            });


            $('.hallmark_checkes').change(function(){

                if($(this).is(':checked')){
                    $('input[name=hallmark_'+$(this).attr('data-hallmark_id')+']').val(1);
                } else {
                    $('input[name=hallmark_'+$(this).attr('data-hallmark_id')+']').val(0);
                }

            });


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


            //gallery
            $('.gallery_tab').click(function(){
                initProductGallery();
            });



            $("body").on("mouseenter", ".product_img_wrapper", function(e){
                $('.delete_img[data-img_id=' + $(this).attr('data-img_id') + ']').stop(true).show(200);
            });
            $("body").on("mouseleave", ".product_img_wrapper", function(e){
                if( !$(e.toElement).hasClass('delete_img') ) {
                    $('.delete_img').stop(true).hide(200);
                }
            });

            $("body").on("click", ".delete_img", function(e){
                var img_id = $(this).attr('data-img_id');


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
                        url: "/ajax/delete_product_image",
                        data: { img_id: img_id }
                    })
                    .done(function( msg ) {
                        //kép hozzáadása a listához
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_product_gallery",
                            data: { product_id: '{{ $product_set->product_id }}' }
                        })
                        .done(function( html ) {
                            $('.product_gallery_container').html(html);
                            initProductGallery();
                        });
                    });


                });

            });

        });
    </script>
@endpush