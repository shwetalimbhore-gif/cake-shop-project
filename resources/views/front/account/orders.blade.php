@extends('layouts.front')

@section('title', 'My Orders - ' . setting('site_name'))
@section('page-title', 'My Orders')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">My Orders</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}">My Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Orders</li>
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
                    <a href="{{ route('account.orders') }}" class="sidebar-menu-item active">
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

        <!-- Orders Content -->
        <div class="col-lg-9">
            <div class="orders-card">
                <div class="orders-header">
                    <h4 class="orders-title">My Orders</h4>
                    @if(isset($orders) && $orders->total() > 0)
                        <p class="orders-subtitle">You have placed {{ $orders->total() }} orders</p>
                    @endif
                </div>

                @if(isset($orders) && $orders->count() > 0)
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td class="order-number">{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $order->items->count() }}</span>
                                    </td>
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

                    <!-- ===== MODERN PAGINATION SECTION ===== -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-4">
                        <div class="text-muted small mb-2 mb-sm-0">
                            Showing
                            <span class="fw-bold">{{ $orders->firstItem() ?? 0 }}</span>
                            to
                            <span class="fw-bold">{{ $orders->lastItem() ?? 0 }}</span>
                            of
                            <span class="fw-bold">{{ $orders->total() }}</span>
                            results
                        </div>

                        @if ($orders->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <i class="fas fa-chevron-left"></i>
                                                <span class="d-none d-sm-inline ms-1">Prev</span>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                                <i class="fas fa-chevron-left"></i>
                                                <span class="d-none d-sm-inline ms-1">Prev</span>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                                <span class="d-none d-sm-inline me-1">Next</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <span class="d-none d-sm-inline me-1">Next</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
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

/* ===== ORDERS CARD ===== */
.orders-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: var(--shadow-sm);
}

.orders-header {
    margin-bottom: 25px;
}

.orders-title {
    font-family: 'Prata', serif;
    font-size: 1.8rem;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.orders-subtitle {
    color: var(--taupe);
    font-size: 0.95rem;
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

.badge {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
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

/* View Button */
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

/* ===== PAGINATION STYLES ===== */
.pagination {
    gap: 5px;
}

.page-link {
    border: none;
    border-radius: 8px !important;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--charcoal);
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s;
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    text-decoration: none;
}

.page-link i {
    font-size: 0.8rem;
}

.page-link span {
    font-size: 0.85rem;
}

.page-link:hover {
    background: var(--terracotta);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(201, 124, 93, 0.2);
}

.page-item.active .page-link {
    background: var(--terracotta);
    color: white;
    border-color: var(--terracotta);
}

.page-item.disabled .page-link {
    background: #f5f5f5;
    color: var(--taupe);
    cursor: not-allowed;
    opacity: 0.6;
}

.page-item.disabled .page-link:hover {
    transform: none;
    box-shadow: none;
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
    .orders-card {
        padding: 20px;
    }

    .orders-title {
        font-size: 1.5rem;
    }

    .orders-table th,
    .orders-table td {
        padding: 12px 8px;
        font-size: 0.9rem;
    }

    .page-link {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }

    .page-link span {
        display: none;
    }

    .page-link i {
        margin: 0;
    }
}

@media (max-width: 576px) {
    .orders-table {
        min-width: 600px;
    }

    .pagination {
        justify-content: center;
        width: 100%;
    }
}
</style>
@endsection
