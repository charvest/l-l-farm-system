<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

final class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        try {
            $table = (new Product())->getTable();

            $typeCol  = Schema::hasColumn($table, 'type')
                ? 'type'
                : (Schema::hasColumn($table, 'category') ? 'category' : null);

            $stockCol = Schema::hasColumn($table, 'stock')
                ? 'stock'
                : (Schema::hasColumn($table, 'quantity') ? 'quantity' : null);

            $imageCol = Schema::hasColumn($table, 'image') ? 'image' : null;

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
                ['name' => 'Piglet (8 weeks)',        'type' => 'Piglet',       'price' => 2500,  'stock' => 12, 'img' => ['8weekspig.jpg','8weekspig.jpeg']],
                ['name' => 'Grower Pig (30-40kg)',    'type' => 'Live pig',     'price' => 8500,  'stock' => 6,  'img' => ['30kgpig.jpg','30kgpig.jpeg']],
                ['name' => 'Sow (Breeding Ready)',    'type' => 'Live pig',     'price' => 18500, 'stock' => 2,  'img' => ['pig.jpg','pig.jpeg']],

                // Poultry
                ['name' => 'Native Chicken (Live)',   'type' => 'Live chicken', 'price' => 350,   'stock' => 40, 'img' => ['chicken.jpg','chicken.jpeg']],
                ['name' => 'Egg-Laying Hen',          'type' => 'Layer',        'price' => 480,   'stock' => 18, 'img' => ['eggs.jpg','eggs.jpeg']],
                ['name' => 'Fresh Eggs (1 dozen)',    'type' => 'Egg',          'price' => 120,   'stock' => 25, 'img' => ['eggs.jpg','eggs.jpeg']],

                // Vegetables (including the missing ones)
                ['name' => 'Fresh Eggplant (1kg)',    'type' => 'Vegetable',    'price' => 70,    'stock' => 25, 'img' => ['eggplant.jpeg','eggplant.jpg']],
                ['name' => 'Tomatoes (1kg)',          'type' => 'Vegetable',    'price' => 85,    'stock' => 20, 'img' => ['tomato.jpg','tomato.jpeg']],
                ['name' => 'Lettuce (1kg)',           'type' => 'Vegetable',    'price' => 90,    'stock' => 25, 'img' => ['lettuce.jpg','lettuce.jpeg']],
                ['name' => 'Potato (1kg)',            'type' => 'Vegetable',    'price' => 85,    'stock' => 25, 'img' => ['potato.jpg','potato.jpeg']],
                ['name' => 'Carrots (1kg)',           'type' => 'Vegetable',    'price' => 70,    'stock' => 25, 'img' => ['carrots.jpg','carrots.jpeg']],
                ['name' => 'Cabbage (1pc)',           'type' => 'Vegetable',    'price' => 60,    'stock' => 20, 'img' => ['cabbage.jpg','cabbage.jpeg']],

                // Fruits (make sure these files exist in public/images/placeholders/)
                ['name' => 'Bananas (1 dozen)',       'type' => 'Fruit',        'price' => 60,    'stock' => 30, 'img' => ['banana.jpeg','banana.jpg']],
                ['name' => 'Watermelon (1pc)',        'type' => 'Fruit',        'price' => 180,   'stock' => 15, 'img' => ['watermelon.jpeg','watermelon.jpg']],
                ['name' => 'Avocado (1kg)',           'type' => 'Fruit',        'price' => 220,   'stock' => 12, 'img' => ['avocado.jpeg','avocado.jpg']],
                ['name' => 'Orange (1kg)',            'type' => 'Fruit',        'price' => 140,   'stock' => 20, 'img' => ['orange.jpeg','orange.jpg']],
                ['name' => 'Mango (1kg)',             'type' => 'Fruit',        'price' => 160,   'stock' => 18, 'img' => ['mango.jpeg','mango.jpg']],
            ];

            foreach ($items as $i) {
                $update = ['price' => $i['price']];

                if ($typeCol) {
                    $update[$typeCol] = $i['type'];
                }
                if ($stockCol) {
                    $update[$stockCol] = $i['stock'];
                }
                if ($imageCol) {
                    $img = $pickImage($i['img']);
                    if ($img) {
                        $update[$imageCol] = $img; // filename only
                    }
                }

                Product::query()->updateOrCreate(
                    ['name' => $i['name']],
                    $update
                );
            }
        } finally {
            Model::reguard();
        }
    }
}