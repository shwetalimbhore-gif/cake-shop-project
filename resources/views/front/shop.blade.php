@extends('layouts.front')

@section('title', 'Our Cake Collection - ' . setting('site_name', 'Cozy Cravings'))
@section('page-title', 'Our Cake Collection')

@section('content')
<!-- Modern Breadcrumb with Background -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Our Cake Collection</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cake Collection</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5">
    <!-- Search Bar Section -->
    <div class="search-section" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="search-card">
                    <form action="{{ route('shop') }}" method="GET" class="search-form" id="searchForm">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}" id="sortInput">
                        @endif
                        @if(request('eggless'))
                            <input type="hidden" name="eggless" value="{{ request('eggless') }}">
                        @endif
                        @if(request('min_price'))
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        @endif
                        @if(request('max_price'))
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        @endif

                        <div class="search-wrapper">
                            <input type="text"
                                   name="search"
                                   class="form-control search-input"
                                   placeholder="Search cakes..."
                                   value="{{ request('search') }}">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('shop', array_merge(request()->except('search', 'page'))) }}"
                                   class="search-clear">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filters-sidebar">
                <div class="filters-header">
                    <h5 class="filters-title">
                        <i class="fas fa-sliders-h me-2"></i>Filter Cakes
                    </h5>
                    @if(request('category') || request('eggless') || request('min_price') || request('max_price') || request('search'))
                        <a href="{{ route('shop') }}" class="clear-all-filters">
                            <i class="fas fa-undo-alt me-1"></i>Clear All
                        </a>
                    @endif
                </div>

                <!-- Category Filter -->
                <div class="filter-section">
                    <h6 class="filter-section-title">
                        <i class="fas fa-tags me-2"></i>Categories
                    </h6>
                    <div class="filter-options">
                        <div class="category-list">
                            <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => null])) }}"
                               class="category-item {{ !request('category') ? 'active' : '' }}">
                                <span>All Cakes</span>
                                <span class="category-count">{{ $products->total() }}</span>
                            </a>
                            @foreach($categories as $category)
                            <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => $category->id])) }}"
                               class="category-item {{ request('category') == $category->id ? 'active' : '' }}">
                                <span>
                                    @if($category->name == 'Birthday Cakes')
                                        <i class="fas fa-birthday-cake me-2" style="color: var(--terracotta);"></i>
                                    @elseif($category->name == 'Wedding Cakes')
                                        <i class="fas fa-heart me-2" style="color: #ff6b8b;"></i>
                                    @elseif($category->name == 'Cupcakes')
                                        <i class="fas fa-candy-cane me-2" style="color: #a7b5a3;"></i>
                                    @elseif($category->name == 'Custom Cakes')
                                        <i class="fas fa-star me-2" style="color: #d4af37;"></i>
                                    @endif
                                    {{ $category->name }}
                                </span>
                                <span class="category-count">{{ $category->products_count ?? $category->products->count() }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Eggless Filter Section -->
                <div class="filter-section">
                    <h6 class="filter-section-title">
                        <i class="fas fa-leaf me-2"></i>Dietary Preferences
                    </h6>
                    <div class="filter-options">
                        <div class="dietary-options">
                            <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => 'yes'])) }}"
                               class="dietary-option {{ request('eggless') == 'yes' ? 'active' : '' }}">
                                <div class="dietary-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div class="dietary-info">
                                    <span class="dietary-name">Eggless Only</span>
                                </div>
                                @if(request('eggless') == 'yes')
                                    <i class="fas fa-check-circle text-success"></i>
                                @endif
                            </a>

                            <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => 'no'])) }}"
                               class="dietary-option {{ request('eggless') == 'no' ? 'active' : '' }}">
                                <div class="dietary-icon">
                                    <i class="fas fa-egg"></i>
                                </div>
                                <div class="dietary-info">
                                    <span class="dietary-name">With Egg</span>
                                </div>
                                @if(request('eggless') == 'no')
                                    <i class="fas fa-check-circle text-success"></i>
                                @endif
                            </a>

                            @if(request('eggless'))
                                <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}"
                                   class="dietary-clear">
                                    <i class="fas fa-times me-1"></i>Clear Dietary Filter
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-section">
                    <h6 class="filter-section-title">
                        <i class="fas fa-dollar-sign me-2"></i>Price Range
                    </h6>
                    <div class="filter-options">
                        <form method="GET" action="{{ route('shop') }}" id="priceFilterForm" class="price-filter-form">
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

                            <div class="price-inputs">
                                <div class="price-input-group">
                                    <label class="price-label">Min</label>
                                    <div class="price-field">
                                        <span class="price-currency">{{ setting('currency_symbol', '$') }}</span>
                                        <input type="number" name="min_price"
                                               class="price-input"
                                               value="{{ request('min_price') }}"
                                               placeholder="0"
                                               min="0">
                                    </div>
                                </div>
                                <div class="price-separator">
                                    <i class="fas fa-minus"></i>
                                </div>
                                <div class="price-input-group">
                                    <label class="price-label">Max</label>
                                    <div class="price-field">
                                        <span class="price-currency">{{ setting('currency_symbol', '$') }}</span>
                                        <input type="number" name="max_price"
                                               class="price-input"
                                               value="{{ request('max_price') }}"
                                               placeholder="1000"
                                               min="0">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="apply-price-btn">
                                <i class="fas fa-filter me-2"></i>Apply Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Sort Bar -->
            <div class="results-bar">
                <div class="results-info">
                    <i class="fas fa-cake me-2"></i>
                    @if(request('search'))
                        <span class="fw-bold">{{ $products->total() }}</span> results for
                        <span class="search-term">"{{ request('search') }}"</span>
                    @else
                        Showing <span class="fw-bold">{{ $products->firstItem() ?? 0 }}</span> -
                        <span class="fw-bold">{{ $products->lastItem() ?? 0 }}</span> of
                        <span class="fw-bold">{{ $products->total() }}</span> cakes
                    @endif
                </div>

                <!-- Sort Form -->
                <form method="GET" action="{{ route('shop') }}" id="sortForm" class="sort-options">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('eggless'))
                        <input type="hidden" name="eggless" value="{{ request('eggless') }}">
                    @endif
                    @if(request('min_price'))
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                    @endif
                    @if(request('max_price'))
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                    @endif

                    <label for="sortSelect" class="sort-label">
                        <i class="fas fa-sort-amount-down-alt me-2"></i>Sort by:
                    </label>
                    <select name="sort" class="sort-select" id="sortSelect">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </form>
            </div>

            <!-- Active Filters Display -->
            @if(request('category') || request('eggless') || request('min_price') || request('max_price') || request('search'))
            <div class="active-filters">
                <span class="active-filters-label">
                    <i class="fas fa-filter me-1"></i>Active filters:
                </span>
                <div class="filter-tags">
                    @if(request('category'))
                        @php
                            $cat = $categories->firstWhere('id', request('category'));
                        @endphp
                        @if($cat)
                            <span class="filter-tag">
                                {{ $cat->name }}
                                <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => null])) }}" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                    @endif

                    @if(request('eggless') == 'yes')
                        <span class="filter-tag">
                            <i class="fas fa-leaf me-1"></i>Eggless
                            <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}" class="remove-filter">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('eggless') == 'no')
                        <span class="filter-tag">
                            <i class="fas fa-egg me-1"></i>With Egg
                            <a href="{{ route('shop', array_merge(request()->except('eggless'), ['eggless' => null])) }}" class="remove-filter">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('min_price'))
                        <span class="filter-tag">
                            Min: {{ setting('currency_symbol', '$') }}{{ request('min_price') }}
                            <a href="{{ route('shop', array_merge(request()->except('min_price'), ['min_price' => null])) }}" class="remove-filter">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('max_price'))
                        <span class="filter-tag">
                            Max: {{ setting('currency_symbol', '$') }}{{ request('max_price') }}
                            <a href="{{ route('shop', array_merge(request()->except('max_price'), ['max_price' => null])) }}" class="remove-filter">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('search'))
                        <span class="filter-tag">
                            "{{ request('search') }}"
                            <a href="{{ route('shop', array_merge(request()->except('search'), ['search' => null])) }}" class="remove-filter">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
            @endif

            <!-- Products Grid -->
            <div class="row g-4">
                @forelse($products as $product)
                <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="cake-card">
                        <div class="cake-image-wrapper">
                            @if($product->featured_image)
                                <img src="{{ asset('storage/' . $product->featured_image) }}"
                                     alt="{{ $product->name }}"
                                     class="cake-image">
                            @else
                                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587"
                                     alt="{{ $product->name }}"
                                     class="cake-image">
                            @endif

                            <!-- Badges -->
                            <div class="cake-badges">
                                @if($product->sale_price && $product->sale_price < $product->regular_price)
                                    <span class="badge-sale">SALE</span>
                                @endif

                                @if($product->is_featured)
                                    <span class="badge-featured">
                                        <i class="fas fa-crown"></i> Featured
                                    </span>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="cake-actions">
                                <a href="{{ route('product.details', $product->slug) }}" class="action-btn view-btn" title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form d-inline">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="action-btn cart-btn" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="cake-info">
                            <div class="cake-category">
                                <i class="fas fa-tag me-1"></i>
                                {{ $product->category->name ?? 'Cake' }}
                            </div>

                            <h3 class="cake-title">
                                <a href="{{ route('product.details', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Eggless Badge (INSIDE THE LOOP) -->
                            <div class="cake-dietary-badge">
                                @if($product->is_eggless)
                                    <span class="dietary-badge eggless-badge">
                                        <i class="fas fa-leaf"></i> 100% Eggless
                                    </span>
                                @else
                                    <span class="dietary-badge with-egg-badge">
                                        <i class="fas fa-egg"></i> Contains Egg
                                    </span>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="cake-price">
                                @if($product->sale_price && $product->sale_price < $product->regular_price)
                                    <span class="original-price">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->regular_price, 2) }}
                                    </span>
                                    <span class="sale-price">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->sale_price, 2) }}
                                    </span>
                                    <span class="discount-badge">
                                        -{{ round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) }}%
                                    </span>
                                @else
                                    <span class="regular-price">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($product->regular_price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="empty-state-title">No Cakes Found</h3>
                        <p class="empty-state-text">
                            @if(request('search'))
                                No cakes match your search "{{ request('search') }}".
                            @else
                                No cakes match your selected filters.
                            @endif
                        </p>
                        <div class="empty-state-actions">
                            <a href="{{ route('shop') }}" class="btn-primary-modern btn-modern">
                                <i class="fas fa-redo-alt me-2"></i>Clear All Filters
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
/* ===== PAGE HEADER ===== */
.page-header {
    background: linear-gradient(135deg, #fdf8f2 0%, #f7e6e0 100%);
    padding: 50px 0 30px;
    margin-bottom: 40px;
}

.page-title {
    font-family: 'Prata', serif;
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 10px;
}

/* ===== SEARCH BAR ===== */
.search-section {
    margin-bottom: 30px;
}

.search-card {
    background: white;
    border-radius: 50px;
    padding: 5px;
    box-shadow: var(--shadow-md);
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    height: 50px;
    border: 2px solid transparent;
    border-radius: 50px !important;
    padding: 0 100px 0 20px;
    font-size: 0.95rem;
    transition: all 0.3s;
    width: 100%;
}

.search-input:focus {
    border-color: var(--terracotta);
    box-shadow: 0 0 0 3px rgba(201, 124, 93, 0.1);
    outline: none;
}

.search-btn {
    position: absolute;
    right: 5px;
    top: 5px;
    height: 40px;
    width: 80px;
    background: var(--terracotta);
    color: white;
    border: none;
    border-radius: 40px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.search-btn:hover {
    background: #b86a4a;
}

.search-clear {
    position: absolute;
    right: 95px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--taupe);
    font-size: 1rem;
}

/* ===== FILTERS SIDEBAR ===== */
.filters-sidebar {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: var(--shadow-sm);
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--sand);
}

.filters-title {
    font-size: 1.2rem;
    margin: 0;
    color: var(--charcoal);
}

.clear-all-filters {
    color: var(--terracotta);
    text-decoration: none;
    font-size: 0.85rem;
}

.filter-section {
    margin-bottom: 20px;
}

.filter-section-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--charcoal);
}

/* Category List */
.category-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: var(--cream);
    border-radius: 8px;
    text-decoration: none;
    color: var(--charcoal);
    transition: all 0.3s;
    font-size: 0.9rem;
}

.category-item:hover {
    background: var(--sand);
    transform: translateX(3px);
}

.category-item.active {
    background: var(--terracotta);
    color: white;
}

.category-count {
    background: white;
    padding: 2px 6px;
    border-radius: 20px;
    font-size: 0.75rem;
}

/* Dietary Options */
.dietary-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.dietary-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: var(--cream);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.dietary-option:hover {
    background: var(--sand);
}

.dietary-option.active {
    background: rgba(201, 124, 93, 0.1);
    border: 1px solid var(--terracotta);
}

.dietary-icon {
    width: 32px;
    height: 32px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--terracotta);
    font-size: 1rem;
}

.dietary-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--charcoal);
}

.dietary-clear {
    display: block;
    text-align: center;
    margin-top: 10px;
    padding: 8px;
    background: var(--cream);
    border-radius: 8px;
    color: var(--terracotta);
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.dietary-clear:hover {
    background: var(--sand);
}

/* Price Filter */
.price-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}

.price-input-group {
    flex: 1;
}

.price-label {
    font-size: 0.7rem;
    color: var(--taupe);
    margin-bottom: 3px;
}

