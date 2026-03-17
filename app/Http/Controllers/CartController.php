<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => 1
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            unset($cart[$product->id]);
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product removed!');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared!');
    }
}