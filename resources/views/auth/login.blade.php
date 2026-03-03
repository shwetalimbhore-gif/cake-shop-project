@extends('layouts.guest')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <!-- Left Side - Login Form -->
            <div class="col-lg-5 col-md-7">
                <div class="login-card" data-aos="fade-right">
                    <!-- Logo/Brand -->
                    <div class="text-center mb-4">
                        <a href="{{ route('home') }}" class="login-brand">
                            @if(setting('site_logo'))
                                <img src="{{ asset('storage/' . setting('site_logo')) }}"
                                     alt="{{ setting('site_name') }}"
                                     class="brand-logo">
                            @else
                                <span class="brand-text">{{ setting('site_name', 'Cozy Cravings') }}</span>
                            @endif
                        </a>
                    </div>

                    <!-- Welcome Text -->
                    <div class="text-center mb-4">
                        <h2 class="welcome-title">Welcome Back!</h2>
                        <p class="welcome-subtitle">Sign in to continue to your account</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="login-form">
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
                                       autocomplete="email"
                                       autofocus>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input id="password"
                                       type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="Enter your password"
                                       required
                                       autocomplete="current-password">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="forgot-password-link" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn-login">
                            <span>Sign In</span>
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <span class="register-text">Don't have an account?</span>
                            <a href="{{ route('register') }}" class="register-link">
                                Create Account
                            </a>
                        </div>

                        <!-- Social Login (Optional) -->
                        <div class="social-login mt-4">
                            <p class="social-divider">
                                <span>Or continue with</span>
                            </p>
                            <div class="social-buttons">
                                <a href="#" class="social-btn google">
                                    <i class="fab fa-google"></i>
                                    <span class="d-none d-sm-inline">Google</span>
                                </a>
                                <a href="#" class="social-btn facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    <span class="d-none d-sm-inline">Facebook</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side - Hero Image -->
            <div class="col-lg-7 d-none d-lg-block">
                <div class="login-hero" data-aos="fade-left">
                    <div class="hero-content text-center">
                        <h1 class="hero-title">Welcome to {{ setting('site_name', 'Cozy Cravings') }}</h1>
                        <p class="hero-subtitle">Discover our delicious collection of handcrafted cakes</p>

                        <!-- Features -->
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <span>Freshly Baked</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <span>Eggless Options</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <span>Free Delivery</span>
                            </div>
                        </div>

                        <!-- Testimonial -->
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <i class="fas fa-quote-left quote-icon"></i>
                                <p class="testimonial-text">The best cakes I've ever had! Absolutely delicious and beautiful presentation.</p>
                                <div class="testimonial-author">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&size=40&background=C97C5D&color=fff" alt="Sarah">
                                    <div>
                                        <strong>Sarah Johnson</strong>
                                        <span class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== LOGIN PAGE STYLES ===== */
    .login-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #fdf8f2 0%, #f7e6e0 100%);
        position: relative;
        overflow: hidden;
    }

    /* Animated Background */
    .login-page::before {
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

    .login-page::after {
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

    /* Login Card */
    .login-card {
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
    .login-brand {
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

    /* Welcome Text */
    .welcome-title {
        font-family: 'Prata', serif;
        font-size: 2rem;
        color: var(--charcoal);
        margin-bottom: 10px;
    }

    .welcome-subtitle {
        color: var(--taupe);
        font-size: 1rem;
    }

    /* Form Elements */
    .form-label {
        font-weight: 600;
        color: var(--charcoal);
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .input-group {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
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
    }

    .form-control:focus + .input-group-text {
        border-color: var(--terracotta);
    }

    .toggle-password {
        border: 2px solid var(--sand);
        border-left: none;
        background: white;
        color: var(--taupe);
        padding: 0.75rem 1.2rem;
    }

    .toggle-password:hover {
        color: var(--terracotta);
        background: white;
    }

    /* Remember Me & Forgot Password */
    .form-check-input {
        border-color: var(--sand);
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--terracotta);
        border-color: var(--terracotta);
    }

    .forgot-password-link {
        color: var(--terracotta);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .forgot-password-link:hover {
        color: #b86a4a;
        text-decoration: underline;
    }

    /* Login Button */
    .btn-login {
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
        box-shadow: 0 10px 20px rgba(201, 124, 93, 0.2);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(201, 124, 93, 0.3);
    }

    /* Register Link */
    .register-text {
        color: var(--taupe);
        font-size: 0.95rem;
        margin-right: 5px;
    }

    .register-link {
        color: var(--terracotta);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .register-link:hover {
        color: #b86a4a;
        text-decoration: underline;
    }

    /* Social Login */
    .social-divider {
        text-align: center;
        position: relative;
        color: var(--taupe);
        font-size: 0.9rem;
        margin: 20px 0;
    }

    .social-divider::before,
    .social-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 45%;
        height: 1px;
        background: var(--sand);
    }

    .social-divider::before {
        left: 0;
    }

    .social-divider::after {
        right: 0;
    }

    .social-divider span {
        background: white;
        padding: 0 10px;
    }

    .social-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .social-btn {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        text-decoration: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        font-weight: 500;
    }

    .social-btn.google {
        background: #DB4437;
    }

    .social-btn.facebook {
        background: #4267B2;
    }

    .social-btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Hero Section */
    .login-hero {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        padding: 60px 40px;
        margin-left: 30px;
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
    }

    .hero-title {
        font-family: 'Prata', serif;
        font-size: 2.5rem;
        color: var(--charcoal);
        margin-bottom: 20px;
    }

    .hero-subtitle {
        color: var(--taupe);
        font-size: 1.1rem;
        margin-bottom: 50px;
    }

    /* Features Grid */
    .features-grid {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-bottom: 50px;
    }

    .feature-item {
        text-align: center;
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--terracotta), #b86a4a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 1.8rem;
        box-shadow: 0 10px 20px rgba(201, 124, 93, 0.3);
        transition: all 0.3s;
    }

    .feature-item:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .feature-item span {
        color: var(--charcoal);
        font-weight: 500;
        font-size: 0.95rem;
    }

    /* Testimonial */
    .testimonial-card {
        background: var(--cream);
        border-radius: 20px;
        padding: 30px;
        max-width: 400px;
        margin: 0 auto;
        position: relative;
    }

    .testimonial-content {
        position: relative;
    }

    .quote-icon {
        position: absolute;
        top: -10px;
        left: -10px;
        font-size: 2rem;
        color: var(--terracotta);
        opacity: 0.3;
    }

    .testimonial-text {
        color: var(--charcoal);
        font-style: italic;
        margin-bottom: 20px;
        line-height: 1.6;
        padding-left: 20px;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .testimonial-author img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .testimonial-author strong {
        color: var(--charcoal);
        display: block;
        margin-bottom: 3px;
    }

    .rating i {
        color: #ffc107;
        font-size: 0.8rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .login-card {
            margin: 20px;
        }
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 30px 20px;
        }

        .welcome-title {
            font-size: 1.5rem;
        }

        .social-buttons {
            flex-direction: column;
        }

        .social-btn {
            padding: 10px;
        }
    }

    @media (max-width: 576px) {
        .login-page {
            padding: 20px;
        }

        .login-card {
            padding: 25px 15px;
        }

        .features-grid {
            flex-wrap: wrap;
            gap: 20px;
        }

        .feature-item {
            flex: 0 0 calc(50% - 20px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle Password Visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Form Validation Animation
    const form = document.querySelector('.login-form');
    const inputs = form.querySelectorAll('input');

    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-group').style.transform = 'scale(1.02)';
            this.closest('.input-group').style.transition = 'transform 0.3s';
        });

        input.addEventListener('blur', function() {
            this.closest('.input-group').style.transform = 'scale(1)';
        });
    });

    // Loading State on Submit
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('.btn-login');
        button.innerHTML = '<span>Signing In...</span><i class="fas fa-spinner fa-spin ms-2"></i>';
        button.disabled = true;
    });
</script>
@endpush
