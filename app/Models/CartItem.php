<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items'; // Make sure this matches your table name

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Get the cart that owns the item
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get subtotal for this item
     */
    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get display options
     */
    public function getOptionsTextAttribute()
    {
        if (!$this->options) {
            return '';
        }

        $text = [];
        if (isset($this->options['size'])) {
            $text[] = 'Size: ' . $this->options['size'];
        }
        if (isset($this->options['flavor'])) {
            $text[] = 'Flavor: ' . $this->options['flavor'];
        }

        return implode(' | ', $text);
    }
}
