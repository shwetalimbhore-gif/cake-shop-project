@extends('frontend.layouts.app')

@section('content')
    <div class="text-center mb-4">
        <h1>Welcome to SweetCravings 🍰</h1>
        <p class="lead">Delicious cakes for every occasion</p>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    @endif

                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">₹ {{ $product->price }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">No cakes available.</p>
        @endforelse
    </div>
@endsection
