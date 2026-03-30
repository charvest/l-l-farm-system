{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    $types = $types ?? collect();
    $filters = $filters ?? ['q' => '', 'type' => '', 'sort' => 'newest'];

    $q = (string) ($filters['q'] ?? '');
    $type = (string) ($filters['type'] ?? '');
    $sort = (string) ($filters['sort'] ?? 'newest');

    $fallback = asset('images/placeholders/pig.jpg');
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Farm Products</h1>
            <p class="mt-1 text-sm text-gray-600">Search, filter, and shop in Philippine Peso (₱).</p>
        </div>

        <form method="GET" class="w-full sm:w-auto">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-2">
                <div class="sm:col-span-1">
                    <label class="sr-only" for="q">Search</label>
                    <input
                        id="q"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Search products…"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm focus:border-green-600 focus:ring-green-600"
                    />
                </div>

                <div class="sm:col-span-1">
                    <label class="sr-only" for="type">Type</label>
                    <select
                        id="type"
                        name="type"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm focus:border-green-600 focus:ring-green-600"
                    >
                        <option value="">All types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <label class="sr-only" for="sort">Sort</label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm focus:border-green-600 focus:ring-green-600"
                    >
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name: A → Z</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name: Z → A</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-green-700 px-4 py-2 text-sm font-extrabold text-white shadow hover:bg-green-800 transition"
                >
                    Apply
                </button>

                @if($q !== '' || $type !== '' || $sort !== 'newest')
                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-gray-700 hover:text-gray-900">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
            @php
                $img = \App\Support\ProductImages::urlFor($product);
                $stock = (int) ($product->stock ?? $product->quantity ?? 0);
                $inStock = $stock > 0;
                $available = $product->available_date ?? $product->available_at ?? null;
                $availableText = $available ? (is_string($available) ? $available : $available->toDateString()) : null;
            @endphp

            <div class="group rounded-2xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden hover:shadow-md transition">
                <div class="relative aspect-[4/3] bg-gray-50">
                    <img
                        src="{{ $img }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                        class="h-full w-full object-cover"
                        onerror="this.src='{{ $fallback }}';"
                    />

                    <div class="absolute left-3 top-3 flex gap-2">
                        <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-extrabold text-gray-800 shadow">
                            {{ $product->type ?? 'Farm Product' }}
                        </span>

                        <span class="rounded-full {{ $inStock ? 'bg-green-700' : 'bg-red-600' }} px-3 py-1 text-xs font-extrabold text-white shadow">
                            {{ $inStock ? 'In stock' : 'Out of stock' }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-base font-extrabold text-gray-900 leading-snug">
                            {{ $product->name }}
                        </h2>
                        <span class="shrink-0 rounded-full bg-green-50 px-3 py-1 text-sm font-extrabold text-green-800">
                            {{ \App\Support\Money::php($product->price) }}
                        </span>
                    </div>

                    <div class="mt-3 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-500">Stock</span>
                            <span class="font-extrabold {{ $inStock ? 'text-green-800' : 'text-red-600' }}">
                                {{ $stock }}
                            </span>
                        </div>

                        @if($availableText)
                            <div class="mt-1 flex items-center justify-between">
                                <span class="font-semibold text-gray-500">Available</span>
                                <span class="font-bold text-gray-700">{{ $availableText }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <a
                            href="{{ route('products.show', $product) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition"
                        >
                            View
                        </a>

                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1" />
                            <button
                                type="submit"
                                {{ $inStock ? '' : 'disabled' }}
                                class="inline-flex w-full items-center justify-center rounded-xl bg-green-700 px-4 py-2 text-sm font-extrabold text-white shadow hover:bg-green-800 transition disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-black/5 sm:col-span-2 lg:col-span-3">
                <div class="text-lg font-extrabold text-gray-900">No products found.</div>
                <p class="mt-2 text-sm text-gray-600">Try removing filters or searching a different keyword.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-xl bg-green-700 px-4 py-2 text-sm font-extrabold text-white hover:bg-green-800 transition">
                    Reset
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection