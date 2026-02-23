@extends('layouts.admin')

@section('title', 'Edit Order ' . $order->order_number . ' - Admin Panel')
@section('page-title', 'Edit Order #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Order Information
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 fw-semibold">Order Status</h5>

                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">Order Status</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', $order->status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="payment_status" class="form-label fw-semibold">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                    @foreach($paymentStatuses as $status)
                                        <option value="{{ $status }}" {{ old('payment_status', $order->payment_status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="tracking_number" class="form-label fw-semibold">Tracking Number</label>
                                <input type="text" name="tracking_number" id="tracking_number"
                                       class="form-control @error('tracking_number') is-invalid @enderror"
                                       value="{{ old('tracking_number', $order->tracking_number) }}">
                                @error('tracking_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="admin_notes" class="form-label fw-semibold">Admin Notes</label>
                                <textarea name="admin_notes" id="admin_notes" rows="4"
                                          class="form-control @error('admin_notes') is-invalid @enderror">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                @error('admin_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 fw-semibold">Shipping Information</h5>

                            <div class="mb-4">
                                <label for="shipping_name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="shipping_name" id="shipping_name"
                                       class="form-control @error('shipping_name') is-invalid @enderror"
                                       value="{{ old('shipping_name', $order->shipping_name) }}" required>
                                @error('shipping_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="shipping_email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="shipping_email" id="shipping_email"
                                       class="form-control @error('shipping_email') is-invalid @enderror"
                                       value="{{ old('shipping_email', $order->shipping_email) }}" required>
                                @error('shipping_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="shipping_phone" class="form-label fw-semibold">Phone</label>
                                <input type="text" name="shipping_phone" id="shipping_phone"
                                       class="form-control @error('shipping_phone') is-invalid @enderror"
                                       value="{{ old('shipping_phone', $order->shipping_phone) }}" required>
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="shipping_address" class="form-label fw-semibold">Address</label>
                                <input type="text" name="shipping_address" id="shipping_address"
                                       class="form-control @error('shipping_address') is-invalid @enderror"
                                       value="{{ old('shipping_address', $order->shipping_address) }}" required>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="shipping_city" class="form-label fw-semibold">City</label>
                                        <input type="text" name="shipping_city" id="shipping_city"
                                               class="form-control @error('shipping_city') is-invalid @enderror"
                                               value="{{ old('shipping_city', $order->shipping_city) }}" required>
                                        @error('shipping_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="shipping_state" class="form-label fw-semibold">State</label>
                                        <input type="text" name="shipping_state" id="shipping_state"
                                               class="form-control @error('shipping_state') is-invalid @enderror"
                                               value="{{ old('shipping_state', $order->shipping_state) }}" required>
                                        @error('shipping_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="shipping_zip" class="form-label fw-semibold">ZIP Code</label>
                                        <input type="text" name="shipping_zip" id="shipping_zip"
                                               class="form-control @error('shipping_zip') is-invalid @enderror"
                                               value="{{ old('shipping_zip', $order->shipping_zip) }}" required>
                                        @error('shipping_zip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="shipping_country" class="form-label fw-semibold">Country</label>
                                        <input type="text" name="shipping_country" id="shipping_country"
                                               class="form-control @error('shipping_country') is-invalid @enderror"
                                               value="{{ old('shipping_country', $order->shipping_country) }}" required>
                                        @error('shipping_country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Information -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-truck text-info me-2"></i>
                                Tracking Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.orders.tracking', $order) }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Tracking Number</label>
                                        <input type="text" name="tracking_number" class="form-control"
                                            value="{{ $order->tracking_number }}" placeholder="e.g., 1Z999AA123456789">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Courier Name</label>
                                        <select name="courier_name" class="form-select">
                                            <option value="">Select Courier</option>
                                            <option value="FedEx" {{ $order->courier_name == 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                            <option value="UPS" {{ $order->courier_name == 'UPS' ? 'selected' : '' }}>UPS</option>
                                            <option value="USPS" {{ $order->courier_name == 'USPS' ? 'selected' : '' }}>USPS</option>
                                            <option value="DHL" {{ $order->courier_name == 'DHL' ? 'selected' : '' }}>DHL</option>
                                            <option value="BlueDart" {{ $order->courier_name == 'BlueDart' ? 'selected' : '' }}>BlueDart</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Estimated Delivery</label>
                                        <input type="datetime-local" name="estimated_delivery" class="form-control"
                                            value="{{ $order->estimated_delivery ? $order->estimated_delivery->format('Y-m-d\TH:i') : '' }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Current Location</label>
                                        <input type="text" name="current_location" class="form-control"
                                            value="{{ $order->current_location }}" placeholder="e.g., New York Distribution Center">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Delivery Notes</label>
                                        <textarea name="delivery_notes" class="form-control" rows="2"
                                                placeholder="Additional notes for customer">{{ $order->delivery_notes }}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-truck me-2"></i>Update Tracking
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
