@extends('layouts.app', ['activePage' => 'currency', 'activeButton' => 'currency', 'title' => 'Zemef ERP', 'navName' => 'Deviza szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Deviza szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/currencies" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_currency" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="currency_id" value="{{ $currency->currency_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Deviza adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Név</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $currency->name) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('sign') ? ' has-danger' : '' }}">
                                                        Jel</label>
                                                    <input type="text" class="form-control{{ $errors->has('sign') ? ' is-invalid' : '' }}" name="sign" maxlength="40" value="{{ old('sign', $currency->sign) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'sign'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('value') ? ' has-danger' : '' }}">
                                                        Árfolyam</label>
                                                    <input type="number" class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}" name="value" maxlength="100" value="{{ old('value', $currency->value) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'value'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input @if($currency->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktív
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input @if($currency->default) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="default">
                                                        <span class="form-check-sign"></span>
                                                        Alapértelmezett?
                                                        </label>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">

                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input @if($currency->editable) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="editable">
                                                        <span class="form-check-sign"></span>
                                                        Szerkeszthető?
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