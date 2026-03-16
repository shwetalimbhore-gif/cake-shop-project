@extends('layouts.admin')

@section('title', 'Monthly Sales Report - Admin Panel')
@section('page-title', 'Monthly Sales Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} Report
                </h5>
                <form method="GET" action="{{ route('admin.reports.monthly-sales') }}" class="d-flex gap-2">
                    <select name="month" class="form-select" style="width: auto;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="form-select" style="width: auto;">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> View
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Orders</h6>
                        <h3 class="mb-0">{{ number_format($sales->total_orders ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Revenue</h6>
                        <h3 class="mb-0">{{ format_currency($sales->total_revenue ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Avg. Order Value</h6>
                        <h3 class="mb-0">{{ format_currency($sales->average_order_value ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Unique Customers</h6>
                        <h3 class="mb-0">{{ number_format($sales->unique_customers ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Growth Indicators -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Growth vs Previous Month</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center p-3 border-end">
                            <span class="text-muted d-block mb-2">Revenue Growth</span>
                            <h3 class="fw-bold {{ $growthData['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $growthData['revenue_growth'] >= 0 ? '+' : '' }}{{ $growthData['revenue_growth'] }}%
                            </h3>
                            <small class="text-muted">vs {{ format_currency($growthData['previous_revenue']) }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3">
                            <span class="text-muted d-block mb-2">Order Growth</span>
                            <h3 class="fw-bold {{ $growthData['orders_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $growthData['orders_growth'] >= 0 ? '+' : '' }}{{ $growthData['orders_growth'] }}%
                            </h3>
                            <small class="text-muted">vs {{ $growthData['previous_orders'] }} orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Order Status Breakdown</h6>
                @foreach($statusBreakdown as $status)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>
                        <span class="badge bg-{{
                            $status->status == 'delivered' ? 'success' :
                            ($status->status == 'pending' ? 'warning' :
                            ($status->status == 'cancelled' ? 'danger' : 'info'))
                        }} me-2">{{ ucfirst($status->status) }}</span>
                    </span>
                    <div>
                        <span class="fw-bold">{{ $status->count }}</span>
                        <small class="text-muted ms-2">({{ format_currency($status->total) }})</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Daily Breakdown Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Daily Sales Trend
                </h5>
            </div>
            <div class="card-body">
                <canvas id="dailySalesChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Daily Breakdown Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-table text-primary me-2"></i>
                    Daily Breakdown
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>% of Monthly</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyBreakdown as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                <td>{{ number_format($day->orders) }}</td>
                                <td>{{ format_currency($day->revenue) }}</td>
                                <td>
                                    @if($sales->total_revenue > 0)
                                        {{ round(($day->revenue / $sales->total_revenue) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $previousDay = $loop->index > 0 ? $dailyBreakdown[$loop->index - 1] : null;
                                        $trend = $previousDay ? (($day->revenue - $previousDay->revenue) / $previousDay->revenue) * 100 : 0;
                                    @endphp
                                    @if($trend > 0)
                                        <span class="text-success">
                                            <i class="fas fa-arrow-up"></i> {{ round($trend, 1) }}%
                                        </span>
                                    @elseif($trend < 0)
                                        <span class="text-danger">
                                            <i class="fas fa-arrow-down"></i> {{ round(abs($trend), 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if($dailyBreakdown->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No data for this month</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    const dailyData = @json($dailyBreakdown);

    const labels = dailyData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });

    const orders = dailyData.map(item => item.orders);
    const revenue = dailyData.map(item => item.revenue);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Orders',
                    data: orders,
                    borderColor: '#ff6b8b',
                    backgroundColor: 'rgba(255, 107, 139, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue',
                    data: revenue,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>
@endpush
@endsection
