@extends('layouts.front')

@section('title', 'Contact Us - ' . setting('site_name'))
@section('page-title', 'Contact Us')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <!-- Contact Info Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="contact-card text-center">
                <div class="contact-icon-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4 class="contact-card-title">Visit Us</h4>
                <p class="contact-card-text">
                    {{ setting('contact_address', '123 Bakery Street, Sweet City, SC 12345') }}
                </p>
                <div class="contact-card-footer">
                    <i class="fas fa-clock me-2"></i>
                    Mon-Sat: 9am - 6pm
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="contact-card text-center">
                <div class="contact-icon-wrapper" style="background: linear-gradient(135deg, #10B981, #059669);">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h4 class="contact-card-title">Call Us</h4>
                <p class="contact-card-text">
                    {{ setting('contact_phone', '+1 234 567 8900') }}
                </p>
                <div class="contact-card-footer">
                    <i class="fas fa-clock me-2"></i>
                    24/7 Support Available
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="contact-card text-center">
                <div class="contact-icon-wrapper" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4 class="contact-card-title">Email Us</h4>
                <p class="contact-card-text">
                    {{ setting('contact_email', 'info@mycakeshop.com') }}
                </p>
                <div class="contact-card-footer">
                    <i class="fas fa-reply me-2"></i>
                    Response within 24h
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-6">
            <div class="form-card">
                <div class="form-card-header">
                    <h3 class="form-card-title">
                        <i class="fas fa-paper-plane me-2" style="color: var(--terracotta);"></i>
                        Send us a Message
                    </h3>
                    <p class="form-card-subtitle">We'd love to hear from you! Fill out the form below and we'll get back to you soon.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                    @csrf

                    <div class="row g-4">
                        <!-- Name Field -->
                        <div class="col-12">
                            <label for="name" class="form-label">Your Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}"
                                       placeholder="Enter your full name"
                                       required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="col-12">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                       placeholder="Enter your email"
                                       required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject Field -->
                        <div class="col-12">
                            <label for="subject" class="form-label">Subject</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-heading"></i>
                                </span>
                                <input type="text"
                                       name="subject"
                                       id="subject"
                                       class="form-control @error('subject') is-invalid @enderror"
                                       value="{{ old('subject') }}"
                                       placeholder="What is this about?"
                                       required>
                            </div>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div class="col-12">
                            <label for="message" class="form-label">Message</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-comment"></i>
                                </span>
                                <textarea name="message"
                                          id="message"
                                          class="form-control @error('message') is-invalid @enderror"
                                          rows="5"
                                          placeholder="Write your message here..."
                                          required>{{ old('message') }}</textarea>
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <span><i class="fas fa-paper-plane me-2"></i>Send Message</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Map & Additional Info -->
        <div class="col-lg-6">
            <!-- Map Card -->
            <div class="map-card mb-4">
                <div class="map-card-header">
                    <h4 class="map-card-title">
                        <i class="fas fa-map-marked-alt me-2" style="color: var(--terracotta);"></i>
                        Our Location
                    </h4>
                </div>
                <div class="map-container">
                    @if(setting('contact_map'))
                        <iframe src="{{ setting('contact_map') }}"
                                width="100%"
                                height="350"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"></iframe>
                    @else
                        <div class="map-placeholder">
                            <i class="fas fa-map fa-3x mb-3" style="color: var(--taupe);"></i>
                            <p>Map location not set</p>
                            <small>{{ setting('contact_address', '123 Bakery Street, Sweet City, SC 12345') }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Opening Hours Card -->
            <div class="hours-card">
                <div class="hours-card-header">
                    <h4 class="hours-card-title">
                        <i class="fas fa-clock me-2" style="color: var(--terracotta);"></i>
                        Opening Hours
                    </h4>
                </div>
                <div class="hours-card-body">
                    @php
                        $hours = json_decode(setting('opening_hours', '{}'), true);
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $today = now()->format('l');
                    @endphp

                    <div class="hours-list">
                        @foreach($days as $day)
                            <div class="hours-item {{ $day == $today ? 'today' : '' }}">
                                <span class="day">{{ $day }}</span>
                                <span class="time">{{ $hours[$day] ?? 'Closed' }}</span>
                                @if($day == $today)
                                    @php
                                        $isOpen = isset($hours[$day]) && $hours[$day] != 'Closed';
                                    @endphp
                                    <span class="today-badge {{ $isOpen ? 'open' : 'closed' }}">
                                        {{ $isOpen ? 'Open Today' : 'Closed Today' }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Current Status -->
                    @php
                        $todayHours = $hours[now()->format('l')] ?? 'Closed';
                        $isOpen = $todayHours != 'Closed' &&
                                 now()->format('H:i') >= explode('-', $todayHours)[0] &&
                                 now()->format('H:i') <= explode('-', $todayHours)[1];
                    @endphp

                    <div class="current-status mt-4">
                        <div class="status-indicator {{ $isOpen ? 'open' : 'closed' }}">
                            <i class="fas fa-circle me-2"></i>
                            <span>We are currently {{ $isOpen ? 'open' : 'closed' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="social-card mt-4">
                <div class="social-card-header">
                    <h5 class="social-card-title">
                        <i class="fas fa-share-alt me-2" style="color: var(--terracotta);"></i>
                        Follow Us
                    </h5>
                </div>
                <div class="social-card-body">
                    <div class="social-links">
                        @if(setting('facebook_url'))
                            <a href="{{ setting('facebook_url') }}" class="social-link facebook" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>
                        @endif
                        @if(setting('instagram_url'))
                            <a href="{{ setting('instagram_url') }}" class="social-link instagram" target="_blank">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        @endif
                        @if(setting('twitter_url'))
                            <a href="{{ setting('twitter_url') }}" class="social-link twitter" target="_blank">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== CONTACT CARDS ===== */
.contact-card {
    background: white;
    border-radius: 20px;
    padding: 30px 25px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    transition: all 0.3s;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.contact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--terracotta), #b86a4a);
}

.contact-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.contact-icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
    transition: all 0.3s;
}

.contact-card:hover .contact-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.contact-card-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.contact-card-text {
    color: var(--taupe);
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 60px;
}

.contact-card-footer {
    padding-top: 15px;
    border-top: 1px dashed var(--sand);
    color: var(--charcoal);
    font-size: 0.9rem;
}

.contact-card-footer i {
    color: var(--terracotta);
}

/* ===== FORM CARD ===== */
.form-card {
    background: white;
    border-radius: 20px;
    padding: 35px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--sand);
}

.form-card-header {
    margin-bottom: 30px;
}

.form-card-title {
    font-family: 'Prata', serif;
    font-size: 2rem;
    color: var(--charcoal);
    margin-bottom: 10px;
}

.form-card-subtitle {
    color: var(--taupe);
    font-size: 1rem;
}

/* ===== FORM STYLES ===== */
.form-label {
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.input-group {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    transition: all 0.3s;
}

.input-group:focus-within {
    box-shadow: 0 5px 15px rgba(201, 124, 93, 0.15);
    transform: translateY(-2px);
}

.input-group-text {
    background: white;
    border: 1px solid var(--sand);
    border-right: none;
    color: var(--taupe);
    padding: 0.75rem 1.2rem;
}

.form-control {
    border: 1px solid var(--sand);
    border-left: none;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: var(--terracotta);
    box-shadow: none;
    outline: none;
}

.form-control::placeholder {
    color: var(--taupe);
    opacity: 0.6;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* ===== SUBMIT BUTTON ===== */
.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
    margin-top: 20px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(201, 124, 93, 0.3);
}

.btn-submit i.fa-arrow-right {
    transition: transform 0.3s;
}

.btn-submit:hover i.fa-arrow-right {
    transform: translateX(5px);
}

/* ===== MAP CARD ===== */
.map-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
}

.map-card-header {
    padding: 18px 25px;
    border-bottom: 1px solid var(--sand);
    background: var(--cream);
}

.map-card-title {
    font-family: 'Prata', serif;
    font-size: 1.3rem;
    color: var(--charcoal);
    margin: 0;
}

.map-container {
    height: 350px;
    overflow: hidden;
}

.map-container iframe {
    width: 100%;
    height: 100%;
}

.map-placeholder {
    width: 100%;
    height: 100%;
    background: var(--cream);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--taupe);
    text-align: center;
    padding: 20px;
}

