@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
    <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-black/5">
        <div class="text-2xl font-extrabold text-gray-900">Order confirmed ✅</div>
        <p class="mt-2 text-sm text-gray-600">
            Your order ID is <span class="font-extrabold text-gray-900">#{{ $orderId }}</span>.
        </p>

        <div class="mt-6 rounded-xl bg-green-50 p-4 ring-1 ring-green-100">
            <div class="text-sm font-semibold text-gray-600">Total</div>
            <div class="mt-1 text-2xl font-extrabold text-green-800">{{ $totalText }}</div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('products.index') }}"
               class="rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                Continue shopping
            </a>

            <a href="{{ route('cart.index') }}"
               class="rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition">
                View cart
            </a>
        </div>
    </div>
</div>
@endsection