@extends('layouts.admin')

@section('title', 'Monthly Overview - Admin Panel')
@section('page-title', 'Monthly Overview Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.monthly-overview') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.monthly-overview') }}" class="btn btn-secondary mt-4">
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
                    <h3>${{ number_format($summary['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_orders'] }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary['total_months'] }}</h3>
                    <p>Total Months</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($summary['avg_monthly_revenue'], 2) }}</h3>
                    <p>Avg Monthly Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Month Alert -->
    @if($summary['best_month'])
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-success">
                <i class="fas fa-trophy"></i>
                <strong>Best Performing Month:</strong>
                {{ \Carbon\Carbon::create()->month($summary['best_month']->month)->format('F') }} {{ $summary['best_month']->year }}
                with ${{ number_format($summary['best_month']->total_revenue, 2) }} revenue
            </div>
        </div>
    </div>
    @endif

    <!-- Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Orders</th>
                                    <th>Custom Cakes</th>
                                    <th>Revenue</th>
                                    <th>Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $data)
                                <tr>
                                    <td>{{ $data->year }}</td>
                                    <td>{{ \Carbon\Carbon::create()->month($data->month)->format('F') }}</td>
                                    <td>{{ $data->total_orders }}</td>
                                    <td>{{ $data->custom_cakes }}</td>
                                    <td class="text-success">${{ number_format($data->total_revenue, 2) }}</td>
                                    <td>${{ number_format($data->avg_order_value, 2) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyData->map(function($data) {
                return \Carbon\Carbon::create()->month($data->month)->format('F') . ' ' . $data->year;
            })) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyData->pluck('total_revenue')) !!},
                backgroundColor: 'rgba(60,141,188,0.7)',
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
                    ticks: { callback: value => '$' + value }
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

    var exportUrl = '{{ route("admin.reports.export", "monthly-overview") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