/* ===== HOURS CARD ===== */
.hours-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
}

.hours-card-header {
    padding: 18px 25px;
    border-bottom: 1px solid var(--sand);
    background: var(--cream);
}

.hours-card-title {
    font-family: 'Prata', serif;
    font-size: 1.3rem;
    color: var(--charcoal);
    margin: 0;
}

.hours-card-body {
    padding: 25px;
}

.hours-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.hours-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: var(--cream);
    border-radius: 12px;
    position: relative;
}

.hours-item.today {
    background: rgba(201, 124, 93, 0.1);
    border: 1px solid var(--terracotta);
}

.day {
    font-weight: 600;
    color: var(--charcoal);
    min-width: 100px;
}

.time {
    color: var(--taupe);
    font-weight: 500;
}

.today-badge {
    margin-left: auto;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 600;
}

.today-badge.open {
    background: #e8f5e9;
    color: #1b5e20;
}

.today-badge.closed {
    background: #ffebee;
    color: #b71c1c;
}

.current-status {
    padding: 15px;
    background: var(--cream);
    border-radius: 12px;
    text-align: center;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 600;
}

.status-indicator.open {
    background: #e8f5e9;
    color: #1b5e20;
}

.status-indicator.closed {
    background: #ffebee;
    color: #b71c1c;
}

