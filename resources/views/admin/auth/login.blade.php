<x-guest-layout>
    <div class="w-full max-w-lg rounded-3xl bg-white p-8 shadow-xl ring-1 ring-black/5">
        <div class="flex items-center justify-between">
            <div class="font-extrabold text-gray-900">L&amp;L Farm</div>
            <div class="text-sm font-semibold text-gray-500">Admin</div>
        </div>

        <h1 class="mt-6 text-2xl font-extrabold text-gray-900">Admin Sign In</h1>
        <p class="mt-1 text-sm text-gray-500">This login is for administrators only.</p>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-2 block w-full"
                              :value="old('email')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" class="mt-2 block w-full"
                              required autocomplete="current-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300" />
                <span class="text-sm text-gray-600">Remember me</span>
            </label>

            <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-green-700 px-4 py-3 font-semibold text-white hover:bg-green-800 transition">
                Sign In
            </button>
        </form>
    </div>
</x-guest-layout>