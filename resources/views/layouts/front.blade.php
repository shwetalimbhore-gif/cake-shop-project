<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ setting('site_description', 'Where Every Bite Feels Like Home') }}">
    <meta name="keywords" content="{{ setting('site_keywords', 'cakes, bakery') }}">

    <title>@yield('title', setting('site_name'))</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    <style>
        :root {
            --primary-color: #ff6b8b;
            --secondary-color: #ff8da1;
            --dark-color: #2d3349;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        .cursive-font {
            font-family: 'Dancing Script', cursive;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 15px 0;
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color) !important;
            font-family: 'Dancing Script', cursive;
        }

        .navbar-brand i {
            color: var(--primary-color);
            margin-right: 8px;
        }

        .nav-link {
            font-weight: 500;
            color: #4a5568 !important;
            margin: 0 5px;
            padding: 8px 15px !important;
            border-radius: 30px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(255,107,139,0.1);
        }

        .nav-link.active {
            color: white !important;
            background: var(--primary-color);
        }

        /* Cart Badge */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #fff5f7 0%, #ffe4e8 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-title {
            font-size: 52px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 20px;
        }

        .hero-title span {
            color: var(--primary-color);
            font-family: 'Dancing Script', cursive;
            font-size: 64px;
        }

        .hero-text {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 30px;
        }

        .hero-image {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(255,107,139,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,107,139,0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Product Cards */
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            margin-bottom: 30px;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(255,107,139,0.15);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        .product-image {
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .product-overlay .btn {
            transform: translateY(20px);
            transition: all 0.3s;
        }

        .product-card:hover .product-overlay .btn {
            transform: translateY(0);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: var(--primary-color);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-title a {
            color: var(--dark-color);
            text-decoration: none;
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .product-price small {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
            margin-right: 8px;
        }

        /* Category Cards */
        .category-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            text-align: center;
            padding: 30px 20px;
            margin-bottom: 30px;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(255,107,139,0.15);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 32px;
        }

        .category-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .category-count {
            color: #999;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
        }

        .footer h5:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 8px;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 30px;
            margin-top: 40px;
            text-align: center;
            color: #999;
        }

        /* Breadcrumb */
        .breadcrumb-area {
            background: linear-gradient(135deg, #fff5f7 0%, #ffe4e8 100%);
            padding: 40px 0;
            margin-bottom: 50px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 10px 0 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            .hero-title span {
                font-size: 48px;
            }
            .hero-section {
                padding: 50px 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(setting('site_logo'))
                    <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}" style="height: 50px;">
                @else
                    <i class="fas fa-birthday-cake"></i> {{ setting('site_name', 'MyCakeShop') }}
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="position-relative me-3 text-dark">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        @php $cartCount = count(Session::get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- User Menu -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('account.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('account.orders') }}">
                                        <i class="fas fa-shopping-bag me-2"></i>My Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('account.profile') }}">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a>
                                </li>
                                @if(auth()->user()->is_admin)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-primary" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-cog me-2"></i>Admin Panel
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>
                        @if(setting('site_logo'))
                            <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}" style="height: 40px;">
                        @else
                            <i class="fas fa-birthday-cake me-2"></i>{{ setting('site_name', 'MyCakeShop') }}
                        @endif
                    </h5>
                    <p class="text-muted">{{ setting('site_description', 'Delicious cakes for every occasion') }}</p>
                    <div class="social-links">
                        @if(setting('facebook_url'))
                            <a href="{{ setting('facebook_url') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(setting('instagram_url'))
                            <a href="{{ setting('instagram_url') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(setting('twitter_url'))
                            <a href="{{ setting('twitter_url') }}" target="_blank"><i class="fab fa-twitter"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop') }}">Shop</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i>{{ setting('contact_address', '123 Bakery Street') }}</li>
                        <li><i class="fas fa-phone me-2"></i>{{ setting('contact_phone', '+1 234 567 8900') }}</li>
                        <li><i class="fas fa-envelope me-2"></i>{{ setting('contact_email', 'info@mycakeshop.com') }}</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 mb-4">
                    <h5>Opening Hours</h5>
                    <ul class="footer-links">
                        @php
                            $hours = json_decode(setting('opening_hours', '{}'), true);
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        @endphp
                        @foreach($days as $day)
                            <li>
                                <span class="d-inline-block" style="width: 80px;">{{ $day }}:</span>
                                {{ $hours[$day] ?? '09:00-18:00' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} {{ setting('site_name', 'MyCakeShop') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>

    @stack('scripts')
</body>
</html>
