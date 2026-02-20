@extends('layouts.front')

@section('title', 'Checkout - ' . setting('site_name'))
@section('page-title', 'Checkout')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Checkout</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf

        <div class="row">
            <!-- Billing & Shipping Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Full Name *</label>
                                <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror"
                                       value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required>
                                @error('shipping_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email Address *</label>
                                <input type="email" name="shipping_email" class="form-control @error('shipping_email') is-invalid @enderror"
                                       value="{{ old('shipping_email', auth()->user()->email ?? '') }}" required>
                                @error('shipping_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone Number *</label>
                                <input type="text" name="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror"
                                       value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" required>
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Address *</label>
                                <input type="text" name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror"
                                       value="{{ old('shipping_address') }}" required>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City *</label>
                                <input type="text" name="shipping_city" class="form-control @error('shipping_city') is-invalid @enderror"
                                       value="{{ old('shipping_city') }}" required>
                                @error('shipping_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State *</label>
                                <input type="text" name="shipping_state" class="form-control @error('shipping_state') is-invalid @enderror"
                                       value="{{ old('shipping_state') }}" required>
                                @error('shipping_state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">ZIP Code *</label>
                                <input type="text" name="shipping_zip" class="form-control @error('shipping_zip') is-invalid @enderror"
                                       value="{{ old('shipping_zip') }}" required>
                                @error('shipping_zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Country *</label>
                                <input type="text" name="shipping_country" class="form-control @error('shipping_country') is-invalid @enderror"
                                       value="{{ old('shipping_country', 'USA') }}" required>
                                @error('shipping_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="sameAsShipping" checked>
                            <label class="form-check-label" for="sameAsShipping">
                                Billing address same as shipping
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Billing Information (hidden by default) -->
                <div class="card border-0 shadow-sm mb-4" id="billingSection" style="display: none;">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Billing Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="billing_name" class="form-control" value="{{ old('billing_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="billing_email" class="form-control" value="{{ old('billing_email') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="billing_phone" class="form-control" value="{{ old('billing_phone') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" name="billing_state" class="form-control" value="{{ old('billing_state') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">ZIP Code</label>
                                <input type="text" name="billing_zip" class="form-control" value="{{ old('billing_zip') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="billing_country" class="form-control" value="{{ old('billing_country', 'USA') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label" for="credit_card">
                                        <i class="fas fa-credit-card me-2"></i>Credit Card
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal me-2"></i>PayPal
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="cod" value="cash_on_delivery">
                                    <label class="form-check-label" for="cod">
                                        <i class="fas fa-money-bill me-2"></i>Cash on Delivery
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Details (shown when credit card selected) -->
                        <div id="creditCardDetails" class="mt-3">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" placeholder="123">
                                </div>
                            </div>
                            <p class="small text-muted">
                                <i class="fas fa-lock me-1"></i>
                                This is a demo - no real payment will be processed
                            </p>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Your Order</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cart as $item)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="fw-semibold">{{ $item['name'] }}</span>
                                    @if(!empty($item['size']))
                                        <br><small class="text-muted">Size: {{ $item['size'] }}</small>
                                    @endif
                                    <br><small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                </div>
                                <span class="fw-bold">{{ format_currency($item['price'] * $item['quantity']) }}</span>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">{{ format_currency($total) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Delivery:</span>
                            <span class="fw-semibold">
                                @if($deliveryFee == 0)
                                    <span class="text-success">Free</span>
                                @else
                                    {{ format_currency($deliveryFee) }}
                                @endif
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary fs-4">{{ format_currency($grandTotal) }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="placeOrderBtn">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>

                        <p class="text-muted small text-center mt-3 mb-0">
                            By placing your order, you agree to our
                            <a href="#" class="text-primary">Terms of Service</a> and
                            <a href="#" class="text-primary">Privacy Policy</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Toggle billing section
    document.getElementById('sameAsShipping').addEventListener('change', function() {
        document.getElementById('billingSection').style.display = this.checked ? 'none' : 'block';
    });

    // Toggle credit card details
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('creditCardDetails').style.display =
                this.value === 'credit_card' ? 'block' : 'none';
        });
    });

    // Prevent form submission for demo
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // For demo, we'll still submit to show flow
        // In real app, you'd remove this
        if (confirm('This is a demo. Proceed to success page?')) {
            this.submit();
        }
    });
</script>
@endpush
@endsection
