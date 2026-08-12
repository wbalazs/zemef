@extends('layouts.app', ['activePage' => 'city', 'activeButton' => 'city', 'title' => 'Zemef ERP', 'navName' => 'Település szerkesztése' ])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header ">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">Település szerkesztése</h3>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('master.cities.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Vissza</a>
                    </div>
                </div>
            </div>
            <form id="master_form" method="post" action="{{ route('master.cities.store') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="city_id" value="{{ $city->id ?? '' }}">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card-body ">
                            <h6 class="heading-small text-muted mb-4">Település adatai</h6>
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('country_id') ? ' has-danger' : '' }}">Ország</label>
                                            <select class="form-control{{ $errors->has('country_id') ? ' is-invalid' : '' }}" name="country_id" value="{{ old('name', $city->country_id ?? '') }}" placeholder="" required autofocus>
                                                <option value="">Válasszon</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" {{ old('country_id', $city->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'country_id'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('zip') ? ' has-danger' : '' }}">Irányítószám</label>
                                            <input type="hidden" name="zip_id" value="{{ old('zip_id', $zip->id ?? '0') }}">
                                            <input type="text" class="form-control{{ $errors->has('zip') ? ' is-invalid' : '' }}" name="zip" maxlength="100" value="{{ old('zip', $zip->name ?? '') }}" placeholder="" required>
                                            @include('alerts.feedback', ['field' => 'zip'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="form-control-label{{ $errors->has('city') ? ' has-danger' : '' }}">Település</label>
                                            <input type="hidden" name="city_id" value="{{ old('city_id', $city->id ?? '0') }}">
                                            <input type="text" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" maxlength="100" value="{{ old('city', $city->name ?? '') }}" placeholder="" required autofocus>
                                            @include('alerts.feedback', ['field' => 'city'])
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-success save_master"><i class="fa fa-save"></i> Mentés</button>
                    </div>
                </div>
            </form>
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