<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'L&L Farm') }}</title>

    {{-- Bootstrap (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Sticky slider nav styles --}}
    <link href="{{ asset('css/sticky-tabs.css') }}" rel="stylesheet">
</head>
<body>

{{-- Optional: small “global” ecommerce navbar (brand/cart/login) --}}
<nav class="navbar navbar-expand-lg bg-success navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">🐷 L&L Farm</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#globalNav" aria-controls="globalNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="globalNav" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Cart</a></li>
             @auth
   
    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">Profile</a></li>

    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
@endauth
            </ul>
        </div>
    </div>
</nav>

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/sticky-tabs.js') }}"></script>
</body>
</html>
