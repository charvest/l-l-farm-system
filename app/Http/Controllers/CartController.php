<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $qty = (int) $request->input('quantity', 1);
        if ($qty < 1) {
            $qty = 1;
        }

        if ((int) $product->stock <= 0) {
            return back()->with('success', 'Product is out of stock.');
        }

        if ($product->stock !== null) {
            $qty = min($qty, (int) $product->stock);
        }

        $cart = session()->get('cart', []);
        $existing = (int) ($cart[$product->id]['quantity'] ?? 0);
        $newQty = $existing + $qty;

        if ($product->stock !== null) {
            $newQty = min($newQty, (int) $product->stock);
        }

        $cart[$product->id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $newQty,
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product removed!');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared!');
    }
}
