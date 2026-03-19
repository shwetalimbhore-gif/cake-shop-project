@extends('layouts.admin')

@section('title', 'Daily Sales Report - Admin Panel')
@section('page-title', 'Daily Sales Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.daily-sales') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Generate Report
                        </button>
                        <a href="{{ route('admin.reports.daily-sales') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row mb-3">
        <div class="col-12 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($summary['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_orders'] }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary['total_custom'] }}</h3>
                    <p>Custom Cakes</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $summary['total_standard'] }}</h3>
                    <p>Standard Cakes</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($summary['avg_daily_revenue'], 2) }}</h3>
                    <p>Avg Daily Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $summary['avg_daily_orders'] }}</h3>
                    <p>Avg Daily Orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Day Info -->
    @if($summary['best_day'])
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-success">
                <i class="fas fa-trophy"></i>
                <strong>Best Performing Day:</strong>
                {{ Carbon\Carbon::parse($summary['best_day']->date)->format('l, F d, Y') }}
                with ${{ number_format($summary['best_day']->total_revenue, 2) }} revenue
                ({{ $summary['best_day']->total_orders }} orders)
            </div>
        </div>
    </div>
    @endif

    <!-- Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daily Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daily Sales Details</h5>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $dailySales->count() }} Days</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="salesTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Orders</th>
                                    <th>Custom</th>
                                    <th>Standard</th>
                                    <th>Revenue</th>
                                    <th>Avg Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailySales as $index => $sale)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ Carbon\Carbon::parse($sale->date)->format('M d, Y') }}</td>
                                    <td>{{ $sale->day_name }}</td>
                                    <td>{{ $sale->total_orders }}</td>
                                    <td><span class="badge badge-info">{{ $sale->custom_cakes }}</span></td>
                                    <td><span class="badge badge-secondary">{{ $sale->standard_cakes }}</span></td>
                                    <td class="font-weight-bold text-success">${{ number_format($sale->total_revenue, 2) }}</td>
                                    <td>${{ number_format($sale->total_revenue / $sale->total_orders, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="text-right">Totals:</th>
                                    <th>{{ $dailySales->sum('total_orders') }}</th>
                                    <th>{{ $dailySales->sum('custom_cakes') }}</th>
                                    <th>{{ $dailySales->sum('standard_cakes') }}</th>
                                    <th>${{ number_format($dailySales->sum('total_revenue'), 2) }}</th>
                                    <th>${{ number_format($dailySales->avg('total_revenue') / $dailySales->avg('total_orders'), 2) }}</th>
                                </tr>
                            </tfoot>
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
    // Daily Sales Chart
    var ctx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['dates']) !!},
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: {!! json_encode($chartData['revenues']) !!},
                    borderColor: 'rgba(60,141,188,0.8)',
                    backgroundColor: 'rgba(60,141,188,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y-revenue'
                },
                {
                    label: 'Orders',
                    data: {!! json_encode($chartData['orders']) !!},
                    borderColor: 'rgba(255,99,132,0.8)',
                    backgroundColor: 'rgba(255,99,132,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y-orders'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                'y-revenue': {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Revenue ($)' },
                    ticks: { callback: value => '$' + value }
                },
                'y-orders': {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Number of Orders' },
                    grid: { drawOnChartArea: false },
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});

function exportReport(format) {
    var startDate = $('input[name="start_date"]').val();
    var endDate = $('input[name="end_date"]').val();
    var exportUrl = '{{ route("admin.reports.export", "daily-sales") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
