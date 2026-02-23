<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ setting('site_description', 'Modern artisanal bakery crafting exceptional cakes') }}">

    <title>@yield('title', setting('site_name', 'Cozy Cravings'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Modern, Clean Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Prata&display=swap" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    <style>
        :root {
            --cream: #FDF8F2;
            --taupe: #B7A69A;
            --sage: #A7B5A3;
            --terracotta: #C97C5D;
            --charcoal: #4A4A4A;
            --sand: #E5D9CC;
            --blush: #F7E6E0;
            --olive: #7A8B7A;
            --gold: #D4AF37;
            --ivory: #FFFFF0;
            --shadow-sm: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 30px 60px -15px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            color: var(--charcoal);
            background-color: var(--cream);
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Prata', serif;
            font-weight: 400;
            letter-spacing: -0.02em;
        }

        .display-1, .display-2, .display-3, .display-4, .display-5, .display-6 {
            font-family: 'Prata', serif;
        }

        /* Modern Navbar */
        .navbar-modern {
            background: rgba(253, 248, 242, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-bottom: 1px solid rgba(183, 166, 154, 0.1);
        }

        .navbar-modern.scrolled {
            padding: 12px 0;
            background: rgba(253, 248, 242, 0.98);
            box-shadow: var(--shadow-sm);
        }

        .navbar-brand-modern {
            font-family: 'Prata', serif;
            font-size: 1.8rem;
            color: var(--terracotta) !important;
            letter-spacing: -0.02em;
            transition: opacity 0.3s;
        }

        .navbar-brand-modern:hover {
            opacity: 0.8;
        }

        /* ===== IMPROVED LOGO SIZE ===== */
        .navbar-brand-modern img {
            height: 65px;        /* Increased from 45px to 65px */
            width: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        /* Optional hover effect */
        .navbar-brand-modern img:hover {
            transform: scale(1.02);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .navbar-brand-modern img {
                height: 45px;    /* Smaller on mobile */
            }
        }

        .nav-link-modern {
            color: var(--charcoal) !important;
            font-weight: 500;
            margin: 0 12px;
            padding: 8px 0 !important;
            position: relative;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .nav-link-modern:hover {
            opacity: 1;
        }

        .nav-link-modern:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--terracotta);
            transition: width 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .nav-link-modern:hover:after,
        .nav-link-modern.active:after {
            width: 100%;
        }

        .nav-link-modern.active {
            opacity: 1;
            font-weight: 600;
        }

        /* Modern Buttons */
        .btn-modern {
            padding: 14px 32px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-modern {
            background: var(--terracotta);
            color: white;
            box-shadow: 0 10px 20px -8px rgba(201, 124, 93, 0.3);
        }

        .btn-primary-modern:hover {
            background: #b86a4a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -10px rgba(201, 124, 93, 0.4);
        }

        .btn-outline-modern {
            background: transparent;
            color: var(--charcoal);
            border: 1px solid var(--taupe);
        }

        .btn-outline-modern:hover {
            background: var(--cream);
            border-color: var(--terracotta);
            color: var(--terracotta);
            transform: translateY(-2px);
        }

        /* Modern Cards */
        .card-modern {
            background: white;
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-modern:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
        }

        /* Product Image Container - Fixed */
        .product-image-container {
            position: relative;
            height: 280px;
            overflow: hidden;
            background: var(--sand);
        }

        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .card-modern:hover .product-image-container img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: var(--terracotta);
            color: white;
            padding: 6px 14px;
            border-radius: 2px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            z-index: 2;
        }

        .product-badge.featured {
            background: var(--gold);
            color: var(--charcoal);
            left: auto;
            right: 16px;
        }

        /* Cart Badge */
        .cart-badge-modern {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--terracotta);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-subtitle {
            color: var(--taupe);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 16px;
            display: block;
        }

        .section-title {
            font-size: 2.8rem;
            color: var(--charcoal);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .section-description {
            color: var(--taupe);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Dropdown Modern */
        .dropdown-menu-modern {
            border: none;
            border-radius: 4px;
            box-shadow: var(--shadow-md);
            padding: 12px;
            margin-top: 12px;
            background: white;
            min-width: 220px;
        }

        .dropdown-item-modern {
            border-radius: 2px;
            padding: 10px 16px;
            color: var(--charcoal);
            font-weight: 400;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .dropdown-item-modern:hover {
            background: var(--cream);
            color: var(--terracotta);
            padding-left: 20px;
        }

        .dropdown-item-modern i {
            width: 20px;
            margin-right: 10px;
            color: var(--taupe);
        }

        /* Alerts */
        .alert-modern {
            border: none;
            border-radius: 2px;
            padding: 16px 24px;
            margin: 0;
            font-weight: 500;
        }

        .alert-success-modern {
            background: var(--sage);
            color: white;
        }

        .alert-danger-modern {
            background: var(--terracotta);
            color: white;
        }

        /* Breadcrumb */
        .breadcrumb-modern {
            padding: 40px 0 20px;
            margin-bottom: 40px;
            border-bottom: 1px solid var(--sand);
        }

        .breadcrumb-modern h1 {
            font-size: 2.2rem;
            margin-bottom: 16px;
        }

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-custom .breadcrumb-item {
            font-size: 0.9rem;
        }

        .breadcrumb-custom .breadcrumb-item a {
            color: var(--taupe);
            text-decoration: none;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--charcoal);
        }

        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item:before {
            color: var(--sand);
        }

        /* Footer Modern */
        .footer-modern {
            background: var(--charcoal);
            color: var(--cream);
            padding: 80px 0 40px;
            margin-top: 100px;
            position: relative;
        }

        .footer-title {
            font-size: 1.2rem;
            margin-bottom: 24px;
            color: white;
            letter-spacing: 1px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--taupe);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links-modern {
            margin-top: 20px;
        }

        .social-link-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--cream);
            border-radius: 2px;
            margin-right: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-link-modern:hover {
            background: var(--terracotta);
            color: white;
            transform: translateY(-2px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 30px;
            margin-top: 50px;
            text-align: center;
            color: var(--taupe);
            font-size: 0.85rem;
        }

        /* Pagination Modern */
        .pagination-modern {
            gap: 8px;
        }

        .page-link-modern {
            border: none;
            border-radius: 2px;
            padding: 10px 16px;
            color: var(--charcoal);
            font-weight: 500;
            transition: all 0.3s;
            background: transparent;
        }

        .page-link-modern:hover {
            background: var(--sand);
            color: var(--charcoal);
        }

        .page-item-modern.active .page-link-modern {
            background: var(--terracotta);
            color: white;
        }

        /* Form Elements */
        .form-control-modern {
            border: 1px solid var(--sand);
            border-radius: 2px;
            padding: 14px 16px;
            transition: all 0.3s;
        }

        .form-control-modern:focus {
            border-color: var(--terracotta);
            outline: none;
            box-shadow: 0 0 0 3px rgba(201, 124, 93, 0.1);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }

            .navbar-brand-modern {
                font-size: 1.5rem;
            }

            .product-image-container {
                height: 220px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand-modern" href="{{ route('home') }}">
                @if(setting('site_logo'))
                    <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}">
                @else
                    {{ setting('site_name', 'Cozy Cravings') }}
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link-modern {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Collection</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern {{ request()->routeIs('tracking.*') ? 'active' : '' }}" href="{{ route('tracking.index') }}">Track Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Our Story</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-4">
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="position-relative">
                        <i class="fas fa-shopping-bag" style="color: var(--charcoal); font-size: 1.2rem;"></i>
                        @php $cartCount = count(Session::get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="cart-badge-modern">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- User Menu -->
                    @auth
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                               data-bs-toggle="dropdown" style="color: var(--charcoal);">
                                <i class="fas fa-user-circle me-2" style="font-size: 1.2rem;"></i>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item-modern" href="{{ route('account.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item-modern" href="{{ route('account.orders') }}">
                                        <i class="fas fa-shopping-bag"></i>Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item-modern" href="{{ route('account.profile') }}">
                                        <i class="fas fa-user"></i>Profile
                                    </a>
                                </li>
                                @if(auth()->user()->is_admin)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item-modern" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-cog"></i>Admin
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item-modern text-danger">
                                            <i class="fas fa-sign-out-alt"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--charcoal);">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="btn-modern btn-primary-modern">
                            Join
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer -->
    <div style="height: 90px;"></div>

    <!-- Modern Alerts -->
    @if(session('success'))
        <div class="alert-modern alert-success-modern alert-dismissible fade show" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-modern alert-danger-modern alert-dismissible fade show" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    @yield('content')

    <!-- Modern Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h5 class="footer-title">{{ setting('site_name', 'Cozy Cravings') }}</h5>
                    <p class="text-white-50 mb-4" style="color: var(--taupe) !important;">
                        {{ setting('site_description', 'Modern artisanal bakery crafting exceptional cakes') }}
                    </p>
                    <div class="social-links-modern">
                        @if(setting('facebook_url'))
                            <a href="{{ setting('facebook_url') }}" class="social-link-modern"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(setting('instagram_url'))
                            <a href="{{ setting('instagram_url') }}" class="social-link-modern"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(setting('twitter_url'))
                            <a href="{{ setting('twitter_url') }}" class="social-link-modern"><i class="fab fa-twitter"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Explore</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop') }}">Collection</a></li>
                        <li><a href="{{ route('tracking.index') }}">Track Order</a></li>
                        <li><a href="{{ route('about') }}">Our Story</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Contact</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2" style="color: var(--terracotta);"></i>{{ setting('contact_address', '123 Bakery Lane') }}</li>
                        <li><i class="fas fa-phone me-2" style="color: var(--terracotta);"></i>{{ setting('contact_phone', '+1 234 567 890') }}</li>
                        <li><i class="fas fa-envelope me-2" style="color: var(--terracotta);"></i>{{ setting('contact_email', 'hello@cozycravings.com') }}</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Hours</h5>
                    <ul class="footer-links">
                        @php
                            $hours = json_decode(setting('opening_hours', '{}'), true);
                        @endphp
                        <li><span class="d-inline-block w-50">Mon - Fri:</span> {{ $hours['Monday'] ?? '9am - 6pm' }}</li>
                        <li><span class="d-inline-block w-50">Saturday:</span> {{ $hours['Saturday'] ?? '10am - 5pm' }}</li>
                        <li><span class="d-inline-block w-50">Sunday:</span> {{ $hours['Sunday'] ?? 'Closed' }}</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} {{ setting('site_name', 'Cozy Cravings') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        // Navbar scroll
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Auto close alerts
        setTimeout(function() {
            document.querySelectorAll('.alert-modern').forEach(function(alert) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
