<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand');
            $table->string('country_code', 2); // ISO 3166-1 alpha-2
            $table->text('description')->nullable();
            $table->decimal('face_value', 10, 2);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->integer('stock_quantity')->default(0); // Cache for quick lookup
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
