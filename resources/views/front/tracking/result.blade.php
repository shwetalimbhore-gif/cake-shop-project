@extends('layouts.front')

@section('title', 'Track Order - ' . $order->order_number . ' - ' . setting('site_name'))
@section('page-title', 'Track Order')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Track Order</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tracking.index') }}">Track Order</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Order Header Card -->
            <div class="order-header-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="order-info">
                            <span class="order-label">Order Number</span>
                            <h2 class="order-number">{{ $order->order_number }}</h2>
                            <p class="order-date">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Placed on {{ $order->created_at->format('F d, Y') }} at {{ $order->created_at->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="order-status text-md-end">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'confirmed' => 'primary',
                                    'shipped' => 'secondary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'refunded' => 'dark'
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="status-badge status-{{ $color }}">
                                <i class="fas fa-circle me-2"></i>
                                {{ ucfirst($order->status) }}
                            </span>

                            @if($order->payment_status)
                                @php
                                    $paymentColors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'dark'
                                    ];
                                    $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                @endphp
                                <div class="payment-status mt-2">
                                    <span class="status-badge status-{{ $paymentColor }} small">
                                        <i class="fas fa-credit-card me-2"></i>
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Tracking Timeline Column -->
                <div class="col-lg-8 mb-4">
                    <div class="tracking-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-clock me-2" style="color: var(--terracotta);"></i>
                                Tracking Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Order Placed -->
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-success">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Order Placed</h6>
                                            <span class="timeline-status confirmed">Confirmed</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->created_at->format('M d, Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $order->created_at->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Order Processed -->
                                @if(in_array($order->status, ['processing', 'confirmed', 'shipped', 'delivered']))
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-info">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Order Processed</h6>
                                            <span class="timeline-status processing">Processing</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->updated_at->format('M d, Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $order->updated_at->format('h:i A') }}
                                        </p>
                                        <p class="timeline-note">Your order is being prepared</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Order Shipped -->
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-primary">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Order Shipped</h6>
                                            <span class="timeline-status shipped">Shipped</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->shipped_at ? $order->shipped_at->format('M d, Y') : $order->updated_at->format('M d, Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $order->shipped_at ? $order->shipped_at->format('h:i A') : $order->updated_at->format('h:i A') }}
                                        </p>
                                        @if($order->tracking_number)
                                            <p class="timeline-tracking">
                                                <i class="fas fa-box me-2"></i>
                                                Tracking #:
                                                <span class="tracking-number">{{ $order->tracking_number }}</span>
                                                <button class="btn-copy" onclick="copyTrackingNumber('{{ $order->tracking_number }}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Out for Delivery -->
                                @if($order->status == 'shipped' && $order->estimated_delivery)
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-warning">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Out for Delivery</h6>
                                            <span class="timeline-status pending">In Transit</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Estimated: {{ $order->estimated_delivery->format('M d, Y') }}
                                        </p>
                                        <p class="timeline-note">Your package is on its way!</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Delivered -->
                                @if($order->status == 'delivered')
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Delivered</h6>
                                            <span class="timeline-status delivered">Delivered</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : $order->updated_at->format('M d, Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $order->delivered_at ? $order->delivered_at->format('h:i A') : $order->updated_at->format('h:i A') }}
                                        </p>
                                        <p class="timeline-note text-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Package delivered successfully
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Cancelled -->
                                @if($order->status == 'cancelled')
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="timeline-icon bg-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="timeline-title">Order Cancelled</h6>
                                            <span class="timeline-status cancelled">Cancelled</span>
                                        </div>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->cancelled_at ? $order->cancelled_at->format('M d, Y') : $order->updated_at->format('M d, Y') }}
                                        </p>
                                        @if($order->cancellation_reason)
                                            <p class="timeline-note text-danger">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Reason: {{ $order->cancellation_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Column -->
                <div class="col-lg-4">
                    <div class="summary-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-shopping-bag me-2" style="color: var(--terracotta);"></i>
                                Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span class="summary-label">Subtotal:</span>
                                <span class="summary-value">{{ format_currency($order->subtotal) }}</span>
                            </div>
                            @if($order->tax > 0)
                            <div class="summary-item">
                                <span class="summary-label">Tax:</span>
                                <span class="summary-value">{{ format_currency($order->tax) }}</span>
                            </div>
                            @endif
                            @if($order->shipping_cost > 0)
                            <div class="summary-item">
                                <span class="summary-label">Shipping:</span>
                                <span class="summary-value">{{ format_currency($order->shipping_cost) }}</span>
                            </div>
                            @endif
                            @if($order->discount > 0)
                            <div class="summary-item">
                                <span class="summary-label">Discount:</span>
                                <span class="summary-value text-danger">-{{ format_currency($order->discount) }}</span>
                            </div>
                            @endif
                            <div class="summary-divider"></div>
                            <div class="summary-item total">
                                <span class="summary-label">Total:</span>
                                <span class="summary-value">{{ format_currency($order->total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="info-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-truck me-2" style="color: var(--terracotta);"></i>
                                Delivery Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <div>
                                    <span class="info-label">Name</span>
                                    <span class="info-value">{{ $order->shipping_name }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <span class="info-label">Email</span>
                                    <span class="info-value">{{ $order->shipping_email }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <span class="info-label">Phone</span>
                                    <span class="info-value">{{ $order->shipping_phone }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <span class="info-label">Address</span>
                                    <span class="info-value">
                                        {{ $order->shipping_address }},<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                        {{ $order->shipping_country }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Need Help -->
                    <div class="help-card mt-4">
                        <div class="card-body text-center">
                            <i class="fas fa-headset fa-2x mb-3" style="color: var(--terracotta);"></i>
                            <h6>Need Help?</h6>
                            <p class="small text-muted mb-3">Our support team is here for you 24/7</p>
                            <a href="{{ route('contact') }}" class="btn-help">
                                <i class="fas fa-envelope me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items (Optional) -->
            @if($order->items && $order->items->count() > 0)
            <div class="items-card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-boxes me-2" style="color: var(--terracotta);"></i>
                        Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-name">{{ $item->product_name }}</span>
                                            @if($item->options)
                                                <small class="product-options">
                                                    @php $options = is_array($item->options) ? $item->options : json_decode($item->options, true); @endphp
                                                    @if(!empty($options['size']))
                                                        <span>Size: {{ $options['size'] }}</span>
                                                    @endif
                                                    @if(!empty($options['flavor']))
                                                        <span>Flavor: {{ $options['flavor'] }}</span>
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ format_currency($item->price) }}</td>
                                    <td>{{ format_currency($item->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Track Another Order -->
            <div class="text-center mt-4">
                <a href="{{ route('tracking.index') }}" class="btn-track-another">
                    <i class="fas fa-search me-2"></i>
                    Track Another Order
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ORDER HEADER CARD ===== */
.order-header-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
}

.order-label {
    display: block;
    color: var(--taupe);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.order-number {
    font-family: 'Prata', serif;
    font-size: 1.8rem;
    color: var(--terracotta);
    margin-bottom: 10px;
}

.order-date {
    color: var(--taupe);
    font-size: 0.95rem;
    margin: 0;
}

.order-date i {
    color: var(--terracotta);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-badge.small {
    padding: 5px 12px;
    font-size: 0.8rem;
}

.status-badge i {
    font-size: 0.6rem;
}

.status-success {
    background: #e8f5e9;
    color: #1b5e20;
}

.status-warning {
    background: #fff3e0;
    color: #b45f06;
}

.status-info {
    background: #e1f5fe;
    color: #01579b;
}

.status-primary {
    background: #e3f2fd;
    color: #0d47a1;
}

.status-secondary {
    background: #f5f5f5;
    color: #616161;
}

.status-danger {
    background: #ffebee;
    color: #b71c1c;
}

.status-dark {
    background: #eeeeee;
    color: #212121;
}

/* ===== TRACKING CARDS ===== */
.tracking-card,
.summary-card,
.info-card,
.items-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    overflow: hidden;
}

.card-header {
    padding: 20px 25px;
    border-bottom: 1px solid var(--sand);
    background: var(--cream);
}

.card-title {
    font-family: 'Prata', serif;
    font-size: 1.2rem;
    color: var(--charcoal);
    margin: 0;
}

.card-body {
    padding: 25px;
}

/* ===== TIMELINE ===== */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--sand);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    position: relative;
    z-index: 2;
}

.timeline-content {
    padding-left: 20px;
    padding-bottom: 20px;
    border-bottom: 1px dashed var(--sand);
}

.timeline-item:last-child .timeline-content {
    border-bottom: none;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 10px;
}

.timeline-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin: 0;
}

.timeline-status {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
}

.timeline-status.confirmed {
    background: #e3f2fd;
    color: #0d47a1;
}

.timeline-status.processing {
    background: #e1f5fe;
    color: #01579b;
}

.timeline-status.shipped {
    background: #e8eaf6;
    color: #1a237e;
}

.timeline-status.delivered {
    background: #e8f5e9;
    color: #1b5e20;
}

.timeline-status.cancelled {
    background: #ffebee;
    color: #b71c1c;
}

.timeline-status.pending {
    background: #fff3e0;
    color: #b45f06;
}

.timeline-date {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.timeline-date i {
    color: var(--terracotta);
    font-size: 0.8rem;
}

.timeline-note {
    color: var(--charcoal);
    font-size: 0.9rem;
    background: var(--cream);
    padding: 8px 12px;
    border-radius: 8px;
    margin-top: 8px;
}

.timeline-tracking {
    background: var(--cream);
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 0.9rem;
    color: var(--charcoal);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.tracking-number {
    font-family: monospace;
    font-weight: 600;
    color: var(--terracotta);
}

.btn-copy {
    background: none;
    border: none;
    color: var(--taupe);
    cursor: pointer;
    transition: all 0.3s;
    padding: 0 5px;
}

.btn-copy:hover {
    color: var(--terracotta);
}

/* ===== SUMMARY CARD ===== */
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.summary-item.total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid var(--sand);
}

.summary-label {
    color: var(--taupe);
}

.summary-value {
    font-weight: 600;
    color: var(--charcoal);
}

.summary-item.total .summary-label {
    font-size: 1rem;
    font-weight: 600;
    color: var(--charcoal);
}

.summary-item.total .summary-value {
    font-size: 1.2rem;
    color: var(--terracotta);
}

.summary-divider {
    height: 1px;
    background: var(--sand);
    margin: 15px 0;
}

/* ===== INFO CARD ===== */
.info-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item i {
    width: 20px;
    color: var(--terracotta);
    font-size: 1.1rem;
    margin-top: 3px;
}

.info-label {
    display: block;
    color: var(--taupe);
    font-size: 0.8rem;
    margin-bottom: 3px;
}

.info-value {
    color: var(--charcoal);
    font-size: 0.95rem;
    line-height: 1.5;
}

/* ===== ITEMS TABLE ===== */
.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th {
    text-align: left;
    padding: 12px;
    color: var(--taupe);
    font-weight: 500;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--sand);
}

.items-table td {
    padding: 15px 12px;
    border-bottom: 1px solid var(--sand);
    color: var(--charcoal);
}

.items-table tr:last-child td {
    border-bottom: none;
}

.product-info {
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.product-options {
    color: var(--taupe);
    font-size: 0.8rem;
}

.product-options span {
    display: inline-block;
    margin-right: 10px;
}

/* ===== HELP CARD ===== */
.help-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
}

.btn-help {
    display: inline-block;
    padding: 10px 25px;
    background: var(--cream);
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid var(--sand);
}

.btn-help:hover {
    background: var(--terracotta);
    color: white;
    border-color: var(--terracotta);
}

/* ===== TRACK ANOTHER BUTTON ===== */
.btn-track-another {
    display: inline-block;
    padding: 12px 30px;
    background: transparent;
    color: var(--terracotta);
    text-decoration: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid var(--terracotta);
}

.btn-track-another:hover {
    background: var(--terracotta);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .order-header-card {
        padding: 20px;
    }

    .order-number {
        font-size: 1.5rem;
    }

    .order-status {
        text-align: left !important;
        margin-top: 15px;
    }

    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .items-table {
        min-width: 500px;
    }
}

@media (max-width: 576px) {
    .card-header {
        padding: 15px 20px;
    }

    .card-body {
        padding: 20px;
    }

    .timeline-icon {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }

    .timeline-content {
        padding-left: 15px;
    }

    .timeline-title {
        font-size: 1rem;
    }
}
</style>

<script>
    function copyTrackingNumber(trackingNumber) {
        navigator.clipboard.writeText(trackingNumber).then(function() {
            toastr.success('Tracking number copied to clipboard!');
        }, function() {
            toastr.error('Failed to copy tracking number');
        });
    }
</script>
@endsection
