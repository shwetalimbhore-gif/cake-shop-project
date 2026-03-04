@extends('layouts.front')

@section('title', $about->meta_title ?? 'Our Story - ' . setting('site_name'))
@section('meta_description', $about->meta_description ?? '')
@section('meta_keywords', $about->meta_keywords ?? '')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">{{ $about->hero_title ?? 'Our Story' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $about->hero_title ?? 'Our Story' }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hero Section -->
<section class="about-hero" @if($about->hero_image) style="background-image: url('{{ asset('storage/' . $about->hero_image) }}');" @endif>
    <div class="hero-overlay">
        <div class="container text-center">
            <h1 class="hero-title">{{ $about->hero_title ?? 'Our Story' }}</h1>
            <p class="hero-subtitle">{{ $about->hero_subtitle ?? 'The journey of our bakery' }}</p>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="story-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                @if($about->story_image)
                    <img src="{{ asset('storage/' . $about->story_image) }}"
                         alt="{{ $about->story_title }}"
                         class="img-fluid rounded-4 shadow-lg">
                @else
                    <img src="https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80"
                         alt="Our Bakery"
                         class="img-fluid rounded-4 shadow-lg">
                @endif
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                @if($about->story_tagline)
                    <span class="section-subtitle">{{ $about->story_tagline }}</span>
                @endif
                <h2 class="section-title">{{ $about->story_title ?? 'Our Sweet Beginning' }}</h2>
                <p class="story-text">{{ $about->story_content ?? '' }}</p>
                @if($about->story_year)
                    <div class="established-badge">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Established in {{ $about->story_year }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
@if($about->mission_title || $about->vision_title)
<section class="mission-vision-section py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            @if($about->mission_title)
            <div class="col-md-6" data-aos="fade-up">
                <div class="mission-card h-100">
                    <div class="card-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="card-title">{{ $about->mission_title }}</h3>
                    <p class="card-text">{{ $about->mission_description }}</p>
                    @if($about->mission_image)
                        <img src="{{ asset('storage/' . $about->mission_image) }}"
                             alt="Mission" class="mt-3 img-fluid rounded-3">
                    @endif
                </div>
            </div>
            @endif

            @if($about->vision_title)
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="vision-card h-100">
                    <div class="card-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="card-title">{{ $about->vision_title }}</h3>
                    <p class="card-text">{{ $about->vision_description }}</p>
                    @if($about->vision_image)
                        <img src="{{ asset('storage/' . $about->vision_image) }}"
                             alt="Vision" class="mt-3 img-fluid rounded-3">
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Core Values -->
@if($about->values)
<section class="values-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Our Core Values</h2>
            <p class="section-subtitle">The principles that guide everything we do</p>
        </div>

        <div class="row g-4">
            @foreach($about->values as $value)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="value-card text-center">
                    <div class="value-icon">
                        <i class="fas {{ $value['icon'] ?? 'fa-star' }}"></i>
                    </div>
                    <h4 class="value-title">{{ $value['title'] }}</h4>
                    <p class="value-text">{{ $value['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Statistics -->
@if($about->statistics)
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            @foreach($about->statistics as $stat)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="stat-card text-center">
                    <h2 class="stat-value">{{ $stat['value'] }}{{ $stat['suffix'] ?? '' }}</h2>
                    <p class="stat-label">{{ $stat['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Team Section -->
@if($about->team_members)
<section class="team-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">{{ $about->team_title ?? 'Meet Our Team' }}</h2>
            <p class="section-subtitle">{{ $about->team_description ?? 'The talented people behind your favorite cakes' }}</p>
        </div>

        <div class="row g-4">
            @foreach($about->team_members as $member)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="team-card text-center">
                    <div class="team-image-wrapper">
                        @if(isset($member['image']) && !empty($member['image']))
                            <img src="{{ asset('storage/' . $member['image']) }}"
                                 alt="{{ $member['name'] }}"
                                 class="team-image">
                        @else
                            <div class="team-image-placeholder">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="team-name">{{ $member['name'] }}</h4>
                    <p class="team-position">{{ $member['position'] }}</p>
                    <p class="team-bio">{{ $member['bio'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
/* ===== BREADCRUMB ===== */
.breadcrumb-modern {
    background: linear-gradient(135deg, var(--cream), var(--sand));
    padding: 50px 0 30px;
    border-bottom: 1px solid var(--sand);
}

.breadcrumb-modern h1 {
    font-family: 'Prata', serif;
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 10px;
}

/* ===== HERO SECTION ===== */
.about-hero {
    height: 60vh;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-title {
    font-family: 'Prata', serif;
    font-size: 3.5rem;
    color: white;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.5rem;
    color: white;
    opacity: 0.9;
}

/* ===== STORY SECTION ===== */
.story-section {
    padding: 80px 0;
}

.section-subtitle {
    display: block;
    color: var(--terracotta);
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin-bottom: 15px;
}

.section-title {
    font-family: 'Prata', serif;
    font-size: 2.5rem;
    color: var(--charcoal);
    margin-bottom: 25px;
}

.story-text {
    color: var(--charcoal);
    line-height: 1.8;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.established-badge {
    display: inline-block;
    padding: 10px 25px;
    background: var(--terracotta);
    color: white;
    border-radius: 50px;
    font-weight: 600;
}

/* ===== MISSION & VISION CARDS ===== */
.mission-card,
.vision-card {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--sand);
    transition: all 0.3s;
}

.mission-card:hover,
.vision-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.card-icon {
    width: 70px;
    height: 70px;
    background: var(--terracotta);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
    color: white;
    font-size: 1.8rem;
}

/* ===== VALUE CARDS ===== */
.value-card {
    background: white;
    border-radius: 20px;
    padding: 30px 25px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    transition: all 0.3s;
    height: 100%;
}

.value-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.value-icon {
    width: 60px;
    height: 60px;
    background: var(--cream);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: var(--terracotta);
    font-size: 1.5rem;
    transition: all 0.3s;
}

.value-card:hover .value-icon {
    background: var(--terracotta);
    color: white;
    transform: scale(1.1);
}

.value-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.value-text {
    color: var(--taupe);
    line-height: 1.6;
    margin: 0;
}

/* ===== STATISTICS CARDS ===== */
.stat-card {
    background: white;
    border-radius: 20px;
    padding: 40px 25px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    color: var(--terracotta);
    margin-bottom: 10px;
    line-height: 1.2;
}

.stat-label {
    color: var(--taupe);
    font-size: 1.1rem;
    margin: 0;
}

/* ===== TEAM CARDS ===== */
.team-card {
    background: white;
    border-radius: 20px;
    padding: 30px 25px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sand);
    transition: all 0.3s;
    height: 100%;
}

.team-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.team-image-wrapper {
    width: 150px;
    height: 150px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--terracotta);
}

.team-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-image-placeholder {
    width: 100%;
    height: 100%;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--taupe);
}

.team-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.team-position {
    color: var(--terracotta);
    font-weight: 500;
    margin-bottom: 15px;
}

.team-bio {
    color: var(--taupe);
    line-height: 1.6;
    margin: 0;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .stat-value {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .section-title {
        font-size: 1.8rem;
    }

    .team-image-wrapper {
        width: 120px;
        height: 120px;
    }
}
</style>
@endsection
