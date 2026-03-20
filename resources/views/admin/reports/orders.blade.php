@extends('layouts.admin')

@section('title', 'Orders Reports - Admin Panel')
@section('page-title', 'Orders & Operations Reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Filter Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.orders') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <a href="{{ route('admin.reports.orders') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards with All Statuses -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $orderSummary['total'] ?? 0 }}</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box" style="background-color: #28a745;">
                <div class="inner">
                    <h3>{{ $orderSummary['completed'] ?? 0 }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box" style="background-color: #17a2b8;">
                <div class="inner">
                    <h3>{{ $orderSummary['confirmed'] ?? 0 }}</h3>
                    <p>Confirmed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box" style="background-color: #20c997;">
                <div class="inner">
                    <h3>{{ $orderSummary['delivered'] ?? 0 }}</h3>
                    <p>Delivered</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $orderSummary['pending'] ?? 0 }}</h3>
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
                    <h3>{{ $orderSummary['cancelled'] ?? 0 }}</h3>
                    <p>Cancelled</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown Chart -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Revenue</th>
                                </thead>
                            <tbody>
                                @foreach($statusBreakdown as $status)
                                @php
                                    $percentage = ($orderSummary['total'] > 0) ? ($status->count / $orderSummary['total']) * 100 : 0;

                                    // Set color based on status
                                    $badgeClass = '';
                                    $bgColor = '';
                                    if($status->status == 'completed') {
                                        $badgeClass = 'success';
                                        $bgColor = '#28a745';
                                    } elseif($status->status == 'confirmed') {
                                        $badgeClass = 'info';
                                        $bgColor = '#17a2b8';
                                    } elseif($status->status == 'delivered') {
                                        $badgeClass = 'teal';
                                        $bgColor = '#20c997';
                                    } elseif($status->status == 'pending') {
                                        $badgeClass = 'warning';
                                        $bgColor = '#ffc107';
                                    } elseif($status->status == 'processing') {
                                        $badgeClass = 'primary';
                                        $bgColor = '#007bff';
                                    } elseif($status->status == 'cancelled') {
                                        $badgeClass = 'danger';
                                        $bgColor = '#dc3545';
                                    } else {
                                        $badgeClass = 'secondary';
                                        $bgColor = '#6c757d';
                                    }
                                @endphp

                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $badgeClass }}" style="background-color: {{ $bgColor }};">
                                            {{ ucfirst($status->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $status->count }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $percentage }}%; background-color: {{ $bgColor }};">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($status->revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table with Status Badges -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                    <td>{{ $order->user->name ?? ($order->walkin_customer_name ?? 'Guest') }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @if($order->status == 'completed')
                                            <span class="badge" style="background-color: #28a745;">Completed</span>
                                        @elseif($order->status == 'confirmed')
                                            <span class="badge" style="background-color: #17a2b8;">Confirmed</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge" style="background-color: #20c997;">Delivered</span>
                                        @elseif($order->status == 'pending')
                                            <span class="badge" style="background-color: #ffc107; color: #000;">Pending</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge" style="background-color: #007bff;">Processing</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge" style="background-color: #dc3545;">Cancelled</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->is_custom_cake)
                                            <span class="badge badge-primary">Custom</span>
                                        @else
                                            <span class="badge badge-secondary">Standard</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_method)
                                            <span class="badge badge-info">{{ ucfirst($order->payment_method) }}</span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No orders found for the selected period</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // Status Chart with All Statuses
    var ctx = document.getElementById('statusChart').getContext('2d');

    // Prepare data for chart
    var statusLabels = [];
    var statusCounts = [];
    var statusColors = [];

    @foreach($statusBreakdown as $status)
        statusLabels.push('{{ ucfirst($status->status) }}');
        statusCounts.push({{ $status->count }});

        @if($status->status == 'completed')
            statusColors.push('#28a745');
        @elseif($status->status == 'confirmed')
            statusColors.push('#17a2b8');
        @elseif($status->status == 'delivered')
            statusColors.push('#20c997');
        @elseif($status->status == 'pending')
            statusColors.push('#ffc107');
        @elseif($status->status == 'processing')
            statusColors.push('#007bff');
        @elseif($status->status == 'cancelled')
            statusColors.push('#dc3545');
        @else
            statusColors.push('#6c757d');
        @endif
    @endforeach

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = {{ $orderSummary['total'] ?? 0 }};
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' orders (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});

function exportReport(format) {
    var startDate = $('input[name="start_date"]').val();
    var endDate = $('input[name="end_date"]').val();

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    var exportUrl = '{{ route("admin.reports.export", "orders") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format +
                    '&type=summary';
    window.location.href = exportUrl;
}
</script>
@endsection
