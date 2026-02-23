@extends('layouts.front')

@section('title', 'Our Cakes - ' . setting('site_name', 'Cozy Cravings'))
@section('page-title', 'Our Cakes')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Our Cakes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Our Cakes</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Categories</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="{{ route('shop') }}"
                           class="text-decoration-none d-flex justify-content-between align-items-center"
                           style="color: {{ !request('category') ? 'var(--primary-dark)' : 'var(--secondary)' }};
                                  font-weight: {{ !request('category') ? '600' : '400' }};">
                            All Cakes
                            <span class="badge rounded-pill" style="background: var(--gray-light); color: var(--dark);">
                                {{ $products->total() }}
                            </span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li class="mb-3">
                        <a href="{{ route('shop', ['category' => $category->id]) }}"
                           class="text-decoration-none d-flex justify-content-between align-items-center"
                           style="color: {{ request('category') == $category->id ? 'var(--primary-dark)' : 'var(--secondary)' }};
                                  font-weight: {{ request('category') == $category->id ? '600' : '400' }};">
                            {{ $category->name }}
                            <span class="badge rounded-pill" style="background: var(--gray-light); color: var(--dark);">
                                {{ $category->products_count ?? $category->products->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                <hr class="my-4" style="background: var(--gray);">

                <h5 class="fw-bold mb-4">Price Range</h5>
                <form method="GET" action="{{ route('shop') }}">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label small">Min Price</label>
                        <input type="number" name="min_price" class="form-control"
                               value="{{ request('min_price') }}" min="0" placeholder="0">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small">Max Price</label>
                        <input type="number" name="max_price" class="form-control"
                               value="{{ request('max_price') }}" min="0" placeholder="1000">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>

                    @if(request('min_price') || request('max_price') || request('category'))
                        <a href="{{ route('shop') }}" class="btn btn-outline w-100 mt-3">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Sort Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <p class="mb-2 mb-md-0">
                    Showing <span class="fw-bold">{{ $products->firstItem() ?? 0 }}</span> -
                    <span class="fw-bold">{{ $products->lastItem() ?? 0 }}</span> of
                    <span class="fw-bold">{{ $products->total() }}</span> cakes
                </p>

                <div class="d-flex align-items-center">
                    <label class="me-2 text-muted small">Sort by:</label>
                    <select class="form-select form-select-sm" id="sortSelect" style="width: 160px;">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-4">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="card">
                        <!-- Image Container - FIXED -->
                        <div class="product-image-container">
                            @if($product->featured_image)
                                <img src="{{ asset('storage/' . $product->featured_image) }}"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-cake fa-4x" style="color: var(--gray); opacity: 0.5;"></i>
                                </div>
                            @endif

                            <!-- Badges -->
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="product-badge">Sale</span>
                            @endif

                            @if($product->is_featured)
                                <span class="product-featured">Featured</span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <p class="small text-muted mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>

                            <h5 class="fw-bold mb-3" style="min-height: 48px;">
                                <a href="{{ route('product.details', $product->slug) }}"
                                   class="text-decoration-none" style="color: var(--dark);">
                                    {{ $product->name }}
                                </a>
                            </h5>

                            <!-- Rating -->
                            <div class="mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star" style="color: {{ $i <= 4 ? 'var(--warning)' : 'var(--gray)' }}; font-size: 0.8rem;"></i>
                                @endfor
                                <span class="text-muted ms-2 small">(24)</span>
                            </div>

                            <!-- Price and Cart -->
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    @if($product->sale_price && $product->sale_price < $product->regular_price)
                                        <span class="text-muted text-decoration-line-through small me-2">
                                            {{ setting('currency_symbol', '₹') }}{{ number_format($product->regular_price, 2) }}
                                        </span>
                                        <span class="fw-bold fs-5" style="color: var(--primary-dark);">
                                            {{ setting('currency_symbol', '₹') }}{{ number_format($product->sale_price, 2) }}
                                        </span>
                                    @else
                                        <span class="fw-bold fs-5" style="color: var(--primary-dark);">
                                            {{ setting('currency_symbol', '₹') }}{{ number_format($product->regular_price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary rounded-circle p-3"
                                            style="width: 45px; height: 45px;"
                                            title="Add to Cart">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-cake fa-4x text-muted mb-3"></i>
                    <h4 class="fw-bold">No Cakes Found</h4>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Clear Filters</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sortSelect').addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
</script>
@endsection
