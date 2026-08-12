@extends('layouts.app', ['activePage' => 'nav_standard', 'activeButton' => 'nav_standard', 'title' => 'Zemef ERP', 'navName' => 'Norma szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    
                    <div class="col-12 mt-2">
                        @include('alerts.success')
                        @include('alerts.errors')
                    </div>

                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Norma szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/nav_standard" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_nav_standard" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="standard_id" value="{{ $nav_standard?->standard_id }}">
                        
                        <div class="card-body ">
                            <h6 class="heading-small text-muted mb-4">Norma adatai</h6>
                            <div class="row">
                                <div class="col-lg-3">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('amort') ? ' has-danger' : '' }}">
                                                Amortizációs költség</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('amort') ? ' is-invalid' : '' }}" name="amort" value="{{ old('amort', $nav_standard?->amort) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'amort'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('benzin_1000') ? ' has-danger' : '' }}">
                                                Benzin 1000 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('benzin_1000') ? ' is-invalid' : '' }}" name="benzin_1000" value="{{ old('benzin_1000', $nav_standard?->benzin_1000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'benzin_1000'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('benzin_1500') ? ' has-danger' : '' }}">
                                                Benzin 1500 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('benzin_1500') ? ' is-invalid' : '' }}" name="benzin_1500" value="{{ old('benzin_1500', $nav_standard?->benzin_1500) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'benzin_1500'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('benzin_2000') ? ' has-danger' : '' }}">
                                                Benzin 2000 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('benzin_2000') ? ' is-invalid' : '' }}" name="benzin_2000" value="{{ old('benzin_2000', $nav_standard?->benzin_2000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'benzin_2000'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('benzin_3000') ? ' has-danger' : '' }}">
                                                Benzin 3000 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('benzin_3000') ? ' is-invalid' : '' }}" name="benzin_3000" value="{{ old('benzin_3000', $nav_standard?->benzin_3000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'benzin_3000'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('benzin_5000') ? ' has-danger' : '' }}">
                                                Benzin 3001 cm3 felett</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('benzin_5000') ? ' is-invalid' : '' }}" name="benzin_5000" value="{{ old('benzin_5000', $nav_standard?->benzin_5000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'benzin_5000'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('dizel_1500') ? ' has-danger' : '' }}">
                                                Dízel 1500 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('dizel_1500') ? ' is-invalid' : '' }}" name="dizel_1500" value="{{ old('dizel_1500', $nav_standard?->dizel_1500) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'dizel_1500'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('dizel_2000') ? ' has-danger' : '' }}">
                                                Dízel 2000 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('dizel_2000') ? ' is-invalid' : '' }}" name="dizel_2000" value="{{ old('dizel_2000', $nav_standard?->dizel_2000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'dizel_2000'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('dizel_3000') ? ' has-danger' : '' }}">
                                                Dízel 3000 cm3-ig</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('dizel_3000') ? ' is-invalid' : '' }}" name="dizel_3000" value="{{ old('dizel_3000', $nav_standard?->dizel_3000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'dizel_3000'])
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('dizel_5000') ? ' has-danger' : '' }}">
                                                Dízel 3001 cm3 felett</label>
                                            
                                            <input type="number" step="0.1" class="form-control{{ $errors->has('dizel_5000') ? ' is-invalid' : '' }}" name="dizel_5000" value="{{ old('dizel_5000', $nav_standard?->dizel_5000) }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'dizel_5000'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                            
                                    <button type="button" class="btn btn-success save_master">Mentés</button>

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