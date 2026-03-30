@extends('layouts.app')

@section('content')
@php
    $u = $user ?? Auth::user();

    $initials = 'U';
    if ($u) {
        $parts = preg_split('/\s+/', trim((string) $u->name)) ?: [];
        $initials = strtoupper(substr($parts[0] ?? 'U', 0, 1) . substr($parts[1] ?? '', 0, 1));
        $initials = trim($initials) !== '' ? $initials : 'U';
    }

    $orderCount = (int) ($orderCount ?? 0);
    $pendingCount = (int) ($pendingCount ?? 0);
    $processingCount = (int) ($processingCount ?? 0);
    $shippedCount = (int) ($shippedCount ?? 0);
    $deliveredCount = (int) ($deliveredCount ?? 0);
    $wishlistCount = (int) ($wishlistCount ?? 0);
    $couponCount = (int) ($couponCount ?? 0);
    $points = (int) ($points ?? 0);
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- LEFT: profile + orders + services --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile card (mobile-app vibe) --}}
            <div class="overflow-hidden rounded-2xl shadow-sm ring-1 ring-black/5">
                <div class="relative bg-gradient-to-r from-green-700 to-green-600 px-6 py-6 text-white">
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/20"></div>
                        <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-white/10"></div>
                    </div>

                    <div class="relative flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-12 w-12 place-items-center rounded-full bg-white/15 text-sm font-extrabold">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0">
                                <div class="truncate text-lg font-extrabold">{{ $u->name ?? 'User' }}</div>
                                <div class="truncate text-sm text-white/90">{{ $u->email ?? '' }}</div>
                            </div>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/15 transition"
                           aria-label="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/>
                                <path d="M19.4 15a1.8 1.8 0 0 0 .4 2l.1.1a2.2 2.2 0 0 1-1.6 3.8 2 2 0 0 1-1.4-.6l-.1-.1a1.8 1.8 0 0 0-2-.4 1.8 1.8 0 0 0-1.1 1.6V22a2.2 2.2 0 0 1-4.4 0v-.1a1.8 1.8 0 0 0-1.1-1.6 1.8 1.8 0 0 0-2 .4l-.1.1a2 2 0 0 1-1.4.6A2.2 2.2 0 0 1 2.3 17l.1-.1a1.8 1.8 0 0 0 .4-2 1.8 1.8 0 0 0-1.6-1.1H1a2.2 2.2 0 0 1 0-4.4h.1a1.8 1.8 0 0 0 1.6-1.1 1.8 1.8 0 0 0-.4-2l-.1-.1A2.2 2.2 0 0 1 3.9 2.3c.5 0 1 .2 1.4.6l.1.1a1.8 1.8 0 0 0 2 .4 1.8 1.8 0 0 0 1.1-1.6V2a2.2 2.2 0 0 1 4.4 0v.1a1.8 1.8 0 0 0 1.1 1.6 1.8 1.8 0 0 0 2-.4l.1-.1a2 2 0 0 1 1.4-.6 2.2 2.2 0 0 1 1.6 3.8l-.1.1a1.8 1.8 0 0 0-.4 2 1.8 1.8 0 0 0 1.6 1.1H23a2.2 2.2 0 0 1 0 4.4h-.1a1.8 1.8 0 0 0-1.6 1.1Z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="relative mt-6 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-xl bg-white/10 p-3">
                            <div class="text-xs font-bold text-white/90">Wishlist</div>
                            <div class="mt-1 text-lg font-extrabold">{{ $wishlistCount }}</div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-3">
                            <div class="text-xs font-bold text-white/90">Coupons</div>
                            <div class="mt-1 text-lg font-extrabold">{{ $couponCount }}</div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-3">
                            <div class="text-xs font-bold text-white/90">Points</div>
                            <div class="mt-1 text-lg font-extrabold">{{ $points }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-extrabold text-gray-900">My Orders</div>
                        <a href="#orders" class="text-xs font-bold text-green-800 underline underline-offset-2">View all</a>
                    </div>

                    <div class="mt-4 grid grid-cols-4 gap-3 text-center text-xs">
                        @php
                            $chips = [
                                ['label' => 'Pending', 'count' => $pendingCount],
                                ['label' => 'Processing', 'count' => $processingCount],
                                ['label' => 'Shipped', 'count' => $shippedCount],
                                ['label' => 'Delivered', 'count' => $deliveredCount],
                            ];
                        @endphp

                        @foreach($chips as $c)
                            <div class="rounded-xl border border-gray-200 p-3">
                                <div class="text-gray-600 font-bold">{{ $c['label'] }}</div>
                                <div class="mt-1 text-base font-extrabold text-green-800">{{ (int)$c['count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="text-sm font-extrabold text-gray-900">Services</div>

                <div class="mt-4 grid gap-3 sm:grid-cols-4">
                    <a href="{{ route('products.index') }}" class="rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="text-sm font-extrabold text-gray-900">Browse</div>
                        <div class="mt-1 text-xs text-gray-600">Products</div>
                    </a>

                    <a href="{{ route('cart.index') }}" class="rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="text-sm font-extrabold text-gray-900">Cart</div>
                        <div class="mt-1 text-xs text-gray-600">View items</div>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="text-sm font-extrabold text-gray-900">Account</div>
                        <div class="mt-1 text-xs text-gray-600">Settings</div>
                    </a>

                    <a href="{{ route('home') }}#contact" class="rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="text-sm font-extrabold text-gray-900">Support</div>
                        <div class="mt-1 text-xs text-gray-600">Help</div>
                    </a>
                </div>
            </div>

            {{-- Orders list --}}
            <div id="orders" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-extrabold text-gray-900">Order History</div>
                    <div class="text-xs font-bold text-gray-600">Total: {{ $orderCount }}</div>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-extrabold text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $o)
                            <tr class="bg-white">
                                <td class="px-4 py-3 font-extrabold text-gray-900">#{{ $o->id }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ optional($o->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php $st = (string)($o->status ?? 'Pending'); @endphp
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-extrabold text-green-800 ring-1 ring-green-200">
                                        {{ $st }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-gray-900">
                                    ₱{{ number_format((float)($o->total_amount ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-sm text-gray-600" colspan="4">No orders yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: settings list --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                    <a href="{{ route('home') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 hover:bg-gray-50">←</a>
                    <div class="text-sm font-extrabold text-gray-900">Settings</div>
                </div>

                <div class="p-3">
                    <div class="divide-y divide-gray-100 rounded-xl border border-gray-200">
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                            <div class="text-sm font-bold text-gray-800">Account Settings</div>
                            <div class="text-gray-400">›</div>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                            <div class="text-sm font-bold text-gray-800">Address Book</div>
                            <div class="text-gray-400">›</div>
                        </a>

                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="text-sm font-bold text-gray-800">Country</div>
                            <div class="text-sm font-extrabold text-gray-700">Philippines</div>
                        </div>

                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="text-sm font-bold text-gray-800">Currency</div>
                            <div class="text-sm font-extrabold text-gray-700">PHP</div>
                        </div>

                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="text-sm font-bold text-gray-800">Language</div>
                            <div class="text-sm font-extrabold text-gray-700">English</div>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                            <div class="text-sm font-bold text-gray-800">Notification Settings</div>
                            <div class="text-gray-400">›</div>
                        </a>

                        <a href="{{ route('home') }}#faq" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                            <div class="text-sm font-bold text-gray-800">Privacy Policy</div>
                            <div class="text-gray-400">›</div>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection