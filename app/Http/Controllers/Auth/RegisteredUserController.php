<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

final class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        event(new Registered($user));
        Auth::login($user);

        $this->normalizeIntendedUrl();

        $this->consumePendingCart();

       return redirect(route('home', absolute: false));
    }

    private function normalizeIntendedUrl(): void
    {
        $intended = (string) session()->get('url.intended', '');
        $path = parse_url($intended, PHP_URL_PATH) ?: '';

        if ($path === '' || $path === '/login' || $path === '/register') {
            session()->forget('url.intended');
        }
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