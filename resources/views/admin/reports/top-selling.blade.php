@extends('layouts.admin')

@section('title', 'Top Selling Products - Admin Panel')
@section('page-title', 'Top Selling Products')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-crown text-warning me-2"></i>
                    Top Selling Products
                </h5>
                <form method="GET" action="{{ route('admin.reports.top-selling') }}" class="d-flex gap-2">
                    <select name="period" class="form-select">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top by Quantity -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Top 10 by Quantity Sold
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topByQuantity as $index => $product)
                            <tr>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning"><i class="fas fa-crown me-1"></i> #1</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary">#2</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-bronze">#3</span>
                                    @else
                                        #{{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->product_name }}</td>
                                <td>{{ number_format($product->total_sold) }}</td>
                                <td class="text-primary">{{ format_currency($product->revenue) }}</td>
                            </tr>
                            @endforeach
                            @if($topByQuantity->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center py-4">No data available</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top by Revenue -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-dollar-sign text-success me-2"></i>
                    Top 10 by Revenue
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product</th>
                                <th>Revenue</th>
                                <th>Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topByRevenue as $index => $product)
                            <tr>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning"><i class="fas fa-crown me-1"></i> #1</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary">#2</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-bronze">#3</span>
                                    @else
                                        #{{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->product_name }}</td>
                                <td class="text-success fw-bold">{{ format_currency($product->revenue) }}</td>
                                <td>{{ number_format($product->total_sold) }}</td>
                            </tr>
                            @endforeach
                            @if($topByRevenue->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center py-4">No data available</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Visualization -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Revenue Distribution (Top 10)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-bronze {
        background-color: #cd7f32;
        color: white;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const topProducts = @json($topByRevenue);

    const labels = topProducts.map(p => p.product_name.length > 30 ? p.product_name.substring(0, 30) + '...' : p.product_name);
    const revenueData = topProducts.map(p => p.revenue);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: revenueData,
                backgroundColor: [
                    '#ff6b8b', '#28a745', '#17a2b8', '#ffc107', '#dc3545',
                    '#6610f2', '#fd7e14', '#20c997', '#e83e8c', '#6f42c1'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
