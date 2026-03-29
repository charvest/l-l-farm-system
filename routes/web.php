<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Base featured query (exclude broiler)
    $featuredProducts = Product::query()
        ->whereNot(function ($q) {
            $q->where('type', 'Broiler')
              ->orWhere('name', 'like', '%Broiler%');
        })
        ->orderByDesc('created_at')
        ->take(8)
        ->get();

    // Force include carrots if it exists (optional)
    $carrots = Product::query()
        ->where('name', 'like', '%carrot%')
        ->orWhere('type', 'like', '%carrot%')
        ->first();

    if ($carrots && !$featuredProducts->contains('id', $carrots->id)) {
        if ($featuredProducts->count() >= 8) {
            $featuredProducts->splice(7, 1, [$carrots]);
        } else {
            $featuredProducts->push($carrots);
        }
    }

    $categories = Category::query()->orderBy('name')->get();

    $testimonials = [
        ['name' => 'Local Customer', 'title' => 'Fresh and affordable', 'quote' => 'Super fresh produce and easy ordering. We love the weekly updates!'],
        ['name' => 'Neighborhood Buyer', 'title' => 'Great service', 'quote' => 'Fast response and clear availability dates. Quality animals too.'],
        ['name' => 'Small Business Owner', 'title' => 'Reliable supplier', 'quote' => 'Bulk orders are smooth and the farm is very accommodating.'],
    ];

    $faqs = [
        ['q' => 'Do you offer reservation for piglets?', 'a' => 'Yes. Reserve piglets through the products page and we will confirm availability and pickup date.'],
        ['q' => 'Do you deliver?', 'a' => 'Delivery can be arranged depending on location and order size. Contact us for details.'],
        ['q' => 'What payment methods are accepted?', 'a' => 'You can start with cash on pickup. Other options can be added later (GCash/Bank transfer).'],
        ['q' => 'How do I know when items are available?', 'a' => 'Products show stock and availability dates. Weekly deals and updates will also be posted on the homepage.'],
    ];

    return view('welcome', compact('featuredProducts', 'categories', 'testimonials', 'faqs'));
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
