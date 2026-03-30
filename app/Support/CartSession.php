<?php

namespace App\Support;

use App\Models\Product;

final class CartSession
{
    /**
     * @return array<string,array{name:string,price:float,quantity:int}>
     */
    public static function get(): array
    {
        /** @var array<string,array{name:string,price:mixed,quantity:mixed}> $cart */
        $cart = session()->get('cart', []);

        $normalized = [];
        foreach ($cart as $id => $row) {
            $normalized[(string) $id] = [
                'name' => (string) ($row['name'] ?? ''),
                'price' => (float) ($row['price'] ?? 0),
                'quantity' => max(0, (int) ($row['quantity'] ?? 0)),
            ];
        }

        return $normalized;
    }

    public static function put(array $cart): void
    {
        session()->put('cart', $cart);
    }

    public static function add(Product $product, int $quantity): void
    {
        $quantity = max(1, $quantity);

        $cart = self::get();
        $current = (int) ($cart[(string) $product->id]['quantity'] ?? 0);
        $next = $current + $quantity;

        $stock = (int) ($product->stock ?? 0);
        if ($stock > 0) {
            $next = min($next, $stock);
        }

        $cart[(string) $product->id] = [
            'name' => (string) $product->name,
            'price' => (float) $product->price,
            'quantity' => $next,
        ];

        self::put($cart);
    }

    public static function count(): int
    {
        $count = 0;
        foreach (self::get() as $row) {
            $count += max(0, (int) ($row['quantity'] ?? 0));
        }
        return $count;
    }
}

