@extends('layouts.frontend')

@section('title', $category->name)

@section('content')
<div class="container py-5">
    <h2 class="mb-4">{{ $category->name }} Cakes 🎂</h2>

    @if($products->count())
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5>{{ $product->name }}</h5>
                            <p>₹{{ $product->price }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No cakes available in this category.</p>
    @endif
</div>
@endsection
