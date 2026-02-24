@extends('layouts.front')

@section('title', 'Our Cakes - ' . setting('site_name', 'Cozy Cravings'))
@section('page-title', 'Our Cakes')

@section('content')
<!-- Modern Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Our Cakes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Our Cakes</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <!-- Search Bar Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="search-container" data-aos="fade-up">
                <form action="{{ route('shop') }}" method="GET" class="search-form">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('eggless'))
                        <input type="hidden" name="eggless" value="{{ request('eggless') }}">
                    @endif

                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text"
                               name="search"
                               class="form-control search-input"
                               placeholder="Search for cakes by name or description..."
                               value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('shop', array_merge(request()->except('search', 'page'))) }}"
                               class="search-clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>

                    <div class="search-hints">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Search by cake name, flavor, or description
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card-modern p-4">
                <h5 class="mb-4" style="font-family: 'Prata', serif;">Categories</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => null])) }}"
                           class="text-decoration-none d-flex justify-content-between align-items-center"
                           style="color: {{ !request('category') ? 'var(--terracotta)' : 'var(--taupe)' }};
                                  font-weight: {{ !request('category') ? '500' : '400' }};">
                            All Cakes
                            <span class="badge bg-light text-dark rounded-0 px-3 py-1">{{ $products->total() }}</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li class="mb-3">
                        <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => $category->id])) }}"
                           class="text-decoration-none d-flex justify-content-between align-items-center"
                           style="color: {{ request('category') == $category->id ? 'var(--terracotta)' : 'var(--taupe)' }};
                                  font-weight: {{ request('category') == $category->id ? '500' : '400' }};">
                            {{ $category->name }}
                            <span class="badge bg-light text-dark rounded-0 px-3 py-1">
                                {{ $category->products_count ?? $category->products->count() }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                <hr class="my-4" style="background: var(--sand);">

                <!-- Eggless Filter -->
                <h5 class="mb-4" style="font-family: 'Prata', serif;">Dietary</h5>
                <div class="mb-4">
                    <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => 'yes'])) }}"
                       class="d-block mb-2 text-decoration-none {{ request('eggless') == 'yes' ? 'fw-bold' : '' }}"
                       style="color: {{ request('eggless') == 'yes' ? 'var(--terracotta)' : 'var(--taupe)' }};">
                        <i class="fas fa-leaf me-2" style="color: var(--sage);"></i>Eggless Only
                    </a>
                    <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => 'no'])) }}"
                       class="d-block mb-2 text-decoration-none {{ request('eggless') == 'no' ? 'fw-bold' : '' }}"
                       style="color: {{ request('eggless') == 'no' ? 'var(--terracotta)' : 'var(--taupe)' }};">
                        <i class="fas fa-egg me-2" style="color: var(--gold);"></i>With Egg
                    </a>
                    <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}"
                       class="d-block text-decoration-none"
                       style="color: var(--taupe);">
                        <i class="fas fa-undo me-2"></i>Clear Filter
                    </a>
                </div>

                <hr class="my-4" style="background: var(--sand);">

                <h5 class="mb-4" style="font-family: 'Prata', serif;">Price Range</h5>
                <form method="GET" action="{{ route('shop') }}" id="priceFilterForm">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('eggless'))
                        <input type="hidden" name="eggless" value="{{ request('eggless') }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label small text-muted">Min Price ({{ setting('currency_symbol', '$') }})</label>
                        <input type="number" name="min_price" class="form-control form-control-modern"
                               value="{{ request('min_price') }}" placeholder="0" min="0">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted">Max Price ({{ setting('currency_symbol', '$') }})</label>
                        <input type="number" name="max_price" class="form-control form-control-modern"
                               value="{{ request('max_price') }}" placeholder="1000" min="0">
                    </div>

                    <button type="submit" class="btn-modern btn-primary-modern w-100">Apply Filter</button>

                    @if(request('min_price') || request('max_price') || request('category') || request('search') || request('eggless'))
                        <a href="{{ route('shop') }}" class="btn-modern btn-outline-modern w-100 mt-3">Clear All</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Sort Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    @if(request('search'))
                        <p class="mb-2 mb-md-0">
                            <span class="fw-bold">Search results for: </span>
                            <span class="badge bg-light text-dark px-3 py-2">"{{ request('search') }}"</span>
                            <span class="text-muted ms-2">({{ $products->total() }} results)</span>
                        </p>
                    @else
                        <p class="mb-2 mb-md-0">
                            Showing <span class="fw-bold">{{ $products->firstItem() ?? 0 }}</span> -
                            <span class="fw-bold">{{ $products->lastItem() ?? 0 }}</span> of
                            <span class="fw-bold">{{ $products->total() }}</span> cakes
                        </p>
                    @endif
                </div>

                <select class="form-select form-select-modern" id="sortSelect" style="width: 180px;">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                </select>
            </div>

            <!-- Active Filters Display -->
            @if(request('eggless') || request('sort') && request('sort') != 'latest')
            <div class="mb-4">
                <span class="text-muted me-2">Active filters:</span>
                @if(request('eggless') == 'yes')
                    <span class="badge bg-light text-dark px-3 py-2 me-2">
                        Eggless <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}" class="ms-2 text-danger"><i class="fas fa-times"></i></a>
                    </span>
                @endif
                @if(request('eggless') == 'no')
                    <span class="badge bg-light text-dark px-3 py-2 me-2">
                        With Egg <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}" class="ms-2 text-danger"><i class="fas fa-times"></i></a>
                    </span>
                @endif
            </div>
            @endif

            <!-- Products Grid -->
            <div class="row g-4">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 30 }}">
                    <div class="card-modern">
                        <div class="product-image-container">
                            @if($product->featured_image)
                                <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}">
                            @else
                                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587" alt="{{ $product->name }}">
                            @endif

                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="product-badge">SALE</span>
                            @endif

                            @if($product->is_eggless)
                                <span class="product-badge eggless">
                                    <i class="fas fa-leaf me-1"></i>Eggless
                                </span>
                            @endif
                        </div>

                        <div class="p-4">
                            <p class="small text-muted mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <h5 class="mb-3" style="font-family: 'Prata', serif;">
                                <a href="{{ route('product.details', $product->slug) }}"
                                   class="text-decoration-none" style="color: var(--charcoal);">
                                    {{ $product->name }}
                                </a>
                            </h5>

                            @if(request('search') && strpos(strtolower($product->description), strtolower(request('search'))) !== false)
                                <p class="small text-muted mb-2">
                                    <i class="fas fa-search text-primary me-1"></i>
                                    Matches description
                                </p>
                            @endif

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

                                <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-action" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="fw-bold">No cakes found</h4>
                    @if(request('search'))
                        <p class="text-muted mb-3">No results for "{{ request('search') }}"</p>
                        <a href="{{ route('shop') }}" class="btn-modern btn-primary-modern">
                            Clear Search
                        </a>
                    @else
                        <p class="text-muted mb-3">Try adjusting your filters</p>
                        <a href="{{ route('shop') }}" class="btn-modern btn-primary-modern">
                            Reset Filters
                        </a>
                    @endif
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

