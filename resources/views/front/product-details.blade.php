@extends('layouts.front')

@section('title', $product->name . ' - ' . setting('site_name'))
@section('meta_description', $product->short_description)

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="product-wrapper">
        <div class="row g-5">
            <!-- Product Images Column -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <!-- Main Image -->
                    <div class="main-image-container">
                        @if($product->featured_image && file_exists(public_path('storage/' . $product->featured_image)))
                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                 alt="{{ $product->name }}"
                                 class="main-product-image"
                                 id="mainProductImage">
                        @else
                            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=1089&q=80"
                                 alt="{{ $product->name }}"
                                 class="main-product-image">
                        @endif

                        <!-- Badges -->
                        <div class="product-badges">
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="badge-sale">SALE</span>
                            @endif
                            @if($product->is_featured)
                                <span class="badge-featured">FEATURED</span>
                            @endif
                            @if($product->is_eggless)
                                <span class="badge-eggless">
                                    <i class="fas fa-leaf"></i> EGGLESS
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if($product->images && $product->images->count() > 0)
                    <div class="thumbnail-gallery">
                        <div class="row g-2">
                            @foreach($product->images as $image)
                            <div class="col-3">
                                <div class="thumbnail-item" onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="Gallery"
                                         class="thumbnail-image">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Details Column -->
            <div class="col-lg-6">
                <div class="product-details">
                    <!-- Category & Meta -->
                    <div class="product-meta">
                        <span class="product-category">
                            <i class="fas fa-tag me-2"></i>{{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        <span class="product-sku">SKU: {{ $product->sku }}</span>
                    </div>

                    <!-- Title -->
                    <h1 class="product-title">{{ $product->name }}</h1>

                    <!-- Price Display - REAL BAKERY STYLE -->
                    <div class="price-display">
                        @php
                            $sizePrices = json_decode($product->size_prices, true);
                            $minPrice = !empty($sizePrices) ? min($sizePrices) : ($product->sale_price ?? $product->regular_price);
                        @endphp

                        @if($product->sale_price && $product->sale_price < $product->regular_price)
                            <div class="price-row">
                                <span class="current-price" id="dynamicPrice">{{ format_currency($product->sale_price) }}</span>
                                <span class="original-price">{{ format_currency($product->regular_price) }}</span>
                                <span class="discount-badge">Save {{ $product->discount_percentage }}%</span>
                            </div>
                        @else
                            <div class="price-row">
                                <span class="current-price" id="dynamicPrice">{{ format_currency($minPrice) }}</span>
                            </div>
                        @endif
                        <span class="starting-from-label">Starting from • Prices vary by size</span>
                    </div>

                    <!-- Short Description -->
                    <div class="short-description">
                        <p>{{ $product->short_description }}</p>
                    </div>

                    <hr class="divider">

                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add', $product) }}" method="POST" id="addToCartForm" class="add-to-cart-form">
                        @csrf

                        <!-- Size Selection - REAL BAKERY STYLE with serving information -->
                        @if($product->sizes && !empty(json_decode($product->sizes, true)))
                        <div class="option-group">
                            <div class="option-header">
                                <h4 class="option-title">Choose Size</h4>
                                <span class="option-required">Required</span>
                            </div>
                            <p class="option-subtitle">Select the size that's right for your occasion</p>

                            <div class="size-grid">
                                @php
                                    $sizes = json_decode($product->sizes, true);
                                    $sizePrices = json_decode($product->size_prices, true) ?? [];
                                    $sizeServings = json_decode($product->size_servings, true) ?? [];
                                @endphp
                                @foreach($sizes as $index => $size)
                                <div class="size-card">
                                    <input type="radio" class="size-radio" name="size" id="size_{{ $index }}"
                                           value="{{ $size }}"
                                           data-price="{{ $sizePrices[$index] ?? $minPrice }}"
                                           data-index="{{ $index }}"
                                           {{ $loop->first ? 'checked' : '' }}
                                           required>
                                    <label class="size-label" for="size_{{ $index }}">
                                        <span class="size-name">{{ $size }}</span>
                                        @if(isset($sizeServings[$index]))
                                            <span class="size-servings">
                                                <i class="fas fa-users me-1"></i>Serves {{ $sizeServings[$index] }}
                                            </span>
                                        @endif
                                        <span class="size-price">{{ format_currency($sizePrices[$index] ?? $minPrice) }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Flavor Selection - REAL BAKERY STYLE with price adjustments -->
                        @if($product->flavors && !empty(json_decode($product->flavors, true)))
                        <div class="option-group">
                            <div class="option-header">
                                <h4 class="option-title">Choose Flavor</h4>
                                <span class="option-required">Required</span>
                            </div>
                            <p class="option-subtitle">Select your favorite flavor (premium flavors may have additional cost)</p>

                            <div class="flavor-grid">
                                @php
                                    $flavors = json_decode($product->flavors, true);
                                    $flavorPrices = json_decode($product->flavor_prices, true) ?? [];
                                @endphp
                                @foreach($flavors as $index => $flavor)
                                <div class="flavor-card">
                                    <input type="radio" class="flavor-radio" name="flavor" id="flavor_{{ $index }}"
                                           value="{{ $flavor }}"
                                           data-price="{{ $flavorPrices[$index] ?? 0 }}"
                                           data-index="{{ $index }}"
                                           {{ $loop->first ? 'checked' : '' }}
                                           required>
                                    <label class="flavor-label" for="flavor_{{ $index }}">
                                        <span class="flavor-name">{{ $flavor }}</span>
                                        @if(isset($flavorPrices[$index]) && $flavorPrices[$index] > 0)
                                            <span class="flavor-price">+{{ format_currency($flavorPrices[$index]) }}</span>
                                        @else
                                            <span class="flavor-price included">Included</span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Hidden input for calculated price -->
                        <input type="hidden" name="calculated_price" id="calculatedPrice" value="{{ $minPrice }}">

                        <!-- Quantity and Add to Cart - REAL BAKERY STYLE -->
                        <div class="action-section">
                            <div class="quantity-wrapper">
                                <h4 class="option-title">Quantity</h4>
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn" onclick="decrementQuantity()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity"
                                           class="quantity-input" value="1" min="1"
                                           max="{{ $product->stock_quantity }}" readonly>
                                    <button type="button" class="quantity-btn" onclick="incrementQuantity()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="stock-info">
                                    @if($product->stock_quantity > 0)
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        <span>{{ $product->stock_quantity }} available</span>
                                    @else
                                        <i class="fas fa-times-circle text-danger me-1"></i>
                                        <span>Out of stock</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Total Price Display - REAL BAKERY STYLE -->
                            <div class="total-price-wrapper">
                                <h4 class="option-title">Total</h4>
                                <div class="total-price" id="totalPrice">
                                    {{ format_currency($minPrice) }}
                                </div>
                                <span class="total-note">Including all taxes</span>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="submit" class="btn-add-to-cart" {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart me-2"></i>
                            Add to Cart
                        </button>
                    </form>

                    <!-- Additional Info - REAL BAKERY STYLE -->
                    <div class="additional-info">
                        <div class="info-item">
                            <i class="fas fa-truck"></i>
                            <div>
                                <strong>Free Delivery</strong>
                                <span>On orders over {{ format_currency(setting('free_delivery_threshold', 100)) }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-undo-alt"></i>
                            <div>
                                <strong>Satisfaction Guaranteed</strong>
                                <span>100% money-back guarantee</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>Secure Payment</strong>
                                <span>SSL encrypted checkout</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Description - REAL BAKERY STYLE -->
<div class="product-description mt-5">
    <div class="description-header">
        <h3 class="description-title">Product Details</h3>
        <div class="description-tabs">
            <button class="tab-btn active" onclick="showTab('description')">Description</button>
            <button class="tab-btn" onclick="showTab('ingredients')">Ingredients</button>
            <button class="tab-btn" onclick="showTab('reviews')">
                Reviews
                @php
                    $reviewCount = 0;
                    if (isset($product->reviews) && $product->reviews) {
                        $reviewCount = $product->reviews->count();
                    }
                @endphp
                ({{ $reviewCount }})
            </button>
        </div>
    </div>

    <div class="description-content active" id="description-tab">
        {!! nl2br(e($product->description)) !!}
    </div>

    <div class="description-content" id="ingredients-tab" style="display: none;">
        <h5>Ingredients</h5>
        <p>All our cakes are made with premium ingredients including fresh butter, eggs, flour, and sugar.
           Specific ingredient information can be provided upon request due to dietary restrictions.</p>
        <p class="text-muted mt-3">
            <i class="fas fa-info-circle me-2"></i>
            For detailed allergen information, please contact us before ordering.
        </p>
    </div>

    <div class="description-content" id="reviews-tab" style="display: none;">
        @if(isset($product->reviews) && $product->reviews && $product->reviews->count() > 0)
            @foreach($product->reviews as $review)
                <div class="review-item">
                    <div class="review-header">
                        <strong>{{ $review->customer_name ?? 'Anonymous' }}</strong>
                        <div class="review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($review->rating ?? 0))
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <p>{{ $review->comment ?? 'No comment provided.' }}</p>
                    <small class="text-muted">{{ $review->created_at ? $review->created_at->format('M d, Y') : '' }}</small>
                </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="far fa-star fa-3x text-muted mb-3"></i>
                <h5>No reviews yet</h5>
                <p class="text-muted">Be the first to review this product!</p>
            </div>
        @endif
    </div>
</div>

<!-- Related Products - REAL BAKERY STYLE -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<section class="related-products-section py-5">
    <div class="container">
        <h2 class="section-title">You May Also Like</h2>
        <p class="section-subtitle">Customers who bought this also enjoyed</p>

        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-lg-3 col-md-6">
                <div class="related-product-card">
                    <a href="{{ route('product.details', $related->slug) }}" class="related-product-image">
                        @if($related->featured_image)
                            <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587" alt="{{ $related->name }}">
                        @endif
                        @if($related->sale_price && $related->sale_price < $related->regular_price)
                            <span class="related-badge">Sale</span>
                        @endif
                    </a>
                    <div class="related-product-info">
                        <h5 class="related-product-title">
                            <a href="{{ route('product.details', $related->slug) }}">{{ $related->name }}</a>
                        </h5>
                        <div class="related-product-price">
                            {{ format_currency($related->starting_price ?? $related->regular_price) }}
                        </div>
                        <small class="text-muted">Starting from</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
/* ===== BREADCRUMB ===== */
.breadcrumb-section {
    background: linear-gradient(135deg, #fdf8f2, #f7e6e0);
    padding: 20px 0;
    border-bottom: 1px solid var(--sand);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item a {
    color: var(--terracotta);
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-item.active {
    color: var(--charcoal);
}

/* ===== PRODUCT GALLERY ===== */
.product-gallery {
    background: white;
    border-radius: 24px;
    padding: 20px;
    box-shadow: var(--shadow-sm);
}

.main-image-container {
    position: relative;
    width: 100%;
    height: 450px;
    overflow: hidden;
    border-radius: 16px;
    background: var(--cream);
    margin-bottom: 20px;
}

.main-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.main-product-image:hover {
    transform: scale(1.02);
}

.product-badges {
    position: absolute;
    top: 20px;
    left: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 2;
}

.badge-sale {
    background: #ff4757;
    color: white;
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 1px;
    box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
}

.badge-featured {
    background: var(--gold);
    color: var(--charcoal);
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
}

.badge-eggless {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
}

.thumbnail-gallery {
    margin-top: 15px;
}

.thumbnail-item {
    cursor: pointer;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.thumbnail-item:hover {
    border-color: var(--terracotta);
    transform: scale(1.05);
}

.thumbnail-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
}

/* ===== PRODUCT DETAILS ===== */
.product-details {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: var(--shadow-sm);
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.product-category {
    color: var(--terracotta);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.product-sku {
    color: var(--taupe);
    font-size: 0.8rem;
}

.product-title {
    font-family: 'Prata', serif;
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 20px;
    line-height: 1.2;
}

/* ===== PRICE DISPLAY ===== */
.price-display {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px dashed var(--sand);
}

.price-row {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.current-price {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--terracotta);
    line-height: 1;
}

.original-price {
    font-size: 1.5rem;
    color: var(--taupe);
    text-decoration: line-through;
}

.discount-badge {
    background: #ff4757;
    color: white;
    padding: 5px 15px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 600;
}

.starting-from-label {
    display: block;
    color: var(--taupe);
    font-size: 0.9rem;
    margin-top: 8px;
}

.short-description {
    color: var(--charcoal);
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 25px;
}

.divider {
    margin: 25px 0;
    border-color: var(--sand);
    opacity: 0.5;
}

/* ===== OPTIONS ===== */
.option-group {
    margin-bottom: 30px;
}

.option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.option-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin: 0;
}

.option-required {
    font-size: 0.8rem;
    color: var(--terracotta);
    background: rgba(201, 124, 93, 0.1);
    padding: 3px 10px;
    border-radius: 30px;
}

.option-subtitle {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 20px;
}

/* Size Grid */
.size-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 15px;
}

.size-card {
    position: relative;
}

.size-radio {
    position: absolute;
    opacity: 0;
}

.size-label {
    display: flex;
    flex-direction: column;
    padding: 20px 15px;
    background: white;
    border: 2px solid var(--sand);
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    height: 100%;
}

.size-radio:checked + .size-label {
    border-color: var(--terracotta);
    background: rgba(201, 124, 93, 0.03);
    transform: scale(1.02);
    box-shadow: 0 10px 20px rgba(201, 124, 93, 0.1);
}

.size-name {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 8px;
    color: var(--charcoal);
}

.size-servings {
    font-size: 0.8rem;
    color: var(--taupe);
    margin-bottom: 12px;
}

.size-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--terracotta);
}

/* Flavor Grid */
.flavor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
}

.flavor-card {
    position: relative;
}

.flavor-radio {
    position: absolute;
    opacity: 0;
}

.flavor-label {
    display: flex;
    flex-direction: column;
    padding: 15px;
    background: white;
    border: 2px solid var(--sand);
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.flavor-radio:checked + .flavor-label {
    border-color: var(--terracotta);
    background: rgba(201, 124, 93, 0.03);
    transform: scale(1.02);
}

.flavor-name {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 8px;
    color: var(--charcoal);
}

.flavor-price {
    font-size: 1rem;
    font-weight: 600;
    color: var(--terracotta);
}

.flavor-price.included {
    color: #10b981;
}

/* ===== ACTION SECTION ===== */
.action-section {
    display: flex;
    gap: 30px;
    margin-bottom: 25px;
    padding: 25px;
    background: linear-gradient(135deg, var(--cream), #fff);
    border-radius: 20px;
    border: 1px solid var(--sand);
}

.quantity-wrapper {
    flex: 1;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.quantity-btn {
    width: 45px;
    height: 45px;
    border: none;
    background: white;
    border-radius: 12px;
    color: var(--charcoal);
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    font-size: 1.2rem;
}

.quantity-btn:hover {
    background: var(--terracotta);
    color: white;
    transform: scale(1.05);
}

.quantity-input {
    width: 70px;
    height: 45px;
    text-align: center;
    border: 2px solid var(--sand);
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    background: white;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--taupe);
    font-size: 0.9rem;
}

.stock-info i {
    font-size: 1rem;
}

.total-price-wrapper {
    text-align: right;
    min-width: 180px;
}

.total-price {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--terracotta);
    line-height: 1.2;
    margin-bottom: 5px;
}

.total-note {
    font-size: 0.8rem;
    color: var(--taupe);
}

/* ===== ADD TO CART BUTTON ===== */
.btn-add-to-cart {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, var(--terracotta), #b86a4a);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(201, 124, 93, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-add-to-cart:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(201, 124, 93, 0.4);
}

.btn-add-to-cart:disabled {
    background: var(--taupe);
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

/* ===== ADDITIONAL INFO ===== */
.additional-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    background: var(--cream);
    border-radius: 16px;
    padding: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    color: var(--charcoal);
}

.info-item i {
    font-size: 1.5rem;
    color: var(--terracotta);
    width: 40px;
    text-align: center;
}

.info-item strong {
    display: block;
    font-size: 0.9rem;
    margin-bottom: 3px;
}

.info-item span {
    font-size: 0.8rem;
    color: var(--taupe);
}

/* ===== PRODUCT DESCRIPTION ===== */
.product-description {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: var(--shadow-sm);
    margin-top: 40px;
}

.description-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.description-title {
    font-family: 'Prata', serif;
    font-size: 1.8rem;
    color: var(--charcoal);
    margin: 0;
}

.description-tabs {
    display: flex;
    gap: 10px;
}

.tab-btn {
    padding: 8px 20px;
    background: transparent;
    border: 2px solid var(--sand);
    border-radius: 30px;
    color: var(--charcoal);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.tab-btn:hover {
    border-color: var(--terracotta);
    color: var(--terracotta);
}

.tab-btn.active {
    background: var(--terracotta);
    border-color: var(--terracotta);
    color: white;
}

.description-content {
    color: var(--charcoal);
    line-height: 1.8;
    font-size: 1rem;
}

.description-content.active {
    display: block;
}

/* Review Item */
.review-item {
    padding: 20px 0;
    border-bottom: 1px solid var(--sand);
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.review-rating i {
    font-size: 0.9rem;
}

/* ===== RELATED PRODUCTS ===== */
.related-products-section {
    background: linear-gradient(135deg, var(--cream), white);
    margin-top: 60px;
    padding: 60px 0;
}

.section-title {
    font-family: 'Prata', serif;
    font-size: 2.2rem;
    color: var(--charcoal);
    margin-bottom: 10px;
    text-align: center;
}

.section-subtitle {
    text-align: center;
    color: var(--taupe);
    margin-bottom: 40px;
    font-size: 1.1rem;
}

.related-product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
    height: 100%;
}

.related-product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.related-product-image {
    position: relative;
    display: block;
    height: 220px;
    overflow: hidden;
}

.related-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.related-product-card:hover .related-product-image img {
    transform: scale(1.08);
}

.related-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff4757;
    color: white;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 600;
}

.related-product-info {
    padding: 20px;
    text-align: center;
}

.related-product-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
    line-height: 1.4;
}

.related-product-title a {
    color: var(--charcoal);
    text-decoration: none;
    transition: color 0.3s;
}

.related-product-title a:hover {
    color: var(--terracotta);
}

.related-product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--terracotta);
    margin-bottom: 5px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .main-image-container {
        height: 400px;
    }

    .action-section {
        flex-direction: column;
        gap: 20px;
    }

    .total-price-wrapper {
        text-align: left;
    }

    .description-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .product-title {
        font-size: 2rem;
    }

    .current-price {
        font-size: 2rem;
    }

    .size-grid, .flavor-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .additional-info {
        grid-template-columns: 1fr;
    }

    .product-details {
        padding: 25px;
    }

    .description-tabs {
        width: 100%;
        justify-content: stretch;
    }

    .tab-btn {
        flex: 1;
        text-align: center;
    }
}
</style>

<script>
    // Get DOM elements
    const sizeRadios = document.querySelectorAll('.size-radio');
    const flavorRadios = document.querySelectorAll('.flavor-radio');
    const dynamicPriceSpan = document.getElementById('dynamicPrice');
    const totalPriceSpan = document.getElementById('totalPrice');
    const calculatedPriceInput = document.getElementById('calculatedPrice');
    const quantityInput = document.getElementById('quantity');

    // Base price from product
    let basePrice = {{ $minPrice ?? ($product->sale_price ?? $product->regular_price) }};

    // Function to calculate total price
    function calculateTotal() {
        let sizePrice = basePrice;
        let flavorExtra = 0;

        // Get selected size price
        const selectedSize = document.querySelector('.size-radio:checked');
        if (selectedSize && selectedSize.dataset.price) {
            sizePrice = parseFloat(selectedSize.dataset.price);
        }

        // Get selected flavor extra price
        const selectedFlavor = document.querySelector('.flavor-radio:checked');
        if (selectedFlavor && selectedFlavor.dataset.price) {
            flavorExtra = parseFloat(selectedFlavor.dataset.price);
        }

        // Calculate total with quantity
        const quantity = parseInt(quantityInput.value) || 1;
        const unitPrice = sizePrice + flavorExtra;
        const total = unitPrice * quantity;

        // Update display
        if (dynamicPriceSpan) {
            dynamicPriceSpan.textContent = formatCurrency(unitPrice);
        }
        totalPriceSpan.textContent = formatCurrency(total);
        calculatedPriceInput.value = unitPrice;
    }

    // Format currency helper
    function formatCurrency(amount) {
        return '{{ setting('currency_symbol', '$') }}' + amount.toFixed(2);
    }

    // Add event listeners
    sizeRadios.forEach(radio => {
        radio.addEventListener('change', calculateTotal);
    });

    flavorRadios.forEach(radio => {
        radio.addEventListener('change', calculateTotal);
    });

    quantityInput.addEventListener('input', calculateTotal);

    // Quantity functions
    function incrementQuantity() {
        let max = parseInt(quantityInput.getAttribute('max'));
        let current = parseInt(quantityInput.value);
        if (current < max) {
            quantityInput.value = current + 1;
            calculateTotal();
        }
    }

    function decrementQuantity() {
        let current = parseInt(quantityInput.value);
        if (current > 1) {
            quantityInput.value = current - 1;
            calculateTotal();
        }
    }

    // Change main image
    function changeMainImage(src) {
        document.getElementById('mainProductImage').src = src;
    }

    // Tab switching
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.description-content').forEach(tab => {
            tab.style.display = 'none';
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName + '-tab').style.display = 'block';

        // Add active class to clicked button
        event.target.classList.add('active');
    }

    // Initial calculation
    calculateTotal();
</script>
@endsection
