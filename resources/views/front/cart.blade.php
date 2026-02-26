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
    @if($cart->items->isEmpty())
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div class="mb-4">
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
                <div class="card-modern mb-4">
                    <div class="card-header bg-transparent p-4">
                        <h5 class="mb-0">Cart Items ({{ $cart->items->sum('quantity') }})</h5>
                    </div>

                    <div class="p-4">
                        @foreach($cart->items as $item)
                        <div class="row align-items-center cart-item mb-4 pb-4 border-bottom">
                            <div class="col-md-2 col-4">
                                @if($item->product->featured_image)
                                    <img src="{{ asset('storage/' . $item->product->featured_image) }}"
                                         alt="{{ $item->product->name }}"
                                         class="img-fluid rounded-3">
                                @else
                                    <img src="https://via.placeholder.com/100x100"
                                         alt="{{ $item->product->name }}"
                                         class="img-fluid rounded-3">
                                @endif
                            </div>

                            <div class="col-md-4 col-8">
                                <h6 class="fw-semibold mb-2">
                                    <a href="{{ route('product.details', $item->product->slug) }}"
                                       class="text-decoration-none" style="color: var(--charcoal);">
                                        {{ $item->product->name }}
                                    </a>
                                </h6>
                                @if($item->options_text)
                                    <p class="mb-0 small text-muted">{{ $item->options_text }}</p>
                                @endif
                            </div>

                            <div class="col-md-3 col-6 mt-3 mt-md-0">
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary update-cart"
                                            data-id="{{ $item->id }}"
                                            data-action="decrease">-</button>
                                    <input type="text" class="form-control text-center quantity-input"
                                           value="{{ $item->quantity }}"
                                           data-id="{{ $item->id }}"
                                           style="width: 40px;">
                                    <button class="btn btn-outline-secondary update-cart"
                                            data-id="{{ $item->id }}"
                                            data-action="increase">+</button>
                                </div>
                            </div>

                            <div class="col-md-2 col-4 mt-3 mt-md-0 text-center">
                                <span class="fw-bold" style="color: var(--terracotta);">
                                    {{ format_currency($item->subtotal) }}
                                </span>
                            </div>

                            <div class="col-md-1 col-2 mt-3 mt-md-0 text-end">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0"
                                            onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('shop') }}" class="btn-modern btn-outline-modern">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-modern btn-outline-modern"
                                        onclick="return confirm('Clear all items?')">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card-modern">
                    <div class="card-header bg-transparent p-4">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>

                    <div class="p-4">
                        @php
                            $deliveryCharge = setting('delivery_charges', 10);
                            $freeThreshold = setting('free_delivery_threshold', 100);
                            $deliveryFee = ($cart->total_amount >= $freeThreshold) ? 0 : $deliveryCharge;
                            $tax = $cart->total_amount * (setting('tax_rate', 10) / 100);
                            $grandTotal = $cart->total_amount + $tax + $deliveryFee;
                        @endphp

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">{{ format_currency($cart->total_amount) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tax ({{ setting('tax_rate', 10) }}%):</span>
                            <span class="fw-semibold">{{ format_currency($tax) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery:</span>
                            <span class="fw-semibold">
                                @if($deliveryFee == 0)
                                    <span class="text-success">Free</span>
                                @else
                                    {{ format_currency($deliveryFee) }}
                                @endif
                            </span>
                        </div>

                        @if($cart->total_amount < $freeThreshold)
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Add {{ format_currency($freeThreshold - $cart->total_amount) }} more for free delivery!
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold fs-4" style="color: var(--terracotta);">
                                {{ format_currency($grandTotal) }}
                            </span>
                        </div>

                        <!-- FIXED: Check if cart is not empty before showing checkout button -->
                        @if($cart->items->isNotEmpty())
                            <a href="{{ route('checkout.index') }}" class="btn-modern btn-primary-modern w-100">
                                Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @else
                            <a href="{{ route('shop') }}" class="btn-modern btn-primary-modern w-100">
                                Shop Now <i class="fas fa-shopping-bag ms-2"></i>
                            </a>
                        @endif

                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure checkout - SSL encrypted
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Update cart via AJAX
    $('.update-cart').click(function(e) {
        e.preventDefault();
        let button = $(this);
        let id = button.data('id');
        let action = button.data('action');
        let input = button.closest('.input-group').find('.quantity-input');
        let currentVal = parseInt(input.val());

        let newVal = action === 'increase' ? currentVal + 1 : currentVal - 1;

        if (newVal >= 1) {
            $.ajax({
                url: '{{ route("cart.update", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: newVal
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update cart');
                }
            });
        }
    });

    // Update on manual input
    $('.quantity-input').change(function() {
        let input = $(this);
        let id = input.data('id');
        let newVal = parseInt(input.val());

        if (newVal >= 1) {
            $.ajax({
                url: '{{ route("cart.update", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: newVal
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update cart');
                    input.val(1);
                }
            });
        } else {
            input.val(1);
        }
    });
</script>
@endpush
@endsection
