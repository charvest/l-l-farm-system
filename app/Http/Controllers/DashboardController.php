<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        $totalRevenue = OrderItem::select(DB::raw('SUM(quantity * price) as total'))
            ->value('total') ?? 0;

        // NEW PRODUCT DATA
        $totalProducts = Product::count();
        $availableProducts = Product::where('status', 'Available')->count();
        $outOfStock = Product::where('status', 'Out of Stock')->count();

        $recentOrders = Order::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'totalProducts',
            'availableProducts',
            'outOfStock',
            'recentOrders'
        ));
    }
}