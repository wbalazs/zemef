@extends('layouts.app', ['activePage' => 'users', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Felhasználó létrehozása'])

@section('content')
    <div class="content">
        <div class="container-fluid mt--6">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Felhasználó létrehozása</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <button type="submit" class="btn btn-success save_user">Mentés</button>
                                    <a href="{{ route('user.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="user_form" method="post" action="{{ route('user.store') }}" enctype="multipart/form-data">
                                @csrf
                                <h6 class="heading-small text-muted mb-4">Felhasználó adatai</h6>
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">
                                            <i class="w3-xxlarge fa fa-user"></i>{{ __('users.Name') }}
                                        </label>
                                        <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Name') }}" value="{{ old('name') }}" required autofocus>
        
                                        @include('alerts.feedback', ['field' => 'name'])
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-email">{{ __('users.Email') }}</label>
                                        <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Email') }}" value="{{ old('email') }}" required autofocus>

                                        @include('alerts.feedback', ['field' => 'email'])
                                    </div>
                                    <div class="form-group{{ $errors->has('role_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">{{ __('users.Role') }}</label>
                                        <select name="role_id" id="input-role" class="selectpicker form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Role') }}" required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ $role->id == old('role_id') ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'role_id'])
                                    </div>
                                    <div class="form-group{{ $errors->has('employee_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">Munkavállaló</label>
                                        <select name="employee_id" id="input-role" class="selectpicker form-control{{ $errors->has('employee_id') ? ' is-invalid' : '' }}" placeholder="">
                                            <option value="">-</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}">{{ $employee->lastname.' '.$employee->firstname }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'role_id'])
                                    </div>
                                    <div class="form-group{{ $errors->has('sign_code') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-sign_code">Aláírókód</label>
                                        <input type="password" name="sign_code" id="input-sign_code" class="form-control{{ $errors->has('sign_code') ? ' is-invalid' : '' }}" placeholder="" value="" autocomplete="new-password">
                                    
                                        @include('alerts.feedback', ['field' => 'sign_code'])
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="bank_contact">
                                            <span class="form-check-sign"></span>
                                            Banki kapcsolat
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_goldsmith">
                                            <span class="form-check-sign"></span>
                                            Ötvös
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('bank_accounts') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">Bankszámlák</label>
                                        <select title="- válasszon bankszámlákat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="bank_accounts[]" id="input-role" class="selectpicker form-control{{ $errors->has('bank_accounts') ? ' is-invalid' : '' }}" placeholder="">
                                            @foreach($bank_accounts as $bank_account)
                                                <option value="{{ $bank_account->bank_account_id }}" >{{ $bank_account->bank_account_number . ' ('.$bank_account->bank_account_number.')' }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'bank_accounts'])
                                    </div>




                                    <div class="form-group{{ $errors->has('photo') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">{{ __('users.Photo') }}</label> <br>
                                        <div class="custom-file">
                                            <input type="file" name="photo" class="form-control-file{{ $errors->has('photo') ? ' is-invalid' : '' }}" id="input-picture" accept="image/*">
                                        </div>
        
                                        @include('alerts.feedback', ['field' => 'photo'])
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-password">{{ __('users.Password') }}</label>
                                        <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Password') }}" value="">

                                        @include('alerts.feedback', ['field' => 'password'])
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-password-confirmation">{{ __('users.Confirm New Password') }}</label>
                                        <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('users.Confirm Password') }}" value="">
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $('.save_user').click(function(){
                $('#user_form').submit();
            });

        });
    </script>
@endpush