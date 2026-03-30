<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
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

        // Save the cart intent so login/register can complete it.
        session()->put('pending_cart', [
            'route' => $routeName,          // cart.add / cart.update ...
            'product_id' => $productId,
            'quantity' => $qty,
        ]);

        // After auth, return user to the page they were on.
        $returnTo = url()->previous();
        Redirect::setIntendedUrl($returnTo);

        return redirect()->route('login')->with('success', 'Please login to add items to your cart.');
    }
}
