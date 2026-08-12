@extends('layouts.app', ['activePage' => 'product_status', 'activeButton' => 'product_status', 'title' => 'Zemef ERP', 'navName' => 'Termék állapot szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Termék állapot szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/product_statuses" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_product_status" enctype="multipart/form-data">
                        @csrf

                        <input id="master_form" type="hidden" name="product_status_id" value="{{ $product_status->product_status_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Termék állapot adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Megnevezés</label>
                                                    
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $product_status->name) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('price') ? ' has-danger' : '' }}">
                                                        Ár</label>
                                                    <input type="text" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" maxlength="255" value="{{ old('price', $product_status->price) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'price'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('vat_type_id') ? ' has-danger' : '' }}">
                                                        Áfa típus</label>
                                                    <select class="form-control{{ $errors->has('vat_type_id') ? ' is-invalid' : '' }}" name="vat_type_id">
                                                        <option value="">- válasszon -</option>
                                                        @foreach($vat_types as $vat_type)
                                                            <option value="{{ $vat_type->vat_type_id }}" @if((string) old('vat_type_id', $product_status->vat_type_id) === (string) $vat_type->vat_type_id) selected @endif>{{ $vat_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @include('alerts.feedback', ['field' => 'vat_type_id'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('from') ? ' has-danger' : '' }}">
                                                        Érvényes ettől</label>
                                                    <input type="date" class="form-control{{ $errors->has('from') ? ' is-invalid' : '' }}" name="from" value="{{ old('from', $product_status->from ? \Carbon\Carbon::parse($product_status->from)->format('Y-m-d') : '') }}">
                                                    @include('alerts.feedback', ['field' => 'from'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input @if(old('status', $product_status->status)) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktív
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input @if(old('label', $product_status->label)) checked @endif class="form-check-input" type="checkbox" value="1" name="label">
                                                        <span class="form-check-sign"></span>
                                                        Címke
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
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
                            <button type="button" class="btn btn-success save_master">Mentés</button>
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

            $('.save_master').click(function(){
                $('#master_form').submit();
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