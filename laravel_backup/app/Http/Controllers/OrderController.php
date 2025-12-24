<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        try {
            $order = $this->orderService->createOrder(
                Auth::user(),
                $product,
                $request->quantity
            );

            return redirect()->route('orders.show', $order->uuid);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Show order status / payment page.
     */
    public function show(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'stripeKey' => env('STRIPE_KEY'), // Mock or Flutterwave public key
        ]);
    }
    
    /**
     * List user orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['voucherCodes']) // Eager load codes? Maybe only count?
            ->latest()
            ->paginate(10);
            
        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Verify payment (Mock).
     */
    public function verifyPayment(Request $request, Order $order)
    {
        // In real life, verify signature from Flutterwave/Stripe
        // For now, assume success if query param 'status' is 'successful'
        
        if ($request->query('status') === 'successful') {
            try {
                $this->orderService->fulfillOrder($order, $request->query('tx_ref') ?? 'mock_ref');
                return redirect()->route('orders.show', $order->uuid)->with('success', 'Payment successful!');
            } catch (\Exception $e) {
                return back()->withErrors(['message' => 'Payment verification failed: ' . $e->getMessage()]);
            }
        }

        return back()->withErrors(['message' => 'Payment failed or cancelled.']);
    }
}
