@extends('layouts.front')

@section('title', 'About Us - ' . setting('site_name'))
@section('page-title', 'About Us')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="container">
        <h1 class="display-5 fw-bold">About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Our Story -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1950&q=80"
                     alt="Our Bakery"
                     class="img-fluid rounded-4 shadow-lg">
            </div>
            <div class="col-lg-6">
                <h2 class="display-6 fw-bold mb-4">Our Sweet Story</h2>
                <p class="lead text-muted mb-4">Founded in 2020, {{ setting('site_name', 'MyCakeShop') }} started with a simple mission: to create joy through delicious, handcrafted cakes.</p>
                <p class="text-muted mb-4">What began as a small home bakery has grown into a beloved local destination for cake lovers. Every cake we create is made with love, using the finest ingredients and traditional baking methods passed down through generations.</p>
                <p class="text-muted mb-4">Our team of skilled bakers and decorators pour their hearts into every creation, ensuring that each cake not only tastes amazing but looks beautiful too. Whether it's a birthday celebration, wedding, or just a Tuesday treat, we're here to make your moments sweeter.</p>

                <div class="row mt-5">
                    <div class="col-4 text-center">
                        <h3 class="text-primary fw-bold">500+</h3>
                        <p class="text-muted">Happy Customers</p>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="text-primary fw-bold">50+</h3>
                        <p class="text-muted">Cake Flavors</p>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="text-primary fw-bold">4.9</h3>
                        <p class="text-muted">Customer Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Our Core Values</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-heart fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Made with Love</h4>
                        <p class="text-muted mb-0">Every cake is baked with passion and attention to detail, ensuring each bite brings a smile.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-soft-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-leaf fa-2x text-success"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Quality Ingredients</h4>
                        <p class="text-muted mb-0">We use only the finest, freshest ingredients. No preservatives, no shortcuts.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-soft-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-hand-holding-heart fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Customer First</h4>
                        <p class="text-muted mb-0">Your satisfaction is our priority. We work closely with you to create your dream cake.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Meet Our Team</h2>
            <p class="text-muted">The talented people behind your favorite cakes</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&size=128&background=ff6b8b&color=fff&length=2&font-size=0.50&bold=true&rounded=true"
                             alt="Sarah Johnson"
                             class="rounded-circle mb-4"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="fw-semibold mb-1">Sarah Johnson</h5>
                        <p class="text-primary mb-3">Head Baker</p>
                        <p class="text-muted small">With over 10 years of experience, Sarah creates magic in the kitchen.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://ui-avatars.com/api/?name=Michael+Chen&size=128&background=ff6b8b&color=fff&length=2&font-size=0.50&bold=true&rounded=true"
                             alt="Michael Chen"
                             class="rounded-circle mb-4"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="fw-semibold mb-1">Michael Chen</h5>
                        <p class="text-primary mb-3">Master Decorator</p>
                        <p class="text-muted small">Michael's artistic touch turns cakes into edible masterpieces.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://ui-avatars.com/api/?name=Emily+Rodriguez&size=128&background=ff6b8b&color=fff&length=2&font-size=0.50&bold=true&rounded=true"
                             alt="Emily Rodriguez"
                             class="rounded-circle mb-4"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="fw-semibold mb-1">Emily Rodriguez</h5>
                        <p class="text-primary mb-3">Customer Experience</p>
                        <p class="text-muted small">Emily ensures every customer gets exactly what they dreamed of.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">What Our Customers Say</h2>
            <p class="text-muted">Don't just take our word for it</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="text-muted mb-4">"The birthday cake I ordered was absolutely stunning and delicious! Everyone at the party couldn't stop raving about it."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=John+Smith&size=40&background=random&rounded=true"
                                 alt="John Smith"
                                 class="rounded-circle me-3">
                            <div>
                                <h6 class="fw-semibold mb-0">John Smith</h6>
                                <small class="text-muted">Happy Customer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="text-muted mb-4">"Our wedding cake was everything we dreamed of and more. The design was perfect and it tasted amazing!"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Jessica+Taylor&size=40&background=random&rounded=true"
                                 alt="Jessica Taylor"
                                 class="rounded-circle me-3">
                            <div>
                                <h6 class="fw-semibold mb-0">Jessica Taylor</h6>
                                <small class="text-muted">Bride</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="text-muted mb-4">"I order cupcakes for every office event. They're always fresh, beautiful, and delivered on time. Highly recommend!"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=David+Wilson&size=40&background=random&rounded=true"
                                 alt="David Wilson"
                                 class="rounded-circle me-3">
                            <div>
                                <h6 class="fw-semibold mb-0">David Wilson</h6>
                                <small class="text-muted">Regular Customer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
