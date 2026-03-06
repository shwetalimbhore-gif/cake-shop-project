@extends('layouts.guest')

@section('title', 'Forgot Password - ' . setting('site_name', 'Cozy Cravings'))

@section('content')
<div class="forgot-password-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7">
                <div class="forgot-card" data-aos="fade-up">
                    <!-- Logo/Brand -->
                    <div class="text-center mb-4">
                        <a href="{{ route('home') }}" class="forgot-brand">
                            @if(setting('site_logo'))
                                <img src="{{ asset('storage/' . setting('site_logo')) }}"
                                     alt="{{ setting('site_name') }}"
                                     class="brand-logo">
                            @else
                                <span class="brand-text">{{ setting('site_name', 'Cozy Cravings') }}</span>
                            @endif
                        </a>
                    </div>

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="forgot-title">Forgot Password?</h2>
                        <p class="forgot-subtitle">No worries! Enter your email and we'll send you a reset link.</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="forgot-form">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-group mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input id="email"
                                       type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Enter your email"
                                       required
                                       autofocus>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-reset" id="submitBtn">
                            <span><i class="fas fa-paper-plane me-2"></i>Send Reset Link</span>
                        </button>

                        <!-- Back to Login -->
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="back-to-login">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Login
                            </a>
                        </div>
                    </form>

                    <!-- Help Text -->
                    <div class="help-text mt-4 text-center">
                        <p class="mb-0">
                            <i class="fas fa-question-circle me-1"></i>
                            Remember your password? <a href="{{ route('login') }}">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== FORGOT PASSWORD PAGE STYLES ===== */
.forgot-password-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #fdf8f2 0%, #f7e6e0 100%);
    position: relative;
    overflow: hidden;
    padding: 20px;
}

/* Animated Background */
.forgot-password-page::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 800px;
    height: 800px;
    background: radial-gradient(circle, rgba(201, 124, 93, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 15s ease-in-out infinite;
}

.forgot-password-page::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255, 215, 140, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 20s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, 30px); }
}

/* Forgot Card */
.forgot-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 40px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 2;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

/* Brand */
.forgot-brand {
    display: inline-block;
    text-decoration: none;
}

.brand-logo {
    height: 60px;
    width: auto;
}

.brand-text {
    font-family: 'Prata', serif;
    font-size: 2rem;
    color: var(--terracotta);
    font-weight: 600;
}

/* Header */
.forgot-title {
    font-family: 'Prata', serif;
    font-size: 2rem;
    color: var(--charcoal);
    margin-bottom: 10px;
}

.forgot-subtitle {
    color: var(--taupe);
    font-size: 1rem;
    line-height: 1.5;
}

/* Form Elements */
.form-label {
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.input-group {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s;
}

.input-group:focus-within {
    box-shadow: 0 5px 20px rgba(201, 124, 93, 0.15);
    transform: translateY(-2px);
}

.input-group-text {
    background: white;
    border: 2px solid var(--sand);
    border-right: none;
    color: var(--taupe);
    padding: 0.75rem 1.2rem;
}

.form-control {
    border: 2px solid var(--sand);
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

/* Submit Button */
.btn-reset {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    color: white;
    border: none;
    border-radius: 15px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
}

.btn-reset:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(201, 124, 93, 0.3);
}

.btn-reset:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Back to Login */
.back-to-login {
    color: var(--terracotta);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}

.back-to-login:hover {
    color: #b86a4a;
    transform: translateX(-5px);
}

/* Help Text */
.help-text {
    color: var(--taupe);
    font-size: 0.9rem;
}

.help-text a {
    color: var(--terracotta);
    text-decoration: none;
    font-weight: 600;
}

.help-text a:hover {
    color: #b86a4a;
    text-decoration: underline;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #2e7d32;
}

.alert-danger {
    background: #ffebee;
    color: #c62828;
    border-left: 4px solid #c62828;
}

.alert ul {
    list-style: none;
    padding-left: 0;
}

/* Invalid Feedback */
.invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .forgot-card {
        padding: 30px 20px;
    }

    .forgot-title {
        font-size: 1.8rem;
    }

    .brand-text {
        font-size: 1.8rem;
    }

    .brand-logo {
        height: 50px;
    }
}

@media (max-width: 576px) {
    .forgot-card {
        padding: 25px 15px;
    }

    .forgot-title {
        font-size: 1.5rem;
    }

    .forgot-subtitle {
        font-size: 0.9rem;
    }

    .btn-reset {
        padding: 12px;
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
    const emailInput = document.getElementById('email');
    emailInput?.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.remove('is-invalid');
        }
    });
</script>
@endsection
