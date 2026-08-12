@extends('layouts.app',  ['activePage' => 'role', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Csoport létrehozása'])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Csoport létrehozása</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('role.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="post" action="{{ route('role.store') }}" >
                        @csrf
                        <h6 class="heading-small text-muted mb-4">Csoport adatai</h6>
                        <fieldset>
                            <div class="form-group">
                                <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    Megnevezés</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="30" value="{{ old('name') }}" placeholder="{{ __('Name') }}" required autofocus>
                                @include('alerts.feedback', ['field' => 'name'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">Leírás</label>
                                <textarea name="description"  class="form-control{{ $errors->has('description') ? ' has-error is-invalid' : '' }}" rows="3" maxlength="300" placeholder="{{ __('Description') }}" required autofocus> {{old('description')}}</textarea>
                                <small class="form-text text-muted"></small>
                                @include('alerts.feedback', ['field' => 'description'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-warning">Csoport létrehozása</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection