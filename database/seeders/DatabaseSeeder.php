<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Pig', 'Chicken', 'Produce'] as $name) {
            Category::query()->updateOrCreate(['name' => $name], []);
        }
    }
}

 
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()
            ->whereIn('name', ['Pig', 'Chicken', 'Produce'])
            ->pluck('id', 'name');

        $products = [
            // Pigs
            [
                'category' => 'Pig',
                'name' => 'Piglet (8 weeks)',
                'type' => 'Piglet',
                'price' => '2500.00',
                'stock' => 12,
                'health' => 'Vaccinated',
                'size' => 'Small',
                'gender' => 'Mixed',
                'status' => 'Available',
                'availability_date' => now()->toDateString(),
                'description' => 'Healthy piglets, ready for reservation.',
            ],
            [
                'category' => 'Pig',
                'name' => 'Grower Pig (30–40kg)',
                'type' => 'Live pig',
                'price' => '8500.00',
                'stock' => 6,
                'health' => 'Healthy',
                'size' => 'Medium',
                'gender' => 'Male',
                'status' => 'Available',
                'availability_date' => now()->addDays(3)->toDateString(),
                'description' => 'Great for backyard raising or small farms.',
            ],
            [
                'category' => 'Pig',
                'name' => 'Sow (Breeding Ready)',
                'type' => 'Live pig',
                'price' => '18500.00',
                'stock' => 2,
                'health' => 'Healthy',
                'size' => 'Large',
                'gender' => 'Female',
                'status' => 'Available',
                'availability_date' => now()->addDays(7)->toDateString(),
                'description' => 'Breeding sow with good temperament.',
            ],

            // Chickens
            [
                'category' => 'Chicken',
                'name' => 'Native Chicken (Live)',
                'type' => 'Live chicken',
                'price' => '350.00',
                'stock' => 40,
                'health' => 'Healthy',
                'size' => 'Medium',
                'gender' => 'Mixed',
                'status' => 'Available',
                'availability_date' => now()->toDateString(),
                'description' => 'Farm-raised native chickens.',
            ],
            [
                'category' => 'Chicken',
                'name' => 'Broiler Chicken (Ready for Meat)',
                'type' => 'Broiler',
                'price' => '220.00',
                'stock' => 55,
                'health' => 'Healthy',
                'size' => 'Medium',
                'gender' => 'Mixed',
                'status' => 'Available',
                'availability_date' => now()->addDays(2)->toDateString(),
                'description' => 'Meat-type broilers, good weight and quality.',
            ],
            [
                'category' => 'Chicken',
                'name' => 'Egg-Laying Hen',
                'type' => 'Layer',
                'price' => '480.00',
                'stock' => 18,
                'health' => 'Vaccinated',
                'size' => 'Medium',
                'gender' => 'Female',
                'status' => 'Available',
                'availability_date' => now()->addDays(1)->toDateString(),
                'description' => 'Laying hens, productive and farm-raised.',
            ],

            // Produce
            [
                'category' => 'Produce',
                'name' => 'Fresh Eggplant (1kg)',
                'type' => 'Vegetable',
                'price' => '70.00',
                'stock' => 25,
                'health' => null,
                'size' => null,
                'gender' => null,
                'status' => 'Available',
                'availability_date' => now()->toDateString(),
                'description' => 'Freshly harvested eggplant.',
            ],
            [
                'category' => 'Produce',
                'name' => 'Bananas (1 dozen)',
                'type' => 'Fruit',
                'price' => '60.00',
                'stock' => 30,
                'health' => null,
                'size' => null,
                'gender' => null,
                'status' => 'Available',
                'availability_date' => now()->toDateString(),
                'description' => 'Sweet bananas from our farm.',
            ],
            [
                'category' => 'Produce',
                'name' => 'Tomatoes (1kg)',
                'type' => 'Vegetable',
                'price' => '85.00',
                'stock' => 20,
                'health' => null,
                'size' => null,
                'gender' => null,
                'status' => 'Available',
                'availability_date' => now()->toDateString(),
                'description' => 'Juicy red tomatoes, great for cooking.',
            ],
        ];

        foreach ($products as $p) {
            $categoryName = $p['category'];
            $categoryId = $categoryIds[$categoryName] ?? null;

            if (!$categoryId) {
                continue;
            }

            unset($p['category']);

            Product::query()->updateOrCreate(
                ['name' => $p['name'], 'category_id' => $categoryId],
                ['category_id' => $categoryId] + $p
            );
        }
    }
}


// File: database/seeders/DatabaseSeeder.php
 

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}