.price-field {
    position: relative;
}

.price-currency {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8rem;
    color: var(--charcoal);
}

.price-input {
    width: 100%;
    padding: 8px 8px 8px 20px;
    border: 1px solid var(--sand);
    border-radius: 6px;
    font-size: 0.85rem;
}

.price-separator {
    color: var(--taupe);
    font-size: 0.7rem;
    margin-top: 15px;
}

.apply-price-btn {
    width: 100%;
    padding: 10px;
    background: var(--terracotta);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.apply-price-btn:hover {
    background: #b86a4a;
}

/* Results Bar */
.results-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 12px 15px;
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow-sm);
}

.results-info {
    font-size: 0.9rem;
    color: var(--charcoal);
}

.sort-options {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.sort-label {
    color: var(--taupe);
    font-size: 0.85rem;
    margin: 0;
}

.sort-select {
    padding: 6px 20px 6px 10px;
    border: 1px solid var(--sand);
    border-radius: 6px;
    background: white;
    color: var(--charcoal);
    font-size: 0.85rem;
    cursor: pointer;
}

/* Active Filters */
.active-filters {
    margin-bottom: 20px;
    padding: 12px 15px;
    background: rgba(201, 124, 93, 0.05);
    border-radius: 8px;
}

.active-filters-label {
    font-size: 0.85rem;
    color: var(--charcoal);
    font-weight: 600;
    margin-right: 10px;
}

.filter-tags {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: white;
    border-radius: 30px;
    font-size: 0.8rem;
    border: 1px solid var(--sand);
}

.remove-filter {
    color: var(--taupe);
    font-size: 0.7rem;
}

/* Cake Cards */
.cake-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.cake-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.cake-image-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.cake-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.cake-card:hover .cake-image {
    transform: scale(1.05);
}

.cake-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    z-index: 2;
}

