<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_orders' => Order::count(),
                'total_sales' => Order::where('status', 'paid')->sum('total_amount'),
                'total_products' => Product::count(),
                'total_users' => User::count(),
            ]
        ]);
    }
}
