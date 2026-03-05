@extends('layouts.front')

@section('title', 'Complete Payment - ' . setting('site_name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h4 class="mb-0 fw-bold">Complete Payment</h4>
                </div>
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">Secure Payment</h5>
                        <p class="text-muted">You're about to pay <span class="fw-bold text-primary">{{ format_currency($order->total) }}</span> for order #{{ $order->order_number }}</p>
                    </div>

                    <div class="order-summary bg-light p-4 rounded-3 mb-4">
                        <h6 class="fw-bold mb-3">Order Summary</h6>
                        @foreach($order->items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <span>{{ format_currency($item->subtotal) }}</span>
                        </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span class="text-primary">{{ format_currency($order->total) }}</span>
                        </div>
                    </div>

                    <button id="payBtn" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-credit-card me-2"></i>Pay {{ format_currency($order->total) }}
                    </button>

                    <p class="text-muted small mt-3">
                        <i class="fas fa-shield-alt me-1"></i>
                        Powered by Razorpay - Secure payment gateway
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $keyId }}",
        "amount": "{{ $amount }}",
        "currency": "{{ $currency }}",
        "name": "{{ $name }}",
        "description": "{{ $description }}",
        "image": "{{ asset(setting('site_logo') ?? 'images/logo.png') }}",
        "order_id": "{{ $razorpayOrder->id }}",
        "handler": function (response) {
            // Send payment response to server
            fetch('{{ route("razorpay.success") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                })
            })
            .then(res => res.json())
            .then(data => {
                window.location.href = '{{ route("checkout.success", $order) }}';
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = '{{ route("checkout.index") }}';
            });
        },
        "modal": {
            "ondismiss": function() {
                window.location.href = '{{ route("checkout.index") }}';
            }
        },
        "prefill": {
            "name": "{{ $prefill['name'] }}",
            "email": "{{ $prefill['email'] }}",
            "contact": "{{ $prefill['contact'] }}"
        },
        "theme": {
            "color": "#C97C5D"
        }
    };

    var rzp = new Razorpay(options);

    document.getElementById('payBtn').onclick = function(e) {
        rzp.open();
        e.preventDefault();
    }

    rzp.on('payment.failed', function(response) {
        fetch('{{ route("razorpay.failure") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ error: response.error })
        })
        .then(() => {
            window.location.href = '{{ route("checkout.index") }}';
        });
    });
</script>
@endpush
