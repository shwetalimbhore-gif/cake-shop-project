@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="row align-items-center">

        <!-- LEFT TEXT -->
        <div class="col-md-6">
            <h1 class="display-5 fw-bold">SweetCravings</h1>
            <p class="lead">Where Cravings Meet Cakes</p>
            <p>Freshly baked cakes for every celebration.</p>

            <a href="#" class="btn btn-primary btn-lg">
                Order Cakes
            </a>
        </div>

        <!-- RIGHT IMAGE -->
        <div class="col-md-6 text-center">
            <img src="{{ asset('images/cake.webp') }}"
                 class="img-fluid rounded"
                 alt="Cake">
        </div>

    </div>
</div>
@endsection
