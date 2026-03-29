@extends('layouts.app')

@section('content')
@php
    // Subfolder-safe: works with /l-l-farm-system/public and normal vhost
    $publicBase = rtrim(request()->getBaseUrl(), '/');
    $img = fn (string $file) => $publicBase . '/images/' . ltrim($file, '/');

    // Put these in public/images/
    $heroImages = [
        $img('hero-1.jpg'),
        $img('hero-2.jpg'),
        $img('hero-3.jpg'),
    ];

    $categoryParam = function (string $name): string {
        $n = strtolower(trim($name));
        return match (true) {
            str_contains($n, 'pig') => 'pig',
            str_contains($n, 'chicken') => 'chicken',
            str_contains($n, 'produce'), str_contains($n, 'vegetable'), str_contains($n, 'fruit') => 'produce',
            default => $n,
        };
    };

    $categoryIcon = function (string $name): string {
        $n = strtolower(trim($name));
        return match (true) {
            str_contains($n, 'pig') => '🐖',
            str_contains($n, 'chicken') => '🐔',
            str_contains($n, 'produce'), str_contains($n, 'vegetable'), str_contains($n, 'fruit') => '🥬',
            default => '🛒',
        };
    };

    $categoriesForHome = ($categories ?? collect())
        ->take(3)
        ->values();

    if ($categoriesForHome->count() === 0) {
        $categoriesForHome = collect([
            (object) ['name' => 'Pigs'],
            (object) ['name' => 'Chickens'],
            (object) ['name' => 'Produce'],
        ]);
    }

    // Override images for specific featured products
 $featuredImageOverride = function ($product) use ($img): ?string {
        $name = strtolower((string)($product->name ?? ''));
        $type = strtolower((string)($product->type ?? ''));
        $text = trim($name . ' ' . $type);

        // Piglet (8 weeks)
        if (str_contains($text, 'piglet') && (str_contains($text, '8 week') || str_contains($text, '8 weeks') || str_contains($text, '(8'))) {
            return $img('placeholders/8weekspig.jpeg');
        }

        // Grower Pig (30–40kg / 30kg)
        if (str_contains($text, 'grower') && str_contains($text, 'kg') && (str_contains($text, '30') || str_contains($text, '30–40') || str_contains($text, '30-40'))) {
            return $img('placeholders/30kgpig.jpeg');
        }

        return null;
    };
    // Your REAL placeholder files in public/images/placeholders/
     // Subfolder-safe absolute URL
    $publicBase = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
    $img = fn (string $file) => $publicBase . '/images/' . ltrim($file, '/');

     $productImage = function ($p) use ($img): string {
        $t = strtolower(trim(($p->name ?? '') . ' ' . ($p->type ?? '') . ' ' . ($p->description ?? '')));

        if (str_contains($t, 'piglet') && (str_contains($t, '8 week') || str_contains($t, '8 weeks') || str_contains($t, '(8)'))) {
            return $img('placeholders/8weekspig.jpeg');
        }
        if (str_contains($t, 'grower') && str_contains($t, 'kg') && (str_contains($t, '30') || str_contains($t, '30–40') || str_contains($t, '30-40'))) {
            return $img('placeholders/30kgpig.jpeg');
        }

        return match (true) {
            str_contains($t, 'eggplant') => $img('placeholders/eggplant.jpg'),
            str_contains($t, 'tomato') => $img('placeholders/tomato.jpg'),
            str_contains($t, 'cabbage') => $img('placeholders/cabbage.jpg'),
            str_contains($t, 'lettuce') => $img('placeholders/lettuce.jpg'),
            str_contains($t, 'potato') => $img('placeholders/potato.jpg'),
            str_contains($t, 'carrot') || str_contains($t, 'carrots') => $img('placeholders/carrots.jpg'),
            str_contains($t, 'egg') || str_contains($t, 'eggs') => $img('placeholders/eggs.jpg'),
            str_contains($t, 'chicken') => $img('placeholders/chicken.jpg'),
            str_contains($t, 'pig') || str_contains($t, 'piglet') => $img('placeholders/pig.jpg'),
            default => $img('placeholders/lettuce.jpg'),
        };
    };
@endphp

{{-- HERO --}}
<section
    class="relative w-full overflow-hidden"
    data-hero-slider
    data-hero-images='@json($heroImages)'
    style="background-image: linear-gradient(to right, rgba(0,0,0,.65), rgba(0,0,0,.15)), url('{{ $heroImages[0] }}'); background-size: cover; background-position: center;"
