@extends('layouts.front')

@section('title', 'Shopping Cart - ' . setting('site_name'))
@section('page-title', 'Shopping Cart')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Shopping Cart</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    @if(empty($cart))
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-5x text-muted"></i>
            </div>
            <h3 class="mb-3">Your Cart is Empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
        </div>
    @else
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Cart Items ({{ count($cart) }})</h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach($cart as $id => $item)
                        <div class="row align-items-center cart-item mb-4 pb-4 border-bottom">
                            <div class="col-md-2 col-4">
                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/100x100' }}"
                                     alt="{{ $item['name'] }}"
                                     class="img-fluid rounded-3">
                            </div>

                            <div class="col-md-4 col-8">
                                <h6 class="fw-semibold mb-2">
                                    <a href="{{ route('product.details', $item['slug']) }}" class="text-decoration-none text-dark">
                                        {{ $item['name'] }}
                                    </a>
                                </h6>
                                @if(!empty($item['size']))
                                    <p class="mb-1 small"><strong>Size:</strong> {{ $item['size'] }}</p>
                                @endif
                                @if(!empty($item['flavor']))
                                    <p class="mb-0 small"><strong>Flavor:</strong> {{ $item['flavor'] }}</p>
                                @endif
                            </div>

                            <div class="col-md-3 col-6 mt-3 mt-md-0">
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary update-cart"
                                            data-id="{{ $id }}"
                                            data-action="decrease">-</button>
                                    <input type="text" class="form-control text-center quantity-input"
                                           value="{{ $item['quantity'] }}"
                                           data-id="{{ $id }}"
                                           style="width: 40px;">
                                    <button class="btn btn-outline-secondary update-cart"
                                            data-id="{{ $id }}"
                                            data-action="increase">+</button>
                                </div>
                            </div>

                            <div class="col-md-2 col-4 mt-3 mt-md-0 text-center">
                                <span class="fw-bold text-primary">
                                    {{ format_currency($item['price'] * $item['quantity']) }}
                                </span>
                            </div>

                            <div class="col-md-1 col-2 mt-3 mt-md-0 text-end">
                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Clear all items?')">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">{{ format_currency($total) }}</span>
                        </div>

                        @php
                            $deliveryCharge = setting('delivery_charges', 10);
                            $freeThreshold = setting('free_delivery_threshold', 100);
                            $deliveryFee = ($total >= $freeThreshold) ? 0 : $deliveryCharge;
                        @endphp

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

                        @if($total < $freeThreshold)
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Add {{ format_currency($freeThreshold - $total) }} more for free delivery!
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary fs-4">{{ format_currency($total + $deliveryFee) }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>

                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure checkout - SSL encrypted
                        </p>
                    </div>
                </div>

                <!-- Coupon Code -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="mb-3">Have a Coupon?</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter code">
                            <button class="btn btn-outline-primary" type="button">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Update cart quantity via AJAX
    $('.update-cart').click(function() {
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
                }
            });
        } else {
            input.val(1);
        }
    });
</script>
@endpush
@endsection
