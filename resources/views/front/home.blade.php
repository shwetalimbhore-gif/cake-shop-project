@extends('layouts.front')

@section('title', setting('site_name', 'Cozy Cravings') . ' - Where Every Bite Feels Like Home')
@section('meta_description', setting('site_description', 'Where Every Bite Feels Like Home'))

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    Delicious <span>Cakes</span><br>
                    For Every Occasion
                </h1>
                <p class="hero-text">
                    Handcrafted with love and the finest ingredients. From birthday celebrations to wedding dreams, we make every moment sweeter.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Custom Order
                    </a>
                </div>

                <!-- Stats -->
                <div class="row mt-5">
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">500+</h3>
                        <small class="text-muted">Happy Customers</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">50+</h3>
                        <small class="text-muted">Cake Flavors</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">100%</h3>
                        <small class="text-muted">Fresh Ingredients</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <img src="{{ asset('images/hero-cake.png') }}" alt="Delicious Cake" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Our Categories</h2>
            <p class="text-muted">Explore our delicious range of cakes</p>
        </div>

        <div class="row">
            @forelse($categories as $category)
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="category-card">
                        <div class="category-icon">
                            @if($category->name == 'Birthday Cakes')
                                <i class="fas fa-birthday-cake"></i>
                            @elseif($category->name == 'Wedding Cakes')
                                <i class="fas fa-heart"></i>
                            @elseif($category->name == 'Cupcakes')
                                <i class="fas fa-candy-cane"></i>
                            @else
                                <i class="fas fa-cake"></i>
                            @endif
                        </div>
                        <h5 class="category-title">{{ $category->name }}</h5>
                        <span class="category-count">{{ $category->products_count ?? $category->products->count() }} items</span>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No categories available</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Featured Products</h2>
            <p class="text-muted">Our most popular and best-selling cakes</p>
        </div>

        <div class="row">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-6">
                <div class="product-card">
                    @if($product->sale_price && $product->sale_price < $product->regular_price)
                        <div class="product-badge">Sale</div>
                    @endif

                    <div class="product-image">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}">
                        @else
                            <img src="https://via.placeholder.com/300x300" alt="{{ $product->name }}">
                        @endif

                        <div class="product-overlay">
                            <a href="{{ route('product.details', $product->slug) }}" class="btn btn-light">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        <h5 class="product-title">
                            <a href="{{ route('product.details', $product->slug) }}">{{ $product->name }}</a>
                        </h5>
                        <div class="product-price">
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <small>{{ format_currency($product->regular_price) }}</small>
                                {{ format_currency($product->sale_price) }}
                            @else
                                {{ format_currency($product->regular_price) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No featured products available</p>
            </div>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Why Choose Us</h2>
            <p class="text-muted">What makes our cakes special</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-leaf fa-2x text-primary"></i>
                    </div>
                    <h5>Fresh Ingredients</h5>
                    <p class="text-muted">We use only the finest, freshest ingredients in all our cakes.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-soft-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                    <h5>Free Delivery</h5>
                    <p class="text-muted">Free delivery on orders over {{ format_currency(setting('free_delivery_threshold', 100)) }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-soft-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h5>Custom Orders</h5>
                    <p class="text-muted">Create your own unique cake design for special occasions.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-soft-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-heart fa-2x text-info"></i>
                    </div>
                    <h5>Made with Love</h5>
                    <p class="text-muted">Every cake is baked with passion and attention to detail.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Latest Products</h2>
            <p class="text-muted">Freshly baked and ready to order</p>
        </div>

        <div class="row">
            @forelse($latestProducts as $product)
            <div class="col-lg-3 col-md-4 col-6">
                <div class="product-card">
                    <div class="product-image">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}">
                        @else
                            <img src="https://via.placeholder.com/300x300" alt="{{ $product->name }}">
                        @endif

                        <div class="product-overlay">
                            <a href="{{ route('product.details', $product->slug) }}" class="btn btn-light">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        <h5 class="product-title">
                            <a href="{{ route('product.details', $product->slug) }}">{{ $product->name }}</a>
                        </h5>
                        <div class="product-price">{{ format_currency($product->regular_price) }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No products available</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .bg-soft-primary { background: rgba(255,107,139,0.1); }
    .bg-soft-success { background: rgba(34,197,94,0.1); }
    .bg-soft-warning { background: rgba(245,158,11,0.1); }
    .bg-soft-info { background: rgba(14,165,233,0.1); }
</style>
@endpush