>
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex min-h-[72vh] items-center py-12">
            <div class="max-w-xl">
                <span class="inline-flex items-center rounded bg-white px-4 py-2 text-2xl font-extrabold tracking-wide text-green-800 shadow">
                    Fresh From Our Farm
                </span>

                <p class="mt-5 max-w-lg text-sm font-semibold tracking-wide text-white/90 md:text-base">
                    Reserve Piglets • Buy Live Pigs • Fresh Chickens • Fruits &amp; Vegetables
                </p>

                <p class="mt-3 max-w-lg text-sm text-white/80">
                    Farm-raised products with clear pricing, availability dates, and easy ordering.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-green-700 px-6 py-3 font-bold text-white shadow hover:bg-green-800 transition">
                        Shop Now
                    </a>

                    <a href="#about"
                       class="inline-flex items-center justify-center rounded-lg bg-white/10 px-6 py-3 font-bold text-white ring-1 ring-white/30 hover:bg-white/15 transition">
                        Meet L&amp;L Farm
                    </a>
                </div>
            </div>
        </div>

        <div class="pb-10">
            <div class="mx-auto flex w-fit items-center gap-3" data-hero-dots aria-label="Hero slider dots">
                <button type="button" class="h-3 w-3 rounded-full bg-green-400 ring-2 ring-white/70" data-hero-dot="0" aria-label="Slide 1"></button>
                <button type="button" class="h-3 w-3 rounded-full bg-white/60 hover:bg-white/80 transition" data-hero-dot="1" aria-label="Slide 2"></button>
                <button type="button" class="h-3 w-3 rounded-full bg-white/60 hover:bg-white/80 transition" data-hero-dot="2" aria-label="Slide 3"></button>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="mx-auto max-w-6xl px-4 py-10" id="categories">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">Shop by Category</h2>
            <p class="mt-1 text-sm text-gray-600">Choose what you need today.</p>
        </div>
       
   
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-3">
        @foreach ($categoriesForHome as $c)
            @php
                $name = $c->name ?? 'Category';
                $param = $categoryParam($name);
            @endphp

            <a href="{{ route('products.index', ['category' => $param]) }}"
               class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-xl">
                <div class="text-3xl">{{ $categoryIcon($name) }}</div>
                <h3 class="mt-3 text-lg font-extrabold text-gray-900 group-hover:text-green-800 transition">{{ $name }}</h3>
                <p class="mt-2 text-sm text-gray-600">Browse {{ strtolower($name) }} products.</p>
                <div class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-green-700">
                    Explore <span class="opacity-0 -translate-x-1 transition group-hover:opacity-100 group-hover:translate-x-0">→</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- FEATURED PRODUCTS --}}
<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-12" id="featured">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Featured Products</h2>
                <p class="mt-1 text-sm text-gray-600">Popular picks and newest arrivals.</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-green-700 hover:underline">
                Shop all →
            </a>
        </div>

        @php
            // Image source always from public/images/placeholders/*
       
            $placeholderFromText = function (string $t) use ($img): string {
        return match (true) {
            str_contains($t, 'eggplant') => $img('placeholders/eggplant.jpg'),
            str_contains($t, 'tomato') => $img('placeholders/tomato.jpg'),
            str_contains($t, 'cabbage') => $img('placeholders/cabbage.jpg'),
            str_contains($t, 'lettuce') => $img('placeholders/lettuce.jpg'),
            str_contains($t, 'potato') => $img('placeholders/potato.jpg'),
            str_contains($t, 'carrot') || str_contains($t, 'carrots') => $img('placeholders/carrots.jpg'),
            str_contains($t, 'egg') || str_contains($t, 'eggs') => $img('placeholders/eggs.jpg'),
            str_contains($t, 'chicken') => $img('placeholders/chicken.jpg'),
            str_contains($t, 'pig') || str_contains($t, 'piglet') => $img('placeholders/pig.jpg'),
            default => $img('placeholders/lettuce.jpg'),
        };
    };

    $imageCandidates = function ($p) use ($img, $placeholderFromText): array {
        $t = strtolower(trim(($p->name ?? '') . ' ' . ($p->type ?? '') . ' ' . ($p->description ?? '')));

        // Default chain: try per-product image by ID, then placeholder
        $fallback = $placeholderFromText($t);
        $base = [
            'src' => $img('products/' . $p->id . '.jpg'),
            'alt1' => $img('products/' . $p->id . '.jpeg'),
            'alt2' => $fallback,
            'alt3' => '',
            'alt4' => '',
            'fallback' => $img('placeholders/lettuce.jpg'),
        ];

        // Piglet (8 weeks) special image: try placeholders + products + then pig.jpg
        if (str_contains($t, 'piglet') && (str_contains($t, '8 week') || str_contains($t, '8 weeks') || str_contains($t, '(8'))) {
            return [
                'src' => $img('placeholders/8weekspig.jpeg'),
                'alt1' => $img('placeholders/8weekspig.jpg'),
                'alt2' => $img('products/8weekspig.jpeg'),
                'alt3' => $img('products/8weekspig.jpg'),
                'alt4' => $img('placeholders/pig.jpg'),
                'fallback' => $img('placeholders/pig.jpg'),
            ];
        }

        // Grower (30kg / 30–40kg) special image
        if (str_contains($t, 'grower') && str_contains($t, 'kg') && (str_contains($t, '30') || str_contains($t, '30–40') || str_contains($t, '30-40'))) {
            return [
                'src' => $img('placeholders/30kgpig.jpeg'),
                'alt1' => $img('placeholders/30kgpig.jpg'),
                'alt2' => $img('products/30kgpig.jpeg'),
                'alt3' => $img('products/30kgpig.jpg'),
                'alt4' => $img('placeholders/pig.jpg'),
                'fallback' => $img('placeholders/pig.jpg'),
            ];
        }

        return $base;
    };
