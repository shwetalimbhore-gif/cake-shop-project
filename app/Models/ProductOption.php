<?php
// app/Models/ProductOption.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = ['product_id', 'type', 'name', 'additional_price', 'order', 'is_active'];

    protected $casts = [
        'additional_price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
