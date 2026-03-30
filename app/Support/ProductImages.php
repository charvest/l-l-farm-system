<?php

namespace App\Support;

use App\Models\Product;

final class ProductImages
{
    private const DIR = 'images/placeholders';

    public static function urlFor(Product $product): string
    {
        $fallback = self::firstExisting(['pig.jpg', 'tomato.jpg', 'chicken.jpg']) ?? asset(self::DIR . '/pig.jpg');

        // If DB image is set, trust it ONLY if it exists under placeholders.
        if (!empty($product->image)) {
            $file = basename((string) $product->image);
            $u = self::assetIfExists($file);
            if ($u) {
                return $u;
            }
        }

        $name = strtolower(trim((string) ($product->name ?? '')));
        $type = strtolower(trim((string) ($product->type ?? $product->category ?? '')));

        // Name-first rules (order matters).
        $rules = [
            // Fruits
            [['banana'],        ['banana.jpeg', 'banana.jpg']],
            [['watermelon'],    ['watermelon.jpeg', 'watermelon.jpg']],
            [['avocado'],       ['avocado.jpeg', 'avocado.jpg']],
            [['orange'],        ['orange.jpeg', 'orange.jpg']],
            [['mango'],         ['mango.jpeg', 'mango.jpg']],

            // Vegetables (eggplant BEFORE egg)
            [['eggplant'],      ['eggplant.jpeg', 'eggplant.jpg']],
            [['lettuce'],       ['lettuce.jpeg', 'lettuce.jpg']],
            [['potato'],        ['potato.jpeg', 'potato.jpg']],
            [['carrot'],        ['carrots.jpeg', 'carrots.jpg']],
            [['cabbage'],       ['cabbage.jpeg', 'cabbage.jpg']],
            [['tomato'],        ['tomato.jpeg', 'tomato.jpg']],

            // Eggs
            [['eggs'],          ['eggs.jpeg', 'eggs.jpg']],
            [['egg'],           ['eggs.jpeg', 'eggs.jpg']],

            // Pigs
            [['piglet', '8'],   ['8weekspig.jpeg', '8weekspig.jpg']],
            [['piglet', 'week'],['8weekspig.jpeg', '8weekspig.jpg']],
            [['grower'],        ['30kgpig.jpeg', '30kgpig.jpg']],
            [['30kg'],          ['30kgpig.jpeg', '30kgpig.jpg']],
            [['30 kg'],         ['30kgpig.jpeg', '30kgpig.jpg']],
            [['sow'],           ['pig.jpeg', 'pig.jpg']],
            [['pig'],           ['pig.jpeg', 'pig.jpg']],

            // Chickens
            [['broiler'],       ['chicken.jpeg', 'chicken.jpg']],
            [['native', 'chicken'], ['chicken.jpeg', 'chicken.jpg']],
            [['hen'],           ['chicken.jpeg', 'chicken.jpg']],
            [['chicken'],       ['chicken.jpeg', 'chicken.jpg']],
        ];

        foreach ($rules as [$needles, $candidates]) {
            $ok = true;
            foreach ($needles as $needle) {
                if (!str_contains($name, $needle)) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return self::firstExisting($candidates) ?? $fallback;
            }
        }

        // Type-based fallback if name didn't match.
        if (str_contains($type, 'fruit')) {
            return self::firstExisting(['banana.jpeg', 'banana.jpg', 'mango.jpeg', 'mango.jpg', 'orange.jpeg', 'orange.jpg']) ?? $fallback;
        }
        if (str_contains($type, 'vegetable')) {
            return self::firstExisting(['eggplant.jpeg', 'eggplant.jpg', 'lettuce.jpg', 'potato.jpg', 'tomato.jpg']) ?? $fallback;
        }
        if (str_contains($type, 'egg')) {
            return self::firstExisting(['eggs.jpg', 'eggs.jpeg']) ?? $fallback;
        }
        if (str_contains($type, 'chicken') || str_contains($type, 'poultry')) {
            return self::firstExisting(['chicken.jpg', 'chicken.jpeg']) ?? $fallback;
        }
        if (str_contains($type, 'pig')) {
            return self::firstExisting(['pig.jpg', '30kgpig.jpg']) ?? $fallback;
        }

        return $fallback;
    }

    private static function firstExisting(array $files): ?string
    {
        foreach ($files as $f) {
            $u = self::assetIfExists($f);
            if ($u) {
                return $u;
            }
        }
        return null;
    }

    private static function assetIfExists(string $file): ?string
    {
        $rel = self::DIR . '/' . ltrim($file, '/');
        return is_file(public_path($rel)) ? asset($rel) : null;
    }
}

