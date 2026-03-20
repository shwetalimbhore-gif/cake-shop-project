@extends('layouts.admin')

@section('title', 'Flavor Trends - Admin Panel')
@section('page-title', 'Flavor Trends Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.flavor-trends') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.flavor-trends') }}" class="btn btn-secondary mt-4">
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
                    <h3>{{ $summary['total_flavors'] }}</h3>
                    <p>Total Flavors</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_quantity'] }}</h3>
                    <p>Total Units</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ ucfirst($summary['top_flavor']) }}</h3>
                    <p>Top Flavor</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $summary['top_flavor_quantity'] }}</h3>
                    <p>Top Flavor Units</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Flavor Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="flavorPieChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Flavor Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Flavor</th>
                                    <th>Quantity</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flavorSummary as $flavor)
                                @php
                                    $percentage = ($flavor->total / $summary['total_quantity']) * 100;
                                @endphp
                                <tr>
                                    <td><span class="badge badge-primary">{{ ucfirst($flavor->flavor) }}</span></td>
                                    <td>{{ $flavor->total }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $percentage }}%">
                                                {{ round($percentage, 1) }}%
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

    <!-- Monthly Trends Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Flavor Trends</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Flavor</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Quantity</th>
                                    <th>Orders</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flavorTrends as $trend)
                                @php
                                    $maxQuantity = $flavorTrends->max('total_quantity');
                                    $popularity = ($trend->total_quantity / $maxQuantity) * 100;
                                @endphp
                                <tr>
                                    <td><span class="badge badge-info">{{ ucfirst($trend->flavor) }}</span></td>
                                    <td>{{ $trend->year }}</td>
                                    <td>{{ \Carbon\Carbon::create()->month($trend->month)->format('F') }}</td>
                                    <td>{{ $trend->total_quantity }}</td>
                                    <td>{{ $trend->order_count }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: {{ $popularity }}%">
                                                {{ round($popularity) }}%
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Flavor Distribution Pie Chart
    var ctx = document.getElementById('flavorPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($flavorSummary->pluck('flavor')->map(function($f) { return ucfirst($f); })) !!},
            datasets: [{
                data: {!! json_encode($flavorSummary->pluck('total')) !!},
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
                legend: { position: 'right' }
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

    var exportUrl = '{{ route("admin.reports.export", "flavor-trends") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
