{{-- File: resources/views/components/site-footer.blade.php --}}
<footer class="mt-12 bg-green-950 text-green-50">
    <a href="#top"
       class="block bg-green-900/70 py-3 text-center text-sm font-semibold tracking-wide hover:bg-green-900 transition">
        Back to top
    </a>

    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-12 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <h3 class="text-sm font-bold tracking-wider text-white">Get to Know Us</h3>
            <ul class="mt-4 space-y-2 text-sm text-green-100/90">
                <li><a class="hover:underline" href="{{ route('home') }}">About L&amp;L Farm</a></li>
                <li><a class="hover:underline" href="{{ route('products.index') }}">Our Products</a></li>
                <li><a class="hover:underline" href="{{ route('home') }}#contact">Contact</a></li>
                <li><a class="hover:underline" href="{{ route('home') }}#deals">Weekly Deals</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold tracking-wider text-white">Shop With Us</h3>
            <ul class="mt-4 space-y-2 text-sm text-green-100/90">
                <li><a class="hover:underline" href="{{ route('products.index', ['category' => 'pig']) }}">Pigs &amp; Piglets</a></li>
                <li><a class="hover:underline" href="{{ route('products.index', ['category' => 'chicken']) }}">Chickens</a></li>
                <li><a class="hover:underline" href="{{ route('products.index', ['category' => 'produce']) }}">Fruits &amp; Vegetables</a></li>
                <li><a class="hover:underline" href="{{ route('cart.index') }}">Your Cart</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold tracking-wider text-white">Customer Support</h3>
            <ul class="mt-4 space-y-2 text-sm text-green-100/90">
                <li><a class="hover:underline" href="#">Shipping &amp; Pickup (placeholder)</a></li>
                <li><a class="hover:underline" href="#">Returns (placeholder)</a></li>
                <li><a class="hover:underline" href="#">FAQs (placeholder)</a></li>
                <li><a class="hover:underline" href="#">Bulk Orders (placeholder)</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold tracking-wider text-white">Connect</h3>
            <ul class="mt-4 space-y-2 text-sm text-green-100/90">
                <li><a class="hover:underline" href="#">Facebook (placeholder)</a></li>
                <li><a class="hover:underline" href="#">Instagram (placeholder)</a></li>
                <li><a class="hover:underline" href="#">Email us (placeholder)</a></li>
            </ul>

            <div class="mt-6 rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                <p class="text-sm font-semibold text-white">Get farm updates</p>
                <p class="mt-1 text-xs text-green-100/80">Deals &amp; availability alerts (UI only)</p>
                <div class="mt-3 flex gap-2">
                    <input
                        type="email"
                        placeholder="you@email.com"
                        class="w-full rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-sm text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-green-400"
                    />
                    <button
                        type="button"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500 transition"
                    >
                        Join
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 py-6 sm:flex-row">
            <div class="flex items-center gap-2 font-bold tracking-wide text-white">
                <span aria-hidden="true">🐷</span>
                <span>L&amp;L Farm</span>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-green-100/80">
                <a class="hover:underline" href="{{ route('home') }}">Home</a>
                <a class="hover:underline" href="{{ route('products.index') }}">Products</a>
                <a class="hover:underline" href="{{ route('cart.index') }}">Cart</a>
                @auth
                    <a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a>
                @endauth
            </div>

            <p class="text-xs text-green-100/70">
                © {{ now()->year }} L&amp;L Farm. All rights reserved.
            </p>
        </div>
    </div>
</footer>