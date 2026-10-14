@extends('mobile.customer.layout')
@section('title', 'Bejelentkezés')
@section('content')
    <section class="login-section">
        <div class="login-mark" aria-hidden="true"><i class="fas fa-id-card"></i></div>
        <p class="eyebrow">Ügyfélkártya</p>
        <h1>Bejelentkezés</h1>
        <form method="post" action="{{ route('mobile.customer.authenticate') }}" class="login-form">
            @csrf
            @if($errors->any())
                <div class="form-error" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>{{ $errors->first() }}</span></div>
            @endif
            <div class="field">
                <label for="customer-email">Email cím</label>
                <input id="customer-email" name="email" type="email" autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" value="{{ old('email') }}" maxlength="255" required>
            </div>
            <div class="field">
                <label for="customer-card-code">Kártyakód</label>
                <div class="password-field">
                    <input id="customer-card-code" name="password" type="password" autocomplete="current-password" autocapitalize="none" spellcheck="false" maxlength="50" required>
                    <button id="toggle-card-code" class="icon-button" type="button" title="Kártyakód megjelenítése" aria-label="Kártyakód megjelenítése" aria-pressed="false"><i class="fas fa-eye" aria-hidden="true"></i></button>
                </div>
            </div>
            <button class="primary-button" type="submit">Belépés <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
        </form>
    </section>
@endsection
@section('scripts')
<script>
    document.getElementById('toggle-card-code').addEventListener('click', function() {
        const input = document.getElementById('customer-card-code');
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        this.setAttribute('aria-pressed', String(visible));
        const label = visible ? 'Kártyakód elrejtése' : 'Kártyakód megjelenítése';
        this.setAttribute('aria-label', label);
        this.title = label;
        this.querySelector('i').className = visible ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
</script>
@endsection