@extends('layouts.admin')

@section('title', 'Order Summary - Admin Panel')
@section('page-title', 'Order Summary Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.orders.summary') }}" id="filterForm">
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
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <a href="{{ route('admin.reports.orders.summary') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $summary['total_orders'] }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($summary['completion_rate'], 1) }}%</h3>
                    <p>Completion Rate</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($summary['cancellation_rate'], 1) }}%</h3>
                    <p>Cancellation Rate</p>
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
    </div>

    <!-- Status Breakdown Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Number of Orders</th>
                                    <th>Percentage</th>
                                    <th>Total Revenue</th>
                                    <th>Average Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusBreakdown as $status)
                                @php
                                    $percentage = ($summary['total_orders'] > 0) ? ($status->count / $summary['total_orders']) * 100 : 0;
                                    $avgValue = $status->count > 0 ? $status->revenue / $status->count : 0;
                                    $badgeClass = $status->status == 'completed' ? 'success' :
                                                  ($status->status == 'pending' ? 'warning' :
                                                  ($status->status == 'cancelled' ? 'danger' : 'info'));
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $badgeClass }} badge-lg">
                                            {{ ucfirst($status->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $status->count }}</td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-{{ $badgeClass }}" style="width: {{ $percentage }}%">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($status->revenue, 2) }}</td>
                                    <td>${{ number_format($avgValue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th>{{ $summary['total_orders'] }}</th>
                                    <th>100%</th>
                                    <th>${{ number_format($summary['total_revenue'], 2) }}</th>
                                    <th>${{ number_format($summary['avg_order'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Trend Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Weekly Order Trend by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Distribution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Hourly Order Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" style="height: 400px;"></canvas>
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
    // Weekly Chart
    new Chart(document.getElementById('weeklyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklyTrend->pluck('day')) !!},
            datasets: [
                {
                    label: 'Completed',
                    data: {!! json_encode($weeklyTrend->pluck('completed_count')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Pending',
                    data: {!! json_encode($weeklyTrend->pluck('pending_count')) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.5)',
                    borderColor: 'rgba(255, 193, 7, 1)',
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
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Hourly Chart
    new Chart(document.getElementById('hourlyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($hourlyDistribution->pluck('hour')->map(function($h) { return $h . ':00'; })) !!},
            datasets: [
                {
                    label: 'Completed',
                    data: {!! json_encode($hourlyDistribution->pluck('completed_count')) !!},
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Pending',
                    data: {!! json_encode($hourlyDistribution->pluck('pending_count')) !!},
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Cancelled',
                    data: {!! json_encode($hourlyDistribution->pluck('cancelled_count')) !!},
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
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
});
</script>
@endsection
