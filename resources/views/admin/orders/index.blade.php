@extends('layouts.admin')

@section('title', 'Orders - Admin Panel')
@section('page-title', 'Orders Management')

@section('content')
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white border-0 shadow-lg h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Orders</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h2>
                        <div class="mt-2 small">
                            <span class="text-white-50 me-2">Online: {{ number_format($stats['online_orders']) }}</span>
                            <span class="text-white-50">Walk-in: {{ number_format($stats['walkin_orders']) }}</span>
                        </div>
                    </div>
                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white border-0 shadow-lg h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Revenue</h6>
                        <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_revenue']) }}</h2>
                        <div class="mt-2 small">
                            <span class="text-white-50 me-2">Online: {{ format_currency($stats['online_revenue']) }}</span>
                            <span class="text-white-50">Walk-in: {{ format_currency($stats['walkin_revenue']) }}</span>
                        </div>
                    </div>
                    <i class="fas fa-rupee-sign fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white border-0 shadow-lg h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Pending Orders</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['pending_orders']) }}</h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white border-0 shadow-lg h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Today's Revenue</h6>
                        <h2 class="mb-0 fw-bold">{{ format_currency($stats['today_revenue']) }}</h2>
                    </div>
                    <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-shopping-cart text-primary me-2"></i>
            Manage Orders
        </h5>
        <div>
            <a href="{{ route('admin.orders.walkin.create') }}" class="btn btn-warning me-2">
                <i class="fas fa-store me-2"></i>New Walk-in Order
            </a>
            <a href="{{ route('admin.orders.export') }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search order #, customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="order_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="online" {{ request('order_type') == 'online' ? 'selected' : '' }}>Online Orders</option>
                        <option value="walkin" {{ request('order_type') == 'walkin' ? 'selected' : '' }}>Walk-in Orders</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control"
                           placeholder="From" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Items</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            @if($order->order_type == 'walkin')
                                <span class="badge bg-warning">
                                    <i class="fas fa-store me-1"></i>Walk-in
                                </span>
                            @else
                                <span class="badge bg-info">
                                    <i class="fas fa-globe me-1"></i>Online
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $order->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if($order->order_type == 'walkin')
                                {{ $order->walkin_customer_name }}<br>
                                <small class="text-muted">{{ $order->walkin_customer_phone }}</small>
                                @if($order->created_by_admin)
                                    <br><small class="text-info">by: {{ $order->creator->name ?? 'Admin' }}</small>
                                @endif
                            @else
                                {{ $order->shipping_name }}<br>
                                <small class="text-muted">{{ $order->shipping_email }}</small>
                            @endif
                        </td>
                        <td class="fw-bold">{{ format_currency($order->total) }}</td>
                        <td>
                            <span class="badge {{ $order->status_badge }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $order->payment_status_badge }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $order->items->count() }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order) }}"
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($order->order_type == 'walkin')
                                <a href="{{ route('admin.orders.walkin.receipt', $order) }}"
                                   class="btn btn-sm btn-warning" title="Print Receipt">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                @endif
                                <button type="button" class="btn btn-sm btn-danger delete-order"
                                        data-id="{{ $order->id }}"
                                        data-number="{{ $order->order_number }}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Orders Found</h5>
                            <p class="text-muted mb-0">There are no orders to display.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
            </div>
            <div>
                @if ($orders->hasPages())
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            @if ($orders->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            @if ($orders->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete order <strong id="deleteOrderNumber"></strong>?</p>
                <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone! All order items will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Delete order
    $(document).on('click', '.delete-order', function() {
        var id = $(this).data('id');
        var number = $(this).data('number');
        $('#deleteOrderNumber').text(number);
        $('#deleteForm').attr('action', '/admin/orders/' + id);
        $('#deleteModal').modal('show');
    });

    // Auto-submit filters on change
    $('select[name="order_type"], select[name="status"], select[name="payment_status"]').change(function() {
        $(this).closest('form').submit();
    });

    // Auto-submit date inputs after 1 second delay
    var dateTimer;
    $('input[name="date_from"], input[name="date_to"]').change(function() {
        clearTimeout(dateTimer);
        var form = $(this).closest('form');
        dateTimer = setTimeout(function() {
            form.submit();
        }, 1000);
    });

    // Search with debounce
    var searchTimer;
    $('input[name="search"]').keyup(function(e) {
        clearTimeout(searchTimer);
        var form = $(this).closest('form');

        if (e.key === 'Enter') {
            form.submit();
            return;
        }

        searchTimer = setTimeout(function() {
            form.submit();
        }, 500);
    });
});
</script>
@endsection
