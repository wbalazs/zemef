@extends('layouts.app', ['activePage' => 'product', 'activeButton' => 'product', 'title' => 'Zemef ERP', 'navName' => 'Termék létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Termék létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="submit" class="btn btn-warning save_product">Mentés</button>
                            <a href="{{ route('product.index') }}" class="btn btn-sm btn-success">Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="product_form" method="post" action="{{ route('product.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Termék alap adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Megnevezés</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name') }}" placeholder="" required autofocus>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('brand') ? ' has-danger' : '' }}">
                                                        Márka</label>
                                                    <input type="text" class="form-control{{ $errors->has('brand') ? ' is-invalid' : '' }}" name="brand" maxlength="100" value="{{ old('brand') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'brand'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('short_description') ? ' has-danger' : '' }}">
                                                        Rövid leírás</label>
                                                    <input type="text" class="form-control{{ $errors->has('short_description') ? ' is-invalid' : '' }}" name="short_description" maxlength="255" value="{{ old('short_description') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'short_description'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('long_description') ? ' has-danger' : '' }}">
                                                        Hosszú leírás</label>
                                                    <textarea rows="8" class="zm_textarea form-control{{ $errors->has('long_description') ? ' is-invalid' : '' }}" name="long_description">{{ old('long_description') }}</textarea>
                                                    @include('alerts.feedback', ['field' => 'long_description'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group{{ $errors->has('category_id') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-role">Kategória</label>
                                                <select title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="category_id" id="input-role" class="selectpicker form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}" placeholder="">
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" >{{ $category->name }}</option>
                                                    @endforeach
                                                </select>

                                                @include('alerts.feedback', ['field' => 'category_id'])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input checked class="form-check-input" type="checkbox" value="" data-gtm-form-interact-field-id="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktív
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4"></h6>
                                    <div class="row">
                                        



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

            $('.save_product').click(function(){
                $('#product_form').submit();
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