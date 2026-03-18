@extends('layouts.admin')

@section('title', 'Low Stock Report - Admin Panel')
@section('page-title', 'Low Stock Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Low Stock Alert
                </h5>
                <form method="GET" action="{{ route('admin.reports.low-stock') }}" class="d-flex gap-2">
                    <select name="threshold" class="form-select" style="width: auto;">
                        <option value="5" {{ $threshold == 5 ? 'selected' : '' }}>≤ 5 units</option>
                        <option value="10" {{ $threshold == 10 ? 'selected' : '' }}>≤ 10 units</option>
                        <option value="20" {{ $threshold == 20 ? 'selected' : '' }}>≤ 20 units</option>
                        <option value="50" {{ $threshold == 50 ? 'selected' : '' }}>≤ 50 units</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.low-stock', ['format' => 'excel', 'threshold' => $threshold]) }}">
                                    <i class="fas fa-file-excel me-2 text-success"></i>Export as Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.export.low-stock', ['format' => 'pdf', 'threshold' => $threshold]) }}">
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

<!-- Alert Summary -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Products Low on Stock</h6>
                        <h3 class="mb-0">{{ number_format($products->total()) }}</h3>
                    </div>
                    <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Out of Stock</h6>
                        <h3 class="mb-0">{{ number_format($outOfStock) }}</h3>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td><span class="badge bg-light text-dark">{{ $product->sku }}</span></td>
                                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td>
                                    <span class="fw-bold {{ $product->stock_quantity <= 0 ? 'text-danger' : ($product->stock_quantity <= 5 ? 'text-warning' : 'text-primary') }}">
                                        {{ $product->stock_quantity }} units
                                    </span>
                                </td>
                                <td>
                                    @if($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($product->stock_quantity <= 5)
                                        <span class="badge bg-warning text-dark">Critical</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Low</span>
                                    @endif
                                </td>
                                <td>{{ $product->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Restock
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @if($products->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                    <h5 class="text-muted">All products are well stocked!</h5>
                                    <p class="text-muted mb-0">No products below the threshold of {{ $threshold }} units.</p>
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

<!-- Quick Restock Suggestions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-truck text-primary me-2"></i>
                    Quick Restock Suggestions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($products->take(4) as $product)
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="fw-semibold">{{ $product->name }}</h6>
                                <p class="text-muted small">Current: {{ $product->stock_quantity }} units</p>
                                <p class="text-muted small">Suggested: {{ max(20, $product->stock_quantity * 2) }} units</p>
                                <button class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-shopping-cart me-1"></i> Order Now
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
