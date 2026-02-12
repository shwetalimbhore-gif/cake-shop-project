{{-- resources/views/front/home.blade.php --}}
@extends('layouts.app')

@section('title', 'MyCakeShop - Delicious Cakes Online')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #ffeaf0, #f3e5f5);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold cursive-font mb-3">
                    <span class="text-primary">Sweet</span> Delights,
                    <span class="text-secondary">Delivered</span> to Your Door
                </h1>
                <p class="lead mb-4">
                    Order custom cakes, cupcakes, and desserts from the best bakers in town.
                    Freshly baked with love and delivered on time for your special occasions.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn-custom btn-lg">
                        <i class="fas fa-shopping-basket me-2"></i>Order Now
                    </a>
                    <a href="{{ route('products.category', 'birthday-cakes') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-birthday-cake me-2"></i>Birthday Cakes
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="row mt-5 pt-3">
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold text-primary">500+</h3>
                            <p class="text-muted mb-0">Happy Customers</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold text-secondary">100+</h3>
                            <p class="text-muted mb-0">Cake Varieties</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold text-success">24/7</h3>
                            <p class="text-muted mb-0">Delivery</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1089&q=80"
                     class="img-fluid rounded shadow-lg" alt="Beautiful Cake">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="cursive-font fs-1 mb-3">Explore Categories</h2>
                <p class="text-muted">Find the perfect cake for every occasion</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('products.category', $category->slug) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="rounded-circle bg-light p-3 d-inline-block">
                                    <i class="fas fa-birthday-cake fa-2x text-primary"></i>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold">{{ $category->name }}</h5>
                            <p class="card-text text-muted small">{{ $category->description ?? 'Delicious cakes' }}</p>
                            <span class="badge bg-primary">{{ $category->products_count }} Items</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="cursive-font fs-1 mb-3">Featured Cakes</h2>
                <p class="text-muted">Our most popular and delicious creations</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card product-card border-0 shadow-sm h-100">
                    <!-- Product Image -->
                    <div class="position-relative overflow-hidden" style="height: 200px;">
                        <img src="{{ $product->primaryImage->image_path ?? 'https://via.placeholder.com/300x200?text=No+Image' }}"
                             class="card-img-top h-100 w-100 object-fit-cover"
                             alt="{{ $product->name }}">
                        @if($product->is_featured)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-danger">Featured</span>
                        </div>
                        @endif
                        <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-10 d-flex align-items-center justify-content-center opacity-0 transition">
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-truncate">{{ $product->name }}</h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-tag me-1"></i>{{ $product->category->name }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary fw-bold">${{ number_format($product->base_price, 2) }}</span>
                            <button class="btn btn-sm btn-outline-primary add-to-cart" data-product-id="{{ $product->id }}">
                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('products.index') }}" class="btn-custom">
                <i class="fas fa-store me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 cake-gradient text-white">
    <div class="container text-center">
        <h2 class="cursive-font fs-1 mb-3">Ready to Sweeten Your Day?</h2>
        <p class="lead mb-4">Customize your cake, choose delivery time, and enjoy fresh baked goodness!</p>
        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg px-5">
            <i class="fas fa-birthday-cake me-2"></i>Order Your Cake Now
        </a>
    </div>
</section>

<style>
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .transition {
        transition: opacity 0.3s;
    }

    .hover-shadow:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .cake-gradient {
        background: linear-gradient(135deg, #ff6b8b, #9d4edd);
    }
</style>

<script>
    // Add to cart functionality (we'll implement this fully later)
    $(document).ready(function() {
        $('.add-to-cart').click(function(e) {
            e.preventDefault();
            let productId = $(this).data('product-id');

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: 1
                },
                success: function(response) {
                    // Update cart count
                    updateCartCount();

                    // Show success message
                    alert('Product added to cart!');
                },
                error: function() {
                    alert('Error adding product to cart');
                }
            });
        });
    });
</script>
@endsection
