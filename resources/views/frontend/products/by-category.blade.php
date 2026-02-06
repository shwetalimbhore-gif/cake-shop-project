@extends('frontend.layouts.app')

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-2 rounded">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }} Cakes</li>
        </ol>
    </nav>

    <!-- Category Title -->
    <h2 class="mb-4 text-center">{{ $category->name }} Cakes 🍰</h2>

    @if($products->isEmpty())
        <p class="text-center text-muted">No cakes found in this category.</p>
    @else
        <div class="row">
            @foreach($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        @if($product->image)
                            <a href="{{ route('products.show', $product) }}">
                                <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                            </a>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <p class="card-text fw-bold text-primary">₹ {{ $product->price }}</p>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Optional hover CSS -->
@push('styles')
<style>
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
</style>
@endpush
@endsection
