@extends('layouts.app', ['activePage' => 'customer_type', 'activeButton' => 'customer_type', 'title' => 'Zemef ERP', 'navName' => 'Ügyfél típus szerkesztése'])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Ügyfél típus szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success save_master">Mentés</button>
                            <a href="{{ route('customer_type.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form id="master_form" method="post" action="{{ route('customer_type.update', $customer_type->customer_type_id) }}" >
                        @csrf
                        @method('patch')
                        <fieldset>
                            <div class="form-group">
                                <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    Név</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="30" value="{{ old('name', $customer_type->name) }}" placeholder="" required autofocus>
                                @include('alerts.feedback', ['field' => 'name'])
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group{{ $errors->has('parent_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-role">Szülő ügyfél típus</label>
                                    <select title="- válasszon kategóriát -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="parent_id" id="input-role" class="selectpicker form-control{{ $errors->has('parent_id') ? ' is-invalid' : '' }}" placeholder="">
                                        @foreach($customer_types as $ct)
                                            <?php if($ct->customer_type_id == $customer_type->customer_type_id){ continue; } ?>
                                            <option @if($ct->customer_type_id == $customer_type->parent_id) selected @endif value="{{ $ct->customer_type_id }}" >{{ $ct->name }}</option>
                                        @endforeach
                                    </select>

                                    @include('alerts.feedback', ['field' => 'parent_id'])
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <fieldset>
                                    <div class="form-group">

                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input @if($customer_type->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
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

                    </form>
                </div>
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

        });
    </script>
@endpush