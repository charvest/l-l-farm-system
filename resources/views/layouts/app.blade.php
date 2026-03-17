<!DOCTYPE html>
<html>
<head>
    <title>L&L Farm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-green-50">

<nav class="bg-green-800 text-white p-4">
    <div class="container mx-auto flex justify-between">

        <h1 class="font-bold text-xl">🐖 L&L Farm</h1>

        <div class="space-x-4">
            <a href="/">Home</a>
            <a href="/dashboard">Dashboard</a>
            <a href="{{ route('profile.edit') }}">Profile</a>
            <a href="{{ route('cart.index') }}">Cart</a>
            <a href="/products">Products</a>
        </div>

    </div>
</nav>

<!-- PAGE CONTENT WILL LOAD HERE -->
<div class="container mx-auto mt-6">
    @yield('content')
</div>

</body>
</html>