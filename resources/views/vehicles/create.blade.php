@extends('layouts.app', ['activePage' => 'vehicle', 'activeButton' => 'vehicle', 'title' => 'Zemef ERP', 'navName' => 'Jármű létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Jármű létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_vehicle">Mentés</button>
                            <a href="{{ route('vehicle.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="vehicle_form" method="post" action="{{ route('vehicle.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Jármű alap adatai</h6>
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
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('plate_number') ? ' has-danger' : '' }}">
                                                        Rendszám</label>
                                                    <input type="text" class="form-control{{ $errors->has('plate_number') ? ' is-invalid' : '' }}" name="plate_number" maxlength="100" value="{{ old('plate_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'plate_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-5">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('insurance_number') ? ' has-danger' : '' }}">
                                                        Biztosítás száma</label>
                                                    <input type="text" class="form-control{{ $errors->has('insurance_number') ? ' is-invalid' : '' }}" name="insurance_number" maxlength="100" value="{{ old('insurance_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'insurance_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('insurance_date') ? ' has-danger' : '' }}">
                                                        Biztosítás évforduló</label>
                                                    <input type="text" class="form-control{{ $errors->has('insurance_date') ? ' is-invalid' : '' }}" name="insurance_date" maxlength="100" value="{{ old('insurance_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'insurance_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('responsible') ? ' has-danger' : '' }}">
                                                        Felelős</label>
                                                    <input type="text" class="form-control{{ $errors->has('responsible') ? ' is-invalid' : '' }}" name="responsible" maxlength="100" value="{{ old('responsible') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'responsible'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('start_km') ? ' has-danger' : '' }}">
                                                        Induló km</label>
                                                    <input type="number" class="form-control{{ $errors->has('start_km') ? ' is-invalid' : '' }}" name="start_km" maxlength="100" value="{{ old('start_km') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'start_km'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('end_km') ? ' has-danger' : '' }}">
                                                        Záró km</label>
                                                    <input type="number" class="form-control{{ $errors->has('end_km') ? ' is-invalid' : '' }}" name="end_km" maxlength="100" value="{{ old('end_km') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'end_km'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('condition') ? ' has-danger' : '' }}">
                                                        Állapot</label>
                                                    <textarea class="zm_textarea form-control{{ $errors->has('condition') ? ' is-invalid' : '' }}" name="condition">{{ old('condition') }}</textarea>
                                                    @include('alerts.feedback', ['field' => 'condition'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('condition_date') ? ' has-danger' : '' }}">
                                                        Állapot dátuma</label>
                                                    <input type="date" class="form-control{{ $errors->has('condition_date') ? ' is-invalid' : '' }}" name="condition_date" value="{{ old('condition_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'condition_date'])
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
                                                        <input checked class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktívv
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

            $('.save_vehicle').click(function(){
                $('#vehicle_form').submit();
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