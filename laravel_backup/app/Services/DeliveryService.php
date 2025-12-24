<?php

namespace App\Services;

use App\Models\Order;
use App\Models\VoucherCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class DeliveryService
{
    /**
     * Reveal a code for a user if they own the order.
     */
    public function revealCode(VoucherCode $code, User $user): string
    {
        if ($code->order->user_id !== $user->id) {
            throw new \Exception("Unauthorized access to this code.");
        }

        if ($code->status !== 'sold') {
            throw new \Exception("Code is not available for revealing.");
        }

        // Log the view action
        Log::channel('audit')->info('Code revealed', [
            'user_id' => $user->id,
            'code_id' => $code->id,
        ]);

        // Code is automatically decrypted by the 'encrypted' cast accessor, 
        // but if we used manual encryption, we'd do Crypt::decryptString($code->code_encrypted);
        return $code->code_encrypted;
    }
}
