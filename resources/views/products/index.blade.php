@extends('layouts.app')

@section('content')

<div class="container mx-auto mt-10">

<h1 class="text-3xl font-bold text-center mb-8">🌾 Farm Products</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

@foreach($products as $product)

<div class="bg-white shadow-lg rounded-lg p-6 text-center">

<h2 class="text-xl font-bold mb-2">
{{ $product->name }}
</h2>

<p class="text-green-700 font-semibold mb-3">
₱{{ $product->price }}
</p>

<form action="{{ route('cart.add', $product->id) }}" method="POST">

@csrf

<button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">

Add to Cart

</button>

</form>

</div>

@endforeach

</div>

</div>

@endsection