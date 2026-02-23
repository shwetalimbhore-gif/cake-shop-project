@extends('layouts.front')

@section('title', 'Order Tracking - ' . $order->order_number)
@section('page-title', 'Order Tracking')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Track Order</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tracking.index') }}">Track Order</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <!-- Order Header -->
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-muted mb-2">Order Number</h5>
                    <h3 class="fw-bold text-primary">{{ $order->order_number }}</h3>
                    <p class="mb-0">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block">
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'shipped' ? 'info' : ($order->status == 'processing' ? 'warning' : 'secondary')) }} p-3 fs-6">
                            <i class="fas fa-{{ $order->status == 'delivered' ? 'check-circle' : ($order->status == 'shipped' ? 'truck' : ($order->status == 'processing' ? 'clock' : 'hourglass')) }} me-2"></i>
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Timeline -->
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-map-marked-alt text-primary me-2"></i>
                Tracking Timeline
            </h5>

            <div class="tracking-timeline">
                <!-- Order Placed -->
                <div class="timeline-item">
                    <div class="timeline-badge bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-semibold mb-1">Order Placed</h6>
                        <p class="text-muted mb-0">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                        <small class="text-success">Confirmed</small>
                    </div>
                </div>

                <!-- Order Processed -->
                @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                <div class="timeline-item">
                    <div class="timeline-badge bg-info">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-semibold mb-1">Order Processed</h6>
                        <p class="text-muted mb-0">{{ $order->updated_at->format('M d, Y - h:i A') }}</p>
                        <small class="text-info">Your order is being prepared</small>
                    </div>
                </div>
                @endif

                <!-- Order Shipped -->
                @if(in_array($order->status, ['shipped', 'delivered']))
                <div class="timeline-item">
                    <div class="timeline-badge bg-primary">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-semibold mb-1">Order Shipped</h6>
                        @if($order->tracking_number)
                            <p class="text-muted mb-1">
                                <strong>Tracking #:</strong> {{ $order->tracking_number }}
                                <button class="btn btn-sm btn-link" onclick="copyToClipboard('{{ $order->tracking_number }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </p>
                        @endif
                        @if($order->courier_name)
                            <p class="text-muted mb-0"><strong>Courier:</strong> {{ $order->courier_name }}</p>
                        @endif
                        @if($order->estimated_delivery)
                            <small class="text-primary">
                                <i class="fas fa-clock me-1"></i>
                                Est. Delivery: {{ $order->estimated_delivery->format('M d, Y') }}
                            </small>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Out for Delivery -->
                @if($order->status == 'delivered' || ($order->current_location && $order->status == 'shipped'))
                <div class="timeline-item">
                    <div class="timeline-badge bg-warning">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-semibold mb-1">Out for Delivery</h6>
                        @if($order->current_location)
                            <p class="text-muted mb-0">Current Location: {{ $order->current_location }}</p>
                        @endif
                        @if($order->delivery_notes)
                            <small class="text-warning">{{ $order->delivery_notes }}</small>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Delivered -->
                @if($order->status == 'delivered')
                <div class="timeline-item">
                    <div class="timeline-badge bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-semibold mb-1">Delivered</h6>
                        <p class="text-muted mb-0">{{ $order->delivered_at?->format('M d, Y - h:i A') }}</p>
                        <small class="text-success">Package delivered successfully</small>
                    </div>
                </div>
                @endif
            </div>

            @if($order->status == 'cancelled')
            <div class="alert alert-danger mt-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This order has been cancelled.
                @if($order->cancellation_reason)
                    Reason: {{ $order->cancellation_reason }}
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Live Tracking Map (if out for delivery) -->
    @if($order->status == 'shipped' && $order->driver_latitude && $order->driver_longitude)
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                Live Location
            </h5>
            <div id="map" style="height: 400px; border-radius: 15px;"></div>
        </div>
    </div>
    @endif

    <!-- Order Summary -->
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-shopping-bag text-primary me-2"></i>
                Order Summary
            </h5>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ format_currency($item->price) }}</td>
                            <td>{{ format_currency($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong class="text-primary">{{ format_currency($order->total) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('shop') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
                @auth
                    <a href="{{ route('account.orders') }}" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-list me-2"></i>My Orders
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-left: 45px;
        padding-bottom: 30px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-badge {
        position: absolute;
        left: -30px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .timeline-content {
        padding-bottom: 20px;
        border-bottom: 1px dashed #e0e0e0;
    }

    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }

    .timeline:before {
        content: '';
        position: absolute;
        left: 10px;
        top: 15px;
        bottom: 10px;
        width: 2px;
        background: #e0e0e0;
        z-index: 1;
    }
</style>

@push('scripts')
@if($order->status == 'shipped' && $order->driver_latitude && $order->driver_longitude)
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
<script>
    function initMap() {
        const driverLocation = {
            lat: {{ $order->driver_latitude }},
            lng: {{ $order->driver_longitude }}
        };

        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: driverLocation,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });

        // Driver marker
        new google.maps.Marker({
            position: driverLocation,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/truck.png',
                scaledSize: new google.maps.Size(50, 50)
            },
            title: 'Your Order'
        });

        // Destination marker (customer address)
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            address: '{{ $order->shipping_address }}, {{ $order->shipping_city }}'
        }, function(results, status) {
            if (status === 'OK') {
                new google.maps.Marker({
                    position: results[0].geometry.location,
                    map: map,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/home.png',
                        scaledSize: new google.maps.Size(40, 40)
                    },
                    title: 'Delivery Address'
                });
            }
        });
    }

    // Refresh driver location every 30 seconds
    setInterval(function() {
        fetch('{{ route("tracking.status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.driver_latitude && data.driver_longitude) {
                    // Update map marker
                    toastr.info('Driver location updated');
                }
            });
    }, 30000);

    initMap();
</script>
@endif

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        toastr.success('Tracking number copied!');
    }
</script>
@endpush
@endsection
