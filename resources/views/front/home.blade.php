@extends('layouts.front')

@section('title', setting('site_name', 'Cozy Cravings') . ' - Where Every Bite Feels Like Home')
@section('meta_description', 'Discover handcrafted cakes made with love and the finest ingredients')

@section('content')
<!-- ========== HERO SECTION WITH 3D ANIMATION ========== -->
<section class="hero-section position-relative overflow-hidden">
    <div class="hero-particles"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-80 py-5">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                    <i class="fas fa-crown me-2"></i>Premium Bakery Since 2020
                </span>
                <h1 class="hero-title display-3 fw-bold mb-4">
                    Where Every Bite <br>
                    <span class="gradient-text">Feels Like Home</span>
                </h1>
                <p class="hero-text lead text-muted mb-4">
                    Handcrafted with love using the finest ingredients. From birthday celebrations to wedding dreams, we make every moment sweeter.
                </p>

                <!-- Search Bar -->
                <div class="search-wrapper mb-4">
                    <form action="{{ route('shop') }}" method="GET" class="position-relative">
                        <input type="text" name="search" class="form-control form-control-lg rounded-pill ps-5"
                               placeholder="Search for cakes, cupcakes, and more...">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 position-absolute top-50 end-0 translate-middle-y me-2">
                            Search
                        </button>
                    </form>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('shop') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3">
                        <i class="fas fa-calendar-alt me-2"></i>Custom Order
                    </a>
                </div>

                <!-- Stats Counter -->
                <div class="row mt-5 pt-3 g-4">
                    <div class="col-4">
                        <div class="stat-item">
                            <h2 class="counter text-primary fw-bold mb-1" data-target="500">0</h2>
                            <p class="text-muted mb-0">Happy Customers</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <h2 class="counter text-primary fw-bold mb-1" data-target="50">0</h2>
                            <p class="text-muted mb-0">Cake Flavors</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-item">
                            <h2 class="counter text-primary fw-bold mb-1" data-target="100">0</h2>
                            <p class="text-muted mb-0">% Fresh</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                <div class="hero-image-wrapper position-relative">
                    <div class="floating-cakes">
                        <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=1089&q=80"
                             alt="Delicious Cake"
                             class="img-fluid hero-main-image rounded-4 shadow-xl">

                        <!-- Floating Elements -->
                        <div class="floating-badge badge-1">
                            <i class="fas fa-star text-warning me-2"></i>
                            <span>4.9 Rating</span>
                        </div>
                        <div class="floating-badge badge-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <span>Free Delivery</span>
                        </div>
                        <div class="floating-badge badge-3">
                            <i class="fas fa-leaf text-success me-2"></i>
                            <span>100% Fresh</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Divider -->
    <div class="wave-divider">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                  class="shape-fill" fill="#ffffff"></path>
        </svg>
    </div>
</section>

<!-- ========== FEATURED CATEGORIES ========== -->
<section class="py-5 position-relative">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="fas fa-tags me-2"></i>Explore Categories
            </span>
            <h2 class="display-5 fw-bold">Our Delicious <span class="gradient-text">Categories</span></h2>
            <p class="text-muted lead">Find the perfect cake for every occasion</p>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="category-card-modern">
                        <div class="category-icon-wrapper">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="category-image">
                            @else
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
                            @endif
                        </div>
                        <div class="category-content">
                            <h5 class="category-title-modern">{{ $category->name }}</h5>
                            <span class="category-count-modern">{{ $category->products_count ?? $category->products->count() }} items</span>
                        </div>
                        <div class="category-overlay"></div>
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

