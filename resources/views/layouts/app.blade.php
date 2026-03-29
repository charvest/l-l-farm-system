{{-- =============================================================================
File: resources/views/layouts/app.blade.php
============================================================================= --}}
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
<div id="top"></div>

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

                    <a class="hover:text-green-800 transition {{ request()->routeIs('cart.*') ? 'text-green-800' : '' }}"
                       href="{{ route('cart.index') }}">Cart</a>

                    @auth
                        <a class="hover:text-green-800 transition {{ request()->routeIs('dashboard') ? 'text-green-800' : '' }}"
                           href="{{ route('dashboard') }}">Dashboard</a>

                        <a class="hover:text-green-800 transition {{ request()->routeIs('profile.*') ? 'text-green-800' : '' }}"
                           href="{{ route('profile.edit') }}">Profile</a>
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
                       class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-slate-700 hover:bg-gray-50 transition"
                       aria-label="Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6h15l-1.5 9h-12z"/>
                            <path d="M6 6l-2-4"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="18" cy="20" r="1"/>
                        </svg>
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
                   href="{{ route('cart.index') }}">Cart</a>

                @auth
                    <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('dashboard') ? 'text-green-800' : '' }}"
                       href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="whitespace-nowrap hover:text-green-800 {{ request()->routeIs('profile.*') ? 'text-green-800' : '' }}"
                       href="{{ route('profile.edit') }}">Profile</a>
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
