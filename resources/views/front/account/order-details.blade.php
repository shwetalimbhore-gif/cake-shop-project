@extends('layouts.front')

@section('title', 'Order Details - ' . $order->order_number . ' - ' . setting('site_name'))
@section('page-title', 'Order Details')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Order Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
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
            <div class="account-sidebar">
                <div class="user-info text-center p-4">
                    <div class="user-avatar-wrapper mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="user-avatar">
                        @else
                            <div class="user-avatar-placeholder">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="user-name">{{ auth()->user()->name }}</h5>
                    <p class="user-email">{{ auth()->user()->email }}</p>
                    <span class="user-badge">Customer</span>
                </div>

                <div class="sidebar-menu">
                    <a href="{{ route('account.dashboard') }}" class="sidebar-menu-item">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}" class="sidebar-menu-item">
                        <i class="fas fa-shopping-bag"></i>My Orders
                    </a>
                    <a href="{{ route('account.profile') }}" class="sidebar-menu-item">
                        <i class="fas fa-user"></i>Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-block">
                        @csrf
                        <button type="submit" class="sidebar-menu-item text-danger w-100 text-start border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Details Content -->
        <div class="col-lg-9">
            <!-- Order Header -->
            <div class="order-header-card mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <span class="order-label">Order Number</span>
                        <h2 class="order-number">{{ $order->order_number }}</h2>
                        <p class="order-date">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div class="order-status mt-3 mt-md-0">
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
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Order Info -->
                <div class="col-lg-7 mb-4">
                    <!-- Order Items -->
                    <div class="items-card mb-4">
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
                                            <th>Price</th>
                                            <th>Quantity</th>
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
                                                                <span><i class="fas fa-arrows-alt me-1"></i>Size: {{ $options['size'] }}</span>
                                                            @endif
                                                            @if(!empty($options['flavor']))
                                                                <span><i class="fas fa-ice-cream me-1"></i>Flavor: {{ $options['flavor'] }}</span>
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="price">{{ format_currency($item->price) }}</td>
                                            <td class="quantity">{{ $item->quantity }}</td>
                                            <td class="subtotal">{{ format_currency($item->subtotal) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary mt-4">
                                <div class="summary-row">
                                    <span class="summary-label">Subtotal:</span>
                                    <span class="summary-value">{{ format_currency($order->subtotal) }}</span>
                                </div>
                                @if($order->tax > 0)
                                <div class="summary-row">
                                    <span class="summary-label">Tax:</span>
                                    <span class="summary-value">{{ format_currency($order->tax) }}</span>
                                </div>
                                @endif
                                @if($order->shipping_cost > 0)
                                <div class="summary-row">
                                    <span class="summary-label">Shipping:</span>
                                    <span class="summary-value">{{ format_currency($order->shipping_cost) }}</span>
                                </div>
                                @endif
                                @if($order->discount > 0)
                                <div class="summary-row">
                                    <span class="summary-label">Discount:</span>
                                    <span class="summary-value text-danger">-{{ format_currency($order->discount) }}</span>
                                </div>
                                @endif
                                <div class="summary-divider"></div>
                                <div class="summary-row total">
                                    <span class="summary-label">Total:</span>
                                    <span class="summary-value">{{ format_currency($order->total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline (if shipped/delivered) -->
                    @if(in_array($order->status, ['shipped', 'delivered', 'processing', 'confirmed']))
                    <div class="timeline-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-clock me-2" style="color: var(--terracotta);"></i>
                                Order Timeline
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
                                        <h6 class="timeline-title">Order Placed</h6>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->created_at->format('M d, Y \a\t h:i A') }}
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
                                        <h6 class="timeline-title">Order Processed</h6>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->updated_at->format('M d, Y \a\t h:i A') }}
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
                                        <h6 class="timeline-title">Order Shipped</h6>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->shipped_at ? $order->shipped_at->format('M d, Y \a\t h:i A') : $order->updated_at->format('M d, Y \a\t h:i A') }}
                                        </p>
                                        @if($order->tracking_number)
                                        <p class="timeline-tracking">
                                            <i class="fas fa-box me-2"></i>
                                            Tracking: <strong>{{ $order->tracking_number }}</strong>
                                        </p>
                                        @endif
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
                                        <h6 class="timeline-title">Delivered</h6>
                                        <p class="timeline-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $order->delivered_at ? $order->delivered_at->format('M d, Y \a\t h:i A') : $order->updated_at->format('M d, Y \a\t h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column - Customer Info -->
                <div class="col-lg-5 mb-4">
                    <!-- Payment Information -->
                    <div class="info-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-credit-card me-2" style="color: var(--terracotta);"></i>
                                Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-row">
                                    <span class="info-label">Method:</span>
                                    <span class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Status:</span>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'dark'
                                        ];
                                        $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="status-badge status-{{ $paymentColor }} small">
                                        <i class="fas fa-circle me-2"></i>
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="info-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-truck me-2" style="color: var(--terracotta);"></i>
                                Shipping Address
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="address-block">
                                <div class="address-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $order->shipping_name }}</span>
                                </div>
                                <div class="address-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        {{ $order->shipping_address }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                        {{ $order->shipping_country }}
                                    </span>
                                </div>
                                <div class="address-item">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $order->shipping_phone }}</span>
                                </div>
                                <div class="address-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $order->shipping_email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address (if different) -->
                    @if($order->billing_name && $order->billing_name != $order->shipping_name)
                    <div class="info-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-file-invoice me-2" style="color: var(--terracotta);"></i>
                                Billing Address
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="address-block">
                                <div class="address-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $order->billing_name }}</span>
                                </div>
                                <div class="address-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        {{ $order->billing_address }}<br>
                                        {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}<br>
                                        {{ $order->billing_country }}
                                    </span>
                                </div>
                                @if($order->billing_phone)
                                <div class="address-item">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $order->billing_phone }}</span>
                                </div>
                                @endif
                                @if($order->billing_email)
                                <div class="address-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $order->billing_email }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Order Notes -->
                    @if($order->notes)
                    <div class="info-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-sticky-note me-2" style="color: var(--terracotta);"></i>
                                Order Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="notes-text">{{ $order->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="action-buttons mt-4">
                        <a href="{{ route('account.orders') }}" class="btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <a href="{{ route('tracking.index') }}?order={{ $order->order_number }}" class="btn-primary">
                            <i class="fas fa-truck me-2"></i>Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ACCOUNT SIDEBAR ===== */
.account-sidebar {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    border: 1px solid var(--sand);
}

.user-info {
    background: linear-gradient(135deg, var(--cream), var(--sand));
}

.user-avatar-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.user-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-sm);
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--terracotta);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 600;
    border: 4px solid white;
    box-shadow: var(--shadow-sm);
}

