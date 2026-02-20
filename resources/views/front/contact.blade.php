@extends('layouts.front')

@section('title', 'Contact Us - ' . setting('site_name'))
@section('page-title', 'Contact Us')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Contact Info -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Visit Us</h5>
                        <p class="text-muted mb-0">{{ setting('contact_address', '123 Bakery Street, Sweet City, SC 12345') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-soft-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-phone fa-2x text-success"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Call Us</h5>
                        <p class="text-muted mb-1">{{ setting('contact_phone', '+1 234 567 8900') }}</p>
                        <p class="text-muted small">Mon-Sat: 9am - 6pm</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-soft-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-envelope fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Email Us</h5>
                        <p class="text-muted mb-0">{{ setting('contact_email', 'info@mycakeshop.com') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Send us a Message</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Your Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Your Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Subject</label>
                                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                           value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Message</label>
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                              rows="5" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Map & Hours -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Our Location</h5>
                    </div>
                    <div class="card-body p-0">
                        @if(setting('contact_map'))
                            <iframe src="{{ setting('contact_map') }}"
                                    width="100%"
                                    height="300"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"></iframe>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                                <i class="fas fa-map-marker-alt fa-3x text-muted me-2"></i>
                                <span class="text-muted">Map location not set</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Opening Hours</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            @php
                                $hours = json_decode(setting('opening_hours', '{}'), true);
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $today = now()->format('l');
                            @endphp

                            @foreach($days as $day)
                                <li class="d-flex justify-content-between py-2 {{ $day == $today ? 'fw-bold text-primary' : '' }}">
                                    <span>{{ $day }}</span>
                                    <span>{{ $hours[$day] ?? '09:00-18:00' }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Current Status:</span>
                            @php
                                $todayHours = $hours[now()->format('l')] ?? '09:00-18:00';
                                $isOpen = $todayHours != 'Closed' &&
                                         now()->format('H:i') >= explode('-', $todayHours)[0] &&
                                         now()->format('H:i') <= explode('-', $todayHours)[1];
                            @endphp

                            @if($isOpen)
                                <span class="text-success fw-semibold">
                                    <i class="fas fa-circle fa-2xs me-1"></i> Open Now
                                </span>
                            @else
                                <span class="text-danger fw-semibold">
                                    <i class="fas fa-circle fa-2xs me-1"></i> Closed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Got questions? We've got answers!</p>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            How far in advance should I order?
                        </h5>
                        <p class="text-muted mb-0">We recommend placing orders at least 3-4 days in advance. For custom designs and wedding cakes, please order 2-3 weeks ahead.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Do you offer delivery?
                        </h5>
                        <p class="text-muted mb-0">Yes! We offer delivery within {{ setting('delivery_radius', 20) }}km. Delivery is free for orders over {{ format_currency(setting('free_delivery_threshold', 100)) }}.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Can you accommodate dietary restrictions?
                        </h5>
                        <p class="text-muted mb-0">Absolutely! We offer gluten-free, dairy-free, and vegan options. Please mention your requirements when ordering.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            How should I store my cake?
                        </h5>
                        <p class="text-muted mb-0">Most cakes can be stored at room temperature for 2-3 days. For longer storage, refrigerate and bring to room temperature before serving.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
