<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'brand',
        'country_code',
        'description',
        'face_value',
        'price',
        'currency',
        'is_active',
        'stock_quantity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'face_value' => 'decimal:2',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function voucherCodes()
    {
        return $this->hasMany(VoucherCode::class);
    }
}
