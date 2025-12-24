<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_id',
        'code_encrypted',
        'code_hash',
        'status',
        'reserved_at',
        'sold_at',
    ];

    protected $casts = [
        'code_encrypted' => 'encrypted',
        'reserved_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
