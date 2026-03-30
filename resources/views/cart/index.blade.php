
@extends('layouts.app')

@section('content')
@php
    $items = $items ?? [];
    $total = (float) ($total ?? 0);
    $fallback = asset('images/placeholders/pig.jpg');
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Your Cart</h1>
            <p class="mt-1 text-sm text-gray-600">Review items, adjust quantity, and confirm totals.</p>
        </div>

        <a href="{{ route('products.index') }}"
           class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition">
            Continue shopping →
        </a>
    </div>

    @if (session('success'))
        <div class="mt-6 rounded-xl bg-green-100 px-4 py-3 text-green-900 ring-1 ring-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (count($items) === 0)
        <div class="mt-8 rounded-2xl bg-white p-10 shadow-sm ring-1 ring-black/5">
            <div class="text-lg font-extrabold text-gray-900">Your cart is empty.</div>
            <p class="mt-2 text-sm text-gray-600">Browse products and add items to your cart.</p>
            <a href="{{ route('products.index') }}"
               class="mt-5 inline-flex rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                Shop now
            </a>
        </div>
    @else
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @foreach ($items as $it)
                    @php
                        $qty = (int) $it['quantity'];
                        $stock = (int) $it['stock'];
                        $canInc = $stock <= 0 ? true : ($qty < $stock);
                        $canDec = $qty > 1;
                    @endphp

                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                        <div class="flex gap-4">
                            <div class="h-24 w-32 shrink-0 overflow-hidden rounded-xl bg-gray-50 ring-1 ring-black/5">
                                <img
                                    src="{{ $it['image'] }}"
                                    alt="{{ $it['name'] }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    onerror="this.src='{{ $fallback }}';"
                                />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-base font-extrabold text-gray-900">{{ $it['name'] }}</div>
                                        <div class="mt-1 text-xs font-bold text-gray-500">
                                            {{ $it['type'] !== '' ? $it['type'] : 'Farm Product' }}
                                            @if(!$it['exists'])
                                                <span class="ml-2 rounded-full bg-yellow-100 px-2 py-0.5 text-[11px] font-extrabold text-yellow-800">
                                                    missing product
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-xs font-semibold text-gray-500">Unit</div>
                                        <div class="text-sm font-extrabold text-green-800">
                                            {{ \App\Support\Money::php($it['price']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('cart.update', $it['id']) }}">
                                            @csrf
                                            <input type="hidden" name="quantity" value="{{ $qty - 1 }}" />
                                            <button
                                                type="submit"
                                                {{ $canDec ? '' : 'disabled' }}
                                                class="h-10 w-10 rounded-xl border border-gray-200 bg-white text-lg font-extrabold text-gray-800 hover:bg-gray-50 transition disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                −
                                            </button>
                                        </form>

                                        <div class="h-10 min-w-[56px] rounded-xl bg-gray-50 px-4 grid place-items-center font-extrabold text-gray-900 ring-1 ring-black/5">
                                            {{ $qty }}
                                        </div>

                                        <form method="POST" action="{{ route('cart.update', $it['id']) }}">
                                            @csrf
                                            <input type="hidden" name="quantity" value="{{ $qty + 1 }}" />
                                            <button
                                                type="submit"
                                                {{ $canInc ? '' : 'disabled' }}
                                                class="h-10 w-10 rounded-xl border border-gray-200 bg-white text-lg font-extrabold text-gray-800 hover:bg-gray-50 transition disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                +
                                            </button>
                                        </form>

                                        @if($stock > 0)
                                            <span class="ml-2 text-xs font-semibold text-gray-500">Stock: {{ $stock }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <div class="text-xs font-semibold text-gray-500">Subtotal</div>
                                            <div class="text-sm font-extrabold text-gray-900">
                                                {{ \App\Support\Money::php($it['subtotal']) }}
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('cart.remove', $it['id']) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-extrabold text-red-700 hover:bg-red-50 transition"
                                                onclick="return confirm('Remove this item from your cart?')"
                                            >
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div class="text-lg font-extrabold text-gray-900">Order Summary</div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Items</span>
                            <span class="font-extrabold text-gray-900">{{ count($items) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Total</span>
                            <span class="text-base font-extrabold text-green-800">{{ \App\Support\Money::php($total) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a
  href="{{ route('checkout.index') }}"
  class="inline-flex w-full items-center justify-center rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition"
>
  Checkout
</a>
                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition"
                                onclick="return confirm('Clear all items from your cart?')"
                            >
                                Clear Cart
                            </button>
                        </form>
                    </div>

                    <p class="mt-4 text-xs text-gray-500">
                        Quantities are capped by current stock to prevent over-ordering.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection