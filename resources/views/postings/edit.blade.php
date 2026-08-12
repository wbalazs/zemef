@extends('layouts.app', ['activePage' => 'posting', 'activeButton' => 'posting', 'title' => 'Zemef ERP', 'navName' => 'Kiküldetés szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="{{ route('posting.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    <form id="posting_form" method="post" action="{{ route('posting.update', $posting->posting_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Kiküldetés alap adatai</h6>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('employee_id') ? ' has-danger' : '' }}">
                                                                Munkavállaló</label>
                                                            <select disabled required title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="employee_id_select" class="selectpicker form-control{{ $errors->has('employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if((!$posting->employee_id && Auth::user()->employee->employee_id == $employee->employee_id) || $employee->employee_id == $posting->employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->employee_id }}">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('customer_id') ? ' has-danger' : '' }}">
                                                                Ügyfél</label>
                                                            <select required title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="customer_id" class="selectpicker form-control{{ $errors->has('customer_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($customers as $customer)
                                                                    <option @if(old('customer_id', $posting->customer_id) == $customer->customer_id) selected @endif value="{{ $customer->customer_id }}" >{{ $customer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('vehicle_id') ? ' has-danger' : '' }}">
                                                                Jármű</label>
                                                            <select required title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="vehicle_id" class="selectpicker form-control{{ $errors->has('vehicle_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($vehicles as $vehicle)
                                                                    <option @if(old('vehicle_id', $posting->vehicle_id) == $vehicle->vehicle_id) selected @endif value="{{ $vehicle->vehicle_id }}" >{{ $vehicle->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('year') ? ' has-danger' : '' }}">
                                                                Kiküldetés dátuma</label>
                                                            <input required type="date" class="form-control{{ $errors->has('posting_date') ? ' is-invalid' : '' }}" name="posting_date" value="{{ old('posting_date', $posting->posting_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'posting_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('from_to') ? ' has-danger' : '' }}">
                                                                Honnan - hová</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('from_to') ? ' is-invalid' : '' }}" name="from_to">{{ old('from_to', $posting->from_to) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'from_to'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                                                                Utazás célja</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}" name="purpose">{{ old('purpose', $posting->purpose) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'purpose'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('distance') ? ' has-danger' : '' }}">
                                                                Távolság (km)</label>
                                                            <input required type="number" class="form-control{{ $errors->has('distance') ? ' is-invalid' : '' }}" name="distance" value="{{ old('distance', $posting->distance) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'distance'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('comment') ? ' has-danger' : '' }}">
                                                                Megjegyzés</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment">{{ old('comment', $posting->comment) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'comment'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                
                                </div>

                                <button type="button" class="btn btn-success save_posting">Mentés</button>
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
        

            $('.save_posting').click(function(){
                $('#posting_form').submit();
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