<!-- ========== FEATURED PRODUCTS ========== -->
<section class="py-5 bg-light position-relative">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="fas fa-crown me-2"></i>Featured Items
            </span>
            <h2 class="display-5 fw-bold">Our <span class="gradient-text">Best Sellers</span></h2>
            <p class="text-muted lead">Most loved cakes by our customers</p>
        </div>

        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-6" data-aos="flip-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="product-card-modern">
                    @if($product->sale_price && $product->sale_price < $product->regular_price)
                        <div class="product-badge-modern bg-danger">
                            -{{ $product->discount_percentage }}%
                        </div>
                    @endif
                    @if($product->is_featured)
                        <div class="product-badge-modern bg-warning">
                            <i class="fas fa-crown"></i>
                        </div>
                    @endif

                    <div class="product-image-modern">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                 alt="{{ $product->name }}"
                                 class="product-img">
                        @else
                            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=1089&q=80"
                                 alt="{{ $product->name }}"
                                 class="product-img">
                        @endif

                        <div class="product-actions">
                            <a href="{{ route('product.details', $product->slug) }}" class="btn-action">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-action">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="product-info-modern">
                        <div class="product-category-modern">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        <h5 class="product-title-modern">
                            <a href="{{ route('product.details', $product->slug) }}">{{ $product->name }}</a>
                        </h5>

                        <div class="product-rating mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= 5)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="ms-2 text-muted small">(24 reviews)</span>
                        </div>

                        <div class="product-price-modern">
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="old-price">{{ format_currency($product->regular_price) }}</span>
                                <span class="new-price">{{ format_currency($product->sale_price) }}</span>
                            @else
                                <span class="new-price">{{ format_currency($product->regular_price) }}</span>
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

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ========== SPECIAL OFFER BANNER ========== -->
<section class="py-5">
    <div class="container">
        <div class="offer-banner rounded-4 p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="row align-items-center">
                <div class="col-lg-8 text-white">
                    <h2 class="display-6 fw-bold mb-3">Special Offer!</h2>
                    <p class="lead mb-4 opacity-75">Get 20% off on your first order. Use code: <span class="bg-white text-dark px-3 py-2 rounded-3 fw-bold">COZY20</span></p>
                    <a href="{{ route('shop') }}" class="btn btn-light btn-lg rounded-pill px-5">
                        Order Now <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80"
                         alt="Special Cake"
                         class="offer-image img-fluid rounded-3 shadow-lg"
                         style="max-height: 200px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== WHY CHOOSE US ========== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="fas fa-heart me-2"></i>Why Choose Us
            </span>
            <h2 class="display-5 fw-bold">What Makes Us <span class="gradient-text">Special</span></h2>
            <p class="text-muted lead">We bake with love and the finest ingredients</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon bg-soft-primary">
                        <i class="fas fa-leaf fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mt-4 mb-3">Fresh Ingredients</h5>
                    <p class="text-muted">We use only the finest, freshest ingredients in all our creations.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon bg-soft-success">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-semibold mt-4 mb-3">Free Delivery</h5>
                    <p class="text-muted">Free delivery on orders over {{ format_currency(setting('free_delivery_threshold', 100)) }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon bg-soft-warning">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h5 class="fw-semibold mt-4 mb-3">Custom Orders</h5>
                    <p class="text-muted">Create your own unique cake design for special occasions.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon bg-soft-danger">
                        <i class="fas fa-heart fa-2x text-danger"></i>
                    </div>
                    <h5 class="fw-semibold mt-4 mb-3">Made with Love</h5>
                    <p class="text-muted">Every cake is baked with passion and attention to detail.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="fas fa-star me-2"></i>Testimonials
            </span>
            <h2 class="display-5 fw-bold">What Our <span class="gradient-text">Customers Say</span></h2>
            <p class="text-muted lead">Real reviews from happy cake lovers</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text">"The birthday cake I ordered was absolutely stunning! Everyone at the party couldn't stop raving about it. Will definitely order again!"</p>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&size=60&background=ff6b8b&color=fff" alt="Sarah" class="rounded-circle me-3">
                        <div>
                            <h6 class="fw-semibold mb-1">Sarah Johnson</h6>
                            <small class="text-muted">Happy Customer</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text">"Our wedding cake was perfect! The design was exactly what we wanted and it tasted amazing. Thank you for making our day so special!"</p>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Michael+Chen&size=60&background=ff6b8b&color=fff" alt="Michael" class="rounded-circle me-3">
                        <div>
                            <h6 class="fw-semibold mb-1">Michael Chen</h6>
                            <small class="text-muted">Groom</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text">"I order cupcakes for every office event. They're always fresh, beautiful, and delivered on time. Highly recommend this bakery!"</p>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Emily+Rodriguez&size=60&background=ff6b8b&color=fff" alt="Emily" class="rounded-circle me-3">
                        <div>
                            <h6 class="fw-semibold mb-1">Emily Rodriguez</h6>
                            <small class="text-muted">Regular Customer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== INSTAGRAM GALLERY ========== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="fab fa-instagram me-2"></i>Instagram
            </span>
            <h2 class="display-5 fw-bold">Follow Us on <span class="gradient-text">Instagram</span></h2>
            <p class="text-muted lead">@cozycravings</p>
        </div>

        <div class="row g-3">
            @for($i = 1; $i <= 6; $i++)
            <div class="col-lg-2 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="{{ $i * 50 }}">
                <div class="instagram-card">
                    <img src="https://images.unsplash.com/photo-{{ [
                        '1578985545062-69928b1d9587',
                        '1588195538326-c5b1e9f80a1b',
                        '1464346529801-4683dcb71a69',
                        '1488474697537-7f88b5f3c49f',
                        '1563729781276-6b5b5b5b5b5b',
                        '1571115765973-4d5d4c5d5d5d'
                    ][$i-1] }}?auto=format&fit=crop&w=600&q=80"
                         alt="Instagram {{ $i }}"
                         class="img-fluid">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram fa-2x text-white"></i>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
