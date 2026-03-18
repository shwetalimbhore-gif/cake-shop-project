@extends('layouts.admin')

@section('title', 'Product Sales Report - Admin Panel')
@section('page-title', 'Product Sales Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-box text-primary me-2"></i>
                    Product-wise Sales
                </h5>
                <form method="GET" action="{{ route('admin.reports.product-sales') }}" class="d-flex gap-2">
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.product-sales', ['format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                    <i class="fas fa-file-excel me-2 text-success"></i>Export as Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.product-sales', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Export as PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Items Sold</h6>
                        <h3 class="mb-0">{{ number_format($summary->total_items_sold ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-boxes fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Revenue</h6>
                        <h3 class="mb-0">{{ format_currency($summary->total_revenue ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Unique Products</h6>
                        <h3 class="mb-0">{{ number_format($summary->unique_products ?? 0) }}</h3>
                    </div>
                    <i class="fas fa-box fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                                <th>Order Count</th>
                                <th>Avg. Price</th>
                                <th>% of Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td class="fw-semibold">{{ $product->product_name }}</td>
                                <td>{{ number_format($product->total_quantity) }}</td>
                                <td class="fw-bold text-primary">{{ format_currency($product->total_revenue) }}</td>
                                <td>{{ number_format($product->order_count) }}</td>
                                <td>{{ $product->total_quantity > 0 ? format_currency($product->total_revenue / $product->total_quantity) : format_currency(0) }}</td>
                                <td>
                                    @if($summary->total_revenue > 0)
                                        {{ round(($product->total_revenue / $summary->total_revenue) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if($products->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No sales data for the selected period</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Chart -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Top 10 Products by Revenue
                </h5>
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const products = @json($products->take(10));

    const labels = products.map(p => p.product_name.length > 20 ? p.product_name.substring(0, 20) + '...' : p.product_name);
    const revenue = products.map(p => p.total_revenue);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenue,
                backgroundColor: 'rgba(255, 107, 139, 0.7)',
                borderColor: '#ff6b8b',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '$' + context.raw.toFixed(2);
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
