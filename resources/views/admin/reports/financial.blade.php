@extends('layouts.admin')

@section('title', 'Financial Reports - Admin Panel')
@section('page-title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Filter Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.financial') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.financial') }}" class="btn btn-secondary mt-4">
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

    <!-- Financial Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($totalCost, 2) }}</h3>
                    <p>Total Cost</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($totalProfit, 2) }}</h3>
                    <p>Total Profit</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($profitMargin, 1) }}%</h3>
                    <p>Profit Margin</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Chart -->
    <div class="row" id="profit">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue vs Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <canvas id="profitChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit by Product -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profit by Product</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity Sold</th>
                                    <th>Revenue</th>
                                    <th>Cost</th>
                                    <th>Profit</th>
                                    <th>Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profitData as $item)
                                @php
                                    $marginColor = $item->margin >= 30 ? 'success' : ($item->margin >= 15 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->quantity_sold }}</td>
                                    <td>${{ number_format($item->revenue, 2) }}</td>
                                    <td>${{ number_format($item->total_cost, 2) }}</td>
                                    <td class="{{ $item->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        ${{ number_format($item->profit, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $marginColor }}">{{ number_format($item->margin, 1) }}%</span>
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

    <!-- Discount Impact & Payment Methods -->
    <div class="row">
        <div class="col-lg-6" id="discounts">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Discount Impact</h5>
                </div>
                <div class="card-body">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Orders with Discount</span>
                            <span class="info-box-number">{{ $discountImpact->orders_with_discount }}</span>
                        </div>
                    </div>
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Discount Given</span>
                            <span class="info-box-number">${{ number_format($discountImpact->total_discount_given, 2) }}</span>
                        </div>
                    </div>
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Discount per Order</span>
                            <span class="info-box-number">${{ number_format($discountImpact->avg_discount, 2) }}</span>
                        </div>
                    </div>

                    <h6 class="mt-4">Revenue Comparison</h6>
                    <div class="progress-group">
                        <span class="progress-text">With Discount</span>
                        <span class="float-right">${{ number_format($discountImpact->revenue_with_discount, 2) }}</span>
                        <div class="progress sm">
                            @php
                                $totalRevenue = $discountImpact->revenue_with_discount + $discountImpact->revenue_without_discount;
                                $withDiscountPercent = $totalRevenue > 0 ? ($discountImpact->revenue_with_discount / $totalRevenue) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $withDiscountPercent }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Without Discount</span>
                        <span class="float-right">${{ number_format($discountImpact->revenue_without_discount, 2) }}</span>
                        <div class="progress sm">
                            <div class="progress-bar bg-primary" style="width: {{ 100 - $withDiscountPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6" id="payments">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" style="height: 250px;"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            @foreach($paymentMethods as $method)
                            <tr>
                                <td>
                                    @switch($method->payment_method)
                                        @case('cash')
                                            <i class="fas fa-money-bill-wave text-success"></i>
                                            @break
                                        @case('card')
                                            <i class="fas fa-credit-card text-primary"></i>
                                            @break
                                        @case('upi')
                                            <i class="fas fa-mobile-alt text-info"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-secondary"></i>
                                    @endswitch
                                    {{ ucfirst($method->payment_method) }}
                                </td>
                                <td>{{ $method->count }} orders</td>
                                <td class="text-right">${{ number_format($method->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seasonal Revenue -->
    <div class="row" id="seasonal">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Seasonal Revenue Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="seasonalChart" style="height: 300px;"></canvas>
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
    @if($profitData->count() > 0)
    // Profit Chart
    var ctx1 = document.getElementById('profitChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($profitData->pluck('name')) !!},
            datasets: [
                {
                    label: 'Revenue',
                    data: {!! json_encode($profitData->pluck('revenue')) !!},
                    backgroundColor: 'rgba(40,167,69,0.8)',
                    borderColor: 'rgba(40,167,69,1)',
                    borderWidth: 1
                },
                {
                    label: 'Cost',
                    data: {!! json_encode($profitData->pluck('total_cost')) !!},
                    backgroundColor: 'rgba(220,53,69,0.8)',
                    borderColor: 'rgba(220,53,69,1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '$' + value }
                }
            }
        }
    });
    @endif

    @if($paymentMethods->count() > 0)
    // Payment Methods Chart
    var ctx2 = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($m) { return ucfirst($m); })) !!},
            datasets: [{
                data: {!! json_encode($paymentMethods->pluck('total')) !!},
                backgroundColor: [
                    'rgba(40,167,69,0.8)',
                    'rgba(23,162,184,0.8)',
                    'rgba(255,193,7,0.8)',
                    'rgba(108,117,125,0.8)'
                ],
                borderColor: [
                    'rgba(40,167,69,1)',
                    'rgba(23,162,184,1)',
                    'rgba(255,193,7,1)',
                    'rgba(108,117,125,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    @endif

    @if($seasonalRevenue->count() > 0)
    // Seasonal Chart
    var ctx3 = document.getElementById('seasonalChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: {!! json_encode($seasonalRevenue->keys()->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })) !!},
            datasets: [{
                label: 'Daily Revenue',
                data: {!! json_encode($seasonalRevenue->map(function($day) {
                    return $day->sum('revenue');
                })) !!},
                borderColor: 'rgba(153,102,255,1)',
                backgroundColor: 'rgba(153,102,255,0.1)',
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
                    ticks: { callback: value => '$' + value }
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

    var exportUrl = '{{ route("admin.reports.export", "financial") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
