@extends('layouts.app', ['activePage' => 'tag', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Címke szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Címke szerkesztése</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('tag.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="post" action="{{ route('tag.update', $tag->id) }}" >
                        @csrf 
                        @method('patch')
                        <fieldset>
                            <div class="form-group">
                                <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    Címke</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="30" value="{{old('name', $tag->name)}}" placeholder="{{ __('Name') }}" required autofocus>
                                @include('alerts.feedback', ['field' => 'name'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label for="example-color-input" class="form-control-label{{ $errors->has('color') ? ' has-danger' : '' }}">Szín</label>
                                <input class="form-control{{ $errors->has('color') ? ' is-invalid' : '' }}" type="color" value="{{ old('color', $tag->color) }}" name="color" id="example-color-input" required>
                                @include('alerts.feedback', ['field' => 'color'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-warning">Mentés</button>
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