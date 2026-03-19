@extends('layouts.admin')

@section('title', 'Top Selling Cakes - Admin Panel')
@section('page-title', 'Top Selling Cakes Report')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Select Date Range</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.top-cakes') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Number of Items</label>
                            <select name="limit" class="form-control">
                                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10</option>
                                <option value="20" {{ $limit == 20 ? 'selected' : '' }}>Top 20</option>
                                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-filter"></i> Generate Report
                        </button>
                        <a href="{{ route('admin.reports.top-cakes') }}" class="btn btn-secondary mt-4">
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
                    <h3>{{ $summary['total_items'] }}</h3>
                    <p>Top Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary['total_quantity'] }}</h3>
                    <p>Total Units Sold</p>
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
                    <h3>${{ number_format($summary['avg_price'], 2) }}</h3>
                    <p>Average Price</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top {{ $limit }} Selling Cakes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="topCakesTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Rank</th>
                                    <th>Cake Name</th>
                                    <th>Flavor</th>
                                    <th>Quantity Sold</th>
                                    <th>Times Ordered</th>
                                    <th>Total Revenue</th>
                                    <th>Contribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCakes as $index => $cake)
                                @php
                                    $contribution = ($cake->total_revenue / $summary['total_revenue']) * 100;
                                @endphp
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <span class="badge badge-warning"><i class="fas fa-crown"></i> #1</span>
                                        @elseif($index == 1)
                                            <span class="badge badge-secondary">#2</span>
                                        @elseif($index == 2)
                                            <span class="badge badge-bronze">#3</span>
                                        @else
                                            #{{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>{{ $cake->name }}</td>
                                    <td>{{ ucfirst($cake->flavor) ?? 'N/A' }}</td>
                                    <td>{{ $cake->total_quantity }}</td>
                                    <td>{{ $cake->times_ordered }}</td>
                                    <td class="text-success">${{ number_format($cake->total_revenue, 2) }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: {{ $contribution }}%">
                                                {{ round($contribution, 1) }}%
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

@section('styles')
<style>
.badge-bronze {
    background-color: #cd7f32;
    color: white;
}
</style>
@endsection

@section('scripts')
<script>
function exportReport(format) {
    var startDate = $('input[name="start_date"]').val();
    var endDate = $('input[name="end_date"]').val();
    var limit = $('select[name="limit"]').val();
    var exportUrl = '{{ route("admin.reports.export", "top-cakes") }}' +
                    '?start_date=' + startDate +
                    '&end_date=' + endDate +
                    '&limit=' + limit +
                    '&format=' + format;
    window.location.href = exportUrl;
}
</script>
@endsection
