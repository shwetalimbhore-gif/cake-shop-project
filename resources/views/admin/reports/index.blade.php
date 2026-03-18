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
                    <h3>{{ number_format($totalRevenue ?? 0) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('admin.reports.sales') }}" class="small-box-footer">View Sales <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ route('admin.reports.orders') }}" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ route('admin.reports.customers') }}" class="small-box-footer">View Customers <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ route('admin.reports.inventory') }}" class="small-box-footer">View Inventory <i class="fas fa-arrow-circle-right"></i></a>
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
                            <a href="{{ route('admin.reports.sales') }}?report=daily" class="text-decoration-none">Daily Sales</a>
                            <span class="badge bg-primary rounded-pill">New</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.reports.sales') }}?report=monthly" class="text-decoration-none">Monthly Overview</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.reports.sales') }}?report=product" class="text-decoration-none">Product-wise Sales</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.reports.sales') }}?report=top" class="text-decoration-none">Top Selling Cakes</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.reports.sales') }}?report=flavors" class="text-decoration-none">Flavor Trends</a>
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
                            <a href="{{ route('admin.reports.orders') }}" class="text-decoration-none">Order Summary</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#custom" class="text-decoration-none">Custom Cake Orders</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#delivery" class="text-decoration-none">Delivery vs Pickup</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.orders') }}#occasion" class="text-decoration-none">Occasion-based Orders</a>
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
                            <a href="{{ route('admin.reports.customers') }}#top" class="text-decoration-none">Top Customers</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#new" class="text-decoration-none">New vs Returning</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#frequency" class="text-decoration-none">Order Frequency</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.customers') }}#special" class="text-decoration-none">Special Date Customers</a>
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
                            <a href="{{ route('admin.reports.inventory') }}#usage" class="text-decoration-none">Raw Material Usage</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.inventory') }}#lowstock" class="text-decoration-none">Low Stock Alerts</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.inventory') }}#movement" class="text-decoration-none">Stock Movement</a>
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
                            <a href="{{ route('admin.reports.financial') }}#profit" class="text-decoration-none">Profit Management</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#discounts" class="text-decoration-none">Discount Impact</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#payments" class="text-decoration-none">Payment Methods</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('admin.reports.financial') }}#seasonal" class="text-decoration-none">Seasonal Revenue</a>
                        </li>
                    </ul>
                </div>
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
                            <h3 class="text-warning">${{ number_format($avgOrderValue ?? 45.50, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
