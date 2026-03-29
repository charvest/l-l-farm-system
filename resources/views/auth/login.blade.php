<x-guest-layout>
    <div class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-black/5">
        <div class="grid md:grid-cols-2">
            {{-- Left / Marketing --}}
            <section class="relative px-8 py-12 md:px-12 md:py-14 bg-gradient-to-br from-green-800 via-green-700 to-green-500 text-white">
                <div class="max-w-md">
                    <h1 class="text-4xl font-extrabold leading-tight">
                        Simplify ordering with <br class="hidden sm:block" />
                        L&amp;L Farm.
                    </h1>

                    <p class="mt-4 text-white/85">
                        Manage your cart, orders, and reservations with a clean, fast dashboard experience.
                    </p>
                </div>

                {{-- Simple “illustration” (no external assets required) --}}
                <div class="mt-10 flex items-end justify-start gap-6">
                    <div class="rounded-2xl bg-white/15 p-5 backdrop-blur">
                        <div class="text-5xl leading-none">🐷</div>
                        <div class="mt-2 text-sm font-semibold tracking-wide">Pigs & Piglets</div>
                        <div class="text-xs text-white/80">Reserve & buy live pigs</div>
                    </div>

                    <div class="rounded-2xl bg-white/15 p-5 backdrop-blur hidden sm:block">
                        <div class="text-5xl leading-none">🐔</div>
                        <div class="mt-2 text-sm font-semibold tracking-wide">Chickens</div>
                        <div class="text-xs text-white/80">Broilers & layers</div>
                    </div>
                </div>

                <div class="pointer-events-none absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            </section>

            {{-- Right / Form --}}
            <section class="px-8 py-12 md:px-12 md:py-14">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-green-700 text-white">
                        {{-- cart icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6h15l-1.5 9h-12z"/>
                            <path d="M6 6l-2-4"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="18" cy="20" r="1"/>
                        </svg>
                    </span>
                    <div class="font-bold tracking-wide text-gray-900">L&amp;L Farm</div>
                </div>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome Back</h2>
                <p class="mt-1 text-sm text-gray-500">Please login to your account</p>

                <x-auth-session-status class="mt-6" :status="session('status')" />

                <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-gray-700" />
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                            @if (Route::has('password.request'))
                                <a class="text-sm text-green-700 hover:underline" href="{{ route('password.request') }}">
                                    {{ __('Forgot Password?') }}
                                </a>
                            @endif
                        </div>

                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <label for="remember_me" class="flex items-center gap-2 pt-1">
                        <input
                            id="remember_me"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-green-700 focus:ring-green-600"
                        />
                        <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    <button
                        type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-green-700 px-4 py-3 font-semibold text-white shadow hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition"
                    >
                        {{ __('Login') }}
                    </button>

                    {{-- Separator --}}
                    <div class="flex items-center gap-4 py-2">
                        <div class="h-px flex-1 bg-gray-200"></div>
                        <div class="text-xs text-gray-400">Or Login With</div>
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    {{-- Social placeholders (UI only) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition"
                            aria-label="Login with Google (placeholder)"
                        >
                            <span class="text-base">G</span> Google
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition"
                            aria-label="Login with Facebook (placeholder)"
                        >
                            <span class="text-base">f</span> Facebook
                        </button>
                    </div>

                    <p class="pt-2 text-center text-sm text-gray-600">
                        Don’t have an account?
                        <a class="font-semibold text-green-700 hover:underline" href="{{ route('register') }}">Signup</a>
                    </p>
                </form>
            </section>
        </div>
    </div>
</x-guest-layout>