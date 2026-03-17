@extends('layouts.admin')

@section('title', 'Walk-in Order Receipt - Admin Panel')
@section('page-title', 'Walk-in Order Receipt')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-receipt text-warning me-2"></i>
                    Order Receipt
                </h5>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                    <a href="{{ route('admin.orders.walkin.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-2"></i>New Order
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">{{ setting('site_name') }}</h4>
                    <p class="text-muted mb-1">{{ setting('contact_address') }}</p>
                    <p class="text-muted mb-1">Phone: {{ setting('contact_phone') }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h6 class="fw-semibold">Receipt #: <span class="text-primary">{{ $order->order_number }}</span></h6>
                        <p class="mb-1">Date: {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p class="mb-1">Cashier: {{ Auth::user()->name }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <h6 class="fw-semibold">Customer Details</h6>
                        <p class="mb-1">Name: {{ $order->walkin_customer_name }}</p>
                        <p class="mb-1">Phone: {{ $order->walkin_customer_phone }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Size/Flavor</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>
                                    @if($item->options)
                                        @php $options = json_decode($item->options, true); @endphp
                                        @if(!empty($options['size']))
                                            <span class="badge bg-light text-dark">{{ $options['size'] }}</span>
                                        @endif
                                        @if(!empty($options['flavor']))
                                            <span class="badge bg-light text-dark">{{ $options['flavor'] }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end"><strong>${{ number_format($order->tax, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="text-primary fs-5">${{ number_format($order->total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Payment Method:</span>
                                <span class="badge bg-info">{{ ucfirst($order->payment_method) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Payment Status:</span>
                                <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            @if($order->walkin_notes)
                            <div class="mt-3">
                                <span class="fw-semibold">Notes:</span>
                                <p class="mb-0 mt-1">{{ $order->walkin_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">Thank you for your purchase!</p>
                    <p class="text-muted small">{{ setting('site_name') }} - Where every cake tells a story</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .btn-primary, .btn-success, .sidebar, .navbar, footer {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    body {
        background: white !important;
        padding: 20px;
    }
}
</style>
@endsection
