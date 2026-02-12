<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - MyCakeShop')</title>

    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="bg-dark text-white" style="width: 250px; min-height: 100vh;">
            <div class="p-3">
                <h4 class="cursive-font mb-4">
                    <i class="fas fa-birthday-cake me-2"></i>Admin Panel
                </h4>

                <hr class="bg-light">

                <ul class="nav nav-pills flex-column">
                    <!-- Dashboard Link -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : '' }}">
                            <i class="fas fa-dashboard me-2"></i> Dashboard
                        </a>
                    </li>

                    <!-- PRODUCTS LINK - ADD THIS HERE -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active bg-primary' : '' }}">
                            <i class="fas fa-box me-2"></i> Products
                        </a>
                    </li>

                    <!-- Categories Link -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'active bg-primary' : '' }}">
                            <i class="fas fa-tags me-2"></i> Categories
                        </a>
                    </li>

                    <!-- Orders Link (add later) -->
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-white">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>

                    <!-- Rest of your sidebar items... -->
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" style="flex: 1; background-color: #f8f9fa;">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-outline-secondary d-lg-none" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <span class="navbar-brand ms-2">
                        @yield('page-title', 'Dashboard')
                    </span>

                    <div class="dropdown ms-auto">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=ff6b8b&color=fff"
                                 class="rounded-circle" width="35" height="35">
                            <span class="ms-2">{{ auth()->user()->name ?? 'Admin' }}</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menu-toggle')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>

    @yield('scripts')
</body>
</html>
