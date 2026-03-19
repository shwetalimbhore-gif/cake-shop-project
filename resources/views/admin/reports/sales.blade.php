{{-- resources/views/admin/reports/sales.blade.php --}}
@extends('layouts.admin')

@section('title', 'Sales Reports - Admin Panel')
@section('page-title', 'Sales Reports')

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
            <form method="GET" action="{{ route('admin.reports.sales') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Report Type</label>
                            <select name="report" class="form-control" onchange="this.form.submit()">
                                <option value="daily" {{ request('report') == 'daily' ? 'selected' : '' }}>Daily Sales</option>
                                <option value="monthly" {{ request('report') == 'monthly' ? 'selected' : '' }}>Monthly Overview</option>
                                <option value="product" {{ request('report') == 'product' ? 'selected' : '' }}>Product-wise Sales</option>
                                <option value="category" {{ request('report') == 'category' ? 'selected' : '' }}>Category-wise Sales</option>
                                <option value="top" {{ request('report') == 'top' ? 'selected' : '' }}>Top Selling Cakes</option>
                                <option value="flavors" {{ request('report') == 'flavors' ? 'selected' : '' }}>Flavor Trends</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary mt-4">
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
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($summary['total_revenue'] ?? 0, 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_orders'] ?? 0 }}</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary['total_cakes_sold'] ?? 0 }}</h3>
                    <p>Cakes Sold</p>
                </div>
                <div class="icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($summary['avg_order_value'] ?? 0, 2) }}</h3>
                    <p>Avg Order Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Section -->
    @if(!request('report') || request('report') == 'daily')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daily Sales Report</h5>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $dailySales->count() }} Days</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($dailySales->count() > 0)
                    <!-- Chart -->
                    <div class="chart-container" style="position: relative; height:400px; margin-bottom: 30px;">
                        <canvas id="dailySalesChart"></canvas>
                    </div>

                    <!-- Summary Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Best Day</span>
                                    <span class="info-box-number">{{ \Carbon\Carbon::parse($dailySales->first()->date)->format('M d, Y') }}</span>
                                    <span class="info-box-text">${{ number_format($dailySales->max('total_revenue'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Average Daily</span>
                                    <span class="info-box-number">${{ number_format($dailySales->avg('total_revenue'), 2) }}</span>
                                    <span class="info-box-text">{{ round($dailySales->avg('total_orders')) }} orders</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Custom Cakes</span>
                                    <span class="info-box-number">{{ $dailySales->sum('custom_cakes') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Standard Cakes</span>
                                    <span class="info-box-number">{{ $dailySales->sum('standard_cakes') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Total Orders</th>
                                    <th>Custom Cakes</th>
                                    <th>Standard Cakes</th>
                                    <th>Total Revenue</th>
                                    <th>Avg per Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailySales as $index => $sale)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('l') }}</td>
                                    <td>{{ $sale->total_orders }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $sale->custom_cakes }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $sale->standard_cakes }}</span>
                                    </td>
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
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No daily sales data available for the selected period.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Monthly Overview Section -->
    @if(request('report') == 'monthly')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Revenue Overview</h5>
                </div>
                <div class="card-body">
                    @if($monthlyData->count() > 0)
                    <canvas id="monthlyChart" style="min-height: 400px;"></canvas>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Total Orders</th>
                                    <th>Total Revenue</th>
                                    <th>Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $data)
                                <tr>
                                    <td>{{ $data->year }}</td>
                                    <td>{{ \Carbon\Carbon::create()->month($data->month)->format('F') }}</td>
                                    <td>{{ $data->total_orders }}</td>
                                    <td>${{ number_format($data->total_revenue, 2) }}</td>
                                    <td>${{ number_format($data->avg_order_value, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No monthly data available for the selected period.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Product-wise Sales Section -->
    @if(request('report') == 'product')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Product-wise Sales</h5>
                </div>
                <div class="card-body">
                    @if($productSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Total Quantity</th>
                                    <th>Total Revenue</th>
                                    <th>Order Count</th>
                                    <th>Avg Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productSales as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->total_quantity }}</td>
                                    <td>${{ number_format($product->total_revenue, 2) }}</td>
                                    <td>{{ $product->order_count }}</td>
                                    <td>${{ number_format($product->total_revenue / $product->total_quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No product sales data available for the selected period.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Cakes Section -->
    @if(request('report') == 'top')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Selling Cakes</h5>
                </div>
                <div class="card-body">
                    @if($topCakes->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cake Name</th>
                                <th>Flavor</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                                <th>Times Ordered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCakes as $index => $cake)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $cake->name }}</td>
                                <td>{{ ucfirst($cake->flavor) ?? 'N/A' }}</td>
                                <td>{{ $cake->total_quantity }}</td>
                                <td>${{ number_format($cake->total_revenue, 2) }}</td>
                                <td>{{ $cake->times_ordered }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info">No top cakes data available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flavor Trends Section -->
    @if(request('report') == 'flavors')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Flavor Trends</h5>
                </div>
                <div class="card-body">
                    @if($flavorTrends->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Flavor</th>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Quantity</th>
                                    <th>Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flavorTrends as $trend)
                                <tr>
                                    <td><span class="badge badge-primary">{{ ucfirst($trend->flavor) }}</span></td>
                                    <td>{{ \Carbon\Carbon::create()->month($trend->month)->format('F') }}</td>
                                    <td>{{ $trend->year ?? now()->year }}</td>
                                    <td>{{ $trend->total_quantity }}</td>
                                    <td>{{ $trend->order_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">No flavor trend data available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Category-wise Sales Section -->
    @if(request('report') == 'category')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Category-wise Sales</h5>
                </div>
                <div class="card-body">
                    @if($categorySales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorySales as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->total_quantity }}</td>
                                    <td>${{ number_format($category->total_revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">No category data available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Daily Sales Chart
    @if($dailySales->count() > 0)
    var ctx1 = document.getElementById('dailySalesChart');
    if(ctx1) {
        new Chart(ctx1.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($dailySales->pluck('date')->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('M d');
                })) !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! json_encode($dailySales->pluck('total_revenue')) !!},
                        borderColor: 'rgba(60,141,188,0.8)',
                        backgroundColor: 'rgba(60,141,188,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y-revenue'
                    },
                    {
                        label: 'Orders',
                        data: {!! json_encode($dailySales->pluck('total_orders')) !!},
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
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    'y-orders': {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value + ' orders';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw;
                                if (context.dataset.label.includes('Revenue')) {
                                    return label + ': $' + value.toFixed(2);
                                }
                                return label + ': ' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Monthly Chart
    @if($monthlyData->count() > 0)
    var ctx2 = document.getElementById('monthlyChart');
    if(ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyData->map(function($data) {
                    return \Carbon\Carbon::create()->month($data->month)->format('F') . ' ' . $data->year;
                })) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($monthlyData->pluck('total_revenue')) !!},
                    backgroundColor: 'rgba(60,141,188,0.5)',
                    borderColor: 'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});

function exportReport(format) {
    // Get current URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    var startDate = urlParams.get('start_date') || '{{ $startDate->format('Y-m-d') }}';
    var endDate = urlParams.get('end_date') || '{{ $endDate->format('Y-m-d') }}';
    var reportType = urlParams.get('report') || 'daily';

    // Create export URL
    var exportUrl = '{{ route("admin.reports.export", ["type" => "sales", "report" => "sales"]) }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&report=' + reportType +
                    '&format=' + format;

    // Redirect to export URL
    window.location.href = exportUrl;
}
</script>
@endsection
