@extends('layouts.admin')

@section('title', 'Daily Sales Report - Admin Panel')
@section('page-title', 'Daily Sales Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-day text-primary me-2"></i>
                    Sales for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                </h5>
                <form method="GET" action="{{ route('admin.reports.daily-sales') }}" class="d-flex gap-2">
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
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
                        <h6 class="text-white-50 mb-2">Paid Revenue</h6>
                        <h3 class="mb-0">{{ format_currency($sales->paid_revenue ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Average Order</h6>
                        <h3 class="mb-0">{{ $sales->total_orders > 0 ? format_currency($sales->total_revenue / $sales->total_orders) : format_currency(0) }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Hourly Sales Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Hourly Sales
                </h5>
            </div>
            <div class="card-body">
                <canvas id="hourlySalesChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-credit-card text-primary me-2"></i>
                    Payment Methods
                </h5>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" height="250"></canvas>
                <div class="mt-4">
                    @foreach($paymentMethods as $method)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>
                            <span class="badge bg-primary me-2">•</span>
                            {{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'cash_on_delivery')) }}
                        </span>
                        <div>
                            <span class="fw-bold">{{ format_currency($method->total) }}</span>
                            <small class="text-muted ms-2">({{ $method->count }} orders)</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hourly Breakdown Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-clock text-primary me-2"></i>
                    Hourly Breakdown
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>% of Daily Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hourlyArray = [];
                                for($i = 0; $i < 24; $i++) {
                                    $hourlyArray[$i] = ['orders' => 0, 'revenue' => 0];
                                }
                                foreach($hourlySales as $sale) {
                                    $hourlyArray[$sale->hour] = [
                                        'orders' => $sale->orders,
                                        'revenue' => $sale->revenue
                                    ];
                                }
                            @endphp
                            @foreach($hourlyArray as $hour => $data)
                                @if($data['orders'] > 0)
                                <tr>
                                    <td>{{ sprintf('%02d:00 - %02d:00', $hour, $hour+1) }}</td>
                                    <td>{{ number_format($data['orders']) }}</td>
                                    <td>{{ format_currency($data['revenue']) }}</td>
                                    <td>
                                        @if($sales->total_revenue > 0)
                                            {{ round(($data['revenue'] / $sales->total_revenue) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @if($sales->total_orders == 0)
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No sales data for this date</p>
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
    // Hourly Sales Chart
    const hourlyCtx = document.getElementById('hourlySalesChart').getContext('2d');
    const hourlyData = @json($hourlySales);

    const hours = [];
    const orders = [];
    const revenue = [];

    for(let i = 0; i < 24; i++) {
        hours.push(i + ':00');
        orders.push(0);
        revenue.push(0);
    }

    hourlyData.forEach(item => {
        orders[item.hour] = item.orders;
        revenue[item.hour] = item.revenue;
    });

    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hours,
            datasets: [
                {
                    label: 'Orders',
                    data: orders,
                    backgroundColor: 'rgba(255, 107, 139, 0.5)',
                    borderColor: '#ff6b8b',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue',
                    data: revenue,
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: '#28a745',
                    borderWidth: 1,
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

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentLabels = [];
    const paymentData = [];
    const paymentColors = ['#ff6b8b', '#28a745', '#17a2b8', '#ffc107'];

    @foreach($paymentMethods as $index => $method)
        paymentLabels.push('{{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'cash_on_delivery')) }}');
        paymentData.push({{ $method->total }});
    @endforeach

    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentData,
                backgroundColor: paymentColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection
