@extends('layouts.admin')

@section('title', 'Reports Dashboard - Admin Panel')
@section('page-title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Modern Summary Cards Row with Gradient Effects -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-shadow transition-all" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Revenue</h6>
                            <h3 class="text-white mb-0">₹{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-arrow-up"></i> +12.5% from last month
                            </small>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-0">
                    <a href="{{ route('admin.reports.daily-sales') }}" class="text-white d-block p-3 text-center text-decoration-none hover-bg-light">
                        View Details <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-shadow transition-all" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Orders</h6>
                            <h3 class="text-white mb-0">{{ number_format($totalOrders ?? 0) }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-sync-alt"></i> {{ number_format($orderGrowth ?? 0) }}% growth
                            </small>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-0">
                    <a href="{{ route('admin.reports.orders') }}" class="text-white d-block p-3 text-center text-decoration-none hover-bg-light">
                        View Orders <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-shadow transition-all" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Customers</h6>
                            <h3 class="text-white mb-0">{{ number_format($totalCustomers ?? 0) }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-user-plus"></i> {{ $newCustomers ?? 0 }} new this month
                            </small>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-0">
                    <a href="{{ route('admin.reports.customers') }}" class="text-white d-block p-3 text-center text-decoration-none hover-bg-light">
                        View Customers <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-shadow transition-all" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Low Stock Items</h6>
                            <h3 class="text-white mb-0">{{ $lowStockCount ?? 0 }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-exclamation-triangle"></i> Needs immediate attention
                            </small>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-0">
                    <a href="{{ route('admin.reports.inventory') }}" class="text-white d-block p-3 text-center text-decoration-none hover-bg-light">
                        View Inventory <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Insights with Icons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Quick Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-light">
                                <div class="mb-2">
                                    <i class="fas fa-ice-cream fa-2x text-primary"></i>
                                </div>
                                <h6 class="text-muted mb-2">Top Flavor</h6>
                                <h4 class="mb-0 fw-bold">{{ $topFlavor ?? 'Chocolate' }}</h4>
                                <small class="text-success">
                                    <i class="fas fa-chart-line"></i> Most popular choice
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-light">
                                <div class="mb-2">
                                    <i class="fas fa-gift fa-2x text-success"></i>
                                </div>
                                <h6 class="text-muted mb-2">Most Popular Occasion</h6>
                                <h4 class="mb-0 fw-bold">{{ $topOccasion ?? 'Birthday' }}</h4>
                                <small class="text-success">
                                    <i class="fas fa-chart-line"></i> {{ $occasionPercentage ?? 45 }}% of orders
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-light">
                                <div class="mb-2">
                                    <i class="fas fa-calendar-day fa-2x text-info"></i>
                                </div>
                                <h6 class="text-muted mb-2">Peak Order Day</h6>
                                <h4 class="mb-0 fw-bold">{{ $peakDay ?? 'Saturday' }}</h4>
                                <small class="text-info">
                                    <i class="fas fa-chart-line"></i> Highest volume day
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-light">
                                <div class="mb-2">
                                    <i class="fas fa-chart-line fa-2x text-warning"></i>
                                </div>
                                <h6 class="text-muted mb-2">Average Order Value</h6>
                                <h4 class="mb-0 fw-bold">₹{{ number_format($avgOrderValue ?? 0, 2) }}</h4>
                                <small class="text-warning">
                                    <i class="fas fa-chart-line"></i> Above average
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Report Categories with Hover Effects -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-lift transition-all">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chart-line text-primary fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Sales Reports</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.reports.daily-sales') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-calendar-day text-primary me-3"></i>
                            Daily Sales
                            <span class="float-end text-muted small">Today's summary →</span>
                        </a>
                        <a href="{{ route('admin.reports.monthly-overview') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-calendar-alt text-primary me-3"></i>
                            Monthly Overview
                            <span class="float-end text-muted small">Trend analysis →</span>
                        </a>
                        <a href="{{ route('admin.reports.product-wise') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-box text-primary me-3"></i>
                            Product-wise Sales
                            <span class="float-end text-muted small">Performance →</span>
                        </a>
                        <a href="{{ route('admin.reports.top-cakes') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-crown text-primary me-3"></i>
                            Top Selling Cakes
                            <span class="float-end text-muted small">Best sellers →</span>
                        </a>
                        <a href="{{ route('admin.reports.flavor-trends') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-ice-cream text-primary me-3"></i>
                            Flavor Trends
                            <span class="float-end text-muted small">Popular choices →</span>
                        </a>
                        <a href="{{ route('admin.reports.category-wise') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-tags text-primary me-3"></i>
                            Category-wise Sales
                            <span class="float-end text-muted small">By category →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-lift transition-all">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-tasks text-success fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Orders & Operations</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.reports.orders') }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-shopping-cart text-success me-3"></i>
                            Order Summary
                            <span class="float-end text-muted small">All orders →</span>
                        </a>
                        <a href="{{ route('admin.reports.orders') }}#custom" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-birthday-cake text-success me-3"></i>
                            Custom Cake Orders
                            <span class="float-end text-muted small">Personalized →</span>
                        </a>
                        <a href="{{ route('admin.reports.orders') }}#delivery" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-truck text-success me-3"></i>
                            Delivery vs Pickup
                            <span class="float-end text-muted small">Logistics →</span>
                        </a>
                        <a href="{{ route('admin.reports.orders') }}#occasion" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-glass-cheers text-success me-3"></i>
                            Occasion-based Orders
                            <span class="float-end text-muted small">Special events →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-lift transition-all">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users text-warning fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Customer Reports</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.reports.customers') }}#top" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-crown text-warning me-3"></i>
                            Top Customers
                            <span class="float-end text-muted small">VIP list →</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}#new" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-user-plus text-warning me-3"></i>
                            New vs Returning
                            <span class="float-end text-muted small">Retention →</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}#frequency" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-chart-line text-warning me-3"></i>
                            Order Frequency
                            <span class="float-end text-muted small">Purchase patterns →</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}#special" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-calendar-star text-warning me-3"></i>
                            Special Date Customers
                            <span class="float-end text-muted small">Birthdays & anniversaries →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Report Categories -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm hover-lift transition-all">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-boxes text-info fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Inventory Reports</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.inventory') }}#usage" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 mb-3 text-center hover-card transition-all">
                                    <i class="fas fa-utensils fa-2x text-info mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Raw Material Usage</h6>
                                    <small class="text-muted">Track consumption</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.inventory') }}#lowstock" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 mb-3 text-center hover-card transition-all">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Low Stock Alerts</h6>
                                    <small class="text-muted">Critical items</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-12">
                            <a href="{{ route('admin.reports.inventory') }}#movement" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 text-center hover-card transition-all">
                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Stock Movement</h6>
                                    <small class="text-muted">Inventory trends</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm hover-lift transition-all">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chart-pie text-danger fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0">Financial Reports</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.financial') }}#profit" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 mb-3 text-center hover-card transition-all">
                                    <i class="fas fa-chart-pie fa-2x text-danger mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Profit Management</h6>
                                    <small class="text-muted">Revenue analysis</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.financial') }}#discounts" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 mb-3 text-center hover-card transition-all">
                                    <i class="fas fa-tags fa-2x text-danger mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Discount Impact</h6>
                                    <small class="text-muted">Promotion analysis</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.financial') }}#payments" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 text-center hover-card transition-all">
                                    <i class="fas fa-credit-card fa-2x text-danger mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Payment Methods</h6>
                                    <small class="text-muted">Transaction analysis</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports.financial') }}#seasonal" class="text-decoration-none">
                                <div class="p-3 bg-light rounded-3 text-center hover-card transition-all">
                                    <i class="fas fa-tree fa-2x text-danger mb-2"></i>
                                    <h6 class="mb-0 fw-bold">Seasonal Revenue</h6>
                                    <small class="text-muted">Peak seasons</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for enhanced styling */
.transition-all {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.hover-lift:hover {
    transform: translateY(-5px);
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.hover-bg-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.list-group-item-action {
    transition: all 0.2s ease;
}

.list-group-item-action:hover {
    transform: translateX(5px);
    background-color: #f8f9fa;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.card-footer a {
    transition: all 0.2s ease;
}
</style>
@endsection
