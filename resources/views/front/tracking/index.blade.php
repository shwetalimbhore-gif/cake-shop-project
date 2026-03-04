@extends('layouts.front')

@section('title', 'Track Your Order - ' . setting('site_name'))
@section('page-title', 'Track Order')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Track Order</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Track Order</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="track-card">
                <div class="track-header text-center mb-4">
                    <div class="track-icon-wrapper mb-3">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h2 class="track-title">Where's My Order?</h2>
                    <p class="track-subtitle">Enter your order number and email to track your package</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('tracking.track') }}" method="POST" class="track-form" id="trackForm">
                    @csrf

                    <!-- Order Number Field -->
                    <div class="form-group mb-4">
                        <label for="order_number" class="form-label">
                            <i class="fas fa-hashtag me-2"></i>Order Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-box"></i>
                            </span>
                            <input type="text"
                                   name="order_number"
                                   id="order_number"
                                   class="form-control @error('order_number') is-invalid @enderror"
                                   value="{{ old('order_number') }}"
                                   placeholder="e.g., ORD-698DE6931C6FD"
                                   required>
                        </div>
                        @error('order_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter the order number from your confirmation email</small>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="Enter your email"
                                   required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use the email you placed the order with</small>
                    </div>

                    <!-- Track Button -->
                    <button type="submit" class="btn-track" id="trackBtn">
                        <span><i class="fas fa-search me-2"></i>Track Order</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <!-- Help Link -->
                    <div class="text-center mt-4">
                        <p class="help-text">
                            <i class="fas fa-question-circle me-1"></i>
                            Having trouble? <a href="{{ route('contact') }}">Contact Support</a>
                        </p>
                    </div>
                </form>

                <!-- Quick Tips -->
                <div class="track-tips mt-5">
                    <h6 class="tips-title">
                        <i class="fas fa-lightbulb me-2"></i>
                        Quick Tips
                    </h6>
                    <ul class="tips-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Order numbers can be found in your confirmation email
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Tracking updates every 2-3 hours
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Need help? Our support team is available 24/7
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== TRACK CARD STYLES ===== */
.track-card {
    background: white;
    border-radius: 24px;
    padding: 40px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--sand);
    transition: all 0.3s;
}

.track-card:hover {
    box-shadow: var(--shadow-lg);
}

/* ===== TRACK HEADER ===== */
.track-header {
    margin-bottom: 30px;
}

.track-icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
}

.track-title {
    font-family: 'Prata', serif;
    font-size: 2rem;
    color: var(--charcoal);
    margin-bottom: 10px;
}

.track-subtitle {
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

.form-label i {
    color: var(--terracotta);
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

/* ===== TRACK BUTTON ===== */
.btn-track {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.1rem;
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

.btn-track:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(201, 124, 93, 0.3);
}

.btn-track i {
    transition: transform 0.3s;
}

.btn-track:hover i.fa-arrow-right {
    transform: translateX(5px);
}

/* ===== HELP TEXT ===== */
.help-text {
    color: var(--taupe);
    font-size: 0.9rem;
}

.help-text a {
    color: var(--terracotta);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.help-text a:hover {
    color: #b86a4a;
    text-decoration: underline;
}

/* ===== TRACK TIPS ===== */
.track-tips {
    border-top: 1px dashed var(--sand);
    padding-top: 25px;
    margin-top: 25px;
}

.tips-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.tips-title i {
    color: var(--terracotta);
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tips-list li i {
    color: #2e7d32;
    font-size: 0.8rem;
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
    color: #2e7d32;
}

.alert-danger {
    background: #ffebee;
    color: #c62828;
}

/* ===== INVALID FEEDBACK ===== */
.invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .track-card {
        padding: 30px 20px;
    }

    .track-title {
        font-size: 1.8rem;
    }

    .track-icon-wrapper {
        width: 70px;
        height: 70px;
        font-size: 1.8rem;
    }

    .btn-track {
        padding: 14px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .track-card {
        padding: 25px 15px;
    }

    .track-title {
        font-size: 1.5rem;
    }

    .track-subtitle {
        font-size: 0.9rem;
    }

    .form-label {
        font-size: 0.9rem;
    }

    .input-group-text,
    .form-control {
        padding: 0.6rem 1rem;
    }

    .tips-list li {
        font-size: 0.85rem;
    }
}
</style>

<script>
    // Loading state on form submit
    document.getElementById('trackForm')?.addEventListener('submit', function(e) {
        const button = document.getElementById('trackBtn');
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Tracking...';
        button.disabled = true;
    });

    // Real-time validation (optional)
    const orderNumber = document.getElementById('order_number');
    const email = document.getElementById('email');

    orderNumber?.addEventListener('input', function() {
        if (this.value.length > 0) {
            this.classList.remove('is-invalid');
        }
    });

    email?.addEventListener('input', function() {
        if (this.validity.valid) {
            this.classList.remove('is-invalid');
        }
    });
</script>
@endsection
