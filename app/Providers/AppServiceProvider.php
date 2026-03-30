<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view): void {
            /** @var array<string,array{quantity?:mixed}> $cart */
            $cart = session()->get('cart', []);
            $count = 0;

            foreach ($cart as $row) {
                $count += max(0, (int) ($row['quantity'] ?? 0));
            }

            $view->with('cartCount', $count);
        });
    }
}
