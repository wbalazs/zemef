@extends('layouts.app', ['activePage' => 'product_type', 'activeButton' => 'product_type', 'title' => 'Zemef ERP', 'navName' => 'Termék típus szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Termék típus szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="/master/product_types" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <form id="master_form" method="post" action="/master/save_product_type" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="product_type_id" value="{{ $product_type->product_type_id }}">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-body ">
                                <h6 class="heading-small text-muted mb-4">Termék típus adatai</h6>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                    Megnevezés</label>

                                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $product_type->name) }}" required>
                                                @include('alerts.feedback', ['field' => 'name'])
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
                                                        <input @if(old('status', $product_type->status)) checked @endif class="form-check-input" type="checkbox" value="1" name="status">
                                                        <span class="form-check-sign"></span>
                                                        Aktív
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
                                <div class="row"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success save_master">Mentés</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.save_master').click(function(){
                $('#master_form').submit();
            });

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
