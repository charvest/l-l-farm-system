<x-guest-layout>
    <div class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-black/5">
        <div class="grid md:grid-cols-2">
            {{-- Left / Marketing --}}
            <section class="relative px-8 py-12 md:px-12 md:py-14 bg-gradient-to-br from-green-800 via-green-700 to-green-500 text-white">
                <div class="max-w-md">
                    <h1 class="text-4xl font-extrabold leading-tight">
                        Create your <br class="hidden sm:block" />
                        L&amp;L Farm account.
                    </h1>

                    <p class="mt-4 text-white/85">
                        Track orders, save your cart, and reserve livestock or produce in a few clicks.
                    </p>
                </div>

                <div class="mt-10 flex items-end justify-start gap-6">
                    <div class="rounded-2xl bg-white/15 p-5 backdrop-blur">
                        <div class="text-5xl leading-none">🥬</div>
                        <div class="mt-2 text-sm font-semibold tracking-wide">Fresh Produce</div>
                        <div class="text-xs text-white/80">Weekly harvest updates</div>
                    </div>

                    <div class="rounded-2xl bg-white/15 p-5 backdrop-blur hidden sm:block">
                        <div class="text-5xl leading-none">🐔</div>
                        <div class="mt-2 text-sm font-semibold tracking-wide">Farm Picks</div>
                        <div class="text-xs text-white/80">Chickens, pigs, & more</div>
                    </div>
                </div>

                <div class="pointer-events-none absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            </section>

            {{-- Right / Form --}}
            <section class="px-8 py-12 md:px-12 md:py-14">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-green-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M19 8v6"/>
                            <path d="M22 11h-6"/>
                        </svg>
                    </span>
                    <div class="font-bold tracking-wide text-gray-900">L&amp;L Farm</div>
                </div>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Create Account</h2>
                <p class="mt-1 text-sm text-gray-500">Sign up to start ordering and reserving.</p>

                <x-auth-session-status class="mt-6" :status="session('status')" />
                <x-input-error class="mt-4" :messages="$errors->get('oauth')" />

                <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Full name')" class="text-sm font-medium text-gray-700" />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-gray-700" />
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            :value="old('email')"
                            required
                            autocomplete="username"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm password')" class="text-sm font-medium text-gray-700" />
                        <x-text-input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="mt-2 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-green-600"
                        />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button
                        type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-green-700 px-4 py-3 font-semibold text-white shadow hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition"
                    >
                        {{ __('Create account') }}
                    </button>

                    <div class="flex items-center gap-4 py-2">
                        <div class="h-px flex-1 bg-gray-200"></div>
                        <div class="text-xs text-gray-400">Or Sign Up With</div>
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a
                            href="{{ route('oauth.redirect', ['provider' => 'google']) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-extrabold text-gray-800 shadow-sm hover:bg-gray-50 transition"
                        >
                            <span class="font-extrabold">G</span>
                            <span>Google</span>
                        </a>

                        <a
                            href="{{ route('oauth.redirect', ['provider' => 'facebook']) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-extrabold text-gray-800 shadow-sm hover:bg-gray-50 transition"
                        >
                            <span class="font-extrabold">f</span>
                            <span>Facebook</span>
                        </a>
                    </div>

                    <p class="pt-2 text-center text-sm text-gray-600">
                        Already have an account?
                        <a class="font-semibold text-green-700 hover:underline" href="{{ route('login') }}">Login</a>
                    </p>
                </form>
            </section>
        </div>
    </div>
</x-guest-layout>