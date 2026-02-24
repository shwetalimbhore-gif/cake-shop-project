@extends('layouts.front')

@section('title', $about->meta_title ?? 'Our Story - ' . setting('site_name'))
@section('meta_description', $about->meta_description ?? '')
@section('meta_keywords', $about->meta_keywords ?? '')

@section('content')
<!-- Hero Section -->
<section class="about-hero" @if($about->hero_image) style="background-image: url('{{ asset('storage/' . $about->hero_image) }}');" @endif>
    <div class="hero-overlay">
        <div class="container text-center">
            <h1 class="display-3 fw-bold text-white">{{ $about->hero_title ?? 'Our Story' }}</h1>
            <p class="lead text-white">{{ $about->hero_subtitle ?? 'The journey of our bakery' }}</p>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                @if($about->story_image)
                    <img src="{{ asset('storage/' . $about->story_image) }}"
                         alt="{{ $about->story_title }}"
                         class="img-fluid rounded-4 shadow">
                @else
                    <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b"
                         alt="Our Bakery"
                         class="img-fluid rounded-4 shadow">
                @endif
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-subtitle">{{ $about->story_tagline ?? 'Since ' . ($about->story_year ?? '2020') }}</span>
                <h2 class="section-title">{{ $about->story_title ?? 'Our Sweet Beginning' }}</h2>
                <p class="text-muted">{{ $about->story_content ?? '' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-up">
                <div class="card-modern p-5 text-center h-100">
                    <div class="mb-4">
                        <i class="fas fa-bullseye fa-3x" style="color: var(--terracotta);"></i>
                    </div>
                    <h3 class="mb-3">{{ $about->mission_title ?? 'Our Mission' }}</h3>
                    <p class="text-muted">{{ $about->mission_description ?? '' }}</p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card-modern p-5 text-center h-100">
                    <div class="mb-4">
                        <i class="fas fa-eye fa-3x" style="color: var(--terracotta);"></i>
                    </div>
                    <h3 class="mb-3">{{ $about->vision_title ?? 'Our Vision' }}</h3>
                    <p class="text-muted">{{ $about->vision_description ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
@if($about->values)
<section class="py-5">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Our Core Values</h2>
            <p class="section-description">The principles that guide everything we do</p>
        </div>
        <div class="row g-4">
            @foreach($about->values as $value)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card-modern p-4 text-center">
                    <div class="mb-3">
                        <i class="fas {{ $value['icon'] ?? 'fa-star' }} fa-3x" style="color: var(--terracotta);"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ $value['title'] }}</h5>
                    <p class="text-muted">{{ $value['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Statistics -->
@if($about->statistics)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            @foreach($about->statistics as $stat)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="text-center">
                    <h2 class="display-3 fw-bold" style="color: var(--terracotta);">{{ $stat['value'] }}{{ $stat['suffix'] }}</h2>
                    <p class="text-muted">{{ $stat['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Team Section -->
@if($about->team_members)
<section class="py-5">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">{{ $about->team_title ?? 'Meet Our Team' }}</h2>
            <p class="section-description">{{ $about->team_description ?? '' }}</p>
        </div>
        <div class="row g-4">
            @foreach($about->team_members as $member)
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card-modern text-center p-4">
                    @if(isset($member['image']) && !empty($member['image']))
                        <img src="{{ asset('storage/' . $member['image']) }}"
                             alt="{{ $member['name'] }}"
                             class="rounded-circle mb-4"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4"
                             style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $member['name'] }}</h5>
                    <p class="text-primary mb-2">{{ $member['position'] }}</p>
                    <p class="text-muted small">{{ $member['bio'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
.about-hero {
    height: 60vh;
    min-height: 500px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-subtitle {
    color: var(--taupe);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    display: block;
    margin-bottom: 10px;
}

.section-title {
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 20px;
}
</style>
@endsection