@endphp

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse (($featuredProducts ?? collect()) as $p)
                @php $src = $productImage($p); @endphp

                <div class="group rounded-2xl bg-green-50 p-5 ring-1 ring-black/5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-white ring-1 ring-black/5">
                        @php $c = $imageCandidates($p); @endphp

<img
    src="{{ $c['src'] }}"
    data-alt1="{{ $c['alt1'] }}"
    data-alt2="{{ $c['alt2'] }}"
    data-alt3="{{ $c['alt3'] }}"
    data-alt4="{{ $c['alt4'] }}"
    data-fallback="{{ $c['fallback'] }}"
    alt="{{ $p->name }}"
    loading="lazy"
    class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
    onerror="
        if(this.dataset.t1!=='1' && this.dataset.alt1){this.dataset.t1='1'; this.src=this.dataset.alt1; return;}
        if(this.dataset.t2!=='1' && this.dataset.alt2){this.dataset.t2='1'; this.src=this.dataset.alt2; return;}
        if(this.dataset.t3!=='1' && this.dataset.alt3){this.dataset.t3='1'; this.src=this.dataset.alt3; return;}
        if(this.dataset.t4!=='1' && this.dataset.alt4){this.dataset.t4='1'; this.src=this.dataset.alt4; return;}
        this.src=this.dataset.fallback;
    "
/>
                    </div>

                    <div class="mt-4 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-extrabold text-gray-900">{{ $p->name }}</h3>
                            <p class="mt-1 text-xs text-gray-600">{{ $p->type ?? 'Farm Product' }}</p>
                        </div>

                        <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-bold text-green-800 ring-1 ring-black/5">
                            ₱{{ number_format((float)($p->price ?? 0), 2) }}
                        </span>
                    </div>

                    <div class="mt-4 text-xs text-gray-600 space-y-1">
                        <div><span class="font-semibold">Stock:</span> {{ (int)($p->stock ?? 0) }}</div>
                        @if (!empty($p->availability_date))
                            <div><span class="font-semibold">Available:</span> {{ $p->availability_date }}</div>
                        @endif
                    </div>

                    <div class="mt-5 flex gap-2">
                        {{-- IMPORTANT: $p exists here, so this is safe --}}
                        <a href="{{ route('products.show', $p) }}"
                           class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-xs font-extrabold text-gray-800 hover:bg-gray-50 transition">
                            View
                        </a>

                        <form method="POST" action="{{ route('cart.add', $p) }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-lg bg-green-700 px-3 py-2 text-xs font-extrabold text-white hover:bg-green-800 transition">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-green-50 p-6 ring-1 ring-black/5 text-gray-700 sm:col-span-2 lg:col-span-4">
                    No products yet. Seed your database and reload the homepage.
                </div>
            @endforelse
        </div>
    </div>
