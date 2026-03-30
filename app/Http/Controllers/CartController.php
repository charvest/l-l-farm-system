<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductImages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CartController extends Controller
{
    /**
     * Cart is stored in session as:
     * [
     *   productId => ['name' => string, 'price' => float, 'quantity' => int]
     * ]
     */
    public function index(): View
    {
        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
        $cart = session()->get('cart', []);

        $ids = array_map('intval', array_keys($cart));
        $products = Product::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0.0;

        foreach ($cart as $id => $row) {
            $pid = (int) $id;
            $p = $products->get($pid);

            $name = (string) ($row['name'] ?? ($p?->name ?? 'Product'));
            $price = (float) ($row['price'] ?? ($p?->price ?? 0));
            $qty = max(1, (int) ($row['quantity'] ?? 1));

            $stock = (int) ($p?->stock ?? 0);
            if ($stock > 0) {
                $qty = min($qty, $stock);
            }

            $line = $price * $qty;
            $total += $line;

            $items[] = [
                'id' => $pid,
                'name' => $name,
                'type' => (string) ($p?->type ?? ''),
                'price' => $price,
                'quantity' => $qty,
                'stock' => $stock,
                'subtotal' => $line,
                'image' => $p ? ProductImages::urlFor($p) : asset('images/placeholders/pig.jpg'),
                'exists' => (bool) $p,
            ];
        }

        // Persist normalized qty (e.g., capped by stock)
        $normalized = [];
        foreach ($items as $it) {
            $normalized[(string) $it['id']] = [
                'name' => $it['name'],
                'price' => $it['price'],
                'quantity' => $it['quantity'],
            ];
        }
        session()->put('cart', $normalized);

        return view('cart.index', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $qty = max(1, (int) $request->input('quantity', 1));

        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
        $cart = session()->get('cart', []);

        $current = (int) ($cart[(string) $product->id]['quantity'] ?? 0);
        $next = $current + $qty;

        $stock = (int) ($product->stock ?? 0);
        if ($stock > 0) {
            $next = min($next, $stock);
        }

        $cart[(string) $product->id] = [
            'name' => (string) $product->name,
            'price' => (float) $product->price,
            'quantity' => max(1, $next),
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $qty = (int) $request->input('quantity', 1);

        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
        $cart = session()->get('cart', []);

        if (!isset($cart[(string) $product->id])) {
            return back();
        }

        if ($qty <= 0) {
            unset($cart[(string) $product->id]);
            session()->put('cart', $cart);
            return back()->with('success', 'Item removed.');
        }

        $stock = (int) ($product->stock ?? 0);
        if ($stock > 0) {
            $qty = min($qty, $stock);
        }

        $cart[(string) $product->id]['quantity'] = max(1, $qty);
        session()->put('cart', $cart);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Product $product): RedirectResponse
    {
        /** @var array<string,mixed> $cart */
        $cart = session()->get('cart', []);

        unset($cart[(string) $product->id]);

        session()->put('cart', $cart);

        return back()->with('success', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
