@extends('layouts.app')

@section('content')
@php
    $items = $items ?? [];
    $total = (float) ($total ?? 0);
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Checkout</h1>
            <p class="mt-1 text-sm text-gray-600">Enter details and place your order.</p>
        </div>

        <a href="{{ route('cart.index') }}"
           class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition">
            ← Back to cart
        </a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-extrabold text-gray-900">Name</label>
                        <input name="customer_name" value="{{ old('customer_name') }}"
                               class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-900 focus:border-green-600 focus:ring-green-600"
                               required />
                        @error('customer_name') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-extrabold text-gray-900">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                               class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-900 focus:border-green-600 focus:ring-green-600"
                               required />
                        @error('customer_email') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-extrabold text-gray-900">Phone (optional)</label>
                        <input name="customer_phone" value="{{ old('customer_phone') }}"
                               class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-900 focus:border-green-600 focus:ring-green-600" />
                    </div>

                    <div>
                        <label class="text-sm font-extrabold text-gray-900">Address (optional)</label>
                        <input name="customer_address" value="{{ old('customer_address') }}"
                               class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-900 focus:border-green-600 focus:ring-green-600" />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-extrabold text-gray-900">Notes (optional)</label>
                    <textarea name="notes" rows="4"
                              class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-900 focus:border-green-600 focus:ring-green-600">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                    Place Order
                </button>
            </form>
        </div>

        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="text-lg font-extrabold text-gray-900">Summary</div>

                <div class="mt-4 space-y-2 text-sm">
                    @foreach($items as $it)
                        <div class="flex items-start justify-between gap-3 text-gray-700">
                            <div class="min-w-0">
                                <div class="truncate font-bold">{{ $it['name'] }}</div>
                                <div class="text-xs text-gray-500">x{{ (int)$it['quantity'] }}</div>
                            </div>
                            <div class="shrink-0 font-extrabold">{{ \App\Support\Money::php($it['subtotal']) }}</div>
                        </div>
                    @endforeach

                    <div class="my-3 border-t border-gray-200"></div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span>Total</span>
                        <span class="text-base font-extrabold text-green-800">{{ \App\Support\Money::php($total) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection