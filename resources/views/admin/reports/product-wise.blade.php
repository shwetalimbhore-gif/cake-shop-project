@extends('layouts.admin')

@section('title', 'Product-wise Sales - Admin Panel')
@section('page-title', 'Product-wise Sales Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.product-wise') }}" id="filterForm">
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
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Generate Report
                        </button>
                        <a href="{{ route('admin.reports.product-wise') }}" class="btn btn-secondary mt-4">
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
                    <h3>{{ $summary['total_products'] }}</h3>
                    <p>Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_quantity'] }}</h3>
                    <p>Units Sold</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($summary['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($summary['avg_product_revenue'], 2) }}</h3>
                    <p>Avg per Product</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Product Sales Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Quantity Sold</th>
                                    <th>Order Count</th>
                                    <th>Total Revenue</th>
                                    <th>Avg Price</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productSales as $index => $product)
                                @php
                                    $maxQuantity = $productSales->max('total_quantity');
                                    $performance = $maxQuantity > 0 ? ($product->total_quantity / $maxQuantity) * 100 : 0;
                                    $stockClass = $product->stock_quantity <= 5 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $stockClass }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>{{ $product->total_quantity }}</td>
                                    <td>{{ $product->order_count }}</td>
                                    <td class="text-success">${{ number_format($product->total_revenue, 2) }}</td>
                                    <td>${{ number_format($product->total_revenue / $product->total_quantity, 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $performance > 50 ? 'success' : ($performance > 25 ? 'warning' : 'danger') }}"
                                                 style="width: {{ $performance }}%">
                                                {{ round($performance) }}%
                                            </div>
                                        </div>
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
</div>
@endsection

@section('scripts')
<script>
function exportReport(format) {
    var startDate = $('input[name="start_date"]').val();
    var endDate = $('input[name="end_date"]').val();

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    var exportUrl = '{{ route("admin.reports.export", "product-wise") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
