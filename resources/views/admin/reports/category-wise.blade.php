@extends('layouts.admin')

@section('title', 'Category-wise Sales - Admin Panel')
@section('page-title', 'Category-wise Sales Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.category-wise') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.category-wise') }}" class="btn btn-secondary mt-4">
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
                    <h3>{{ $summary['total_categories'] }}</h3>
                    <p>Total Categories</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($summary['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary['total_quantity'] }}</h3>
                    <p>Total Items Sold</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $summary['total_orders'] }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $summary['top_category'] }}</h3>
                    <p>Top Category</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($summary['top_category_revenue'], 2) }}</h3>
                    <p>Top Category Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quantity by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="quantityChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Category-wise Sales Details</h5>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $categorySales->count() }} Categories</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Orders</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Revenue</th>
                                    <th>Average Price</th>
                                    <th>Contribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorySales as $index => $category)
                                @php
                                    $revenueContribution = ($category->total_revenue / $summary['total_revenue']) * 100;
                                    $quantityContribution = ($category->total_quantity / $summary['total_quantity']) * 100;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>{{ $category->order_count }}</td>
                                    <td>{{ $category->total_quantity }}</td>
                                    <td class="text-success font-weight-bold">${{ number_format($category->total_revenue, 2) }}</td>
                                    <td>${{ number_format($category->avg_price, 2) }}</td>
                                    <td>
                                        <div class="progress-group">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: {{ $revenueContribution }}%">
                                                    {{ round($revenueContribution, 1) }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">Revenue</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-right">Totals:</th>
                                    <th>{{ $categorySales->sum('order_count') }}</th>
                                    <th>{{ $categorySales->sum('total_quantity') }}</th>
                                    <th>${{ number_format($categorySales->sum('total_revenue'), 2) }}</th>
                                    <th>${{ number_format($categorySales->avg('avg_price'), 2) }}</th>
                                    <th>100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance Cards -->
    <div class="row mt-4">
        @foreach($categorySales as $category)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header" style="background-color: #f8f9fa;">
                    <h6 class="card-title mb-0">{{ $category->name }}</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Orders</small>
                            <h5>{{ $category->order_count }}</h5>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Items</small>
                            <h5>{{ $category->total_quantity }}</h5>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Revenue</small>
                            <h5 class="text-success">${{ number_format($category->total_revenue, 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Revenue Chart
    var ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['revenues']) !!},
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Quantity Chart
    var ctx2 = document.getElementById('quantityChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Quantity Sold',
                data: {!! json_encode($chartData['quantities']) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
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

    var exportUrl = '{{ route("admin.reports.export", "category-wise") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