</section>
{{-- HIGHLIGHTS (hover overlay style like your screenshot) --}}
<section id="highlights" class="bg-white">
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
            {{-- VALUES --}}
            <a href="#about" class="group relative isolate h-[360px] md:h-[420px] overflow-hidden focus:outline-none">
                <img src="{{ $img('values.jpg') }}" alt="Our Values"
                     class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/15 transition-opacity duration-300 group-hover:opacity-0"></div>
 <div class="absolute inset-0 flex items-center justify-center px-8 transition-opacity duration-300 group-hover:opacity-0">
                    <div class="bg-green-600/75 px-10 py-6 text-center shadow-lg">
                        <div class="text-xl font-extrabold text-white">Our Values</div>
                      
                    </div>
                </div>

                <div class="absolute inset-0 bg-green-800/80 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <div class="flex h-full items-center justify-center px-10 text-center">
                        <div class="max-w-md">
                            <h3 class="text-2xl md:text-3xl font-extrabold text-white">Our Values</h3>
                            <p class="mt-3 text-sm md:text-base text-white/90 leading-relaxed">
                                We do what’s right — for the animals, for the employees, for the customers,
                                for the community, and for the earth.
                            </p>
                            <span class="mt-6 inline-flex items-center justify-center rounded-lg bg-green-950 px-7 py-3 text-sm font-extrabold text-white shadow hover:bg-green-900 transition">
                                Tell me more
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            {{-- PRODUCTS --}}
            <a href="{{ route('products.index') }}" class="group relative isolate h-[360px] md:h-[420px] overflow-hidden focus:outline-none">
                <img src="{{ $img('products.jpg') }}" alt="Our Products"
                     class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/10 transition-opacity duration-300 group-hover:opacity-0"></div>

                <div class="absolute inset-0 flex items-center justify-center px-8 transition-opacity duration-300 group-hover:opacity-0">
                    <div class="bg-green-600/75 px-10 py-6 text-center shadow-lg">
                        <div class="text-xl font-extrabold text-white">Our Products</div>
                    </div>
                </div>

                <div class="absolute inset-0 bg-green-700/70 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <div class="flex h-full items-center justify-center px-10 text-center">
                        <div class="max-w-md">
                            <h3 class="text-2xl md:text-3xl font-extrabold text-white">Our Products</h3>
                            <p class="mt-3 text-sm md:text-base text-white/90 leading-relaxed">
                                Pigs, chickens, and fresh vegetables — harvested and prepared with care.
                            </p>
                            <span class="mt-6 inline-flex items-center justify-center rounded-lg bg-white px-7 py-3 text-sm font-extrabold text-green-900 shadow hover:shadow-lg transition">
                                Explore our selection
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            {{-- OPERATIONS --}}
            <a href="#about" class="group relative isolate h-[360px] md:h-[420px] overflow-hidden focus:outline-none">
                <img src="{{ $img('operations.jpg') }}" alt="Our Operations"
                     class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/15 transition-opacity duration-300 group-hover:opacity-0"></div>
 <div class="absolute inset-0 flex items-center justify-center px-8 transition-opacity duration-300 group-hover:opacity-0">
                    <div class="bg-green-600/75 px-10 py-6 text-center shadow-lg">
                        <div class="text-xl font-extrabold text-white">Our Operations</div>
                    </div>
                </div>

                <div class="absolute inset-0 bg-green-900/65 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <div class="flex h-full items-center justify-center px-10 text-center">
                        <div class="max-w-md">
                            <h3 class="text-2xl md:text-3xl font-extrabold text-white">Our Operations</h3>
                            <p class="mt-3 text-sm md:text-base text-white/90 leading-relaxed">
                                Clean processes, careful handling, and consistent quality — from farm to customer.
                            </p>
                            <span class="mt-6 inline-flex items-center justify-center rounded-lg bg-white px-7 py-3 text-sm font-extrabold text-green-900 shadow hover:shadow-lg transition">
                                Tell me more
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- MEET (story) --}}
<section id="about" class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <div class="grid gap-10 md:grid-cols-2 md:items-center">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Meet L&amp;L Farm</h2>

                <p class="mt-4 text-slate-600 leading-relaxed">
                    L&amp;L Farm started in <span class="font-semibold text-slate-900">2010</span> in
                    <span class="font-semibold text-slate-900">Marinduque, Philippines</span>.
                    It began as a small family dream — a father working abroad in
                    <span class="font-semibold text-slate-900">Saudi Arabia</span> returned home to start a new beginning and build a simple,
                    honest business for the community.
                </p>

                <p class="mt-3 text-slate-600 leading-relaxed">
                    From a humble start, the farm focused on raising
                    <span class="font-semibold text-slate-900">pigs</span>,
                    <span class="font-semibold text-slate-900">chickens</span>,
                    and growing <span class="font-semibold text-slate-900">fresh vegetables</span>.
                    Today, we continue that mission: fresh products, transparent availability, and reliable service.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-3 text-sm font-bold text-white shadow hover:bg-green-800 transition">
                        Explore Our Products
                    </a>

                    <a href="#contact"
                       class="inline-flex items-center justify-center rounded-lg border border-green-700 px-5 py-3 text-sm font-bold text-green-900 hover:bg-green-50 transition">
                        Contact Us
                    </a>
                </div>
            </div>

            <div class="rounded-3xl bg-green-50 p-3 shadow-sm ring-1 ring-black/5">
                <div class="aspect-video overflow-hidden rounded-2xl bg-slate-200">
                    <img src="{{ $img('meet.jpg') }}" alt="L&L Farm" class="h-full w-full object-cover" />
                </div>
            </div>
        </div>
    </div>
