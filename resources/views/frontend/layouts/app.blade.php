<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cake Shop</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@include('frontend.layouts.navbar')

<div class="container mt-4">
    @yield('content')
</div>

@include('frontend.layouts.footer')

</body>
</html>
