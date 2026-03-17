@extends('layouts.app')

@section('content')

<div class="bg-green-100 py-24 text-center rounded-xl shadow-inner">
    <h1 class="text-5xl font-extrabold text-green-900 mb-6">
        Fresh From Our Farm 🌾
    </h1>

    <p class="text-lg text-gray-700 mb-8">
        Reserve Piglets • Buy Live Pigs • Fresh Chickens • Fruits & Vegetables
    </p>

    <a href="/products"
       class="bg-green-700 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-green-900 transition">
        Shop Now
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">

    <a href="/products?category=pig"
       class="bg-white p-6 rounded-xl shadow text-center hover:shadow-xl hover:scale-105 transition transform">
        <h2 class="text-xl font-bold">🐖 Pigs & Piglets</h2>
        <p class="text-gray-600 mt-2">Reserve piglets and buy live pigs</p>
    </a>

    <a href="/products?category=chicken"
       class="bg-white p-6 rounded-xl shadow text-center hover:shadow-xl hover:scale-105 transition transform">
        <h2 class="text-xl font-bold">🐔 Chickens</h2>
        <p class="text-gray-600 mt-2">Live chicken & chicken for meat</p>
    </a>

    <a href="/products?category=produce"
       class="bg-white p-6 rounded-xl shadow text-center hover:shadow-xl hover:scale-105 transition transform">
        <h2 class="text-xl font-bold">🍌 Produce</h2>
        <p class="text-gray-600 mt-2">Fresh fruits and vegetables</p>
    </a>

</div>

@endsection