<style>
    /* Search Bar Styles */
    .search-container {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .search-wrapper {
        position: relative;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--taupe);
        font-size: 1.2rem;
        z-index: 2;
    }

    .search-input {
        width: 100%;
        height: 60px;
        padding: 0 50px 0 50px;
        border: 2px solid var(--sand);
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        box-shadow: var(--shadow-sm);
    }

    .search-input:focus {
        border-color: var(--terracotta);
        outline: none;
        box-shadow: 0 0 0 4px rgba(201, 124, 93, 0.1);
    }

    .search-clear {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--taupe);
        text-decoration: none;
        font-size: 1.1rem;
        z-index: 2;
        transition: all 0.3s;
    }

    .search-clear:hover {
        color: var(--terracotta);
    }

    .search-hints {
        margin-top: 10px;
        padding-left: 20px;
    }

    /* Product Card Styles */
    .product-image-container {
        position: relative;
        height: 280px;
        overflow: hidden;
        background: var(--sand);
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .card-modern:hover .product-image-container img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        background: var(--terracotta);
        color: white;
        padding: 6px 14px;
        border-radius: 2px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        z-index: 2;
    }

    .product-badge.eggless {
        background: var(--sage);
        left: auto;
        right: 16px;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--terracotta);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-action:hover {
        background: #b86a4a;
        transform: scale(1.1);
    }

    /* Breadcrumb */
    .breadcrumb-modern {
        padding: 40px 0 20px;
        margin-bottom: 40px;
        border-bottom: 1px solid var(--sand);
    }

    .breadcrumb-modern h1 {
        font-size: 2.2rem;
        margin-bottom: 16px;
    }

    .breadcrumb-custom {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-custom .breadcrumb-item {
        font-size: 0.9rem;
    }

    .breadcrumb-custom .breadcrumb-item a {
        color: var(--taupe);
        text-decoration: none;
    }

    .breadcrumb-custom .breadcrumb-item.active {
        color: var(--charcoal);
    }

    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item:before {
        color: var(--sand);
    }

    /* Form Elements */
    .form-control-modern {
        border: 1px solid var(--sand);
        border-radius: 2px;
        padding: 14px 16px;
        transition: all 0.3s;
    }

    .form-control-modern:focus {
        border-color: var(--terracotta);
        outline: none;
        box-shadow: 0 0 0 3px rgba(201, 124, 93, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-input {
            height: 50px;
            font-size: 0.95rem;
        }

        .product-image-container {
            height: 220px;
        }
    }
</style>

<script>
    // Auto-submit sort
    document.getElementById('sortSelect').addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });

    // Debounced search
    let searchTimer;
    document.querySelector('.search-input')?.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const searchValue = this.value;

        searchTimer = setTimeout(() => {
            if (searchValue.length >= 2 || searchValue.length === 0) {
                const url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        }, 500);
    });

    // Update cart count after adding
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    updateCartCount();
                }
            })
            .catch(error => {
                toastr.error('Failed to add to cart');
            });
        });
    });
</script>
@endsection
