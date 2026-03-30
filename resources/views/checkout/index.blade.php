@extends('layouts.app')

@section('content')
@php
    $items = $items ?? [];
    $subtotal = (float) ($subtotal ?? 0);
    $deliveryFee = (float) ($deliveryFee ?? config('checkout.delivery_fee', 0));
    $etaDays = (int) ($etaDays ?? config('checkout.delivery_eta_days', 2));

    $methodOld = old('fulfillment_method', 'delivery');
    $shipping = $methodOld === 'delivery' ? $deliveryFee : 0.0;
    $total = (float) ($total ?? ($subtotal + $shipping));

    $arrivesBy = now()->addDays(max(0, $etaDays))->format('D, d M');
    $arrivesByUpper = strtoupper($arrivesBy);

    $openDelivery = true;
    $openPayment = $errors->has('payment_method') || $errors->has('payment_reference') || old('payment_method');
    $openReview = false;
@endphp

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Checkout</h1>
        </div>

        <a href="{{ route('cart.index') }}"
           class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-gray-800 hover:bg-gray-50 transition">
            ← Back to cart
        </a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        {{-- LEFT --}}
        <div class="lg:col-span-2">
            <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                @csrf

                {{-- STEP 1 --}}
                <details id="step-delivery" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5" {{ $openDelivery ? 'open' : '' }}>
                    <summary class="bg-gray-900 px-5 py-3 text-sm font-extrabold tracking-wide text-white cursor-pointer select-none">
                        1. DELIVERY OPTIONS
                    </summary>

                    <div class="p-6 space-y-5">
                        {{-- Delivery / Pickup --}}
                        <div class="grid grid-cols-2 overflow-hidden rounded-xl border border-gray-200">
                            <label class="cursor-pointer">
                                <input type="radio" name="fulfillment_method" value="delivery" class="peer sr-only"
                                    {{ $methodOld === 'delivery' ? 'checked' : '' }}>
                                <div class="py-3 text-center text-xs font-extrabold tracking-widest text-gray-700 peer-checked:bg-gray-900 peer-checked:text-white">
                                    DELIVERY
                                </div>
                            </label>
                            <label class="cursor-pointer border-l border-gray-200">
                                <input type="radio" name="fulfillment_method" value="pickup" class="peer sr-only"
                                    {{ $methodOld === 'pickup' ? 'checked' : '' }}>
                                <div class="py-3 text-center text-xs font-extrabold tracking-widest text-gray-700 peer-checked:bg-gray-900 peer-checked:text-white">
                                    PICK UP
                                </div>
                            </label>
                        </div>

                        {{-- Names --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-extrabold tracking-wide text-gray-900">First Name</label>
                                <input name="customer_first_name" value="{{ old('customer_first_name') }}"
                                       class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                       autocomplete="given-name" required />
                                @error('customer_first_name') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-extrabold tracking-wide text-gray-900">Last Name</label>
                                <input name="customer_last_name" value="{{ old('customer_last_name') }}"
                                       class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                       autocomplete="family-name" required />
                                @error('customer_last_name') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Address --}}
                        <div id="address-block" class="space-y-2">
                            <label class="text-xs font-extrabold tracking-wide text-gray-900">Address</label>

                            <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <input name="address_search" value="{{ old('address_search') }}"
                                       placeholder="Start typing the first line of your address"
                                       class="w-full border-0 bg-transparent py-3 text-sm font-semibold text-gray-900 placeholder:text-gray-400 focus:ring-0" />
                            </div>

                            <details>
                                <summary class="cursor-pointer text-xs font-bold text-gray-700 underline underline-offset-2">
                                    Enter address manually
                                </summary>
                                <div class="mt-3">
                                    <textarea id="customer-address" name="customer_address" rows="3"
                                              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                              placeholder="House number, street, city, postcode">{{ old('customer_address') }}</textarea>
                                    @error('customer_address') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                                </div>
                            </details>
                        </div>

                        {{-- Email / Phone --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-extrabold tracking-wide text-gray-900">Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                       class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                       autocomplete="email" required />
                                @error('customer_email') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-extrabold tracking-wide text-gray-900">Phone Number (optional)</label>
                                <input name="customer_phone" value="{{ old('customer_phone') }}"
                                       class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                       autocomplete="tel" />
                            </div>
                        </div>

                        {{-- Notes --}}
                        <details>
                            <summary class="cursor-pointer text-xs font-bold text-gray-700 underline underline-offset-2">
                                Add order notes (optional)
                            </summary>
                            <div class="mt-3">
                                <textarea name="notes" rows="3"
                                          class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                          placeholder="Anything we should know?">{{ old('notes') }}</textarea>
                            </div>
                        </details>

                        <div class="flex justify-end">
                            <button type="button" id="btn-to-payment"
                                    class="rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow hover:bg-orange-700 transition">
                                SAVE &amp; CONTINUE
                            </button>
                        </div>
                    </div>
                </details>

                {{-- STEP 2 --}}
                <details id="step-payment" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5" {{ $openPayment ? 'open' : '' }}>
                    <summary class="bg-gray-900 px-5 py-3 text-sm font-extrabold tracking-wide text-white cursor-pointer select-none">
                        2. PAYMENT
                    </summary>

                    <div class="p-6 space-y-5">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <label class="rounded-xl border border-gray-200 p-4 cursor-pointer hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="cash"
                                           {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-sm font-extrabold text-gray-900">Cash</div>
                                        <div class="text-xs text-gray-600">Pay on delivery / pickup</div>
                                    </div>
                                </div>
                            </label>

                            <label class="rounded-xl border border-gray-200 p-4 cursor-pointer hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="gcash"
                                           {{ old('payment_method') === 'gcash' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-sm font-extrabold text-gray-900">GCash</div>
                                        <div class="text-xs text-gray-600">Enter reference no.</div>
                                    </div>
                                </div>
                            </label>

                            <label class="rounded-xl border border-gray-200 p-4 cursor-pointer hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                           {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-sm font-extrabold text-gray-900">Bank Transfer</div>
                                        <div class="text-xs text-gray-600">Enter reference no.</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method') <div class="-mt-2 text-xs font-bold text-red-600">{{ $message }}</div> @enderror

                        <div id="payment-reference-block">
                            <label class="text-xs font-extrabold tracking-wide text-gray-900">Payment reference</label>
                            <input id="payment-reference" name="payment_reference" value="{{ old('payment_reference') }}"
                                   class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                   placeholder="e.g. GCash Ref # / Bank Ref #" />
                            @error('payment_reference') <div class="mt-1 text-xs font-bold text-red-600">{{ $message }}</div> @enderror
                            <div class="mt-2 text-xs text-gray-600">
                                If you choose GCash/Bank Transfer, enter the reference number. Cash doesn’t need one.
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="btn-to-review"
                                    class="rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow hover:bg-orange-700 transition">
                                CONTINUE TO REVIEW
                            </button>
                        </div>
                    </div>
                </details>

                {{-- STEP 3 --}}
                <details id="step-review" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5" {{ $openReview ? 'open' : '' }}>
                    <summary class="bg-gray-900 px-5 py-3 text-sm font-extrabold tracking-wide text-white cursor-pointer select-none">
                        3. ORDER REVIEW
                    </summary>

                    <div class="p-6 space-y-5">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="text-xs font-extrabold tracking-widest text-gray-900">DELIVERY</div>
                                <div class="mt-2 text-sm text-gray-700 space-y-1">
                                    <div><span class="font-bold">Method:</span> <span id="review-method">—</span></div>
                                    <div><span class="font-bold">Name:</span> <span id="review-name">—</span></div>
                                    <div><span class="font-bold">Email:</span> <span id="review-email">—</span></div>
                                    <div id="review-address-row"><span class="font-bold">Address:</span> <span id="review-address">—</span></div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="text-xs font-extrabold tracking-widest text-gray-900">PAYMENT</div>
                                <div class="mt-2 text-sm text-gray-700 space-y-1">
                                    <div><span class="font-bold">Method:</span> <span id="review-payment">—</span></div>
                                    <div id="review-ref-row"><span class="font-bold">Reference:</span> <span id="review-ref">—</span></div>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full rounded-xl bg-green-700 px-6 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                            PLACE ORDER
                        </button>

                        <div class="text-xs text-gray-600">
                            By placing your order, you confirm your details are correct.
                        </div>
                    </div>
                </details>
            </form>
        </div>

        {{-- RIGHT SUMMARY --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                <div class="flex items-center justify-between bg-gray-100 px-5 py-3">
                    <div class="text-xs font-extrabold tracking-widest text-gray-900">IN YOUR BAG</div>
                    <a href="{{ route('cart.index') }}" class="text-xs font-bold text-gray-700 underline underline-offset-2">
                        Edit
                    </a>
                </div>

                <div class="p-6">
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span id="sum-subtotal" class="font-extrabold" data-value="{{ $subtotal }}">{{ \App\Support\Money::php($subtotal) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-700">
                            <span>Shipping fee</span>
                            <span id="sum-shipping" class="font-extrabold" data-delivery-fee="{{ $deliveryFee }}">{{ \App\Support\Money::php($shipping) }}</span>
                        </div>

                        <div class="mt-2 flex items-center justify-between border-t border-gray-200 pt-3">
                            <span class="text-xs font-extrabold tracking-widest text-gray-900">TOTAL</span>
                            <span id="sum-total" class="text-base font-extrabold text-red-600">{{ \App\Support\Money::php($total) }}</span>
                        </div>

                        <div id="arrives-badge"
                             class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-red-500 bg-red-50 px-3 py-2 text-xs font-extrabold tracking-widest text-red-600"
                             data-arrives-by="{{ $arrivesByUpper }}">
                            ARRIVES BY {{ $arrivesByUpper }}
                        </div>
                    </div>

                    <div class="mt-5 space-y-3 border-t border-gray-200 pt-5">
                        @foreach($items as $it)
                            <div class="text-sm text-gray-700">
                                <div class="font-bold">{{ $it['name'] }}</div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>Qty: {{ (int) $it['quantity'] }}</span>
                                    <span class="font-extrabold text-gray-900">{{ \App\Support\Money::php($it['subtotal']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 text-xs text-gray-500">
                        Delivery fee is applied only for <span class="font-bold">Delivery</span>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('checkout-form');

    const stepDelivery = document.getElementById('step-delivery');
    const stepPayment = document.getElementById('step-payment');
    const stepReview = document.getElementById('step-review');

    const btnToPayment = document.getElementById('btn-to-payment');
    const btnToReview = document.getElementById('btn-to-review');

    const addressBlock = document.getElementById('address-block');
    const addressTextarea = document.getElementById('customer-address');

    const sumSubtotal = document.getElementById('sum-subtotal');
    const sumShipping = document.getElementById('sum-shipping');
    const sumTotal = document.getElementById('sum-total');
    const arrivesBadge = document.getElementById('arrives-badge');

    const refBlock = document.getElementById('payment-reference-block');
    const refInput = document.getElementById('payment-reference');

    const reviewMethod = document.getElementById('review-method');
    const reviewName = document.getElementById('review-name');
    const reviewEmail = document.getElementById('review-email');
    const reviewAddressRow = document.getElementById('review-address-row');
    const reviewAddress = document.getElementById('review-address');

    const reviewPayment = document.getElementById('review-payment');
    const reviewRefRow = document.getElementById('review-ref-row');
    const reviewRef = document.getElementById('review-ref');

    function moneyPHP(amount) {
        try {
            return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
        } catch (e) {
            return '₱' + Number(amount).toFixed(2);
        }
    }

    function getFulfillment() {
        const el = form.querySelector('input[name="fulfillment_method"]:checked');
        return el ? el.value : 'delivery';
    }

    function getPaymentMethod() {
        const el = form.querySelector('input[name="payment_method"]:checked');
        return el ? el.value : 'cash';
    }

    function setArrivesBadge(method) {
        if (method === 'pickup') {
            arrivesBadge.textContent = 'READY FOR PICKUP';
            arrivesBadge.className = 'mt-4 inline-flex w-full items-center justify-center rounded-lg border border-gray-900 bg-gray-50 px-3 py-2 text-xs font-extrabold tracking-widest text-gray-900';
            return;
        }

        const by = arrivesBadge.dataset.arrivesBy || '';
        arrivesBadge.textContent = 'ARRIVES BY ' + by;
        arrivesBadge.className = 'mt-4 inline-flex w-full items-center justify-center rounded-lg border border-red-500 bg-red-50 px-3 py-2 text-xs font-extrabold tracking-widest text-red-600';
    }

    function syncShippingUI() {
        const subtotal = parseFloat(sumSubtotal.dataset.value || '0') || 0;
        const deliveryFee = parseFloat(sumShipping.dataset.deliveryFee || '0') || 0;
        const method = getFulfillment();

        const shipping = method === 'delivery' ? deliveryFee : 0;
        const total = subtotal + shipping;

        sumShipping.textContent = moneyPHP(shipping);
        sumTotal.textContent = moneyPHP(total);

        // Address required only for delivery
        if (addressTextarea) {
            addressTextarea.required = method === 'delivery';
        }

        // Hide address block on pickup (simple)
        if (addressBlock) {
            addressBlock.style.display = method === 'pickup' ? 'none' : '';
        }

        setArrivesBadge(method);
        syncReview();
    }

    function syncPaymentUI() {
        const pm = getPaymentMethod();
        const needsRef = pm !== 'cash';

        if (refBlock) {
            refBlock.style.display = needsRef ? '' : 'none';
        }
        if (refInput) {
            refInput.required = needsRef;
        }

        syncReview();
    }

    function syncReview() {
        const method = getFulfillment();
        const pm = getPaymentMethod();

        const first = (form.querySelector('input[name="customer_first_name"]')?.value || '').trim();
        const last = (form.querySelector('input[name="customer_last_name"]')?.value || '').trim();
        const email = (form.querySelector('input[name="customer_email"]')?.value || '').trim();
        const address = (form.querySelector('textarea[name="customer_address"]')?.value || '').trim();
        const ref = (form.querySelector('input[name="payment_reference"]')?.value || '').trim();

        reviewMethod.textContent = method === 'pickup' ? 'Pick up' : 'Delivery';
        reviewName.textContent = (first || last) ? `${first} ${last}`.trim() : '—';
        reviewEmail.textContent = email || '—';

        if (method === 'pickup') {
            reviewAddressRow.style.display = 'none';
        } else {
            reviewAddressRow.style.display = '';
            reviewAddress.textContent = address || '—';
        }

        const map = { cash: 'Cash', gcash: 'GCash', bank_transfer: 'Bank Transfer' };
        reviewPayment.textContent = map[pm] || pm;

        if (pm === 'cash') {
            reviewRefRow.style.display = 'none';
        } else {
            reviewRefRow.style.display = '';
            reviewRef.textContent = ref || '—';
        }
    }

    function validateStep(stepEl) {
        const fields = stepEl.querySelectorAll('input, textarea, select');
        for (const f of fields) {
            if (!f.checkValidity()) {
                f.reportValidity();
                return false;
            }
        }
        return true;
    }

    btnToPayment?.addEventListener('click', function () {
        if (!validateStep(stepDelivery)) return;
        stepPayment.open = true;
        stepPayment.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    btnToReview?.addEventListener('click', function () {
        if (!validateStep(stepPayment)) return;
        stepReview.open = true;
        stepReview.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    form.querySelectorAll('input[name="fulfillment_method"]').forEach(el => {
        el.addEventListener('change', syncShippingUI);
    });

    form.querySelectorAll('input[name="payment_method"]').forEach(el => {
        el.addEventListener('change', syncPaymentUI);
    });

    form.querySelectorAll('input, textarea').forEach(el => {
        el.addEventListener('input', syncReview);
    });

    // Init
    syncShippingUI();
    syncPaymentUI();
    syncReview();
})();
</script>
@endsection

