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
        'sku',
        'stock_quantity',
        'in_stock',
        'category_id',
        'featured_image',
        'gallery_images',
        'sizes',
        'flavors',
        'is_featured',
        'is_eggless',
        'is_active',
        'views',
        'size_prices',
        'flavor_prices',

    ];

    protected $casts = [
        'gallery_images' => 'array',
        'sizes' => 'array',
        'flavors' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_eggless' => 'boolean',
        'is_active' => 'boolean',
        'in_stock' => 'boolean',
        'size_prices' => 'array',
        'flavor_prices' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

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

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

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
}
