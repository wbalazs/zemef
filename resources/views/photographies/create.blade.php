@extends('layouts.app', ['activePage' => 'tool', 'activeButton' => 'tool', 'title' => 'Zemef ERP', 'navName' => 'Eszköz létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Fotózási lista létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('photography.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="photography_form" method="post" action="{{ route('photography.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Lista tételei</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('list_name') ? ' has-danger' : '' }}">
                                                        Lista megnevezés</label>
                                                    <input type="text" class="form-control{{ $errors->has('list_name') ? ' is-invalid' : '' }}" name="list_name" maxlength="100" value="{{ old('list_name') }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'list_name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('product') ? ' has-danger' : '' }}">
                                                        Termék kiválasztása</label>
                                                    <select class="form-control{{ $errors->has('product') ? ' is-invalid' : '' }} select2 product_select" name="product">
                                                        <option value="">Válasszon terméket</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @include('alerts.feedback', ['field' => 'products_array'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-1" style="align-self: end;text-align: end;">
                                            <button type="button" class="btn btn-primary product_add" disabled="disabled" style="margin-bottom:14px;background-color:#007bff !important;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- //táblázat -->
                                            <div class="table-responsive">
                                                <table class="table align-items-center table-flush">
                                                    <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Termék</th>
                                                        <th scope="col">Műveletek</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="product_table">
                                                    </tbody>
                                                </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a class="btn btn-success save_photography"><i class="fa fa-save"></i> Mentés</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>
    $(document).ready(function () {
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
            } )
            .catch( error => {
        } );
        
    });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            //product select change
            $('.product_select').change(function(){
                let product_id = $(this).val();
                if(product_id == ''){
                    $('.product_add').prop('disabled', true);
                }else {
                    $('.product_add').prop('disabled', false);
                }
            });

            //product add
            $('.product_add').click(function(){
                let product_id = $('.product_select').val();
                let num = $('#product_table tr').length + 1;
                if(product_id == ''){
                    alert('Válasszon terméket!');
                    return false;
                }
                let product_name = $('.product_select').find('option:selected').text();
                let product_table = $('#product_table');
                let product_row = '<tr><td>'+num+'</td><td id="'+product_id+'">'+product_name+'</td><td><a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td></tr>';
                product_table.append(product_row);
                $('.product_add').prop('disabled', true);
            });
            



            $('.save_photography').click(function(e){
                $('#photography_form input[type="hidden"][name="products_array[]"]').remove();

                $('#product_table tr').each(function(){
                    let product_id = $(this).find('td').eq(1).attr('id');
                    if(product_id) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'products_array[]',
                            value: product_id
                        }).appendTo('#photography_form');
                    }
                });

                $('#photography_form').submit();
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
        });
    </script>
@endpush