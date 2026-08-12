@extends('layouts.app', ['activePage' => 'booking', 'activeButton' => 'booking', 'title' => 'Zemef ERP', 'navName' => 'Foglalási beállítás szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Foglalási beállítás szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/bookings" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_booking" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Foglalási beállítás adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                        Megnevezés</label>
                                                    
                                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="200" value="{{ old('name', $booking->name) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'name'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('min_amount') ? ' has-danger' : '' }}">
                                                        Minimum összeg</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('min_amount') ? ' is-invalid' : '' }}" name="min_amount" value="{{ old('min_amount', $booking->min_amount) }}" required>
                                                    @include('alerts.feedback', ['field' => 'min_amount'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('max_amount') ? ' has-danger' : '' }}">
                                                        Maximum összeg</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('max_amount') ? ' is-invalid' : '' }}" name="max_amount" value="{{ old('max_amount', $booking->max_amount) }}" >
                                                    @include('alerts.feedback', ['field' => 'max_amount'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('duration_months') ? ' has-danger' : '' }}">
                                                        Időtartam (hónap)</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('duration_months') ? ' is-invalid' : '' }}" name="duration_months" value="{{ old('duration_months', $booking->duration_months) }}" required>
                                                    @include('alerts.feedback', ['field' => 'duration_months'])
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