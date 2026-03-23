@extends('layouts.admin')

@section('title', 'Financial Reports - Admin Panel')
@section('page-title', 'Financial Dashboard')

@section('content')
<style>
    .financial-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .financial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.7;
    }
    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .profit-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .revenue-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .cost-card {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .margin-card {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        color: white;
    }
    .info-box-custom {
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    .info-box-custom:hover {
        transform: translateX(5px);
    }
    .season-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid;
        transition: all 0.3s;
    }
    .season-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .payment-method-item {
        padding: 12px;
        margin-bottom: 8px;
        background: white;
        border-radius: 8px;
        border-left: 3px solid;
        transition: all 0.2s;
    }
    .payment-method-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    .discount-impact-card {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        border-radius: 15px;
        padding: 20px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: bold;
    }
    .stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
    }
    .container-fluid {
        padding-bottom: 0;
    }
    .row:last-child {
        margin-bottom: 0;
    }
</style>

<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card financial-card shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                Select Date Range
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.financial') }}" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Start Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-calendar"></i></span>
                                </div>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">End Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-calendar"></i></span>
                                </div>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-chart-line mr-2"></i> Generate Report
                            </button>
                            <a href="{{ route('admin.reports.financial') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row mb-4">
        <div class="col-12 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-lg" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel mr-2"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-danger btn-lg" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf mr-2"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="financial-card revenue-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">TOTAL REVENUE</h6>
                            <h2 class="text-white mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                            <small class="text-white-50">from all orders</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="financial-card cost-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">TOTAL COST</h6>
                            <h2 class="text-white mb-0">${{ number_format($totalCost, 2) }}</h2>
                            <small class="text-white-50">ingredients + overhead</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="financial-card profit-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">NET PROFIT</h6>
                            <h2 class="text-white mb-0">${{ number_format($totalProfit, 2) }}</h2>
                            <small class="text-white-50">
                                @if($totalProfit > 0)
                                    <i class="fas fa-arrow-up trend-up"></i> Positive growth
                                @else
                                    <i class="fas fa-arrow-down trend-down"></i> Negative
                                @endif
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="financial-card margin-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">PROFIT MARGIN</h6>
                            <h2 class="text-white mb-0">{{ number_format($profitMargin, 1) }}%</h2>
                            <small class="text-white-50">
                                @if($profitMargin >= 30)
                                    <i class="fas fa-check-circle"></i> Excellent
                                @elseif($profitMargin >= 20)
                                    <i class="fas fa-chart-line"></i> Good
                                @elseif($profitMargin >= 10)
                                    <i class="fas fa-chart-simple"></i> Average
                                @else
                                    <i class="fas fa-exclamation-triangle"></i> Needs Improvement
                                @endif
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Chart -->
    <div class="row mb-4" id="profit">
        <div class="col-12">
            <div class="card financial-card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-primary mr-2"></i>
                        Revenue vs Cost Analysis
                    </h5>
                    <div class="card-tools">
                        <span class="badge badge-info">Last {{ $profitData->count() }} products</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="profitChart" style="height: 450px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit by Product -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card financial-card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes text-success mr-2"></i>
                        Profit Analysis by Product
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                 <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                    <th>Cost</th>
                                    <th>Profit</th>
                                    <th>Margin</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profitData as $item)
                                @php
                                    $marginColor = $item->margin >= 30 ? 'success' : ($item->margin >= 15 ? 'warning' : 'danger');
                                    $profitClass = $item->profit >= 0 ? 'text-success' : 'text-danger';
                                @endphp
                                <tr>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->quantity_sold }}</td>
                                    <td class="text-primary">${{ number_format($item->revenue, 2) }}</td>
                                    <td class="text-secondary">${{ number_format($item->total_cost, 2) }}</td>
                                    <td class="{{ $profitClass }} font-weight-bold">${{ number_format($item->profit, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $marginColor }}" style="width: {{ $item->margin }}%"></div>
                                            </div>
                                            <span class="badge badge-{{ $marginColor }}">{{ number_format($item->margin, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->margin >= 30)
                                            <span class="badge badge-success"><i class="fas fa-crown"></i> Top Performer</span>
                                        @elseif($item->margin >= 15)
                                            <span class="badge badge-info"><i class="fas fa-chart-line"></i> Good</span>
                                        @elseif($item->margin >= 0)
                                            <span class="badge badge-warning"><i class="fas fa-chart-simple"></i> Average</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Loss</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td>Total</td>
                                    <td>{{ $profitData->sum('quantity_sold') }}</td>
                                    <td>${{ number_format($profitData->sum('revenue'), 2) }}</td>
                                    <td>${{ number_format($profitData->sum('total_cost'), 2) }}</td>
                                    <td class="text-success">${{ number_format($profitData->sum('profit'), 2) }}</td>
                                    <td colspan="2">{{ number_format($profitMargin, 1) }}% Overall Margin</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Impact & Payment Methods -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3" id="discounts">
            <div class="card financial-card shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags text-warning mr-2"></i>
                        Discount Impact Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="discount-impact-card mb-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-value">{{ $discountImpact->orders_with_discount ?? 0 }}</div>
                                <div class="stat-label">Orders with Discount</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-value">${{ number_format($discountImpact->total_discount_given ?? 0, 2) }}</div>
                                <div class="stat-label">Total Discount Given</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-value">${{ number_format($discountImpact->avg_discount ?? 0, 2) }}</div>
                                <div class="stat-label">Avg Discount per Order</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">Revenue Impact Comparison</h6>
                    <div class="progress-group mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="fas fa-tag text-warning"></i> With Discount</span>
                            <span class="font-weight-bold">${{ number_format($discountImpact->revenue_with_discount ?? 0, 2) }}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            @php
                                $totalRevenue = ($discountImpact->revenue_with_discount ?? 0) + ($discountImpact->revenue_without_discount ?? 0);
                                $withDiscountPercent = $totalRevenue > 0 ? (($discountImpact->revenue_with_discount ?? 0) / $totalRevenue) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-warning" style="width: {{ $withDiscountPercent }}%">
                                {{ number_format($withDiscountPercent, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="progress-group mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="fas fa-tag-slash text-primary"></i> Without Discount</span>
                            <span class="font-weight-bold">${{ number_format($discountImpact->revenue_without_discount ?? 0, 2) }}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" style="width: {{ 100 - $withDiscountPercent }}%">
                                {{ number_format(100 - $withDiscountPercent, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Insight:</strong>
                        @if(($discountImpact->total_discount_given ?? 0) > 0)
                            Discounts have generated ${{ number_format($discountImpact->revenue_with_discount ?? 0, 2) }} in revenue,
                            which is {{ number_format($withDiscountPercent, 1) }}% of total sales.
                        @else
                            No discounts were applied during this period.
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3" id="payments">
            <div class="card financial-card shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card text-info mr-2"></i>
                        Payment Methods Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="paymentChart" style="height: 250px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            @php
                                $totalPaymentsAmount = $paymentMethods->sum('total');
                            @endphp
                            @foreach($paymentMethods as $method)
                            <div class="payment-method-item" style="border-left-color:
                                @switch($method->payment_method)
                                    @case('cash') #28a745 @break
                                    @case('card') #007bff @break
                                    @case('upi') #17a2b8 @break
                                    @default #6c757d
                                @endswitch">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas
                                            @switch($method->payment_method)
                                                @case('cash') fa-money-bill-wave @break
                                                @case('card') fa-credit-card @break
                                                @case('upi') fa-mobile-alt @break
                                                @default fa-circle
                                            @endswitch mr-2"></i>
                                        <strong>{{ ucfirst($method->payment_method) }}</strong>
                                    </div>
                                    <span class="badge badge-secondary">{{ $method->count }} orders</span>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Total:</span>
                                        <span class="font-weight-bold">${{ number_format($method->total, 2) }}</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 5px;">
                                        @php $percentage = $totalPaymentsAmount > 0 ? ($method->total / $totalPaymentsAmount) * 100 : 0; @endphp
                                        <div class="progress-bar" style="width: {{ $percentage }}%; background-color:
                                            @switch($method->payment_method)
                                                @case('cash') #28a745 @break
                                                @case('card') #007bff @break
                                                @case('upi') #17a2b8 @break
                                                @default #6c757d
                                            @endswitch">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}% of total payments</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seasonal Revenue - FINAL SECTION -->
    <div class="row" id="seasonal">
        <div class="col-12">
            <div class="card financial-card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt text-danger mr-2"></i>
                        Seasonal Revenue Analysis
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        // Categorize revenue by seasons
                        $seasons = [
                            'Spring' => ['March', 'April', 'May'],
                            'Summer' => ['June', 'July', 'August'],
                            'Fall' => ['September', 'October', 'November'],
                            'Winter' => ['December', 'January', 'February']
                        ];

                        $seasonalRevenueData = [];
                        $totalSeasonalRevenue = 0;
                        $seasonalChartLabels = [];
                        $seasonalChartData = [];

                        // Process seasonal revenue data
                        if($seasonalRevenue && count($seasonalRevenue) > 0) {
                            foreach($seasonalRevenue as $date => $revenueItems) {
                                $month = \Carbon\Carbon::parse($date)->format('F');
                                $revenue = $revenueItems->sum('revenue');
                                $seasonalChartLabels[] = \Carbon\Carbon::parse($date)->format('M d');
                                $seasonalChartData[] = $revenue;

                                foreach($seasons as $season => $months) {
                                    if(in_array($month, $months)) {
                                        if(!isset($seasonalRevenueData[$season])) {
                                            $seasonalRevenueData[$season] = 0;
                                        }
                                        $seasonalRevenueData[$season] += $revenue;
                                        $totalSeasonalRevenue += $revenue;
                                        break;
                                    }
                                }
                            }
                        }

                        // Sort seasons in order
                        $seasonOrder = ['Spring', 'Summer', 'Fall', 'Winter'];
                        $sortedSeasons = [];
                        foreach($seasonOrder as $season) {
                            if(isset($seasonalRevenueData[$season])) {
                                $sortedSeasons[$season] = $seasonalRevenueData[$season];
                            }
                        }
                    @endphp

                    @if(!empty($seasonalChartData) && count($seasonalChartData) > 0)
                        <!-- Seasonal Revenue Cards -->
                        @if(!empty($sortedSeasons))
                        <div class="row mb-4">
                            @foreach($sortedSeasons as $season => $revenue)
                            @php
                                $seasonIcons = [
                                    'Spring' => 'fa-seedling',
                                    'Summer' => 'fa-sun',
                                    'Fall' => 'fa-leaf',
                                    'Winter' => 'fa-snowflake'
                                ];
                                $seasonColors = [
                                    'Spring' => '#28a745',
                                    'Summer' => '#fd7e14',
                                    'Fall' => '#dc3545',
                                    'Winter' => '#17a2b8'
                                ];
                                $percentage = $totalSeasonalRevenue > 0 ? ($revenue / $totalSeasonalRevenue) * 100 : 0;
                            @endphp
                            <div class="col-md-3 mb-3">
                                <div class="season-card" style="border-left-color: {{ $seasonColors[$season] }};">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas {{ $seasonIcons[$season] }} mr-2" style="color: {{ $seasonColors[$season] }}; font-size: 24px;"></i>
                                            <h5 class="mb-1">{{ $season }}</h5>
                                            <small class="text-muted">Seasonal Revenue</small>
                                        </div>
                                        <div class="text-right">
                                            <h4 class="mb-0 text-success">${{ number_format($revenue, 2) }}</h4>
                                            <small>{{ number_format($percentage, 1) }}% of total</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-3" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%; background-color: {{ $seasonColors[$season] }};"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Seasonal Trend Chart -->
                        <div class="chart-container" style="position: relative; height: 400px; margin-bottom: 20px;">
                            <canvas id="seasonalChart"></canvas>
                        </div>

                        <!-- Seasonal Insights -->
                        @if(!empty($sortedSeasons) && $totalSeasonalRevenue > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-chart-line"></i>
                                    <strong>Seasonal Insights:</strong>
                                    @php
                                        $bestSeason = array_keys($sortedSeasons, max($sortedSeasons))[0];
                                        $worstSeason = array_keys($sortedSeasons, min($sortedSeasons))[0];
                                    @endphp
                                    <ul class="mb-0 mt-2">
                                        <li><strong>{{ $bestSeason }}</strong> is the highest revenue season with <strong>${{ number_format(max($sortedSeasons), 2) }}</strong> in sales ({{ number_format(($seasonalRevenueData[$bestSeason] / $totalSeasonalRevenue) * 100, 1) }}% of seasonal revenue).</li>
                                        <li><strong>{{ $worstSeason }}</strong> shows the lowest performance with <strong>${{ number_format(min($sortedSeasons), 2) }}</strong> in revenue ({{ number_format(($seasonalRevenueData[$worstSeason] / $totalSeasonalRevenue) * 100, 1) }}% of seasonal revenue).</li>
                                        <li>Consider promoting seasonal specials during {{ $worstSeason }} to boost sales.</li>
                                        <li>Your best performing day in this period: <strong>{{ $seasonalChartLabels[array_search(max($seasonalChartData), $seasonalChartData)] }}</strong> with <strong>${{ number_format(max($seasonalChartData), 2) }}</strong> revenue.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-chart-line fa-3x mb-3 d-block text-muted"></i>
                            <h5>No Seasonal Revenue Data Available</h5>
                            <p class="mb-0">Please select a different date range to view seasonal revenue trends.</p>
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
                    backgroundColor: 'rgba(40,167,69,0.7)',
                    borderColor: 'rgba(40,167,69,1)',
                    borderWidth: 1,
                    borderRadius: 8
                },
                {
                    label: 'Cost',
                    data: {!! json_encode($profitData->pluck('total_cost')) !!},
                    backgroundColor: 'rgba(220,53,69,0.7)',
                    borderColor: 'rgba(220,53,69,1)',
                    borderWidth: 1,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '$' + value },
                    grid: { color: '#e9ecef' }
                },
                x: {
                    grid: { display: false }
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
                    'rgba(0,123,255,0.8)',
                    'rgba(23,162,184,0.8)',
                    'rgba(108,117,125,0.8)'
                ],
                borderColor: [
                    'rgba(40,167,69,1)',
                    'rgba(0,123,255,1)',
                    'rgba(23,162,184,1)',
                    'rgba(108,117,125,1)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                    }
                }
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
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(153,102,255,1)',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '$' + value },
                    grid: { color: '#e9ecef' }
                },
                x: {
                    grid: { display: false }
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
