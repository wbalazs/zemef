@extends('layouts.app', ['activePage' => 'payment', 'activeButton' => 'payment', 'title' => 'Zemef ERP', 'navName' => 'Fizetési módok szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Fizetési módok szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/payments" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_payment" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="payment_id" value="{{ $payment->payment_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Fizetési módok adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Megnevezés</label>
                                                    
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $payment->name) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-3">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('days') ? ' has-danger' : '' }}">
                                                        Napok száma</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('days') ? ' is-invalid' : '' }}" name="days" maxlength="100" value="{{ old('days', $payment->days) }}" placeholder="">
                                                    @include('alerts.feedback', ['field' => 'days'])
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
                                                        <input @if($payment->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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