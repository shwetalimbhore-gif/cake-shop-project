@extends('frontend.layouts.app')

@section('content')
    <h2 class="mb-4">Cake Categories 🎂</h2>

    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <a href="{{ route('categories.products', $category) }}" class="btn btn-primary mt-2">
                            View Cakes
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>No categories available.</p>
        @endforelse
    </div>
@endsection


