@extends('layouts.app', ['activePage' => 'unit_price_type', 'activeButton' => 'unit_price_type', 'title' => 'Zemef ERP', 'navName' => 'Egységár típus szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Egységár típus szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/unit_price_types" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_unit_price_type" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="unit_price_type_id" value="{{ $unit_price_type->unit_price_type_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Egységár típus adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Név</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $unit_price_type->name) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('from_date') ? ' has-danger' : '' }}">
                                                        Érvényesség kezdete</label>
                                                    <input type="date" class="form-control{{ $errors->has('from_date') ? ' is-invalid' : '' }}" name="from_date" maxlength="40" value="{{ old('from_date', $unit_price_type->from_date) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'from_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('price') ? ' has-danger' : '' }}">
                                                        Ár</label>
                                                    <input type="number" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" maxlength="100" value="{{ old('price', $unit_price_type->price) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'price'])
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

                            <button type="button" class="btn btn-success save_master"><i class="fa fa-save"></i> Mentés</button>
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