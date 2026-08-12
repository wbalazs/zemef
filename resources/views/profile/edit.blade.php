@extends('layouts.app', ['activePage' => 'profile', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Profil szerkesztése'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image" data-image="{{ auth()->user()->profilePicture() }}">
                <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
                <div class="row">

                    <div class="card col-md-8">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-0">Profil szerkesztése</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('profile.update') }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                @method('patch')
        
                                <h6 class="heading-small text-muted mb-4">Felhasználó adatai</h6>
        
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-name">
                                                    <i class="w3-xxlarge fa fa-user"></i>{{ __('users.Name') }}
                                                </label>
                                                <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Name') }}" value="{{ old('name', auth()->user()->name) }}" required autofocus>
                
                                                @include('alerts.feedback', ['field' => 'name'])
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-email"><i class="w3-xxlarge fa fa-envelope-o"></i>{{ __('users.Email') }}</label>
                                                <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Email') }}" value="{{ old('email', auth()->user()->email) }}" required>
                
                                                @include('alerts.feedback', ['field' => 'email'])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group{{ $errors->has('photo') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-name">{{ __('users.Photo') }}</label> <br>
                                                @if(auth()->user()->picture) 
                                                    <div class="pb-2" style="max-width:250px;">
                                                        <img class="img-fluid img-thumbnail" src="{{ auth()->user()->profilePicture() }}" alt="profile">
                                                    </div>
                                                @endif
                                                <div class="custom-file">
                                                    <input type="file" name="photo" class="form-control-file {{ $errors->has('photo') ? ' is-invalid' : '' }}" id="input-picture" accept="image/*">
                                                </div>
                
                                                @include('alerts.feedback', ['field' => 'photo'])
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group{{ $errors->has('signpicture') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-name">Aláírás</label> <br>
                                                <!-- Aláírópad -->
                                                <canvas id="signature-pad" width="400" height="200" style="border:1px solid #000;"></canvas>
                                                <!-- Rejtett input a canvas képadatokhoz -->
                                                <input type="hidden" name="signature" id="signature-input">
                                                <!-- Aláírás mentése -->
                                                <button type="button" id="delete-signature"><i class="fa fa-trash"></i> Törlés</button>
                                        
                                        
                                                @include('alerts.feedback', ['field' => 'signpicture'])
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-success mt-4"><i class="fa fa-save"></i> Adatok mentése</button>
                                        </div>
                                    </div>
                            </form>
                            <hr class="my-4" />

                            <form method="post" action="{{ route('profile.sign_code') }}" id="sign_code">
                                @csrf
                                @method('patch')

                                <h6 class="heading-small text-muted mb-4">Aláírókód</h6>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group{{ $errors->has('old_sign_code') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-name">Jelenlegi aláírókód</label>
                                            <input type="password" name="old_sign_code" id="input-name" class="form-control{{ $errors->has('old_sign_code') ? ' is-invalid' : '' }}" autocomplete="off" value="" required autofocus>
            
                                            @include('alerts.feedback', ['field' => 'old_sign_code'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group{{ $errors->has('sign_code') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-sign_code"><i class="w3-xxlarge fa fa-envelope-o"></i>Új aláírókód</label>
                                            <input type="password" name="sign_code" id="input-sign_code" class="form-control{{ $errors->has('sign_code') ? ' is-invalid' : '' }}" value="" required>
            
                                            @include('alerts.feedback', ['field' => 'sign_code'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group{{ $errors->has('sign_code_confirm') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-sign_code"><i class="w3-xxlarge fa fa-envelope-o"></i>Új aláírókód megerősítése</label>
                                            <input type="password" name="sign_code_confirmation" id="input-sign_code" class="form-control{{ $errors->has('sign_code_confirm') ? ' is-invalid' : '' }}" value="" required>
            
                                            @include('alerts.feedback', ['field' => 'sign_code_confirm'])
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-warning mt-4"><i class="fa fa-pencil"></i> Aláírókód megváltoztatása</button>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4" />
                            <form method="post" action="{{ route('profile.password') }}" id="profile">
                                @csrf
                                @method('patch')
        
                                <h6 class="heading-small text-muted mb-4">{{ __('users.Password') }}</h6>
        
                                @include('alerts.success', ['key' => 'password_status'])
                                @include('alerts.error_self_update', ['key' => 'not_allow_password'])
        
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-current-password">
                                                <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('users.Current Password') }}
                                            </label>
                                            <input type="password" name="old_password" id="input-current-password" class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}" placeholder="{{ __('users.Current Password') }}" value="" required>
            
                                            @include('alerts.feedback', ['field' => 'old_password'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-password">
                                                <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('users.New Password') }}
                                            </label>
                                            <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('users.New Password') }}" value="" required>
            
                                            @include('alerts.feedback', ['field' => 'password'])
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="input-password-confirmation">
                                                <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('users.Confirm New Password') }}
                                            </label>
                                            <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('users.Confirm New Password') }}" value="" required>
                                        </div>
                                    </div>
        
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-warning mt-4"><i class="fa fa-key"></i> Jelszó megváltoztatása</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="card-header no-padding">
                                <div class="card-image">
                                    <img src="{{ asset('img/full-screen-image-3.jpg') }}" alt="...">
                                </div>
                            </div>
                            <div class="card-body ">
                                <div class="author">
                                    <a href="#">
                                        <img class="avatar border-gray" src="{{ auth()->user()->profilePicture() }} " alt="...">
                                        <h5 class="card-title">{{ __(auth()->user()->name) }}</h5>
                                    </a>
                                    <p class="card-description">
                                        
                                    </p>
                                </div>
                                
                            </div>
                            <div class="card-footer ">
                                <hr>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let canvas = document.getElementById('signature-pad');
            let ctx = canvas.getContext('2d');
            let drawing = false;
            let signatureInput = document.getElementById('signature-input');

            // A canvas háttér beállítása fehérre
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Ellenőrzés, hogy van-e mentett aláírás és annak betöltése
            let signatureImageURL = "{{ auth()->user()->signPicture() }}";
            if (signatureImageURL) {
                let img = new Image();
                img.onload = function() {
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                };
                img.src = signatureImageURL;
            }

            // A vonalak simítása és a vonalvastagság beállítása
            ctx.lineJoin = ctx.lineCap = 'round';
            ctx.lineWidth = 2;

            function draw(e) {
                if (!drawing) return;
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            }

            canvas.addEventListener('mousedown', function(e) {
                drawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });

            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', function() {
                drawing = false;
                ctx.beginPath();
            });
            canvas.addEventListener('mouseout', function() {
                drawing = false;
                ctx.beginPath();
            });

            // Aláírás mentése az űrlap beküldésekor
            $('form').submit(function() {
                signatureInput.value = canvas.toDataURL('image/png');
            });

            // Aláírás törlése
            document.getElementById('delete-signature').addEventListener('click', function() {
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            });
        });
    </script>
@endpush

