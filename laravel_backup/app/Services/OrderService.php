<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Create a new order and reserve stock.
     */
    public function createOrder(User $user, Product $product, int $quantity, string $currency = 'USD'): Order
    {
        return DB::transaction(function () use ($user, $product, $quantity, $currency) {
            // Check stock
            $count = VoucherCode::where('product_id', $product->id)
                ->where('status', 'available')
                ->lockForUpdate() // Prevent race conditions
                ->count();

            if ($count < $quantity) {
                throw new Exception("Insufficient stock. Only {$count} available.");
            }

            // Reserve codes
            $codes = VoucherCode::where('product_id', $product->id)
                ->where('status', 'available')
                ->limit($quantity)
                ->get();

            $totalAmount = $product->price * $quantity;

            $order = Order::create([
                'uuid' => (string) str()->uuid(),
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'currency' => $currency,
                'status' => 'pending',
                'metadata' => ['product_name' => $product->name, 'quantity' => $quantity],
            ]);

            foreach ($codes as $code) {
                $code->update([
                    'order_id' => $order->id,
                    'status' => 'reserved',
                    'reserved_at' => now(),
                ]);
            }

            return $order;
        });
    }

    /**
     * Mark order as paid and fulfill codes.
     */
    public function fulfillOrder(Order $order, string $paymentProviderRef): void
    {
        DB::transaction(function () use ($order, $paymentProviderRef) {
            $order->update([
                'status' => 'paid',
                'payment_provider_ref' => $paymentProviderRef,
            ]);

            // Release codes to 'sold'
            VoucherCode::where('order_id', $order->id)
                ->update([
                    'status' => 'sold',
                    'sold_at' => now(),
                ]);
        });
    }
}
