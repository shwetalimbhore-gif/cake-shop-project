<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the cart
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate cart total
     */
    public function calculateTotal()
    {
        $total = $this->items->sum(function($item) {
            return $item->unit_price * $item->quantity;
        });

        $this->update(['total_amount' => $total]);

        return $total;
    }

    /**
     * Get cart for current session/user
     */
    public static function getCart()
    {
        if (auth()->check()) {
            // User is logged in - get or create cart for user
            $cart = self::firstOrCreate(
                ['user_id' => auth()->id()],
                ['session_id' => session()->getId()]
            );
        } else {
            // Guest - get or create cart for session
            $cart = self::firstOrCreate(
                ['session_id' => session()->getId()],
                ['user_id' => null]
            );
        }

        return $cart;
    }
}
