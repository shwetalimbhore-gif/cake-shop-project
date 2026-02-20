@extends('layouts.front')

@section('title', 'Order Details - ' . setting('site_name'))
@section('page-title', 'Order #' . $order->order_number)

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Order Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}">My Account</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.orders') }}">My Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 80px; height: 80px; font-size: 32px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
                    <span class="badge bg-primary">Customer</span>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('account.dashboard') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}"
                       class="list-group-item list-group-item-action active">
                        <i class="fas fa-shopping-bag me-2"></i>My Orders
                    </a>
                    <a href="{{ route('account.profile') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    <span class="badge {{ $order->status_badge }} p-2">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">Order Information</h6>
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            <p class="mb-1"><strong>Payment Status:</strong>
                                <span class="badge {{ $order->payment_status_badge }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                            @if($order->tracking_number)
                                <p class="mb-1"><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">Shipping Address</h6>
                            <p class="mb-1">{{ $order->shipping_name }}</p>
                            <p class="mb-1">{{ $order->shipping_address }}</p>
                            <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                            <p class="mb-1">{{ $order->shipping_country }}</p>
                            <p class="mb-1">{{ $order->shipping_phone }}</p>
                            <p class="mb-0">{{ $order->shipping_email }}</p>
                        </div>
                    </div>

                    <h6 class="fw-semibold mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product_name }}
                                        @if(!empty($item->options))
                                            @php $options = json_decode($item->options, true); @endphp
                                            <br>
                                            <small class="text-muted">
                                                @if(!empty($options['size']))
                                                    Size: {{ $options['size'] }}
                                                @endif
                                                @if(!empty($options['flavor']))
                                                    {{ !empty($options['size']) ? '|' : '' }} Flavor: {{ $options['flavor'] }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ format_currency($item->price) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ format_currency($item->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td>{{ format_currency($order->subtotal) }}</td>
                                </tr>
                                @if($order->tax > 0)
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                    <td>{{ format_currency($order->tax) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Delivery:</strong></td>
                                    <td>{{ format_currency($order->shipping_cost) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong class="text-primary">{{ format_currency($order->total) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($order->notes)
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <strong>Order Notes:</strong>
                            <p class="mb-0 mt-2">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="{{ route('account.orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