@endsection

@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    /* Modern Styles */
    :root {
        --primary-gradient: linear-gradient(135deg, #ff6b8b, #ff8da1);
        --shadow-sm: 0 5px 20px rgba(0,0,0,0.05);
        --shadow-md: 0 10px 30px rgba(255,107,139,0.15);
        --shadow-lg: 0 20px 40px rgba(255,107,139,0.2);
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #fff5f7 0%, #ffe4e8 100%);
        min-height: 80vh;
        position: relative;
        padding: 80px 0;
    }

    .hero-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 80%, rgba(255,107,139,0.1) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(255,141,161,0.1) 0%, transparent 50%);
    }

    .gradient-text {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .bg-primary-soft {
        background: rgba(255,107,139,0.1);
        color: #ff6b8b;
    }

    /* Search Bar */
    .search-wrapper .form-control {
        border: 2px solid transparent;
        box-shadow: var(--shadow-sm);
        padding: 15px 60px 15px 45px;
        border-radius: 50px;
        height: 60px;
    }

    .search-wrapper .form-control:focus {
        border-color: #ff6b8b;
        box-shadow: var(--shadow-md);
        outline: none;
    }

    .search-wrapper .btn {
        height: 50px;
    }

    /* Stats Counter */
    .counter {
        font-size: 2.5rem;
        margin-bottom: 0;
    }

    .stat-item {
        text-align: center;
    }

    /* Floating Elements */
    .hero-image-wrapper {
        position: relative;
        padding: 20px;
    }

    .hero-main-image {
        border-radius: 30px;
        box-shadow: var(--shadow-lg);
        transform: perspective(1000px) rotateY(-5deg);
        transition: all 0.5s;
        width: 100%;
    }

    .hero-main-image:hover {
        transform: perspective(1000px) rotateY(0deg);
    }

    .floating-badge {
        position: absolute;
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        animation: float 3s ease-in-out infinite;
    }

    .badge-1 {
        top: 10%;
        left: -10%;
        animation-delay: 0s;
    }

    .badge-2 {
        bottom: 20%;
        right: -10%;
        animation-delay: 1s;
    }

    .badge-3 {
        top: 40%;
        right: -5%;
        animation-delay: 2s;
    }

    /* Category Cards Modern */
    .category-card-modern {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s;
        position: relative;
        height: 200px;
        cursor: pointer;
    }

    .category-card-modern:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-md);
    }

    .category-icon-wrapper {
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }

    .category-card-modern:hover .category-image {
        transform: scale(1.1);
    }

    .category-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
        z-index: 2;
    }

    .category-title-modern {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: white;
    }

    .category-count-modern {
        font-size: 0.85rem;
        opacity: 0.8;
        color: white;
    }

    .category-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff6b8b, #ff8da1);
        color: white;
        font-size: 48px;
    }

    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--primary-gradient);
        opacity: 0;
        transition: all 0.3s;
        z-index: 1;
    }

    .category-card-modern:hover .category-overlay {
        opacity: 0.3;
    }

    /* Product Cards Modern */
    .product-card-modern {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s;
        position: relative;
    }

    .product-card-modern:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-md);
    }

    .product-badge-modern {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ff6b8b;
        color: white;
        padding: 5px 12px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 3;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .product-badge-modern.bg-warning {
        background: #f59e0b;
    }

    .product-image-modern {
        height: 200px;
        position: relative;
        overflow: hidden;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }

    .product-card-modern:hover .product-img {
        transform: scale(1.1);
    }

    .product-actions {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s;
        z-index: 2;
    }

    .product-card-modern:hover .product-actions {
        bottom: 20px;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: none;
        color: #ff6b8b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-decoration: none;
    }

    .btn-action:hover {
        background: var(--primary-gradient);
        color: white;
        transform: scale(1.1);
    }

    .product-info-modern {
        padding: 20px;
    }

    .product-category-modern {
        color: #ff6b8b;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .product-title-modern {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .product-title-modern a {
        color: #2d3349;
        text-decoration: none;
    }

    .product-rating {
        font-size: 0.85rem;
    }

    .product-price-modern {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .old-price {
        color: #999;
        text-decoration: line-through;
        font-size: 0.9rem;
    }

    .new-price {
        color: #ff6b8b;
        font-size: 1.2rem;
        font-weight: 700;
    }

    /* Offer Banner */
    .offer-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 30px;
        overflow: hidden;
        position: relative;
    }

    .offer-image {
        transition: all 0.5s;
    }

    .offer-banner:hover .offer-image {
        transform: scale(1.05);
    }

    /* Feature Cards */
    .feature-card {
        text-align: center;
        padding: 30px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-md);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .bg-soft-primary { background: rgba(255,107,139,0.1); }
    .bg-soft-success { background: rgba(34,197,94,0.1); }
    .bg-soft-warning { background: rgba(245,158,11,0.1); }
    .bg-soft-danger { background: rgba(239,68,68,0.1); }

    /* Testimonial Cards */
    .testimonial-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s;
        height: 100%;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .testimonial-text {
        font-style: italic;
        color: #4a5568;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
    }

    .testimonial-author img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Instagram Cards */
    .instagram-card {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
    }

    .instagram-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }

    .instagram-card:hover img {
        transform: scale(1.1);
    }

    .instagram-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,107,139,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s;
    }

    .instagram-card:hover .instagram-overlay {
        opacity: 1;
    }

    /* Wave Divider */
    .wave-divider {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
    }

    .wave-divider svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px);
        height: 80px;
    }

    .wave-divider .shape-fill {
        fill: #FFFFFF;
    }

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Min Height */
    .min-vh-80 {
        min-height: 80vh;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .floating-badge {
            display: none;
        }

        .counter {
            font-size: 1.8rem;
        }

        .min-vh-80 {
            min-height: auto;
        }
    }

    /* AOS Fix */
    [data-aos] {
        pointer-events: none;
    }

    [data-aos].aos-animate {
        pointer-events: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Counter Animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target'));
                const count = parseInt(counter.innerText);
                const increment = Math.trunc(target / speed);

                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    });
</script>
@endpush

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Counter Animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target'));
                const count = parseInt(counter.innerText);
                const increment = Math.trunc(target / speed);

                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    });
</script>
@endpush
