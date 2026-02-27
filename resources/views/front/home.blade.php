@extends('layouts.front')

@section('title', setting('site_name', 'Cozy Cravings') . ' - Modern Artisanal Bakery')
@section('meta_description', 'Discover our collection of handcrafted cakes made with premium ingredients')

@section('content')
<!-- Modern Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center min-vh-90">
            <div class="col-lg-6" data-aos="fade-right">
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

            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1562777717-dc6984f65c63?ixlib=rb-4.0.3&auto=format&fit=crop&w=1287&q=80"
                         alt="Artisanal cake"
                         class="img-fluid hero-main-image">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-subtitle">Curated Selection</span>
            <h2 class="section-title">Our Collections</h2>
            <p class="section-description">Discover our thoughtfully curated collections, each telling its own story</p>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="category-card">
                        <div class="category-image-container">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="category-image">
                            @else
                                <div class="category-image-placeholder">
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
                            @endif
                        </div>
                        <div class="category-info">
                            <h5 class="category-name">{{ $category->name }}</h5>
                            <span class="category-count">{{ $category->products_count ?? $category->products->count() }} items</span>
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
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-subtitle">Signature Selection</span>
            <h2 class="section-title">Featured Creations</h2>
            <p class="section-description">Our most cherished recipes, perfected over generations</p>
        </div>

        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="product-card">
                    <div class="product-image-container">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                 alt="{{ $product->name }}"
                                 class="product-image">
                        @else
                            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587"
                                 alt="{{ $product->name }}"
                                 class="product-image">
                        @endif

                        @if($product->sale_price && $product->sale_price < $product->regular_price)
                            <span class="product-badge sale-badge">SALE</span>
                        @endif

                        @if($product->is_eggless)
                            <span class="product-badge eggless-badge">
                                <i class="fas fa-leaf"></i> Eggless
                            </span>
                        @endif
                    </div>

                    <div class="product-info">
                        <p class="product-category">{{ $product->category->name ?? 'Cake' }}</p>
                        <h5 class="product-title">
                            <a href="{{ route('product.details', $product->slug) }}">
                                {{ $product->name }}
                            </a>
                        </h5>

                        <div class="product-price">
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="original-price">{{ format_currency($product->regular_price) }}</span>
                                <span class="sale-price">{{ format_currency($product->sale_price) }}</span>
                            @else
                                <span class="regular-price">{{ format_currency($product->regular_price) }}</span>
                            @endif
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
@endsection

@push('styles')
<style>
/* ===== HERO SECTION ===== */
.hero-section {
    background: linear-gradient(135deg, #fdf8f2 0%, #f7e6e0 100%);
    min-height: 90vh;
    padding: 80px 0;
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

.hero-description {
    color: var(--taupe);
    font-size: 1.1rem;
    max-width: 500px;
}

.hero-main-image {
    border-radius: 30px;
    box-shadow: var(--shadow-lg);
    width: 100%;
    height: auto;
}

/* ===== CATEGORY CARDS ===== */
.category-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
    height: 100%;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-md);
}

.category-image-container {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.category-card:hover .category-image {
    transform: scale(1.05);
}

.category-image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--cream), var(--sand));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--terracotta);
}

.category-info {
    padding: 20px;
    text-align: center;
}

.category-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
}

.category-count {
    color: var(--taupe);
    font-size: 0.9rem;
}

/* ===== PRODUCT CARDS ===== */
.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-md);
}

.product-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 600;
    z-index: 2;
}

.sale-badge {
    background: #ff4757;
    color: white;
}

.eggless-badge {
    background: #e8f5e9;
    color: #2e7d32;
    left: auto;
    right: 10px;
}

.product-info {
    padding: 20px;
}

.product-category {
    color: var(--taupe);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.product-title a {
    color: var(--charcoal);
    text-decoration: none;
    transition: color 0.3s;
}

.product-title a:hover {
    color: var(--terracotta);
}

.product-price {
    margin-bottom: 15px;
}

.original-price {
    color: var(--taupe);
    text-decoration: line-through;
    font-size: 0.9rem;
    margin-right: 8px;
}

.sale-price, .regular-price {
    color: var(--terracotta);
    font-weight: 700;
    font-size: 1.2rem;
}

/* ===== SECTION HEADERS ===== */
.section-header {
    margin-bottom: 50px;
}

.section-subtitle {
    color: var(--taupe);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    display: block;
    margin-bottom: 15px;
}

.section-title {
    font-family: 'Prata', serif;
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.section-description {
    color: var(--taupe);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .category-image-container,
    .product-image-container {
        height: 180px;
    }
}
</style>
@endpush
