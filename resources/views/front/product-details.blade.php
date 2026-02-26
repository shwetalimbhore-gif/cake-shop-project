@extends('layouts.front')

@section('title', $product->name . ' - ' . setting('site_name'))
@section('meta_description', $product->short_description)

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
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

<div class="container mb-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="main-image mb-3">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                 alt="{{ $product->name }}"
                                 class="img-fluid rounded-3"
                                 id="mainProductImage">
                        @else
                            <img src="https://via.placeholder.com/600x600"
                                 alt="{{ $product->name }}"
                                 class="img-fluid rounded-3">
                        @endif
                    </div>

                    @if($product->images->count() > 0)
                        <div class="row g-2">
                            @foreach($product->images as $image)
                                <div class="col-3">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="Gallery"
                                         class="img-fluid rounded-3 cursor-pointer gallery-image"
                                         onclick="document.getElementById('mainProductImage').src = this.src">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $product->category->name ?? 'Uncategorized' }}</span>
                        @if($product->is_featured)
                            <span class="badge bg-warning">Featured</span>
                        @endif
                    </div>

                    <h1 class="display-6 fw-bold mb-3">{{ $product->name }}</h1>

                    <div class="product-price fs-2 mb-4">
                        @if($product->sale_price && $product->sale_price < $product->regular_price)
                            <small class="text-muted fs-4">{{ format_currency($product->regular_price) }}</small>
                            {{ format_currency($product->sale_price) }}
                            <span class="badge bg-success ms-2">Save {{ $product->discount_percentage }}%</span>
                        @else
                            {{ format_currency($product->regular_price) }}
                        @endif
                    </div>

                    <p class="text-muted mb-4">{{ $product->short_description }}</p>

                    <hr>

                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add', $product) }}" method="POST" id="addToCartForm">
                        @csrf

                        @if($product->sizes && is_array(json_decode($product->sizes, true)))
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Size</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach(json_decode($product->sizes, true) as $size)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="size" id="size_{{ $loop->index }}"
                                               value="{{ $size }}" required>
                                        <label class="form-check-label" for="size_{{ $loop->index }}">
                                            {{ $size }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->flavors && is_array(json_decode($product->flavors, true)))
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Flavor</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach(json_decode($product->flavors, true) as $flavor)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="flavor" id="flavor_{{ $loop->index }}"
                                               value="{{ $flavor }}" required>
                                        <label class="form-check-label" for="flavor_{{ $loop->index }}">
                                            {{ $flavor }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="row align-items-center mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantity</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity()">-</button>
                                    <input type="number" name="quantity" id="quantity"
                                           class="form-control text-center" value="1" min="1"
                                           max="{{ $product->stock_quantity }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity()">+</button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="stock-status mt-3 mt-md-0">
                                    @if($product->stock_quantity > 0)
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>In Stock ({{ $product->stock_quantity }} available)</span>
                                    @else
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                        <span>Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"
                                    {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </form>

                    <hr>

                    <!-- Product Meta -->
                    <div class="product-meta">
                        <p class="mb-2">
                            <i class="fas fa-tag text-primary me-2"></i>
                            <strong>SKU:</strong> {{ $product->sku }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-box text-primary me-2"></i>
                            <strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-eye text-primary me-2"></i>
                            <strong>Views:</strong> {{ $product->views ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Product Description</h5>
                </div>
                <div class="card-body">
                    <div class="product-description">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this in the product details page -->
    <div class="product-eggless-info mb-4">
        @if($product->is_eggless)
            <div class="eggless-badge-large">
                <i class="fas fa-leaf fa-2x me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1">100% Eggless</h5>
                    <p class="text-muted mb-0">This cake contains no eggs. Perfect for vegetarians!</p>
                </div>
            </div>
        @else
            <div class="with-egg-badge-large">
                <i class="fas fa-egg fa-2x me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1">Contains Egg</h5>
                    <p class="text-muted mb-0">This traditional recipe includes fresh eggs.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row">
                @foreach($relatedProducts as $related)
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="product-card">
                        <div class="product-image">
                            @if($related->featured_image)
                                <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->name }}">
                            @else
                                <img src="https://via.placeholder.com/300x300" alt="{{ $related->name }}">
                            @endif

                            <div class="product-overlay">
                                <a href="{{ route('product.details', $related->slug) }}" class="btn btn-light">
                                    <i class="fas fa-eye me-2"></i>View
                                </a>
                            </div>
                        </div>

                        <div class="product-info">
                            <h5 class="product-title">
                                <a href="{{ route('product.details', $related->slug) }}">{{ $related->name }}</a>
                            </h5>
                            <div class="product-price">{{ format_currency($related->regular_price) }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function incrementQuantity() {
        let input = document.getElementById('quantity');
        let max = parseInt(input.getAttribute('max'));
        let current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decrementQuantity() {
        let input = document.getElementById('quantity');
        let current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }
</script>
@endpush
