@extends('layouts.app', ['activePage' => 'nav_fuel', 'activeButton' => 'nav_fuel', 'title' => 'Zemef ERP', 'navName' => 'Üzemanyagár szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Üzemanyagár szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            
                            <a href="/master/nav_fuels" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                    <form id="master_form" method="post" action="/master/save_nav_fuel" enctype="multipart/form-data">
                        @csrf

                        <input id="master_form" type="hidden" name="fuel_id" value="{{ $nav_fuel->fuel_id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body ">
                                    <h6 class="heading-small text-muted mb-4">Üzemanyagár adatai</h6>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('year') ? ' has-danger' : '' }}">
                                                        Év</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('year') ? ' is-invalid' : '' }}" name="year" min="2022" max="2099" value="{{ old('year', $nav_fuel->year) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'year'])
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label class="form-control-label{{ $errors->has('year') ? ' has-danger' : '' }}">
                                                        Hónap</label>
                                                    
                                                    <select class="select2 form-control" name="month">
                                                        <option @if($nav_fuel->month == 1) selected @endif value="1">Január</option>
                                                        <option @if($nav_fuel->month == 2) selected @endif value="2">Február</option>
                                                        <option @if($nav_fuel->month == 3) selected @endif value="3">Március</option>
                                                        <option @if($nav_fuel->month == 4) selected @endif value="4">Április</option>
                                                        <option @if($nav_fuel->month == 5) selected @endif value="5">Május</option>
                                                        <option @if($nav_fuel->month == 6) selected @endif value="6">Június</option>
                                                        <option @if($nav_fuel->month == 7) selected @endif value="7">Jűlius</option>
                                                        <option @if($nav_fuel->month == 8) selected @endif value="8">Augusztus</option>
                                                        <option @if($nav_fuel->month == 9) selected @endif value="9">Szeptember</option>
                                                        <option @if($nav_fuel->month == 10) selected @endif value="10">Október</option>
                                                        <option @if($nav_fuel->month == 11) selected @endif value="11">November</option>
                                                        <option @if($nav_fuel->month == 12) selected @endif value="12">December</option>
                                                    </select>

                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                   <label class="form-control-label{{ $errors->has('benzin95') ? ' has-danger' : '' }}">
                                                        Ólmozatlan motorbenzin ESZ-95</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('benzin95') ? ' is-invalid' : '' }}" name="benzin95" value="{{ old('benzin95', $nav_fuel->benzin95) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'benzin95'])

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <div class="form-group">

                                                   <label class="form-control-label{{ $errors->has('dizel') ? ' has-danger' : '' }}">
                                                        Gázolaj</label>
                                                    
                                                    <input type="number" class="form-control{{ $errors->has('dizel') ? ' is-invalid' : '' }}" name="dizel" value="{{ old('dizel', $nav_fuel->dizel) }}" placeholder="" required>
                                                    @include('alerts.feedback', ['field' => 'dizel'])

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