</section>

{{-- HOW TO ORDER --}}
<section class="bg-green-50">
    <div class="mx-auto max-w-6xl px-4 py-14" id="how-to-order">
        <h2 class="text-2xl font-extrabold text-gray-900">How to Order</h2>
        <p class="mt-1 text-sm text-gray-600">Simple steps from browsing to pickup/delivery.</p>

        <div class="mt-8 grid gap-6 md:grid-cols-4">
            <div class="rounded-2xl bg-white p-6 ring-1 ring-black/5 shadow-sm">
                <div class="text-3xl">🛒</div>
                <h3 class="mt-3 font-extrabold text-gray-900">Browse</h3>
                <p class="mt-2 text-sm text-gray-600">Explore products and check availability.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 ring-1 ring-black/5 shadow-sm">
                <div class="text-3xl">➕</div>
                <h3 class="mt-3 font-extrabold text-gray-900">Add to Cart</h3>
                <p class="mt-2 text-sm text-gray-600">Add items you want to buy or reserve.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 ring-1 ring-black/5 shadow-sm">
                <div class="text-3xl">✅</div>
                <h3 class="mt-3 font-extrabold text-gray-900">Checkout/Reserve</h3>
                <p class="mt-2 text-sm text-gray-600">Submit your order, we confirm details.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 ring-1 ring-black/5 shadow-sm">
                <div class="text-3xl">🚚</div>
                <h3 class="mt-3 font-extrabold text-gray-900">Pickup/Delivery</h3>
                <p class="mt-2 text-sm text-gray-600">Pick up at the farm or arrange delivery.</p>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-14" id="testimonials">
        <h2 class="text-2xl font-extrabold text-gray-900">What Customers Say</h2>
        <p class="mt-1 text-sm text-gray-600">Real feedback from our community.</p>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach (($testimonials ?? []) as $t)
                <div class="rounded-2xl bg-green-50 p-6 ring-1 ring-black/5 shadow-sm">
                    <div class="text-sm font-extrabold text-gray-900">{{ $t['title'] }}</div>
                    <p class="mt-3 text-sm text-gray-700 leading-relaxed">“{{ $t['quote'] }}”</p>
                    <div class="mt-4 text-xs font-bold text-green-800">— {{ $t['name'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ + CONTACT --}}
<section class="bg-green-50" id="contact">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <div class="grid gap-10 lg:grid-cols-2">
            {{-- FAQ --}}
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">FAQ</h2>
                <p class="mt-1 text-sm text-gray-600">Quick answers to common questions.</p>

                <div class="mt-6 space-y-3">
                    @foreach (($faqs ?? []) as $f)
                        <details class="group rounded-2xl bg-white p-5 ring-1 ring-black/5 shadow-sm">
                            <summary class="cursor-pointer list-none font-extrabold text-gray-900 flex items-center justify-between">
                                <span>{{ $f['q'] }}</span>
                                <span class="text-green-700 transition group-open:rotate-45">+</span>
                            </summary>
                            <p class="mt-3 text-sm text-gray-700 leading-relaxed">{{ $f['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>

            {{-- Contact --}}
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Contact</h2>
                <p class="mt-1 text-sm text-gray-600">Questions? Bulk orders? Reservations?</p>

                <form class="mt-6 rounded-2xl bg-white p-6 ring-1 ring-black/5 shadow-sm space-y-4" method="POST" action="#">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:border-green-600 focus:ring-green-600"
                               placeholder="Name" />
                        <input class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:border-green-600 focus:ring-green-600"
                               placeholder="Email" />
                    </div>

                    <input class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:border-green-600 focus:ring-green-600"
                           placeholder="Subject" />

                    <textarea class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:border-green-600 focus:ring-green-600"
                              rows="5"
                              placeholder="Message"></textarea>

                    <button type="button"
                            class="w-full rounded-xl bg-green-700 px-5 py-3 text-sm font-extrabold text-white shadow hover:bg-green-800 transition">
                        Send Message (placeholder)
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection