{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Reports Dashboard - Admin Panel')
@section('page-title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards Row -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('admin.reports.daily-sales') }}" class="small-box-footer">
                    View Sales <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalOrders ?? 0 }}</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.reports.orders') }}" class="small-box-footer">
                    View Orders <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalCustomers ?? 0 }}</h3>
                    <p>Total Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.reports.customers') }}" class="small-box-footer">
                    View Customers <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $lowStockCount ?? 0 }}</h3>
                    <p>Low Stock Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <a href="{{ route('admin.reports.inventory') }}" class="small-box-footer">
                    View Inventory <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Insights</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h6>Top Flavor</h6>
                            <h3 class="text-primary">{{ $topFlavor ?? 'Chocolate' }}</h3>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Most Popular Occasion</h6>
                            <h3 class="text-success">{{ $topOccasion ?? 'Birthday' }}</h3>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Peak Order Day</h6>
                            <h3 class="text-info">{{ $peakDay ?? 'Saturday' }}</h3>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Average Order Value</h6>
                            <h3 class="text-warning">${{ number_format($avgOrderValue ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Sales Reports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.reports.daily-sales') }}" class="text-decoration-none">
                                <i class="fas fa-calendar-day mr-2"></i> Daily Sales
                            </a>
                            <span class="badge bg-primary rounded-pill">New</span>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.monthly-overview') }}" class="text-decoration-none">
                                <i class="fas fa-calendar-alt mr-2"></i> Monthly Overview
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.product-wise') }}" class="text-decoration-none">
                                <i class="fas fa-box mr-2"></i> Product-wise Sales
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.top-cakes') }}" class="text-decoration-none">
                                <i class="fas fa-crown mr-2"></i> Top Selling Cakes
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.flavor-trends') }}" class="text-decoration-none">
                                <i class="fas fa-ice-cream mr-2"></i> Flavor Trends
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.category-wise') }}" class="text-decoration-none">
                                <i class="fas fa-tags mr-2"></i> Category-wise Sales
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Orders & Operations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}" class="text-decoration-none">
                                <i class="fas fa-shopping-cart mr-2"></i> Order Summary
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#custom" class="text-decoration-none">
                                <i class="fas fa-birthday-cake mr-2"></i> Custom Cake Orders
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#delivery" class="text-decoration-none">
                                <i class="fas fa-truck mr-2"></i> Delivery vs Pickup
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#occasion" class="text-decoration-none">
                                <i class="fas fa-glass-cheers mr-2"></i> Occasion-based Orders
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">Customer Reports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#top" class="text-decoration-none">
                                <i class="fas fa-crown mr-2"></i> Top Customers
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#new" class="text-decoration-none">
                                <i class="fas fa-user-plus mr-2"></i> New vs Returning
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#frequency" class="text-decoration-none">
                                <i class="fas fa-chart-line mr-2"></i> Order Frequency
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#special" class="text-decoration-none">
                                <i class="fas fa-calendar-star mr-2"></i> Special Date Customers
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Inventory Reports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.inventory') }}#usage" class="text-decoration-none">
                                <i class="fas fa-utensils mr-2"></i> Raw Material Usage
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.inventory') }}#lowstock" class="text-decoration-none">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Low Stock Alerts
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.inventory') }}#movement" class="text-decoration-none">
                                <i class="fas fa-chart-line mr-2"></i> Stock Movement
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Financial Reports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#profit" class="text-decoration-none">
                                <i class="fas fa-chart-pie mr-2"></i> Profit Management
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#discounts" class="text-decoration-none">
                                <i class="fas fa-tags mr-2"></i> Discount Impact
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#payments" class="text-decoration-none">
                                <i class="fas fa-credit-card mr-2"></i> Payment Methods
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#seasonal" class="text-decoration-none">
                                <i class="fas fa-tree mr-2"></i> Seasonal Revenue
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
