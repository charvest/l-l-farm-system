<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'L&L Farm') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-green-50 text-gray-900">
<div class="min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="h-1 bg-green-600"></div>
        <div class="mx-auto max-w-6xl px-4">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-extrabold text-green-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white">🛠️</span>
                    <span>Admin Panel</span>
                </a>

                <nav class="flex items-center gap-6 text-sm font-semibold text-slate-700">
                    <a class="hover:text-green-800 transition" href="{{ route('admin.dashboard') }}">Dashboard</a>

                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-700 transition font-semibold">
                            Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <main class="w-full flex-1">
        @yield('content')
    </main>
</div>
</body>
</html>
