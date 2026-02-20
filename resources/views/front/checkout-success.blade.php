@extends('layouts.front')

@section('title', 'Order Confirmed - ' . setting('site_name'))

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <div class="mb-4">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                 style="width: 100px; height: 100px;">
                <i class="fas fa-check fa-4x"></i>
            </div>
        </div>
        <h1 class="display-5 fw-bold mb-3">Thank You for Your Order!</h1>
        <p class="text-muted">Your order has been placed successfully.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Order Number:</strong></p>
                            <p class="text-primary fw-bold">{{ $order->order_number }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Order Date:</strong></p>
                            <p>{{ $order->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Payment Method:</strong></p>
                            <p>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Payment Status:</strong></p>
                            <p><span class="badge bg-warning">{{ ucfirst($order->payment_status) }}</span></p>
                        </div>
                    </div>

                    <h6 class="fw-semibold mb-3">Order Summary</h6>
                    <table class="table table-borderless">
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }} × {{ $item->quantity }}</td>
                            <td class="text-end">{{ format_currency($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><strong>Subtotal</strong></td>
                            <td class="text-end"><strong>{{ format_currency($order->subtotal) }}</strong></td>
                        </tr>
                        @if($order->tax > 0)
                        <tr>
                            <td>Tax</td>
                            <td class="text-end">{{ format_currency($order->tax) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Delivery</td>
                            <td class="text-end">{{ format_currency($order->shipping_cost) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-end"><strong class="text-primary">{{ format_currency($order->total) }}</strong></td>
                        </tr>
                    </table>

                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Shipping Address</h6>
                        <p class="mb-1">{{ $order->shipping_name }}</p>
                        <p class="mb-1">{{ $order->shipping_address }}</p>
                        <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                        <p class="mb-1">{{ $order->shipping_country }}</p>
                        <p class="mb-1">{{ $order->shipping_phone }}</p>
                        <p class="mb-0">{{ $order->shipping_email }}</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-center">
                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                    <a href="{{ route('account.orders') }}" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-list me-2"></i>View My Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
