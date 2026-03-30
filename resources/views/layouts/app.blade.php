<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'L&L Farm') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-green-50 text-gray-900">
@php
    /** Cart badge total qty (session cart) */
    $cartCount = 0;
    foreach ((array) session('cart', []) as $row) {
        $cartCount += max(0, (int) ($row['quantity'] ?? 0));
    }
@endphp

<div id="top"></div>

{{-- Toast (uses Alpine if available; if Alpine isn't loaded it will just display normally) --}}
@if(session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2200)"
        x-show="show"
        x-transition.opacity.duration.300ms
        class="fixed right-4 top-4 z-50 rounded-xl bg-green-700 px-4 py-3 text-sm font-extrabold text-white shadow-lg"
    >
        {{ session('success') }}
    </div>
@endif

<div class="min-h-screen flex flex-col">
    {{-- Herbruck's-inspired header: thin accent + clean white nav --}}
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="h-1 bg-green-600"></div>

        <div class="mx-auto max-w-6xl px-4">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold tracking-wide text-green-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white">🐷</span>
                    <span>L&amp;L Farm</span>
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-700">
                    <a class="hover:text-green-800 transition {{ request()->routeIs('home') ? 'text-green-800' : '' }}"
                       href="{{ route('home') }}">Home</a>

                    <a class="hover:text-green-800 transition {{ request()->routeIs('products.*') ? 'text-green-800' : '' }}"
                       href="{{ route('products.index') }}">Products</a>

                    

                    @auth
                    
                        <a class="hover:text-green-800 transition {{ request()->routeIs('profile.*') ? 'text-green-800' : '' }}"
                           href="{{ route('profile.edit') }}">Profile</a>
                              <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit"
                class="hover:text-red-700 transition font-semibold text-slate-700">
            Logout
        </button>
                    @endauth

                    @guest
                        @if (Route::has('login'))
                            <a class="hover:text-green-800 transition {{ request()->routeIs('login') ? 'text-green-800' : '' }}"
                               href="{{ route('login') }}">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="hover:text-green-800 transition {{ request()->routeIs('register') ? 'text-green-800' : '' }}"
                               href="{{ route('register') }}">Register</a>
                        @endif
                    @endguest
                </nav>

                {{-- Right CTA --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}"
                       class="hidden sm:inline-flex items-center justify-center rounded-full bg-green-700 px-5 py-2 text-sm font-bold text-white shadow hover:bg-green-800 transition">
                        Shop Now
                    </a>

                    <a href="{{ route('cart.index') }}"
                       class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-slate-700 hover:bg-gray-50 transition"
                       aria-label="Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6h15l-1.5 9h-12z"/>
                            <path d="M6 6l-2-4"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="18" cy="20" r="1"/>
                        </svg>

                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 grid h-5 min-w-[1.25rem] place-items-center rounded-full bg-red-600 px-1 text-[11px] font-extrabold text-white">
                                {{ $cartCount > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Mobile nav (simple + clean) --}}
            <nav class="md:hidden flex items-center gap-5 overflow-x-auto border-t border-gray-100 py-2 text-sm font-semibold text-slate-700">
                <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('home') ? 'text-green-800' : '' }}"
                   href="{{ route('home') }}">Home</a>
                <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('products.*') ? 'text-green-800' : '' }}"
                   href="{{ route('products.index') }}">Products</a>
                <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('cart.*') ? 'text-green-800' : '' }}"
                   href="{{ route('cart.index') }}">
                    Cart
                    @if($cartCount > 0)
                        <span class="ml-2 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-extrabold text-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>
@auth
    <a class="hover:text-green-800 transition {{ request()->routeIs('profile.*') ? 'text-green-800' : '' }}"
       href="{{ route('profile.edit') }}">Profile</a>

    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="hover:text-red-700 transition font-semibold text-slate-700">
            Logout
        </button>
    </form>
@endauth

           @auth
    <a class="hover:text-green-800 transition {{ request()->routeIs('profile.*') ? 'text-green-800' : '' }}"
       href="{{ route('profile.edit') }}">Profile</a>

    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit"
                class="hover:text-red-700 transition font-semibold text-slate-700">
            Logout
        </button>
    </form>
@endauth


                @guest
                    @if (Route::has('login'))
                        <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('login') ? 'text-green-800' : '' }}"
                           href="{{ route('login') }}">Login</a>
                    @endif
                    @if (Route::has('register'))
                        <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('register') ? 'text-green-800' : '' }}"
                           href="{{ route('register') }}">Register</a>
                    @endif
                @endguest
            </nav>
        </div>
    </header>

    <main class="w-full flex-1">
        @yield('content')
    </main>

    <x-site-footer />
</div>
</body>
</html>