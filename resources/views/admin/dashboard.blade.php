@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-soft-primary">
                            <i class="fas fa-box text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Products</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalProducts }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-soft-success">
                            <i class="fas fa-tags text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Categories</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalCategories }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-soft-warning">
                            <i class="fas fa-shopping-cart text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Orders</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalOrders }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-soft-info">
                            <i class="fas fa-users text-info fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Customers</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalCustomers }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-4">
        <div class="card bg-gradient-primary text-white border-0 shadow-lg">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-2">Today's Revenue</h6>
                        <h2 class="mb-0 fw-bold">{{ format_currency($todayRevenue) }}</h2>
                        <small class="text-white-50">{{ now()->format('M d, Y') }}</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card bg-gradient-success text-white border-0 shadow-lg">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-2">This Month</h6>
                        <h2 class="mb-0 fw-bold">{{ format_currency($monthRevenue) }}</h2>
                        <small class="text-white-50">{{ now()->format('F Y') }}</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card bg-gradient-info text-white border-0 shadow-lg">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-2">Total Revenue</h6>
                        <h2 class="mb-0 fw-bold">{{ format_currency($totalRevenue) }}</h2>
                        <small class="text-white-50">All time</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 mb-2">
                            <i class="fas fa-clock me-1"></i> Pending
                        </span>
                        <h3 class="mb-0 fw-bold">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="stats-icon-sm bg-soft-warning">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-info bg-opacity-25 text-info px-3 py-2 mb-2">
                            <i class="fas fa-cog me-1"></i> Processing
                        </span>
                        <h3 class="mb-0 fw-bold">{{ $processingOrders }}</h3>
                    </div>
                    <div class="stats-icon-sm bg-soft-info">
                        <i class="fas fa-cog text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success bg-opacity-25 text-success px-3 py-2 mb-2">
                            <i class="fas fa-check-circle me-1"></i> Delivered
                        </span>
                        <h3 class="mb-0 fw-bold">{{ $deliveredOrders }}</h3>
                    </div>
                    <div class="stats-icon-sm bg-soft-success">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-danger bg-opacity-25 text-danger px-3 py-2 mb-2">
                            <i class="fas fa-times-circle me-1"></i> Cancelled
                        </span>
                        <h3 class="mb-0 fw-bold">{{ $cancelledOrders }}</h3>
                    </div>
                    <div class="stats-icon-sm bg-soft-danger">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                    Recent Orders
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->shipping_name }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>{{ format_currency($order->total) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No recent orders</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Low Stock Products
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">
                    Manage <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="card-body">
                @if($lowStockProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>
                                        <span class="fw-bold {{ $product->stock_quantity < 5 ? 'text-danger' : 'text-warning' }}">
                                            {{ $product->stock_quantity }} units
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->stock_quantity <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product->stock_quantity < 5)
                                            <span class="badge bg-danger">Critical</span>
                                        @else
                                            <span class="badge bg-warning">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">All products are well stocked!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-bolt text-primary me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add Product
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-tag me-2"></i>
                            Add Category
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-eye me-2"></i>
                            View Orders
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-user me-2"></i>
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stats-card { transition: all 0.3s ease; border-radius: 16px; }
    .stats-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
    .stats-icon-sm { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .bg-gradient-primary { background: linear-gradient(135deg, #ff6b8b 0%, #ff8da1 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); }
    .text-white-50 { color: rgba(255,255,255,0.7) !important; }
    .opacity-50 { opacity: 0.5; }
</style>
@endsection
