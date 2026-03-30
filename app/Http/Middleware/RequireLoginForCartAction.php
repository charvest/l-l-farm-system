<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

final class RequireLoginForCartAction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            return $next($request);
        }

        $routeName = (string) optional($request->route())->getName();
        $productId = (int) ($request->route('product')?->id ?? $request->route('product') ?? 0);
        $qty = max(1, (int) $request->input('quantity', 1));

        session()->put('pending_cart', [
            'route' => $routeName,
            'product_id' => $productId,
            'quantity' => $qty,
        ]);

        $returnTo = url()->previous();
        Redirect::setIntendedUrl($returnTo);

        return redirect()->route('login')->with('status', 'Please login to add items to your cart.');
    }
}
