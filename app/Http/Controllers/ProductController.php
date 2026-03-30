<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));
        $sort = trim((string) $request->query('sort', 'newest'));

        $productsQuery = Product::query();

        if ($q !== '') {
            $productsQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if ($type !== '') {
            $productsQuery->where('type', $type);
        }

        match ($sort) {
            'price_asc'  => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'name_asc'   => $productsQuery->orderBy('name', 'asc'),
            'name_desc'  => $productsQuery->orderBy('name', 'desc'),
            default      => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(24)->withQueryString();

        $types = Product::query()
            ->select('type')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('products.index', [
            'products' => $products,
            'types' => $types,
            'filters' => compact('q', 'type', 'sort'),
        ]);
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }
}