.badge-sale {
    background: #ff4757;
    color: white;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
}

.badge-featured {
    background: var(--gold);
    color: var(--charcoal);
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
}

.cake-actions {
    position: absolute;
    bottom: -40px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
    transition: all 0.3s;
    z-index: 2;
}

.cake-card:hover .cake-actions {
    bottom: 0;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: white;
    border: none;
    color: var(--charcoal);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    font-size: 0.9rem;
}

.action-btn:hover {
    background: var(--terracotta);
    color: white;
}

.cake-info {
    padding: 15px;
    flex: 1;
}

.cake-category {
    color: var(--taupe);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.cake-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.cake-title a {
    color: var(--charcoal);
    text-decoration: none;
}

/* Eggless Badge */
.cake-dietary-badge {
    margin-bottom: 12px;
}

.dietary-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 500;
}

.eggless-badge {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.with-egg-badge {
    background: #fff3e0;
    color: #bf360c;
    border: 1px solid #ffcc80;
}

.cake-price {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.original-price {
    color: var(--taupe);
    text-decoration: line-through;
    font-size: 0.8rem;
}

.sale-price {
    color: var(--terracotta);
    font-weight: 700;
    font-size: 1.2rem;
}

.regular-price {
    color: var(--terracotta);
    font-weight: 700;
    font-size: 1.2rem;
}

.discount-badge {
    background: #ff4757;
    color: white;
    padding: 2px 6px;
    border-radius: 20px;
    font-size: 0.6rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: var(--cream);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: var(--taupe);
}

.empty-state-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.empty-state-text {
    color: var(--taupe);
    margin-bottom: 20px;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 40px;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }

    .results-bar {
        flex-direction: column;
        gap: 10px;
    }

    .sort-options {
        width: 100%;
    }

    .sort-select {
        flex: 1;
    }

    .cake-image-wrapper {
        height: 180px;
    }
}
</style>

<script>
    // Sort select auto-submit
    document.getElementById('sortSelect').addEventListener('change', function() {
        document.getElementById('sortForm').submit();
    });

    // Debounced search
    let searchTimer;
    document.querySelector('.search-input')?.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const searchValue = this.value;

        searchTimer = setTimeout(() => {
            if (searchValue.length >= 2 || searchValue.length === 0) {
                document.getElementById('searchForm').submit();
            }
        }, 800);
    });

    // Add to cart AJAX
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalHtml = submitButton.innerHTML;

            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitButton.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Product added to cart!');
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                } else {
                    toastr.error('Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Failed to add to cart');
            })
            .finally(() => {
                submitButton.innerHTML = originalHtml;
                submitButton.disabled = false;
            });
        });
    });
</script>
@endsection
