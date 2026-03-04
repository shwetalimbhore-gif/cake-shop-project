@extends('layouts.front')

@section('title', 'My Account - ' . setting('site_name'))
@section('page-title', 'My Account')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">My Account</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Account</li>
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
                    <a href="{{ route('account.dashboard') }}" class="sidebar-menu-item active">
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

        <!-- Dashboard Content -->
        <div class="col-lg-9">
            <!-- Welcome Banner -->
            <div class="welcome-banner mb-4">
                <div class="welcome-content">
                    <h2 class="welcome-title">Welcome back, {{ auth()->user()->name }}!</h2>
                    <p class="welcome-text">Manage your account, view orders, and update your profile.</p>
                </div>
                <div class="welcome-icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ $totalOrders }}</h3>
                            <p class="stat-label">Total Orders</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ format_currency($totalSpent) }}</h3>
                            <p class="stat-label">Total Spent</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ auth()->user()->created_at->format('M Y') }}</h3>
                            <p class="stat-label">Member Since</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="recent-orders-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-clock me-2" style="color: var(--terracotta);"></i>
                        Recent Orders
                    </h5>
                    <a href="{{ route('account.orders') }}" class="view-all-link">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td class="order-number">{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="order-total">{{ format_currency($order->total) }}</td>
                                    <td>
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
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $paymentColors = [
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'dark'
                                            ];
                                            $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $paymentColor }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('account.order.details', $order) }}"
                                           class="btn-view">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for Recent Orders (if needed) -->
                    @if(method_exists($recentOrders, 'links') && $recentOrders->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $recentOrders->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-orders text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h5>No orders yet</h5>
                        <p class="text-muted mb-3">Start shopping to see your orders here</p>
                        <a href="{{ route('shop') }}" class="btn-shop-now">
                            Shop Now <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions mt-4">
                <h5 class="quick-actions-title">
                    <i class="fas fa-bolt me-2" style="color: var(--terracotta);"></i>
                    Quick Actions
                </h5>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('shop') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <span>Shop Now</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('account.orders') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <span>Order History</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('account.profile') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <span>Edit Profile</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('tracking.index') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span>Track Order</span>
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

/* ===== WELCOME BANNER ===== */
.welcome-banner {
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    border-radius: 20px;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.welcome-text {
    opacity: 0.9;
    font-size: 1rem;
}

.welcome-icon {
    font-size: 4rem;
    opacity: 0.3;
}

/* ===== STAT CARDS ===== */
.stat-card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: rgba(201, 124, 93, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--terracotta);
    flex-shrink: 0;
}

.stat-details {
    flex: 1;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--charcoal);
    margin-bottom: 5px;
    line-height: 1.2;
}

.stat-label {
    color: var(--taupe);
    font-size: 0.9rem;
    margin: 0;
}

/* ===== RECENT ORDERS CARD ===== */
.recent-orders-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: var(--shadow-sm);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card-title {
    font-family: 'Prata', serif;
    font-size: 1.3rem;
    color: var(--charcoal);
    margin: 0;
}

.view-all-link {
    color: var(--terracotta);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    font-size: 0.95rem;
}

.view-all-link:hover {
    color: #b86a4a;
    transform: translateX(5px);
}

/* ===== ORDERS TABLE ===== */
.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th {
    text-align: left;
    padding: 15px 10px;
    color: var(--taupe);
    font-weight: 500;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--sand);
}

.orders-table td {
    padding: 15px 10px;
    border-bottom: 1px solid var(--sand);
    color: var(--charcoal);
    vertical-align: middle;
}

.orders-table tr:last-child td {
    border-bottom: none;
}

.orders-table tr:hover td {
    background: var(--cream);
}

.order-number {
    font-weight: 600;
    color: var(--terracotta);
}

.order-total {
    font-weight: 600;
    color: var(--terracotta);
}

/* ===== BADGES ===== */
.badge {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.bg-warning {
    background: #fff3e0 !important;
    color: #b45f06;
}

.bg-success {
    background: #e8f5e9 !important;
    color: #1b5e20;
}

.bg-info {
    background: #e1f5fe !important;
    color: #01579b;
}

.bg-danger {
    background: #ffebee !important;
    color: #b71c1c;
}

.bg-primary {
    background: #e3f2fd !important;
    color: #0d47a1;
}

.bg-secondary {
    background: #f5f5f5 !important;
    color: #616161;
}

.bg-dark {
    background: #eeeeee !important;
    color: #212121;
}

/* ===== VIEW BUTTON ===== */
.btn-view {
    display: inline-block;
    padding: 6px 16px;
    background: var(--cream);
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid var(--sand);
}

.btn-view:hover {
    background: var(--terracotta);
    color: white;
    border-color: var(--terracotta);
}

/* ===== QUICK ACTIONS ===== */
.quick-actions-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.action-card {
    background: white;
    padding: 20px 15px;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: var(--charcoal);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    height: 100%;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    background: var(--terracotta);
    color: white;
}

.action-icon {
    width: 50px;
    height: 50px;
    background: var(--cream);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: var(--terracotta);
    transition: all 0.3s;
}

.action-card:hover .action-icon {
    background: white;
    color: var(--terracotta);
}

.action-card span {
    font-size: 0.9rem;
    font-weight: 500;
    text-align: center;
}

/* ===== EMPTY ORDERS ===== */
.empty-orders {
    color: var(--taupe);
}

.btn-shop-now {
    display: inline-block;
    padding: 10px 25px;
    background: var(--terracotta);
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-shop-now:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .welcome-banner {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }

    .welcome-icon {
        font-size: 3rem;
    }

    .welcome-title {
        font-size: 1.5rem;
    }

    .stat-card {
        padding: 15px;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }

    .recent-orders-card {
        padding: 20px;
    }

    .card-title {
        font-size: 1.2rem;
    }

    .orders-table {
        min-width: 600px;
    }
}

@media (max-width: 576px) {
    .action-card {
        padding: 15px 10px;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .action-card span {
        font-size: 0.8rem;
    }
}
</style>
@endsection