.user-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.user-email {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.user-badge {
    display: inline-block;
    padding: 4px 12px;
    background: var(--cream);
    color: var(--terracotta);
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 500;
}

.sidebar-menu {
    padding: 15px;
}

.sidebar-menu-item {
    display: block;
    padding: 12px 15px;
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s;
    margin-bottom: 5px;
    width: 100%;
    text-align: left;
}

.sidebar-menu-item i {
    width: 24px;
    color: var(--taupe);
    transition: all 0.3s;
    margin-right: 10px;
}

.sidebar-menu-item:hover {
    background: var(--cream);
    color: var(--terracotta);
    transform: translateX(5px);
}

.sidebar-menu-item:hover i {
    color: var(--terracotta);
}

.sidebar-menu-item.active {
    background: var(--terracotta);
    color: white;
}

.sidebar-menu-item.active i {
    color: white;
}

/* ===== ORDER HEADER ===== */
.order-header-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    margin-bottom: 30px;
}

.order-label {
    display: block;
    color: var(--taupe);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.order-number {
    font-family: 'Prata', serif;
    font-size: 1.8rem;
    color: var(--terracotta);
    margin-bottom: 10px;
}

.order-date {
    color: var(--taupe);
    font-size: 0.9rem;
    margin: 0;
}

.order-date i {
    color: var(--terracotta);
}

/* ===== STATUS BADGES ===== */
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

/* ===== CARDS ===== */
.items-card,
.info-card,
.timeline-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    overflow: hidden;
}

