<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'base_price',
        'sku',
        'stock_quantity',
        'in_stock',
        'category_id',
        'featured_image',
        'gallery_images',
        'sizes',
        'size_prices',
        'size_servings',
        'flavors',
        'flavor_prices',
        'has_custom_options',
        'is_featured',
        'is_eggless',
        'is_active',
        'views'
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'sizes' => 'array',
        'size_prices' => 'array',
        'size_servings' => 'array',
        'flavors' => 'array',
        'flavor_prices' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_eggless' => 'boolean',
        'is_active' => 'boolean',
        'in_stock' => 'boolean',
        'has_custom_options' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);

            // Set base price as the smallest size price or regular price
            if (empty($product->base_price)) {
                $sizePrices = json_decode($product->size_prices, true);
                $product->base_price = !empty($sizePrices) ? min($sizePrices) : $product->regular_price;
            }

            // Auto-set in_stock based on stock_quantity
            $product->in_stock = ($product->stock_quantity > 0);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);

            // Auto-update in_stock when stock_quantity changes
            $product->in_stock = ($product->stock_quantity > 0);
        });

        static::saving(function ($product) {
            // Always ensure in_stock reflects stock_quantity
            $product->in_stock = ($product->stock_quantity > 0);
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // Scopes - IMPORTANT: Only show in-stock products
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('in_stock', true);  // Only show in-stock
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->where('is_active', true)
                     ->where('in_stock', true);  // Only show in-stock
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true)
                     ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('in_stock', false)
                     ->orWhere('stock_quantity', '<=', 0);
    }

    public function scopeEggless($query)
    {
        return $query->where('is_eggless', true)
                     ->where('in_stock', true);  // Only show in-stock
    }

    // Check if product is available for purchase
    public function isAvailable()
    {
        return $this->is_active && $this->in_stock && $this->stock_quantity > 0;
    }

    // Get available quantity
    public function getAvailableQuantity()
    {
        return $this->stock_quantity;
    }

    // Decrement stock when order is placed
    public function decrementStock($quantity = 1)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->in_stock = ($this->stock_quantity > 0);
            $this->save();
            return true;
        }
        return false;
    }

    // Increment stock (for order cancellations)
    public function incrementStock($quantity = 1)
    {
        $this->stock_quantity += $quantity;
        $this->in_stock = true;
        $this->save();
        return true;
    }

    // Accessors
    public function getDisplayPriceAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->regular_price) {
            return $this->sale_price;
        }
        return $this->regular_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->regular_price) {
            return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }


    /**
     * Get formatted sizes with prices
     */
    public function getFormattedSizesAttribute()
    {
        // These are already arrays because of $casts
        $sizes = $this->sizes ?? [];
        $sizePrices = $this->size_prices ?? [];
        $sizeServings = $this->size_servings ?? [];

        // Ensure they're arrays
        if (is_string($sizes)) {
            $sizes = json_decode($sizes, true) ?? [];
        }
        if (is_string($sizePrices)) {
            $sizePrices = json_decode($sizePrices, true) ?? [];
        }
        if (is_string($sizeServings)) {
            $sizeServings = json_decode($sizeServings, true) ?? [];
        }

        $result = [];
        foreach ($sizes as $index => $size) {
            $result[] = [
                'name' => $size,
                'price' => $sizePrices[$index] ?? $this->base_price,
                'servings' => $sizeServings[$index] ?? null,
            ];
        }
        return $result;
    }

    /**
     * Get formatted flavors with prices
     */
    public function getFormattedFlavorsAttribute()
    {
        // These are already arrays because of $casts
        $flavors = $this->flavors ?? [];
        $flavorPrices = $this->flavor_prices ?? [];

        // Ensure they're arrays
        if (is_string($flavors)) {
            $flavors = json_decode($flavors, true) ?? [];
        }
        if (is_string($flavorPrices)) {
            $flavorPrices = json_decode($flavorPrices, true) ?? [];
        }

        $result = [];
        foreach ($flavors as $index => $flavor) {
            $result[] = [
                'name' => $flavor,
                'extra_price' => $flavorPrices[$index] ?? 0,
            ];
        }
        return $result;
    }

    /**
     * Get starting price
     */
    public function getStartingPriceAttribute()
    {
        $sizePrices = $this->size_prices ?? [];

        // Ensure it's an array
        if (is_string($sizePrices)) {
            $sizePrices = json_decode($sizePrices, true) ?? [];
        }

        if (!empty($sizePrices)) {
            return min($sizePrices);
        }
        return $this->sale_price ?? $this->regular_price;
    }
}
