<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

final class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $driver = $this->driver($provider);

        if ($provider === 'facebook') {
            $driver = $driver->scopes(['email'])->fields(['name', 'email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $social = $this->driver($provider)->user();

            $email = (string) ($social->getEmail() ?? '');
            if ($email === '') {
                return redirect()->route('login')->with('success', 'Social login failed: missing email permission.');
            }

            $user = User::query()->where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => (string) ($social->getName() ?: Str::before($email, '@')),
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                ]);
            }

            Auth::login($user, true);

            $this->consumePendingCart();

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Throwable $e) {
            return redirect()->route('login')->with('success', 'Social login failed: ' . $e->getMessage());
        }
    }

    private function driver(string $provider): Provider
    {
        $d = Socialite::driver($provider);

        // WHY: localhost/session/cookie state can break OAuth; stateless avoids that.
        if (app()->environment(['local', 'development'])) {
            $d = $d->stateless();
        }

        return $d;
    }

    private function consumePendingCart(): void
    {
        /** @var array{product_id?:int,quantity?:int}|null $pending */
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

        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
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
        session()->flash('success', 'Added to cart.');
    }
}