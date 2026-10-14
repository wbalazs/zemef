<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#164b40">
    <title>@yield('title', 'Ügyfélkártya') | Zemef</title>
    <link rel="icon" href="{{ asset('img/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/fontawesome5/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mobile-customer.css') }}" rel="stylesheet">
</head>
<body>
    <div class="portal-shell">
        <header class="portal-header">
            <a class="brand" href="{{ route('mobile.customer') }}"><i class="fas fa-gem" aria-hidden="true"></i> <span>ZEMEF<span class="brand-caption">Ügyfélkártya</span></span></a>
            @yield('header-actions')
        </header>
        <main id="main-content">@yield('content')</main>
        <footer class="portal-footer">Zemef &copy; {{ date('Y') }}</footer>
    </div>
    @yield('scripts')
</body>
</html>