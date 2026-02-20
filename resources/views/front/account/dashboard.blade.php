@extends('layouts.front')

@section('title', 'My Account - ' . setting('site_name'))
@section('page-title', 'My Account')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">My Account</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
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
                       class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('account.orders') ? 'active' : '' }}">
                        <i class="fas fa-shopping-bag me-2"></i>My Orders
                    </a>
                    <a href="{{ route('account.profile') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('account.profile') ? 'active' : '' }}">
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

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shopping-bag fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-white-50 mb-1">Total Orders</h6>
                                    <h3 class="mb-0">{{ $totalOrders }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-white-50 mb-1">Total Spent</h6>
                                    <h3 class="mb-0">{{ format_currency($totalSpent) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-white-50 mb-1">Member Since</h6>
                                    <h6 class="mb-0">{{ auth()->user()->created_at->format('M Y') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('account.orders') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('account.order.details', $order) }}" class="fw-bold text-primary">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ format_currency($order->total) }}</td>
                                        <td>
                                            <span class="badge {{ $order->status_badge }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->payment_status_badge }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('account.order.details', $order) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No orders yet</h6>
                            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
