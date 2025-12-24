<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled, refunded
            $table->string('payment_method')->nullable();
            $table->string('payment_provider_ref')->nullable(); // External ID
            $table->json('metadata')->nullable(); // For snapshotting user info if needed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
