{{-- @extends('layouts.app')

@section('title', 'Our Cake Categories')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Cake Categories 🎂</h2>

    <div class="row">
        @foreach ($categories as $category)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{ $category->name }}</h5>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection --}}

@extends('layouts.frontend')

@section('title', 'Cake Categories')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Cake Categories 🍰</h2>

    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $category->name }}</h5>

                        <a href="{{ route('categories.products', $category->id) }}" class="btn btn-outline-primary mt-2">
                            View Cakes
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
