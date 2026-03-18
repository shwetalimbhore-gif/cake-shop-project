{{-- resources/views/admin/reports/orders.blade.php --}}
@extends('layouts.admin')

@section('title', 'Orders Reports - Admin Panel')
@section('page-title', 'Orders & Operations Reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Filter Reports</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.orders') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.reports.orders') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Summary Cards -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $orderSummary['total'] }}</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $orderSummary['completed'] }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $orderSummary['pending'] }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $orderSummary['cancelled'] }}</h3>
                    <p>Cancelled</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $orderSummary['processing'] }}</h3>
                    <p>Processing</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $customCakesStats['total'] }}</h3>
                    <p>Custom Cakes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Type Distribution -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Delivery vs Pickup</h5>
                </div>
                <div class="card-body">
                    <canvas id="deliveryPickupChart" style="min-height: 300px;"></canvas>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h4>{{ $deliveryVsPickup['delivery'] }}</h4>
                            <p class="text-muted">Delivery Orders</p>
                        </div>
                        <div class="col-6">
                            <h4>{{ $deliveryVsPickup['pickup'] }}</h4>
                            <p class="text-muted">Pickup Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pre-order vs Walk-in</h5>
                </div>
                <div class="card-body">
                    <canvas id="preorderChart" style="min-height: 300px;"></canvas>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h4>{{ $preorderVsWalkin['pre_order'] }}</h4>
                            <p class="text-muted">Pre-orders</p>
                        </div>
                        <div class="col-6">
                            <h4>{{ $preorderVsWalkin['walk_in'] }}</h4>
                            <p class="text-muted">Walk-in</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occasion-based Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Occasion-based Orders</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($occasionOrders as $occasion)
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    @switch($occasion->occasion)
                                        @case('birthday')
                                            <i class="fas fa-birthday-cake"></i>
                                            @break
                                        @case('wedding')
                                            <i class="fas fa-heart"></i>
                                            @break
                                        @case('anniversary')
                                            <i class="fas fa-ring"></i>
                                            @break
                                        @default
                                            <i class="fas fa-calendar"></i>
                                    @endswitch
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ ucfirst($occasion->occasion) }}</span>
                                    <span class="info-box-number">{{ $occasion->total }} orders</span>
                                    <span class="info-box-text">${{ number_format($occasion->revenue, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Cake Orders -->
    <div class="row" id="custom">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Custom Cake Orders</h5>
                    <div class="card-tools">
                        <span class="badge badge-primary">Avg: ${{ number_format($customCakesStats['avg_price'], 2) }}</span>
                        <span class="badge badge-info">With Message: {{ $customCakesStats['with_message'] }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Cake Design</th>
                                <th>Message</th>
                                <th>Occasion</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customCakes as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->cake_design ?? 'Standard' }}</td>
                                <td>
                                    @if($order->custom_message)
                                        <span class="badge badge-success" title="{{ $order->custom_message }}">
                                            <i class="fas fa-comment"></i> Yes
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($order->occasion) ?? 'N/A' }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($order->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-info">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No custom cake orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $customCakes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Delivery vs Pickup Chart
    var ctx1 = document.getElementById('deliveryPickupChart').getContext('2d');
    var deliveryPickupChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Delivery', 'Pickup'],
            datasets: [{
                data: [{{ $deliveryVsPickup['delivery'] }}, {{ $deliveryVsPickup['pickup'] }}],
                backgroundColor: ['rgba(60,141,188,0.8)', 'rgba(40,167,69,0.8)'],
                borderColor: ['rgba(60,141,188,1)', 'rgba(40,167,69,1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Pre-order vs Walk-in Chart
    var ctx2 = document.getElementById('preorderChart').getContext('2d');
    var preorderChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pre-order', 'Walk-in'],
            datasets: [{
                data: [{{ $preorderVsWalkin['pre_order'] }}, {{ $preorderVsWalkin['walk_in'] }}],
                backgroundColor: ['rgba(255,193,7,0.8)', 'rgba(23,162,184,0.8)'],
                borderColor: ['rgba(255,193,7,1)', 'rgba(23,162,184,1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