.status-indicator i {
    font-size: 0.6rem;
}

/* ===== SOCIAL CARD ===== */
.social-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
}

.social-card-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--sand);
    background: var(--cream);
}

.social-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin: 0;
}

.social-card-body {
    padding: 20px;
}

.social-links {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.social-link {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s;
}

.social-link i {
    font-size: 1.5rem;
}

.social-link span {
    font-size: 0.8rem;
    font-weight: 500;
}

.social-link.facebook {
    background: #4267B2;
    color: white;
}

.social-link.facebook:hover {
    background: #365899;
    transform: translateY(-3px);
}

.social-link.instagram {
    background: linear-gradient(45deg, #f09433, #d62976, #962fbf);
    color: white;
}

.social-link.instagram:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(214, 41, 118, 0.3);
}

.social-link.twitter {
    background: #1DA1F2;
    color: white;
}

.social-link.twitter:hover {
    background: #1a91da;
    transform: translateY(-3px);
}

/* ===== ALERTS ===== */
.alert {
    border: none;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.alert-success {
    background: #e8f5e9;
    color: #1b5e20;
}

/* ===== INVALID FEEDBACK ===== */
.invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .form-card {
        padding: 25px;
    }

    .form-card-title {
        font-size: 1.8rem;
    }

    .contact-card-text {
        min-height: auto;
    }

    .hours-item {
        flex-wrap: wrap;
        gap: 10px;
    }

    .day {
        min-width: 80px;
    }

    .today-badge {
        margin-left: 0;
        width: 100%;
        text-align: center;
    }

    .social-links {
        flex-direction: column;
    }

    .social-link {
        flex-direction: row;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .form-card {
        padding: 20px;
    }

    .form-card-title {
        font-size: 1.5rem;
    }

    .contact-icon-wrapper {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .contact-card-title {
        font-size: 1.1rem;
    }
}
</style>

<script>
    // Loading state on form submit
    document.getElementById('submitBtn')?.addEventListener('click', function() {
        const form = this.closest('form');
        if (form.checkValidity()) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            this.disabled = true;
        }
    });

    // Real-time validation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });
</script>
@endsection
