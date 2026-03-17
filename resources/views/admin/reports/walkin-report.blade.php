@extends('layouts.admin')

@section('title', 'Walk-in vs Online Orders - Admin Panel')
@section('page-title', 'Walk-in vs Online Orders Report')

@section('content')
<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.walkin') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Online Orders</h6>
                        <h2 class="mb-0">{{ number_format($stats['online']) }}</h2>
                        <small class="text-white-50">{{ $stats['online_percentage'] }}% of total</small>
                    </div>
                    <i class="fas fa-globe fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Walk-in Orders</h6>
                        <h2 class="mb-0">{{ number_format($stats['walkin']) }}</h2>
                        <small class="text-white-50">{{ $stats['walkin_percentage'] }}% of total</small>
                    </div>
                    <i class="fas fa-store fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Online Revenue</h6>
                <h3 class="fw-bold text-primary">{{ format_currency($revenue['online']) }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-info" style="width: {{ $revenue['online_percentage'] }}%"></div>
                </div>
                <small class="text-muted">{{ $revenue['online_percentage'] }}% of total revenue</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Walk-in Revenue</h6>
                <h3 class="fw-bold text-warning">{{ format_currency($revenue['walkin']) }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-warning" style="width: {{ $revenue['walkin_percentage'] }}%"></div>
                </div>
                <small class="text-muted">{{ $revenue['walkin_percentage'] }}% of total revenue</small>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Chart -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-chart-pie text-primary me-2"></i>
            Order Type Distribution
        </h5>
    </div>
    <div class="card-body">
        <canvas id="orderTypeChart" height="300"></canvas>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list text-primary me-2"></i>
            Orders List
        </h5>
        <div class="btn-group">
            <a href="{{ route('admin.orders.walkin.create') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-plus me-2"></i>New Walk-in Order
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Customer</th>
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
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>
                            @if($order->order_type == 'walkin')
                                <span class="badge bg-warning">
                                    <i class="fas fa-store me-1"></i>Walk-in
                                </span>
                            @else
                                <span class="badge bg-info">
                                    <i class="fas fa-globe me-1"></i>Online
                                </span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}<br>
                            <small>{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if($order->order_type == 'walkin')
                                {{ $order->walkin_customer_name }}<br>
                                <small>{{ $order->walkin_customer_phone }}</small>
                            @else
                                {{ $order->shipping_name }}<br>
                                <small>{{ $order->shipping_email }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-info">{{ $order->items->count() }}</span></td>
                        <td class="fw-bold text-primary">{{ format_currency($order->total) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('orderTypeChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Online Orders ({{ $stats['online'] }})', 'Walk-in Orders ({{ $stats['walkin'] }})'],
            datasets: [{
                data: [{{ $stats['online'] }}, {{ $stats['walkin'] }}],
                backgroundColor: ['#17a2b8', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = {{ $stats['total'] }};
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} orders (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
