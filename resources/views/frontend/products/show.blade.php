@extends('frontend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset($product->image) }}" class="img-fluid rounded">
            @endif
        </div>

        <div class="col-md-6">
            <h2>{{ $product->name }}</h2>
            <h4 class="text-success">₹ {{ $product->price }}</h4>

            <p class="mt-3">
                {{ $product->description }}
            </p>
        </div>
    </div>
@endsection
