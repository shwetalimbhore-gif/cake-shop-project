@extends('layouts.admin')

@section('title', 'Inventory Reports - Admin Panel')
@section('page-title', 'Inventory Reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Filter Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.inventory') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.inventory') }}" class="btn btn-secondary mt-4">
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

    <!-- Inventory Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $rawMaterialUsage->count() }}</h3>
                    <p>Products Used</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $lowStockProducts->count() }}</h3>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $rawMaterialUsage->sum('total_quantity') }}</h3>
                    <p>Total Units Used</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daily Stock Movement</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockMovementChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Material Usage -->
    <div class="row" id="usage">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Raw Material Usage</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Flavor</th>
                                    <th>Quantity Used</th>
                                    <th>Times Used</th>
                                    <th>Usage Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rawMaterialUsage as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ ucfirst($item->flavor) ?? 'N/A' }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                    <td>{{ $item->times_used }} orders</td>
                                    <td>
                                        @php
                                            $avgPerOrder = $item->times_used > 0 ? $item->total_quantity / $item->times_used : 0;
                                        @endphp
                                        <span class="badge badge-info">{{ number_format($avgPerOrder, 1) }}/order</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="row" id="lowstock">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Low Stock Alerts</h5>
                    <div class="card-tools">
                        <span class="badge badge-danger">Action Required</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        The following items are running low on stock. Please reorder soon.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Last Month Sales</th>
                                    <th>Estimated Days Left</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                @php
                                    $lastMonthSales = \App\Models\OrderItem::where('product_id', $product->id)
                                        ->whereHas('order', function($q) {
                                            $q->where('created_at', '>=', now()->subDays(30));
                                        })
                                        ->sum('quantity');
                                    $dailyAvg = $lastMonthSales / 30;
                                    $daysLeft = $dailyAvg > 0 ? floor($product->stock_quantity / $dailyAvg) : 999;
                                    $statusClass = $daysLeft < 7 ? 'danger' : ($daysLeft < 14 ? 'warning' : 'success');
                                @endphp
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->stock_quantity <= 5 ? 'danger' : 'warning' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>{{ $lastMonthSales }} units</td>
                                    <td>
                                        <span class="badge badge-{{ $statusClass }}">{{ $daysLeft }} days</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Reorder
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> All items are well stocked!
                    </div>
                    @endif
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
    @if($stockMovement->count() > 0)
    // Stock Movement Chart
    var ctx = document.getElementById('stockMovementChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($stockMovement->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })) !!},
            datasets: [{
                label: 'Units Used',
                data: {!! json_encode($stockMovement->pluck('total_used')) !!},
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
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
    @endif
});

function exportReport(format) {
    var startDate = $('input[name="start_date"]').val();
    var endDate = $('input[name="end_date"]').val();

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    var exportUrl = '{{ route("admin.reports.export", "inventory") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
