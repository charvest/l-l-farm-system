<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Middleware\RequireLoginForCartAction;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;

Route::get('/oauth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.redirect');

Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.callback');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

Route::get('/', function () {
    $featuredProducts = Product::query()
        ->whereNot(function ($q) {
            $q->where('type', 'Broiler')->orWhere('name', 'like', '%Broiler%');
        })
        ->orderByDesc('created_at')
        ->take(8)
        ->get();

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
    Route::get('/', [CartController::class, 'index'])->middleware('auth')->name('index');

    Route::post('/add/{product}', [CartController::class, 'add'])
        ->middleware(RequireLoginForCartAction::class)
        ->name('add');

    Route::post('/update/{product}', [CartController::class, 'update'])
        ->middleware(RequireLoginForCartAction::class)
        ->name('update');

    Route::post('/remove/{product}', [CartController::class, 'remove'])
        ->middleware(RequireLoginForCartAction::class)
        ->name('remove');

    Route::post('/clear', [CartController::class, 'clear'])
        ->middleware(RequireLoginForCartAction::class)
        ->name('clear');
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

