@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number . ' - Admin Panel')
@section('page-title', 'Order #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-box text-primary me-2"></i>
                    Order Items
                </h5>
                <span class="badge {{ $order->status_badge }} p-2">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    @if(!empty($item->options) && is_array(json_decode($item->options, true)))
                                        @php $options = json_decode($item->options, true); @endphp
                                        <small class="text-muted">
                                            @foreach($options as $key => $value)
                                                {{ ucfirst($key) }}: {{ $value }}
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        </small>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $item->sku ?? 'N/A' }}</span></td>
                                <td>{{ format_currency($item->price) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold">{{ format_currency($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td>{{ format_currency($order->subtotal) }}</td>
                            </tr>
                            @if($order->tax > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                <td>{{ format_currency($order->tax) }}</td>
                            </tr>
                            @endif
                            @if($order->shipping_cost > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                <td>{{ format_currency($order->shipping_cost) }}</td>
                            </tr>
                            @endif
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                <td>-{{ format_currency($order->discount) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end"><strong class="fs-5">Total:</strong></td>
                                <td><strong class="fs-5 text-primary">{{ format_currency($order->total) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-clock text-info me-2"></i>
                    Order Timeline
                </h5>
            </div>
            <div class="card-body">
                <style>
                    .timeline {
                        position: relative;
                        padding-left: 30px;
                    }
                    .timeline-item {
                        position: relative;
                        padding-left: 45px;
                        padding-bottom: 25px;
                    }
                    .timeline-item:last-child {
                        padding-bottom: 0;
                    }
                    .timeline-badge {
                        position: absolute;
                        left: -30px;
                        top: 0;
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 14px;
                        z-index: 2;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    }
                    .timeline-content {
                        padding-bottom: 15px;
                        border-bottom: 1px solid #eee;
                    }
                    .timeline-item:last-child .timeline-content {
                        border-bottom: none;
                    }
                    .timeline:before {
                        content: '';
                        position: absolute;
                        left: 12px;
                        top: 15px;
                        bottom: 10px;
                        width: 2px;
                        background: #e0e0e0;
                        z-index: 1;
                    }
                    .timeline-title {
                        font-weight: 600;
                        color: #333;
                        margin-bottom: 5px;
                    }
                    .timeline-date {
                        color: #6c757d;
                        font-size: 0.9rem;
                    }
                    .timeline-time {
                        color: #6c757d;
                        font-size: 0.85rem;
                        font-style: italic;
                    }
                </style>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-badge bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Placed</div>
                            <div class="timeline-date">{{ $order->created_at->format('F d, Y') }}</div>
                            <div class="timeline-time">{{ $order->created_at->format('h:i A') }}</div>
                        </div>
                    </div>

                    @if($order->shipped_at)
                    <div class="timeline-item">
                        <div class="timeline-badge bg-info">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Shipped</div>
                            <div class="timeline-date">{{ $order->shipped_at->format('F d, Y') }}</div>
                            <div class="timeline-time">{{ $order->shipped_at->format('h:i A') }}</div>
                            @if($order->tracking_number)
                                <div class="mt-2 small">
                                    <strong>Tracking:</strong> {{ $order->tracking_number }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($order->delivered_at)
                    <div class="timeline-item">
                        <div class="timeline-badge bg-primary">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Delivered</div>
                            <div class="timeline-date">{{ $order->delivered_at->format('F d, Y') }}</div>
                            <div class="timeline-time">{{ $order->delivered_at->format('h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($order->cancelled_at)
                    <div class="timeline-item">
                        <div class="timeline-badge bg-danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Cancelled</div>
                            <div class="timeline-date">{{ $order->cancelled_at->format('F d, Y') }}</div>
                            <div class="timeline-time">{{ $order->cancelled_at->format('h:i A') }}</div>
                            @if($order->cancellation_reason)
                                <div class="mt-2 small">
                                    <strong>Reason:</strong> {{ $order->cancellation_reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user text-primary me-2"></i>
                    Customer Information
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 50px; height: 50px; font-size: 20px;">
                        {{ substr($order->shipping_name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-1 fw-semibold">{{ $order->shipping_name }}</h6>
                        <p class="text-muted mb-0 small">{{ $order->shipping_email }}</p>
                        <p class="text-muted mb-0 small">{{ $order->shipping_phone }}</p>
                    </div>
                </div>

                @if($order->user)
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Registered User:</span>
                    <a href="#" class="fw-bold text-primary">{{ $order->user->name }}</a>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                    Shipping Address
                </h5>
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <address class="mb-0">
                    <strong>{{ $order->shipping_name }}</strong><br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                    {{ $order->shipping_country }}<br>
                    <abbr title="Phone">P:</abbr> {{ $order->shipping_phone }}
                </address>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-credit-card text-success me-2"></i>
                    Payment Information
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted ps-0">Method:</td>
                        <td class="fw-semibold">{{ ucfirst($order->payment_method ?? 'Not specified') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Status:</td>
                        <td>
                            <span class="badge {{ $order->payment_status_badge }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @if($order->payment_status == 'paid')
                    <tr>
                        <td class="text-muted ps-0">Paid on:</td>
                        <td>{{ $order->updated_at->format('M d, Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-bar text-warning me-2"></i>
                    Order Summary
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted ps-0">Order Number:</td>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Order Date:</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Total Items:</td>
                        <td>{{ $order->items->count() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Total Amount:</td>
                        <td class="fw-bold text-success fs-5">{{ format_currency($order->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($order->admin_notes)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-sticky-note text-info me-2"></i>
                    Admin Notes
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $order->admin_notes }}</p>
            </div>
        </div>
        @endif

        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary flex-fill">
                <i class="fas fa-edit me-1"></i>Edit Order
            </a>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-success flex-fill" target="_blank">
                <i class="fas fa-print me-1"></i>Invoice
            </a>
        </div>
    </div>
</div>
@endsection
