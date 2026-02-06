{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SweetCravings')</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">SweetCravings</a>

        <div class="ms-auto">
            <a href="/" class="me-3 text-decoration-none">Home</a>

            <a href="{{ route('categories') }}" class="me-3 text-decoration-none">
                Our Cakes
            </a>

            <a href="#" class="btn btn-primary btn-sm">Cart</a>
        </div>
    </div>
</nav>


<!-- PAGE CONTENT -->
<main class="py-5">
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3">
    © 2026 SweetCravings – Where Cravings Meet Cakes
</footer>

</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SweetCravings')</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    {{-- Navbar --}}
    @include('layouts.frontend-nav')

    <main class="py-4">
        @yield('content')
    </main>

</body>
</html>
