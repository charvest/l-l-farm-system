<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->latest()
            ->limit(8)
            ->get();

        return view('welcome', compact('featuredProducts')); // change to your home view if needed
    }
}
