<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Admin Product List.
     */
    public function index()
    {
        $products = Product::latest()->paginate(20);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'description' => 'nullable|string',
            'face_value' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_active' => 'boolean',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['stock_quantity'] = 0; // Initial stock is 0

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }
}
