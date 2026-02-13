<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - ' . setting('site_name', 'MyCakeShop'))</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7fc;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(195deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.6rem;
            color: #ff6b8b;
            letter-spacing: 1px;
        }

        .sidebar-header h4 i {
            margin-right: 10px;
            color: #ff6b8b;
        }

        .sidebar-header small {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .user-profile {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .avatar-wrapper {
            width: 90px;
            height: 90px;
            margin: 0 auto 15px;
            position: relative;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid #ff6b8b;
            padding: 3px;
            background: white;
            object-fit: cover;
        }

        .default-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 600;
            color: white;
            margin: 0 auto;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .online-indicator {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 14px;
            height: 14px;
            background: #22c55e;
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
        }

        .user-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            margin-bottom: 5px;
        }

        .user-email {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 10px;
            word-break: break-all;
        }

        .role-badge {
            background: rgba(255,107,139,0.15);
            color: #ff6b8b;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
            border: 1px solid rgba(255,107,139,0.3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-section {
            flex: 1;
            overflow-y: auto;
            padding: 20px 15px;
        }

        .nav-section::-webkit-scrollbar {
            width: 4px;
        }

        .nav-section::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .nav-section::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-link i {
            width: 22px;
            margin-right: 12px;
            font-size: 1.1rem;
            color: rgba(255,255,255,0.6);
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            transform: translateX(5px);
        }

        .nav-link:hover i {
            color: #ff6b8b;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            color: white;
            box-shadow: 0 8px 15px rgba(255,107,139,0.3);
        }

        .nav-link.active i {
            color: white;
        }

        .nav-link .badge {
            margin-left: auto;
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .nav-divider {
            margin: 20px 0;
            border-color: rgba(255,255,255,0.08);
        }

        .logout-section {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .logout-btn {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            padding: 12px;
            border-radius: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(239,68,68,0.3);
        }

        .logout-btn i {
            margin-right: 10px;
            font-size: 1rem;
        }

        .version-text {
            text-align: center;
            margin-top: 12px;
            color: rgba(255,255,255,0.3);
            font-size: 0.65rem;
            letter-spacing: 1px;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #f4f7fc;
            transition: all 0.3s ease;
        }

        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            position: relative;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            border-radius: 3px;
        }

        .profile-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 50px;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .profile-dropdown:hover {
            background: #f1f5f9;
        }

        .profile-dropdown img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ff6b8b;
            padding: 2px;
        }

        .default-avatar-small {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .profile-name {
            font-weight: 600;
            color: #0f172a;
            margin-right: 5px;
        }

        .content-area {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.03);
            transition: all 0.3s;
            background: white;
            margin-bottom: 25px;
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 25px;
            border-radius: 20px 20px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #0f172a;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 25px;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: none;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            border: none;
            box-shadow: 0 4px 10px rgba(255,107,139,0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ff5a7e, #ff7a92);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(255,107,139,0.3);
        }

        .btn-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
        }

        .btn-info:hover {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #0284c7;
        }

        .btn-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .badge.bg-success { background: #dcfce7 !important; color: #166534; }
        .badge.bg-warning { background: #fef9c3 !important; color: #854d0e; }
        .badge.bg-danger { background: #fee2e2 !important; color: #991b1b; }
        .badge.bg-info { background: #e0f2fe !important; color: #075985; }
        .badge.bg-secondary { background: #f1f5f9 !important; color: #475569; }

        .form-control, .form-select {
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff6b8b;
            box-shadow: 0 0 0 4px rgba(255,107,139,0.1);
        }

        .form-switch .form-check-input {
            width: 45px;
            height: 24px;
        }

        .form-switch .form-check-input:checked {
            background-color: #ff6b8b;
            border-color: #ff6b8b;
        }

        .alert {
            border: none;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .pagination {
            gap: 5px;
        }

        .page-link {
            border: none;
            border-radius: 10px !important;
            padding: 8px 14px;
            color: #64748b;
            font-weight: 500;
            transition: all 0.3s;
        }

        .page-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #ff6b8b, #ff8da1);
            color: white;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .page-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            .content-area {
                padding: 20px;
            }

            .profile-name {
                display: none;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.5s ease;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-soft-primary { background: linear-gradient(135deg, rgba(255,107,139,0.1) 0%, rgba(255,107,139,0.15) 100%); }
        .bg-soft-success { background: linear-gradient(135deg, rgba(34,197,94,0.1) 0%, rgba(34,197,94,0.15) 100%); }
        .bg-soft-warning { background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, rgba(245,158,11,0.15) 100%); }
        .bg-soft-info { background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, rgba(14,165,233,0.15) 100%); }
        .bg-soft-danger { background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(239,68,68,0.15) 100%); }

        .modal-content {
            border: none;
            border-radius: 20px;
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 25px;
            border-radius: 20px 20px 0 0;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 20px 25px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>
                @if(setting('site_logo'))
                    <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}" style="height: 40px;">
                @else
                    <i class="fas fa-birthday-cake"></i>{{ setting('site_name', 'MyCakeShop') }}
                @endif
            </h4>
            <small>{{ setting('site_description', 'ADMIN PANEL') }}</small>
        </div>

        <div class="user-profile">
            <div class="avatar-wrapper">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}"
                         class="avatar-img">
                @else
                    <div class="default-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <span class="online-indicator"></span>
            </div>
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-email">{{ auth()->user()->email }}</div>
            <div class="role-badge">
                <i class="fas fa-shield-alt me-1"></i> ADMIN
            </div>
        </div>

        <div class="nav-section">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}"
                       class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                        @php $productsCount = \App\Models\Product::count(); @endphp
                        <span class="badge">{{ $productsCount }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}"
                       class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                        @php $categoriesCount = \App\Models\Category::count(); @endphp
                        <span class="badge">{{ $categoriesCount }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}"
                       class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                        @php $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                        <span class="badge">{{ $pendingOrdersCount }}</span>
                    </a>
                </li>

                <hr class="nav-divider">

                <li class="nav-item">
                    <a href="{{ route('admin.profile.index') }}"
                       class="nav-link {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.profile.settings') }}"
                       class="nav-link {{ request()->routeIs('admin.profile.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i>
                        <span>Site Settings</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="logout-section">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
            <div class="version-text">
                <i class="fas fa-circle me-1" style="font-size: 4px;"></i> Version 1.0.0
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title-wrapper">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="dropdown">
                <div class="profile-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="default-avatar-small">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="profile-name">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down" style="color: #94a3b8; font-size: 12px;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.settings') }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
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
        </div>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
        };

        document.getElementById('menuToggle')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar').classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('menuToggle');

            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>
</html>
