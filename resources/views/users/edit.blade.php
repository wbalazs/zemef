@extends('layouts.app', ['activePage' => 'user', 'activeButton' => 'users', 'title' => 'Zemef ERP', 'navName' => 'Felhasználó szerkesztése'])

@section('content')
    <div class="content">
        <div class="container-fluid mt--6">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Felhasználó szerkesztése</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <button type="submit" class="btn btn-success save_user"><i class="fa fa-save"></i>  Mentés</button>
                                    <a href="{{ route('user.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="user_form" method="post" action="{{ route('user.update', $user) }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                <h6 class="heading-small text-muted mb-4">Alap adatok</h6>
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">
                                            <i class="w3-xxlarge fa fa-user"></i>Név
                                        </label>
                                        <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="" value="{{ old('name', $user->name) }}" required autofocus>
        
                                        @include('alerts.feedback', ['field' => 'name'])
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-email">Email cím</label>
                                        <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="" value="{{ old('email', $user->email) }}" required>

                                        @include('alerts.feedback', ['field' => 'email'])
                                    </div>
                                    <div class="form-group{{ $errors->has('role_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">Csoport</label>
                                        <select data-style="btn-info btn-fill btn-block" name="role_id" id="input-role" class="selectpicker form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" placeholder="" required>
                                            <option value="">-</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ $role->id == old('role_id', $user->role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'role_id'])
                                    </div>
                                    <div class="form-group{{ $errors->has('employee_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">Munkavállaló</label>
                                        <select data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" name="employee_id" id="input-role" class="selectpicker form-control{{ $errors->has('employee_id') ? ' is-invalid' : '' }}" placeholder="">
                                            <option value="">-</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->employee_id }}" {{ $employee->employee_id == old('employee_id', $user->employee_id) ? 'selected' : '' }} >{{ $employee->lastname.' '.$employee->firstname }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'employee_id'])
                                    </div>
                                    <div class="form-group{{ $errors->has('sign_code') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-sign_code">Aláírókód</label>
                                        <input type="password" name="sign_code" id="input-sign_code" class="form-control{{ $errors->has('sign_code') ? ' is-invalid' : '' }}" placeholder="" value="" autocomplete="new-password">
                                    
                                        @include('alerts.feedback', ['field' => 'sign_code'])
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input @if($user->bank_contact) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="bank_contact">
                                            <span class="form-check-sign"></span>
                                            Banki kapcsolat
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                            <input @if($user->is_goldsmith) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="is_goldsmith">
                                            <span class="form-check-sign"></span>
                                            Ötvös
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('bank_accounts') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">Bankszámlák</label>
                                        <select title="- válasszon bankszámlákat -" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue" multiple data-style="btn-default btn-outline" data-menu-style="dropdown-blue" name="bank_accounts[]" id="input-role" class="selectpicker form-control{{ $errors->has('bank_accounts') ? ' is-invalid' : '' }}" placeholder="">
                                            @foreach($bank_accounts as $bank_account)
                                                <option @if(in_array($bank_account->bank_account_id, $user->bank_accounts->pluck('bank_account_id')->toArray())) selected @endif value="{{ $bank_account->bank_account_id }}" >{{ $bank_account->bank_account_number . ' ('.$bank_account->bank->name.')' }}</option>
                                            @endforeach
                                        </select>

                                        @include('alerts.feedback', ['field' => 'bank_accounts'])
                                    </div>






                                    <div class="form-group{{ $errors->has('photo') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">Profilkép</label> <br>
                                        @if( $user->picture) 
                                            <div class="pb-2" style="max-width:250px;">
                                                <img class="img-fluid img-thumbnail" src="{{ $user->profilePicture() }}" alt="profile">
                                            </div>
                                        @endif
                                        <div class="custom-file">
                                            <input type="file" name="photo" class="form-control-file{{ $errors->has('photo') ? ' is-invalid' : '' }}" id="input-picture" accept="image/*">
                                        </div>
        
                                        @include('alerts.feedback', ['field' => 'photo'])
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-password">Jelszó</label>
                                        <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="" value="">

                                        @include('alerts.feedback', ['field' => 'password'])
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-password-confirmation">Jelszó megerősítése</label>
                                        <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="" value="">
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