{{-- resources/views/admin/reports/customers.blade.php --}}
@extends('layouts.admin')

@section('title', 'Customer Reports - Admin Panel')
@section('page-title', 'Customer Reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Filter Reports</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.customers') }}" id="filterForm">
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
                        <a href="{{ route('admin.reports.customers') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <div class="btn-group mt-4">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="exportReport('csv')">CSV</a>
                                <a class="dropdown-item" href="#" onclick="exportReport('excel')">Excel</a>
                                <a class="dropdown-item" href="#" onclick="exportReport('pdf')">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $topCustomers->count() }}</h3>
                    <p>Top Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $newCustomers }}</h3>
                    <p>New Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $returningCustomers }}</h3>
                    <p>Returning Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- New vs Returning Chart -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">New vs Returning Customers</h5>
                </div>
                <div class="card-body">
                    <canvas id="customerTypeChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order Frequency Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="frequencyChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="row" id="top">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Customers by Spending</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Total Orders</th>
                                <th>Total Spent</th>
                                <th>Avg Order Value</th>
                                <th>Last Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->total_orders }}</td>
                                <td>${{ number_format($customer->total_spent, 2) }}</td>
                                <td>${{ number_format($customer->avg_order_value, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer->last_order_date)->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Order History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Customer Orders</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? ($order->walkin_customer_name ?? 'Guest') }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->items->count() }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    @if($order->is_custom_cake)
                                        <span class="badge badge-primary">Custom</span>
                                    @else
                                        <span class="badge badge-secondary">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($order->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $customerOrders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Date Customers -->
    <div class="row" id="special">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Customers with Special Date Orders</h5>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $specialDateCustomers->count() }} customers</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($specialDateCustomers->take(8) as $customer)
                        <div class="col-md-3">
                            <div class="card card-widget widget-user-2">
                                <div class="widget-user-header bg-warning">
                                    <h5 class="widget-user-desc">{{ $customer->name }}</h5>
                                    <h6>{{ $customer->email }}</h6>
                                </div>
                                <div class="card-footer p-0">
                                    <ul class="nav flex-column">
                                        @foreach($customer->orders->take(2) as $order)
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                {{ ucfirst($order->occasion) }}
                                                <span class="float-right badge bg-primary">{{ $order->created_at->format('M d') }}</span>
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
    // New vs Returning Chart
    var ctx1 = document.getElementById('customerTypeChart').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['New Customers', 'Returning Customers'],
            datasets: [{
                data: [{{ $newCustomers }}, {{ $returningCustomers }}],
                backgroundColor: ['rgba(40,167,69,0.8)', 'rgba(255,193,7,0.8)'],
                borderColor: ['rgba(40,167,69,1)', 'rgba(255,193,7,1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Order Frequency Chart
    @if($orderFrequency->isNotEmpty())
    var ctx2 = document.getElementById('frequencyChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($orderFrequency->keys()->map(function($count) {
                return $count . ' order' . ($count > 1 ? 's' : '');
            })) !!},
            datasets: [{
                label: 'Number of Customers',
                data: {!! json_encode($orderFrequency->values()) !!},
                backgroundColor: 'rgba(60,141,188,0.8)',
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
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif
});

function exportReport(format) {
    var form = $('#filterForm');
    var action = '{{ route("admin.reports.export", ["type" => "customers", "report" => "customers"]) }}';

    form.append('<input type="hidden" name="format" value="' + format + '">');
    form.attr('action', action);
    form.submit();

    setTimeout(function() {
        $('input[name="format"]').remove();
    }, 100);
}
</script>
@endsection
