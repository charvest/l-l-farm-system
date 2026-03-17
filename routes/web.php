<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController; 
use App\Models\Product;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/products', function () {
    $products = Product::all();
    return view('products.index', compact('products'));
})->name('products.index');

use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');
    
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

use Illuminate\Support\Facades\Auth;
use App\Models\Order;

Route::get('/profile', function () {

    $user = Auth::user();

    $orders = Order::where('user_id', $user->id)
        ->latest()
        ->get();

    return view('profile.edit', [
        'user' => $user,
        'orders' => $orders
    ]);

})->middleware(['auth'])->name('profile.edit');


Route::get('/orders/{id}', function ($id) {

    $order = Order::with('items.product')->findOrFail($id);

    return view('orders.show', compact('order'));

})->middleware('auth')->name('orders.show');

require __DIR__.'/auth.php';