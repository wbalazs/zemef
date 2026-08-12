@extends('layouts.app', ['activePage' => 'holiday', 'activeButton' => 'holiday', 'title' => 'Zemef ERP', 'navName' => 'Szabadságigény felvétele' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Új szabadság</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('holiday.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form action="{{ route('holiday.store') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Igényelhető szabadság: {{ $employeeHoliday->holiday_remainings }} nap</h6>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('start_date') ? ' has-danger' : '' }}">
                                                        Dátum-tól</label>
                                                    <input type="date" class="form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" name="start_date" value="{{ old('start_date') }}" required>
                                                    @include('alerts.feedback', ['field' => 'start_date'])
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('end_date') ? ' has-danger' : '' }}">
                                                        Dátum-ig</label>
                                                    <input type="date" class="form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}" name="end_date" value="{{ old('end_date') }}" required>
                                                    @include('alerts.feedback', ['field' => 'end_date'])
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('notes') ? ' has-danger' : '' }}">
                                                        Megjegyzés</label>
                                                    <input type="text" class="form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" name="notes" maxlength="100" value="{{ old('notes') }}">
                                                    @include('alerts.feedback', ['field' => 'notes'])
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-lg-6 text-left">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Szabadság mentése</button>
                                        </div>
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
    <script type="text/javascript">
        $(document).ready(function() {

            $('.save_business').click(function(){
                $('#business_form').submit();
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