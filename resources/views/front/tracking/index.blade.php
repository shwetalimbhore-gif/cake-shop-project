@extends('layouts.front')

@section('title', 'Track Your Order - ' . setting('site_name'))
@section('page-title', 'Track Order')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Track Your Order</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Track Order</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="bg-soft-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-truck fa-3x text-primary"></i>
                        </div>
                        <h3 class="fw-bold">Where's My Order?</h3>
                        <p class="text-muted">Enter your order number and email to track</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('tracking.track') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Order Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                <input type="text" name="order_number" class="form-control @error('order_number') is-invalid @enderror"
                                       placeholder="e.g., ORD-698DE6931C6FD" value="{{ old('order_number') }}" required>
                            </div>
                            @error('order_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter your email" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                            <i class="fas fa-search me-2"></i>Track Order
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your information is secure
                        </p>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>Need Help?</h5>
                    <p class="text-muted mb-3">If you're having trouble tracking your order, contact our support team.</p>
                    <div class="d-flex gap-3">
                        <a href="tel:{{ setting('contact_phone') }}" class="btn btn-outline-primary flex-fill">
                            <i class="fas fa-phone me-2"></i>Call Us
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary flex-fill">
                            <i class="fas fa-envelope me-2"></i>Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background: rgba(255,107,139,0.1);
    }
</style>
@endsection
