@extends('layouts.front')

@section('title', 'Shop - ' . setting('site_name'))
@section('page-title', 'Our Products')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">Our Products</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('shop') }}" class="text-decoration-none {{ !request('category') ? 'text-primary fw-bold' : 'text-muted' }}">
                                All Categories
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li class="mb-2">
                            <a href="{{ route('shop', ['category' => $category->id]) }}"
                               class="text-decoration-none {{ request('category') == $category->id ? 'text-primary fw-bold' : 'text-muted' }}">
                                {{ $category->name }}
                                <span class="float-end">({{ $category->products->count() ?? 0 }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Price Range</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('shop') }}" id="filterForm">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}" id="sortInput">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Min Price ({{ setting('currency_symbol', '$') }})</label>
                            <input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max Price ({{ setting('currency_symbol', '$') }})</label>
                            <input type="number" name="max_price" class="form-control" value="{{ request('max_price') }}" min="0">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Apply Filter
                        </button>

                        @if(request('min_price') || request('max_price') || request('category'))
                            <a href="{{ route('shop') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Toolbar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>

                <div class="d-flex align-items-center">
                    <label class="me-2 text-muted">Sort by:</label>
                    <select class="form-select" id="sortSelect" style="width: auto;">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>
            </div>

            <!-- Products -->
            <div class="row">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6">
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
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">Clear Filters</a>
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
@endsection

@push('scripts')
<script>
    document.getElementById('sortSelect').addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
</script>
@endpush
