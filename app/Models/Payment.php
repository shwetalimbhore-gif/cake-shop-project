<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'order_id',
        'method',
        'currency',
        'amount',
        'status',
        'json_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
