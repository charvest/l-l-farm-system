@extends('layouts.app')

@section('content')
@php
    $img = \App\Support\ProductImages::urlFor($product);
    $fallback = asset('images/placeholders/pig.jpg');

    $stock = (int) ($product->stock ?? $product->quantity ?? 0);
    $inStock = $stock > 0;

    $maxQty = $inStock ? $stock : 1;
    $maxLabel = $stock;

    $pricePhp = \App\Support\Money::php($product->price);
@endphp

<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $product->type ?? 'Farm Product' }}</p>
        </div>

        <a href="{{ route('products.index') }}"
           class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
            ← Back to Products
        </a>
    </div>

    @if(session('success'))
        <div class="mt-5 rounded-xl bg-green-100 px-4 py-3 text-green-900 ring-1 ring-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-8 grid gap-8 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5">
            <div class="aspect-[4/3] overflow-hidden rounded-xl bg-gray-50">
                <img
                    src="{{ $img }}"
                    alt="{{ $product->name }}"
                    loading="lazy"
                    class="h-full w-full object-cover"
                    onerror="this.src='{{ $fallback }}';"
                />
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-gray-500">Price</div>
                    <div class="mt-1 text-3xl font-extrabold text-green-800">
                        {{ $pricePhp }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-sm font-semibold text-gray-500">Stock</div>
                    <div class="mt-1 text-lg font-extrabold {{ $inStock ? 'text-green-800' : 'text-red-600' }}">
                        {{ $stock }} {{ $inStock ? 'available' : 'out of stock' }}
                    </div>
                </div>
            </div>

            @if(!empty($product->description))
                <div class="mt-6">
                    <div class="text-sm font-semibold text-gray-500">Description</div>
                    <p class="mt-2 text-sm leading-relaxed text-gray-700">{{ $product->description }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-7 space-y-4">
                @csrf

                <div class="flex items-center gap-3">
                    <label for="quantity" class="text-sm font-extrabold text-gray-900">Quantity</label>
                    <input
                        id="quantity"
                        name="quantity"
                        type="number"
                        min="1"
                        max="{{ $maxQty }}"
                        value="1"
                        {{ $inStock ? '' : 'disabled' }}
                        class="w-28 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-bold text-gray-900 focus:border-green-600 focus:ring-green-600"
                    />
                    <span class="text-xs text-gray-500">Max: {{ $maxLabel }}</span>
                </div>

                <button
                    type="submit"
                    {{ $inStock ? '' : 'disabled' }}
                    class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>
@endsection