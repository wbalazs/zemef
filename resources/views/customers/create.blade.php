@extends('layouts.app', ['activePage' => 'customer', 'activeButton' => 'customer', 'title' => 'Zemef ERP', 'navName' => 'Ügyfél létrehozása' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Ügyfél létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_customer">Mentés</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="customer_form" method="post" action="{{ route('customer.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Ügyfél alap adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Név</label>
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name') }}" placeholder="" required autofocus>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-2">
                                            <fieldset>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                    <input checked class="form-check-input" type="radio" name="is_company" id="is_company0" value="0" data-gtm-form-interact-field-id="0">
                                                    <span class="form-check-sign"></span>
                                                    Magánszemély
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="is_company" id="is_company1" value="1" data-gtm-form-interact-field-id="0">
                                                    <span class="form-check-sign"></span>
                                                    Cég
                                                    </label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('mothers_name') ? ' has-danger' : '' }}">
                                                        Anyja neve</label>
                                                    <input type="text" class="only_private form-control{{ $errors->has('mothers_name') ? ' is-invalid' : '' }}" name="mothers_name" maxlength="100" value="{{ old('mothers_name') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'mothers_name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('birth_place') ? ' has-danger' : '' }}">
                                                        Születési hely</label>
                                                    <input type="text" class="only_private form-control{{ $errors->has('birth_place') ? ' is-invalid' : '' }}" name="birth_place" maxlength="100" value="{{ old('birth_place') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'birth_place'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('birth_date') ? ' has-danger' : '' }}">
                                                        Születési idő</label>
                                                    <input type="date" class="only_private form-control{{ $errors->has('birth_date') ? ' is-invalid' : '' }}" name="birth_date" value="{{ old('birth_date') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'birth_date'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('tax_identity') ? ' has-danger' : '' }}">
                                                        Adóazonosítójel</label>
                                                    <input type="text" class="only_private form-control{{ $errors->has('tax_identity') ? ' is-invalid' : '' }}" name="tax_identity" maxlength="10" inputmode="numeric" pattern="^\d{10}$" value="{{ old('tax_identity') }}" placeholder="1234567890">
                                                    @include('alerts.feedback', ['field' => 'tax_identity'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('card_number') ? ' has-danger' : '' }}">
                                                        Kártyaszám</label>
                                                    <input type="text" class="form-control{{ $errors->has('card_number') ? ' is-invalid' : '' }}" name="card_number" maxlength="100" value="{{ old('card_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'card_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-4">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('identity_card_number') ? ' has-danger' : '' }}">
                                                        Személyi igazolvány szám</label>
                                                    <input type="text" class="only_private form-control{{ $errors->has('identity_card_number') ? ' is-invalid' : '' }}" name="identity_card_number" maxidentity_card_number="100" value="{{ old('identity_card_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'identity_card_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('web_address') ? ' has-danger' : '' }}">
                                                        Web cím</label>
                                                    <input type="text" class="only_company form-control{{ $errors->has('web_address') ? ' is-invalid' : '' }}" name="web_address" maxlength="100" value="{{ old('web_address') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'web_address'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('company_registration_number') ? ' has-danger' : '' }}">
                                                        Cégjegyzékszám</label>
                                                    <input type="number" class="only_company form-control{{ $errors->has('company_registration_number') ? ' is-invalid' : '' }}" name="company_registration_number" value="{{ old('company_registration_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'company_registration_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('tax_number') ? ' has-danger' : '' }}">
                                                        Adószám</label>
                                                    <input type="text" class="only_company form-control{{ $errors->has('tax_number') ? ' is-invalid' : '' }}" name="tax_number" maxlength="12" inputmode="numeric" pattern="^\d{7}-\d-\d{2}$" value="{{ old('tax_number') }}" placeholder="1234567-8-90">
                                                    @include('alerts.feedback', ['field' => 'tax_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('registration_number') ? ' has-danger' : '' }}">
                                                        Nyilvántartási szám</label>
                                                    <input type="text" class="only_company form-control{{ $errors->has('registration_number') ? ' is-invalid' : '' }}" name="registration_number" value="{{ old('registration_number') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'registration_number'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('email') ? ' has-danger' : '' }}">
                                                        Email cím</label>
                                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" maxlength="100" value="{{ old('email') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'email'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                                        Telefonszám</label>
                                                    <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" maxlength="100" value="{{ old('phone') }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'phone'])
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
                                                    <textarea rows="8" class="zm_textarea form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment">{{ old('comment') }}</textarea>
                                                    @include('alerts.feedback', ['field' => 'comment'])
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
                            
                            <div class="col-lg-12">
                                <div class="card-body ">
                                    <div class="col-lg-12">
                                        
                                        <fieldset>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-warning">Mentés</button>
                                            </div>
                                        </fieldset>
                                        
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

            function formatTaxIdentity(rawValue) {
                return (rawValue || '').replace(/\D/g, '').substring(0, 10);
            }

            function formatTaxNumber(rawValue) {
                let digits = (rawValue || '').replace(/\D/g, '').substring(0, 10);

                if (digits.length <= 7) {
                    return digits;
                }

                if (digits.length <= 8) {
                    return digits.substring(0, 7) + '-' + digits.substring(7);
                }

                return digits.substring(0, 7) + '-' + digits.substring(7, 8) + '-' + digits.substring(8, 10);
            }

            const taxIdentityInput = $('input[name=tax_identity]');
            taxIdentityInput.on('input', function() {
                $(this).val(formatTaxIdentity($(this).val()));
            });
            taxIdentityInput.val(formatTaxIdentity(taxIdentityInput.val()));

            const taxNumberInput = $('input[name=tax_number]');
            taxNumberInput.on('input', function() {
                $(this).val(formatTaxNumber($(this).val()));
            });
            taxNumberInput.val(formatTaxNumber(taxNumberInput.val()));

            $('.save_customer').click(function(){
                $('#customer_form').submit();
            });

            $('.only_private').prop('disabled', false);
            $('.only_company').prop('disabled', true);

            $('input[name=is_company]').change(function(){
                if($('input[name="is_company"]:checked').val() == 0){
                    $('.only_private').prop('disabled', false);
                    $('.only_company').prop('disabled', true);
                } else {
                    $('.only_private').prop('disabled', true);
                    $('.only_company').prop('disabled', false);
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
        });
    </script>
@endpush