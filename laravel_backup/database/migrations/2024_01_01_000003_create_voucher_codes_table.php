<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->text('code_encrypted');
            $table->string('code_hash')->unique(); // SHA-256 hash for uniqueness check
            $table->string('status')->default('available'); // available, reserved, sold, used
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'status']); // For finding available stock
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_codes');
    }
};
