@extends('layouts.app', ['activePage' => 'tool', 'activeButton' => 'tool', 'title' => 'Zemef ERP', 'navName' => 'Eszköz létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Eszköz létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_tool">Mentés</button>
                            <a href="{{ route('tool.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="tool_form" method="post" action="{{ route('tool.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Eszköz alap adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Név</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name') }}" placeholder="" required autofocus>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('identity_number') ? ' has-danger' : '' }}">
                                                        Azonosító szám</label>
                                                    <input type="text" class="form-control{{ $errors->has('identity_number') ? ' is-invalid' : '' }}" name="identity_number" maxlength="100" value="{{ old('identity_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'identity_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('serial_number') ? ' has-danger' : '' }}">
                                                        Gyári szám</label>
                                                    <input type="text" class="form-control{{ $errors->has('serial_number') ? ' is-invalid' : '' }}" name="serial_number" maxlength="100" value="{{ old('serial_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'serial_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                                        Leírás</label>
                                                    <textarea class="zm_textarea form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description">{{ old('description') }}</textarea>
                                                    @include('alerts.feedback', ['field' => 'description'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('failure_description') ? ' has-danger' : '' }}">
                                                        Hibaleírás</label>
                                                    <textarea class="zm_textarea form-control{{ $errors->has('failure_description') ? ' is-invalid' : '' }}" name="failure_description">{{ old('failure_description') }}</textarea>
                                                    @include('alerts.feedback', ['field' => 'failure_description'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('entry_date') ? ' has-danger' : '' }}">
                                                        Bekerülés dátuma</label>
                                                    <input type="date" class="form-control{{ $errors->has('entry_date') ? ' is-invalid' : '' }}" name="entry_date" value="{{ old('entry_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'entry_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('withdrawal_date') ? ' has-danger' : '' }}">
                                                        Kikerülés dátuma</label>
                                                    <input type="date" class="form-control{{ $errors->has('withdrawal_date') ? ' is-invalid' : '' }}" name="withdrawal_date" value="{{ old('withdrawal_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'withdrawal_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('value') ? ' has-danger' : '' }}">
                                                        Érték</label>
                                                    <input type="number" class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}" name="value" value="{{ old('value') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'value'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" data-gtm-form-interact-field-id="1" name="review_needed" value="1">
                                                        <span class="form-check-sign"></span>
                                                        Felülvizsgálat kell
                                                        </label>
                                                    </div>

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

            $('.save_tool').click(function(){
                $('#tool_form').submit();
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