@extends('layouts.front')

@section('title', 'Shopping Cart - ' . setting('site_name'))
@section('page-title', 'Shopping Cart')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Shopping Cart</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    @if(!isset($cart) || $cart->items->isEmpty())
        <!-- Empty Cart -->
        <div class="empty-cart text-center py-5">
            <div class="empty-cart-icon mb-4">
                <i class="fas fa-shopping-cart fa-5x" style="color: var(--sand);"></i>
            </div>
            <h3 class="mb-3">Your Cart is Empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('shop') }}" class="btn-modern btn-primary-modern">
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
        </div>
    @else
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="cart-items-container">
                    <div class="cart-header">
                        <h5 class="mb-0">Cart Items ({{ $cart->items->sum('quantity') }})</h5>
                    </div>

                    <div class="cart-items-list">
                        @foreach($cart->items as $item)
                        <div class="cart-item" data-id="{{ $item->id }}">
                            <div class="cart-item-image">
                                <a href="{{ route('product.details', $item->product->slug) }}">
                                    @if($item->product->featured_image)
                                        <img src="{{ asset('storage/' . $item->product->featured_image) }}"
                                             alt="{{ $item->product->name }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587"
                                             alt="{{ $item->product->name }}">
                                    @endif
                                </a>
                            </div>

                            <div class="cart-item-details">
                                <h5 class="cart-item-title">
                                    <a href="{{ route('product.details', $item->product->slug) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </h5>

                                @if($item->options)
                                    <div class="cart-item-options">
                                        @php $options = is_array($item->options) ? $item->options : json_decode($item->options, true); @endphp
                                        @if(!empty($options['size']))
                                            <span class="option-badge">Size: {{ $options['size'] }}</span>
                                        @endif
                                        @if(!empty($options['flavor']))
                                            <span class="option-badge">Flavor: {{ $options['flavor'] }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="cart-item-price">
                                    {{ format_currency($item->unit_price) }} each
                                </div>
                            </div>

                            <div class="cart-item-quantity">
                                <div class="quantity-control">
                                    <button class="quantity-btn update-cart" data-id="{{ $item->id }}" data-action="decrease">-</button>
                                    <input type="text" class="quantity-input" value="{{ $item->quantity }}"
                                           data-id="{{ $item->id }}" readonly>
                                    <button class="quantity-btn update-cart" data-id="{{ $item->id }}" data-action="increase">+</button>
                                </div>
                            </div>

                            <div class="cart-item-subtotal">
                                <span class="subtotal-label">Subtotal:</span>
                                <span class="subtotal-value">{{ format_currency($item->unit_price * $item->quantity) }}</span>
                            </div>

                            <div class="cart-item-remove">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="remove-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-btn" title="Remove item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="cart-actions">
                        <a href="{{ route('shop') }}" class="btn-continue">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST" class="clear-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-clear" onclick="return confirm('Clear all items?')">
                                <i class="fas fa-trash me-2"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="summary-title">Order Summary</h5>

                    @php
                        $subtotal = $cart->total_amount;
                        $deliveryCharge = setting('delivery_charges', 10);
                        $freeThreshold = setting('free_delivery_threshold', 100);
                        $deliveryFee = ($subtotal >= $freeThreshold) ? 0 : $deliveryCharge;
                        $tax = $subtotal * (setting('tax_rate', 10) / 100);
                        $grandTotal = $subtotal + $tax + $deliveryFee;
                    @endphp

                    <div class="summary-row">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value">{{ format_currency($subtotal) }}</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Tax ({{ setting('tax_rate', 10) }}%):</span>
                        <span class="summary-value">{{ format_currency($tax) }}</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Delivery:</span>
                        <span class="summary-value">
                            @if($deliveryFee == 0)
                                <span class="text-success">Free</span>
                            @else
                                {{ format_currency($deliveryFee) }}
                            @endif
                        </span>
                    </div>

                    @if($subtotal < $freeThreshold)
                        <div class="delivery-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Add {{ format_currency($freeThreshold - $subtotal) }} more for free delivery!
                        </div>
                    @endif

                    <hr class="summary-divider">

                    <div class="summary-row total">
                        <span class="total-label">Total:</span>
                        <span class="total-value">{{ format_currency($grandTotal) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="checkout-btn">
                        Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                    </a>

                    <p class="secure-info">
                        <i class="fas fa-shield-alt me-1"></i>
                        Secure checkout - SSL encrypted
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* ===== CART STYLES ===== */
.cart-items-container {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.cart-header {
    padding: 20px;
    border-bottom: 1px solid var(--sand);
    background: var(--cream);
}

.cart-header h5 {
    font-family: 'Prata', serif;
    color: var(--charcoal);
    margin: 0;
}

.cart-items-list {
    padding: 20px;
}

.cart-item {
    display: grid;
    grid-template-columns: 100px 1fr auto auto auto;
    gap: 20px;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--sand);
    transition: all 0.3s;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item:hover {
    background: var(--cream);
}

.cart-item-image {
    width: 100px;
    height: 100px;
    overflow: hidden;
    border-radius: 12px;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.cart-item-image img:hover {
    transform: scale(1.05);
}

.cart-item-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.cart-item-title a {
    color: var(--charcoal);
    text-decoration: none;
    transition: color 0.3s;
}

.cart-item-title a:hover {
    color: var(--terracotta);
}

.cart-item-options {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.option-badge {
    background: var(--cream);
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 0.7rem;
    color: var(--charcoal);
    border: 1px solid var(--sand);
}

.cart-item-price {
    color: var(--taupe);
    font-size: 0.9rem;
}

.cart-item-quantity {
    text-align: center;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 5px;
    background: var(--cream);
    border-radius: 30px;
    padding: 3px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border: none;
    background: white;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
}

.quantity-btn:hover {
    background: var(--terracotta);
    color: white;
}

.quantity-input {
    width: 40px;
    text-align: center;
    border: none;
    background: transparent;
    font-weight: 600;
}

.cart-item-subtotal {
    text-align: right;
}

.subtotal-label {
    display: block;
    font-size: 0.8rem;
    color: var(--taupe);
}

.subtotal-value {
    font-weight: 700;
    color: var(--terracotta);
    font-size: 1.2rem;
}

.cart-item-remove .remove-btn {
    background: none;
    border: none;
    color: var(--danger);
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1.1rem;
}

.cart-item-remove .remove-btn:hover {
    transform: scale(1.1);
    color: #ff4757;
}

.cart-actions {
    padding: 20px;
    background: var(--cream);
    display: flex;
    justify-content: space-between;
    border-top: 1px solid var(--sand);
}

.btn-continue {
    padding: 10px 20px;
    background: white;
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 30px;
    transition: all 0.3s;
    border: 1px solid var(--sand);
}

.btn-continue:hover {
    background: var(--sand);
}

.btn-clear {
    padding: 10px 20px;
    background: white;
    color: var(--danger);
    border: 1px solid var(--danger);
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-clear:hover {
    background: var(--danger);
    color: white;
}

/* ===== CART SUMMARY ===== */
.cart-summary {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 100px;
}

.summary-title {
    font-family: 'Prata', serif;
    font-size: 1.2rem;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--sand);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.summary-label {
    color: var(--taupe);
}

.summary-value {
    font-weight: 600;
    color: var(--charcoal);
}

.total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid var(--sand);
}

.total-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--charcoal);
}

.total-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--terracotta);
}

.delivery-info {
    background: #e3f2fd;
    color: #0d47a1;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.checkout-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: var(--terracotta);
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.3s;
}

.checkout-btn:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.secure-info {
    text-align: center;
    color: var(--taupe);
    font-size: 0.8rem;
    margin-top: 15px;
}

/* ===== EMPTY CART ===== */
.empty-cart {
    background: white;
    border-radius: 20px;
    padding: 60px 20px;
    box-shadow: var(--shadow-sm);
}

.empty-cart-icon {
    color: var(--sand);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .cart-item {
        grid-template-columns: 80px 1fr auto;
        gap: 15px;
    }

    .cart-item-subtotal {
        grid-column: 1 / -1;
        text-align: left;
        padding-left: 95px;
    }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .cart-item-image {
        margin: 0 auto;
    }

    .cart-item-subtotal {
        padding-left: 0;
        text-align: center;
    }

    .cart-actions {
        flex-direction: column;
        gap: 10px;
    }

    .btn-continue, .btn-clear {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
    // Update cart via AJAX
    document.querySelectorAll('.update-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const action = this.dataset.action;
            const input = this.closest('.quantity-control').querySelector('.quantity-input');
            let currentVal = parseInt(input.value);

            let newVal = action === 'increase' ? currentVal + 1 : currentVal - 1;

            if (newVal >= 1) {
                fetch(`/cart/update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: newVal })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Remove form confirmation
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Remove this item from cart?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