.card-header {
    padding: 18px 25px;
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
    display: inline-flex;
    align-items: center;
    margin-right: 15px;
}

.product-options i {
    color: var(--terracotta);
    font-size: 0.7rem;
}

.price,
.quantity,
.subtotal {
    font-weight: 500;
}

.subtotal {
    font-weight: 600;
    color: var(--terracotta);
}

/* ===== ORDER SUMMARY ===== */
.order-summary {
    background: var(--cream);
    border-radius: 16px;
    padding: 20px;
    margin-top: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.summary-row.total {
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

.summary-row.total .summary-label {
    font-size: 1rem;
    font-weight: 600;
    color: var(--charcoal);
}

.summary-row.total .summary-value {
    font-size: 1.2rem;
    color: var(--terracotta);
}

.summary-divider {
    height: 1px;
    background: var(--sand);
    margin: 15px 0;
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
    margin-bottom: 25px;
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
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    position: relative;
    z-index: 2;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.timeline-date {
    color: var(--taupe);
    font-size: 0.85rem;
    margin-bottom: 5px;
}

.timeline-date i {
    color: var(--terracotta);
}

.timeline-note {
    font-size: 0.85rem;
    color: var(--charcoal);
    background: var(--cream);
    padding: 6px 12px;
    border-radius: 6px;
    display: inline-block;
}

.timeline-tracking {
    font-size: 0.85rem;
    color: var(--charcoal);
    background: var(--cream);
    padding: 8px 12px;
    border-radius: 6px;
}

.timeline-tracking strong {
    color: var(--terracotta);
}

/* ===== INFO GRID ===== */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-label {
    min-width: 80px;
    color: var(--taupe);
    font-size: 0.9rem;
}

.info-value {
    font-weight: 500;
    color: var(--charcoal);
}

/* ===== ADDRESS BLOCK ===== */
.address-block {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.address-item {
    display: flex;
    gap: 12px;
    color: var(--charcoal);
}

.address-item i {
    width: 20px;
    color: var(--terracotta);
    font-size: 1rem;
    margin-top: 3px;
}

.address-item span {
    flex: 1;
    line-height: 1.6;
}

.notes-text {
    color: var(--charcoal);
    font-style: italic;
    margin: 0;
    line-height: 1.6;
}

/* ===== ACTION BUTTONS ===== */
.action-buttons {
    display: flex;
    gap: 15px;
}

.btn-primary,
.btn-secondary {
    flex: 1;
    padding: 12px;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary {
    background: var(--terracotta);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.btn-secondary {
    background: transparent;
    color: var(--charcoal);
    border: 1px solid var(--sand);
}

.btn-secondary:hover {
    background: var(--cream);
    transform: translateY(-2px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .order-number {
        font-size: 1.5rem;
    }

    .action-buttons {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .order-header-card {
        padding: 20px;
    }

    .card-header {
        padding: 15px 20px;
    }

    .card-body {
        padding: 20px;
    }

    .items-table {
        min-width: 600px;
    }

    .address-item {
        flex-direction: column;
        gap: 5px;
    }

    .address-item i {
        margin-bottom: 5px;
    }
}

@media (max-width: 576px) {
    .order-number {
        font-size: 1.2rem;
    }

    .status-badge {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .info-label {
        min-width: auto;
    }
}
</style>
@endsection
