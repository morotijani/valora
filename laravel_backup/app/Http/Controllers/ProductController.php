<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        return Inertia::render('Catalog/Index', [
            'products' => $products
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        return Inertia::render('Catalog/Show', [
            'product' => $product
        ]);
    }
}
