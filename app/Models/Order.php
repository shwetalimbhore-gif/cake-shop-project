<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'notes',
        'admin_notes',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'order_type',
        'walkin_customer_name',
        'walkin_customer_phone',
        'walkin_notes',
        'created_by_admin',

    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Add this relationship for walk-in orders
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_admin');
    }

    // Add these helper methods
    public function isWalkin()
    {
        return $this->order_type === 'walkin';
    }

    public function isOnline()
    {
        return $this->order_type === 'online';
    }

    public function getOrderTypeBadgeAttribute()
    {
        if ($this->isWalkin()) {
            return '<span class="badge bg-warning"><i class="fas fa-store me-1"></i>Walk-in</span>';
        }
        return '<span class="badge bg-info"><i class="fas fa-globe me-1"></i>Online</span>';
    }

    public function getOrderTypeIconAttribute()
    {
        return $this->isWalkin() ? 'fa-store' : 'fa-globe';
    }

    public function getOrderTypeColorAttribute()
    {
        return $this->isWalkin() ? 'warning' : 'info';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_PROCESSING => 'bg-info',
            self::STATUS_CONFIRMED => 'bg-primary',
            self::STATUS_SHIPPED => 'bg-secondary',
            self::STATUS_DELIVERED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            self::STATUS_REFUNDED => 'bg-dark'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-dark'
        ];

        return $badges[$this->payment_status] ?? 'bg-secondary';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });
    }
}
