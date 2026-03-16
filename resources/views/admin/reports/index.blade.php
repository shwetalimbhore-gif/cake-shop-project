@extends('layouts.admin')

@section('title', 'Reports & Analytics - Admin Panel')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Welcome to the Reports Dashboard. Select a report category to view detailed analytics.
        </div>
    </div>
</div>

<div class="row">
    <!-- Sales & Revenue Reports -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Sales & Revenue
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.daily-sales') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-calendar-day me-2 text-primary"></i>Daily Sales Report</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.monthly-sales') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-calendar-alt me-2 text-primary"></i>Monthly Sales Report</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.product-sales') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-box me-2 text-primary"></i>Product-wise Sales</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.category-sales') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-tags me-2 text-primary"></i>Category Sales</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.top-selling') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-crown me-2 text-primary"></i>Top Selling Products</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.low-selling') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-arrow-down me-2 text-primary"></i>Low Selling Products</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order & Operations Reports -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-shopping-cart text-success me-2"></i>
                    Order & Operations
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.order-summary') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clipboard-list me-2 text-success"></i>Order Summary</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.custom-cake-orders') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-paint-brush me-2 text-success"></i>Custom Cake Orders</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.delivery-vs-pickup') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-truck me-2 text-success"></i>Delivery vs Pickup</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reports -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-users text-info me-2"></i>
                    Customer Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.top-customers') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-star me-2 text-info"></i>Top Customers</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.customer-frequency') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-pie me-2 text-info"></i>Customer Frequency</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Reports -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-boxes text-warning me-2"></i>
                    Inventory Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.low-stock') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Report</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-coins text-danger me-2"></i>
                    Financial Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.payment-methods') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-credit-card me-2 text-danger"></i>Payment Method Report</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-simple me-2 text-primary"></i>
                    Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <h3 class="fw-bold text-primary">{{ number_format(\App\Models\Order::whereDate('created_at', today())->count()) }}</h3>
                            <p class="text-muted mb-0">Today's Orders</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <h3 class="fw-bold text-success">{{ format_currency(\App\Models\Order::whereDate('created_at', today())->sum('total')) }}</h3>
                            <p class="text-muted mb-0">Today's Revenue</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <h3 class="fw-bold text-info">{{ number_format(\App\Models\Order::whereMonth('created_at', now()->month)->count()) }}</h3>
                            <p class="text-muted mb-0">Monthly Orders</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <h3 class="fw-bold text-warning">{{ number_format(\App\Models\Product::where('stock_quantity', '<', 10)->count()) }}</h3>
                            <p class="text-muted mb-0">Low Stock Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
