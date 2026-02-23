@extends('layouts.front')

@section('title', setting('site_name', 'Cozy Cravings') . ' - Modern Artisanal Bakery')
@section('meta_description', 'Discover our collection of handcrafted cakes made with premium ingredients')

@section('content')
<!-- Modern Hero Section -->
<section class="hero-modern position-relative overflow-hidden">
    <div class="container">
        <div class="row min-vh-90 align-items-center">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1200">
                <span class="hero-subtitle">Artisanal Bakery</span>
                <h1 class="hero-title display-1 fw-bold mb-4">
                    Crafting<br>
                    <span class="hero-highlight">Extraordinary</span><br>
                    Cakes
                </h1>
                <p class="hero-description lead mb-5">
                    Where tradition meets modernity. Each creation is thoughtfully
                    crafted using the finest ingredients and time-honored techniques.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('shop') }}" class="btn-modern btn-primary-modern">
                        Explore Collection
                    </a>
                    <a href="{{ route('about') }}" class="btn-modern btn-outline-modern">
                        Our Philosophy
                    </a>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1200">
                <div class="hero-image-wrapper">
                    <div class="hero-image-container">
                        <img src="/images/homePageImage.jpg"
                             alt="Artisanal cake"
                             class="img-fluid hero-main-image">
                        <div class="hero-image-overlay"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimal Decoration -->
    <div class="hero-decoration"></div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Curated Selection</span>
            <h2 class="section-title">Our Collections</h2>
            <p class="section-description">Discover our thoughtfully curated collections, each telling its own story</p>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="card-modern">
                        <div class="product-image-container" style="height: 200px;">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-cake" style="color: var(--sand); font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 text-center">
                            <h5 class="mb-2" style="font-family: 'Prata', serif;">{{ $category->name }}</h5>
                            <span class="small text-muted">{{ $category->products_count ?? $category->products->count() }} items</span>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">No categories available</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5" style="background: var(--ivory);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Signature Selection</span>
            <h2 class="section-title">Featured Creations</h2>
            <p class="section-description">Our most cherished recipes, perfected over generations</p>
        </div>

        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="card-modern">
                    <div class="product-image-container">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587" alt="{{ $product->name }}">
                        @endif

                        @if($product->sale_price && $product->sale_price < $product->regular_price)
                            <span class="product-badge">Sale</span>
                        @endif

                        @if($product->is_featured)
                            <span class="product-badge featured">Featured</span>
                        @endif
                    </div>

                    <div class="p-4">
                        <p class="small text-muted mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        <h5 class="mb-3" style="font-family: 'Prata', serif;">
                            <a href="{{ route('product.details', $product->slug) }}" class="text-decoration-none" style="color: var(--charcoal);">
                                {{ $product->name }}
                            </a>
                        </h5>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($product->sale_price && $product->sale_price < $product->regular_price)
                                    <span class="text-muted text-decoration-line-through small me-2">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->regular_price, 2) }}
                                    </span>
                                    <span class="fw-bold" style="color: var(--terracotta);">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->sale_price, 2) }}
                                    </span>
                                @else
                                    <span class="fw-bold" style="color: var(--terracotta);">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->regular_price, 2) }}
                                    </span>
                                @endif
                            </div>

                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-modern btn-primary-modern" style="padding: 8px 16px;">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">No featured products available</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Philosophy Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80"
                     alt="Our bakery"
                     class="img-fluid rounded-0 shadow-sm">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-subtitle">Our Philosophy</span>
                <h2 class="section-title" style="font-size: 2.2rem;">Where Craft Meets Passion</h2>
                <p class="text-muted mb-4" style="color: var(--taupe);">
                    Founded on the belief that exceptional cakes come from exceptional care,
                    we've dedicated ourselves to perfecting our craft. Every creation begins
                    with the finest ingredients and is brought to life through time-honored
                    techniques passed down through generations.
                </p>
                <div class="row g-4 mt-4">
                    <div class="col-6">
                        <h3 class="fw-bold" style="color: var(--terracotta); font-size: 2.5rem;">70+</h3>
                        <p class="text-muted small">Years of tradition</p>
                    </div>
                    <div class="col-6">
                        <h3 class="fw-bold" style="color: var(--terracotta); font-size: 2.5rem;">50+</h3>
                        <p class="text-muted small">Signature recipes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Modern Hero */
    .hero-modern {
        min-height: 90vh;
        background: linear-gradient(135deg, var(--cream) 0%, var(--ivory) 100%);
        position: relative;
        padding: 40px 0;
    }

    .hero-subtitle {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 4px;
        color: var(--taupe);
        display: block;
        margin-bottom: 20px;
    }

    .hero-title {
        font-family: 'Prata', serif;
        color: var(--charcoal);
        line-height: 1.1;
    }

    .hero-highlight {
        color: var(--terracotta);
        position: relative;
        display: inline-block;
    }

    .hero-highlight:after {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 100%;
        height: 8px;
        background: rgba(201, 124, 93, 0.1);
        z-index: -1;
    }

    .hero-description {
        color: var(--taupe);
        font-size: 1.1rem;
        max-width: 500px;
    }

    .hero-image-wrapper {
        position: relative;
        padding: 20px;
    }

    .hero-image-container {
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .hero-main-image {
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .hero-image-container:hover .hero-main-image {
        transform: scale(1.02);
    }

    .hero-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(201, 124, 93, 0.1), transparent);
        pointer-events: none;
    }

    .hero-decoration {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--taupe), transparent);
    }

    .min-vh-90 {
        min-height: 90vh;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 3rem;
        }

        .min-vh-90 {
            min-height: auto;
        }

        .hero-modern {
            padding: 60px 0;
        }
    }
</style>
@endpush
