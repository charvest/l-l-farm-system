<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

final class SocialAuthController extends Controller
{
    public function redirect(string $provider): SymfonyRedirectResponse
    {
        return $this->driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            try {
                $social = $this->driver($provider)->user();
            } catch (InvalidStateException $e) {
                $social = $this->driver($provider, forceStateless: true)->user();
            }

            $email = (string) ($social->getEmail() ?? '');
            if ($email === '') {
                return redirect()
                    ->route('login')
                    ->withErrors(['oauth' => 'Social login failed: missing email permission.']);
            }

            $user = User::query()->where('email', $email)->first();

            $emailVerifiedAt = null;
            if ($provider === 'google') {
                $verified = (bool) data_get(
                    $social->user,
                    'email_verified',
                    data_get($social->user, 'verified_email', false)
                );

                if ($verified) {
                    $emailVerifiedAt = now();
                }
            }

            if (!$user) {
                $user = User::create([
                    'name' => (string) ($social->getName() ?: Str::before($email, '@')),
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                    'email_verified_at' => $emailVerifiedAt,
                ]);
            } elseif ($emailVerifiedAt && !$user->email_verified_at) {
                $user->forceFill(['email_verified_at' => $emailVerifiedAt])->save();
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            $this->consumePendingCart();

            $fallback = route('home', absolute: false);
            $intended = session()->pull('url.intended');

            if (is_string($intended) && $intended !== '') {
                if (
                    Str::contains($intended, ['/oauth/', '/login', '/register']) ||
                    Str::endsWith($intended, '/oauth')
                ) {
                    $intended = null;
                }
            } else {
                $intended = null;
            }

            return $intended ? redirect()->to($intended) : redirect()->to($fallback);
        } catch (Throwable $e) {
            Log::error('Social login failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            $msg = 'Social login failed. Check logs for details.';
            if (config('app.debug')) {
                $msg = 'Social login failed: ' . $e->getMessage();
            }

            return redirect()->route('login')->withErrors(['oauth' => $msg]);
        }
    }

    private function driver(string $provider, ?bool $forceStateless = null): Provider
    {
        $d = Socialite::driver($provider);

        if ($provider === 'facebook') {
            $d = $d->scopes(['email'])->fields(['name', 'email']);
        }

        $shouldStateless =
            $forceStateless
            ?? (bool) env('OAUTH_STATELESS', false)
            || app()->environment(['local', 'development']);

        if ($shouldStateless) {
            $d = $d->stateless();
        }

        return $d;
    }

    private function consumePendingCart(): void
    {
        $pending = session()->pull('pending_cart');
        if (!$pending) {
            return;
        }

        $productId = (int) ($pending['product_id'] ?? 0);
        $qty = max(1, (int) ($pending['quantity'] ?? 1));

        if ($productId <= 0) {
            return;
        }

        $product = Product::query()->find($productId);
        if (!$product) {
            return;
        }

        $cart = session()->get('cart', []);

        $key = (string) $product->id;
        $current = (int) ($cart[$key]['quantity'] ?? 0);
        $next = $current + $qty;

        $stock = (int) ($product->stock ?? 0);
        if ($stock > 0) {
            $next = min($next, $stock);
        }

        $cart[$key] = [
            'name' => (string) $product->name,
            'price' => (float) $product->price,
            'quantity' => max(1, $next),
        ];

        session()->put('cart', $cart);
        session()->flash('status', 'Added to cart.');
    }
}