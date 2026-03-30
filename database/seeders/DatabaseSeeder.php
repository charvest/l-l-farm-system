<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
        ]);
    }
}



namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        try {
            $productsTable = (new Product())->getTable();

            if (!Schema::hasTable($productsTable)) {
                return;
            }

            $hasProductCol = fn (string $col): bool => Schema::hasColumn($productsTable, $col);

            $nameCol = $hasProductCol('name') ? 'name' : null;
            $priceCol = $hasProductCol('price') ? 'price' : null;

            if ($nameCol === null || $priceCol === null) {
                return;
            }

            $typeCol = $hasProductCol('type')
                ? 'type'
                : ($hasProductCol('category') ? 'category' : null);

            $stockCol = $hasProductCol('stock')
                ? 'stock'
                : ($hasProductCol('quantity') ? 'quantity' : null);

            $imageCol = $hasProductCol('image') ? 'image' : null;
            $categoryIdCol = $hasProductCol('category_id') ? 'category_id' : null;

            $categoryIds = $this->seedCategoriesIfPossible($categoryIdCol);

            $pickImage = function (array $candidates): ?string {
                foreach ($candidates as $file) {
                    if (is_file(public_path('images/placeholders/' . $file))) {
                        return $file;
                    }
                }
                return null;
            };

            $items = [
                // Livestock
                ['name' => 'Piglet (8 weeks)',        'type' => 'Piglet',       'price' => 2500,  'stock' => 12, 'cat' => 'Pigs',     'img' => ['8weekspig.jpg','8weekspig.jpeg']],
                ['name' => 'Grower Pig (30-40kg)',    'type' => 'Live pig',     'price' => 8500,  'stock' => 6,  'cat' => 'Pigs',     'img' => ['30kgpig.jpg','30kgpig.jpeg']],
                ['name' => 'Sow (Breeding Ready)',    'type' => 'Live pig',     'price' => 18500, 'stock' => 2,  'cat' => 'Pigs',     'img' => ['pig.jpg','pig.jpeg']],

                // Poultry
                ['name' => 'Native Chicken (Live)',   'type' => 'Live chicken', 'price' => 350,   'stock' => 40, 'cat' => 'Chickens', 'img' => ['chicken.jpg','chicken.jpeg']],
                ['name' => 'Egg-Laying Hen',          'type' => 'Layer',        'price' => 480,   'stock' => 18, 'cat' => 'Chickens', 'img' => ['eggs.jpg','eggs.jpeg']],
                ['name' => 'Fresh Eggs (1 dozen)',    'type' => 'Egg',          'price' => 120,   'stock' => 25, 'cat' => 'Chickens', 'img' => ['eggs.jpg','eggs.jpeg']],

                // Vegetables
                ['name' => 'Fresh Eggplant (1kg)',    'type' => 'Vegetable',    'price' => 70,    'stock' => 25, 'cat' => 'Produce',  'img' => ['eggplant.jpeg','eggplant.jpg']],
                ['name' => 'Tomatoes (1kg)',          'type' => 'Vegetable',    'price' => 85,    'stock' => 20, 'cat' => 'Produce',  'img' => ['tomato.jpg','tomato.jpeg']],
                ['name' => 'Lettuce (1kg)',           'type' => 'Vegetable',    'price' => 90,    'stock' => 25, 'cat' => 'Produce',  'img' => ['lettuce.jpg','lettuce.jpeg']],
                ['name' => 'Potato (1kg)',            'type' => 'Vegetable',    'price' => 85,    'stock' => 25, 'cat' => 'Produce',  'img' => ['potato.jpg','potato.jpeg']],
                ['name' => 'Carrots (1kg)',           'type' => 'Vegetable',    'price' => 70,    'stock' => 25, 'cat' => 'Produce',  'img' => ['carrots.jpg','carrots.jpeg']],
                ['name' => 'Cabbage (1pc)',           'type' => 'Vegetable',    'price' => 60,    'stock' => 20, 'cat' => 'Produce',  'img' => ['cabbage.jpg','cabbage.jpeg']],

                // Fruits
                ['name' => 'Bananas (1 dozen)',       'type' => 'Fruit',        'price' => 60,    'stock' => 30, 'cat' => 'Produce',  'img' => ['banana.jpeg','banana.jpg']],
                ['name' => 'Watermelon (1pc)',        'type' => 'Fruit',        'price' => 180,   'stock' => 15, 'cat' => 'Produce',  'img' => ['watermelon.jpeg','watermelon.jpg']],
                ['name' => 'Avocado (1kg)',           'type' => 'Fruit',        'price' => 220,   'stock' => 12, 'cat' => 'Produce',  'img' => ['avocado.jpeg','avocado.jpg']],
                ['name' => 'Orange (1kg)',            'type' => 'Fruit',        'price' => 140,   'stock' => 20, 'cat' => 'Produce',  'img' => ['orange.jpeg','orange.jpg']],
                ['name' => 'Mango (1kg)',             'type' => 'Fruit',        'price' => 160,   'stock' => 18, 'cat' => 'Produce',  'img' => ['mango.jpeg','mango.jpg']],
            ];

            foreach ($items as $i) {
                $update = [
                    $priceCol => $i['price'],
                ];

                if ($typeCol !== null) {
                    $update[$typeCol] = $i['type'];
                }

                if ($stockCol !== null) {
                    $update[$stockCol] = $i['stock'];
                }

                if ($imageCol !== null) {
                    $img = $pickImage($i['img']);
                    if ($img !== null) {
                        $update[$imageCol] = $img;
                    }
                }

                if ($categoryIdCol !== null && isset($categoryIds[$i['cat']])) {
                    $update[$categoryIdCol] = $categoryIds[$i['cat']];
                }

                Product::query()->updateOrCreate(
                    [$nameCol => $i['name']],
                    $update
                );
            }
        } finally {
            Model::reguard();
        }
    }

    private function seedCategoriesIfPossible(?string $categoryIdCol): array
    {
        if ($categoryIdCol === null) {
            return [];
        }

        $table = 'categories';
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id') || !Schema::hasColumn($table, 'name')) {
            return [];
        }

        $hasCreated = Schema::hasColumn($table, 'created_at');
        $hasUpdated = Schema::hasColumn($table, 'updated_at');
        $now = now();

        foreach (['Pigs', 'Chickens', 'Produce'] as $name) {
            $values = [];
            if ($hasCreated) {
                $values['created_at'] = $now;
            }
            if ($hasUpdated) {
                $values['updated_at'] = $now;
            }

            DB::table($table)->updateOrInsert(['name' => $name], $values);
        }

        /** @var array<string,int> $map */
        $map = DB::table($table)
            ->whereIn('name', ['Pigs', 'Chickens', 'Produce'])
            ->pluck('id', 'name')
            ->all();

        return $map;
    }
}