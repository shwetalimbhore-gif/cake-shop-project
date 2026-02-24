@extends('layouts.admin')

@section('title', 'Manage About Us - Admin Panel')
@section('page-title', 'Manage About Us Page')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-edit text-primary me-2"></i>
            Edit About Us Content
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.about.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-4" id="aboutTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button">Hero Section</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="story-tab" data-bs-toggle="tab" data-bs-target="#story" type="button">Our Story</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button">Mission & Vision</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="values-tab" data-bs-toggle="tab" data-bs-target="#values" type="button">Core Values</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button">Team</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button">Statistics</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button">SEO</button>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Hero Section -->
                <div class="tab-pane fade show active" id="hero" role="tabpanel">
                    <h5 class="mb-4">Hero Section</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control"
                                   value="{{ old('hero_title', $about->hero_title ?? 'Our Story') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" class="form-control"
                                   value="{{ old('hero_subtitle', $about->hero_subtitle ?? 'The journey of our bakery') }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Hero Image</label>
                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                            @if($about->hero_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $about->hero_image) }}"
                                         alt="Hero Image" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Story Section -->
                <div class="tab-pane fade" id="story" role="tabpanel">
                    <h5 class="mb-4">Our Story</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Story Title</label>
                            <input type="text" name="story_title" class="form-control"
                                   value="{{ old('story_title', $about->story_title ?? 'Our Sweet Beginning') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Established Year</label>
                            <input type="text" name="story_year" class="form-control"
                                   value="{{ old('story_year', $about->story_year ?? '2020') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Tagline</label>
                            <input type="text" name="story_tagline" class="form-control"
                                   value="{{ old('story_tagline', $about->story_tagline ?? 'Where every cake tells a story') }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Story Content</label>
                            <textarea name="story_content" class="form-control" rows="5">{{ old('story_content', $about->story_content ?? '') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Story Image</label>
                            <input type="file" name="story_image" class="form-control" accept="image/*">
                            @if($about->story_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $about->story_image) }}"
                                         alt="Story Image" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mission & Vision -->
                <div class="tab-pane fade" id="mission" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">Mission</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mission Title</label>
                                <input type="text" name="mission_title" class="form-control"
                                       value="{{ old('mission_title', $about->mission_title ?? 'Our Mission') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mission Description</label>
                                <textarea name="mission_description" class="form-control" rows="4">{{ old('mission_description', $about->mission_description ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mission Image</label>
                                <input type="file" name="mission_image" class="form-control" accept="image/*">
                                @if($about->mission_image)
                                    <img src="{{ asset('storage/' . $about->mission_image) }}"
                                         alt="Mission Image" style="max-height: 100px;" class="mt-2">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-4">Vision</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vision Title</label>
                                <input type="text" name="vision_title" class="form-control"
                                       value="{{ old('vision_title', $about->vision_title ?? 'Our Vision') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vision Description</label>
                                <textarea name="vision_description" class="form-control" rows="4">{{ old('vision_description', $about->vision_description ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vision Image</label>
                                <input type="file" name="vision_image" class="form-control" accept="image/*">
                                @if($about->vision_image)
                                    <img src="{{ asset('storage/' . $about->vision_image) }}"
                                         alt="Vision Image" style="max-height: 100px;" class="mt-2">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Values -->
                <div class="tab-pane fade" id="values" role="tabpanel">
                    <h5 class="mb-4">Core Values</h5>
                    <div id="values-container">
                        @php
                            $values = old('values', $about->values ?? [
                                ['title' => 'Quality', 'description' => 'We use only the finest ingredients', 'icon' => 'fa-star'],
                                ['title' => 'Passion', 'description' => 'Baked with love every day', 'icon' => 'fa-heart'],
                                ['title' => 'Tradition', 'description' => 'Family recipes since 1950', 'icon' => 'fa-leaf'],
                            ]);
                        @endphp

                        @foreach($values as $index => $value)
                        <div class="card mb-3 value-item">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <input type="text" name="values[{{ $index }}][title]"
                                               class="form-control" placeholder="Value Title"
                                               value="{{ $value['title'] }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <input type="text" name="values[{{ $index }}][icon]"
                                               class="form-control" placeholder="Icon (e.g., fa-heart)"
                                               value="{{ $value['icon'] }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <input type="text" name="values[{{ $index }}][description]"
                                               class="form-control" placeholder="Description"
                                               value="{{ $value['description'] }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-value">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary" id="add-value">
                        <i class="fas fa-plus me-2"></i>Add Value
                    </button>
                </div>

                <!-- Team Section -->
                <div class="tab-pane fade" id="team" role="tabpanel">
                    <h5 class="mb-4">Team Section</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Title</label>
                        <input type="text" name="team_title" class="form-control"
                               value="{{ old('team_title', $about->team_title ?? 'Meet Our Team') }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Team Description</label>
                        <textarea name="team_description" class="form-control" rows="3">{{ old('team_description', $about->team_description ?? 'The talented people behind your favorite cakes') }}</textarea>
                    </div>

                    <h5 class="mb-4">Team Members</h5>
                    <div id="team-container">
                        @php
                            $teamMembers = old('team_members', $about->team_members ?? [
                                ['name' => 'Sarah Johnson', 'position' => 'Head Baker', 'bio' => '10+ years experience'],
                                ['name' => 'Michael Chen', 'position' => 'Master Decorator', 'bio' => 'Award-winning artist'],
                            ]);
                        @endphp

                        @foreach($teamMembers as $index => $member)
                        <div class="card mb-3 team-member">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <input type="text" name="team_members[{{ $index }}][name]"
                                               class="form-control" placeholder="Name"
                                               value="{{ $member['name'] }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <input type="text" name="team_members[{{ $index }}][position]"
                                               class="form-control" placeholder="Position"
                                               value="{{ $member['position'] }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <input type="text" name="team_members[{{ $index }}][bio]"
                                               class="form-control" placeholder="Bio"
                                               value="{{ $member['bio'] }}">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="file" name="team_members[{{ $index }}][image]"
                                               class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        @if(isset($member['image']) && !empty($member['image']))
                                            <img src="{{ asset('storage/' . $member['image']) }}"
                                                 alt="{{ $member['name'] }}" style="max-height: 50px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary" id="add-team">
                        <i class="fas fa-plus me-2"></i>Add Team Member
                    </button>
                </div>

                <!-- Statistics -->
                <div class="tab-pane fade" id="stats" role="tabpanel">
                    <h5 class="mb-4">Statistics</h5>
                    <div id="stats-container">
                        @php
                            $statistics = old('statistics', $about->statistics ?? [
                                ['label' => 'Years of Experience', 'value' => '70', 'suffix' => '+'],
                                ['label' => 'Happy Customers', 'value' => '5000', 'suffix' => '+'],
                                ['label' => 'Cake Flavors', 'value' => '50', 'suffix' => '+'],
                            ]);
                        @endphp

                        @foreach($statistics as $index => $stat)
                        <div class="card mb-3 stat-item">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <input type="text" name="statistics[{{ $index }}][label]"
                                               class="form-control" placeholder="Label (e.g., Years)"
                                               value="{{ $stat['label'] }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <input type="text" name="statistics[{{ $index }}][value]"
                                               class="form-control" placeholder="Value (e.g., 70)"
                                               value="{{ $stat['value'] }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <input type="text" name="statistics[{{ $index }}][suffix]"
                                               class="form-control" placeholder="Suffix (e.g., +)"
                                               value="{{ $stat['suffix'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-stat">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary" id="add-stat">
                        <i class="fas fa-plus me-2"></i>Add Statistic
                    </button>
                </div>

                <!-- SEO -->
                <div class="tab-pane fade" id="seo" role="tabpanel">
                    <h5 class="mb-4">SEO Settings</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="{{ old('meta_title', $about->meta_title) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $about->meta_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control"
                               value="{{ old('meta_keywords', $about->meta_keywords) }}"
                               placeholder="cake, bakery, artisan">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save All Changes
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Add Value
    document.getElementById('add-value').addEventListener('click', function() {
        const container = document.getElementById('values-container');
        const index = container.children.length;
        const html = `
            <div class="card mb-3 value-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="values[${index}][title]" class="form-control" placeholder="Value Title">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="values[${index}][icon]" class="form-control" placeholder="Icon (e.g., fa-heart)">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="text" name="values[${index}][description]" class="form-control" placeholder="Description">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-value">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Add Team Member
    document.getElementById('add-team').addEventListener('click', function() {
        const container = document.getElementById('team-container');
        const index = container.children.length;
        const html = `
            <div class="card mb-3 team-member">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="team_members[${index}][name]" class="form-control" placeholder="Name">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="text" name="team_members[${index}][position]" class="form-control" placeholder="Position">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="team_members[${index}][bio]" class="form-control" placeholder="Bio">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="file" name="team_members[${index}][image]" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Add Statistic
    document.getElementById('add-stat').addEventListener('click', function() {
        const container = document.getElementById('stats-container');
        const index = container.children.length;
        const html = `
            <div class="card mb-3 stat-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="statistics[${index}][label]" class="form-control" placeholder="Label">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="text" name="statistics[${index}][value]" class="form-control" placeholder="Value">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="text" name="statistics[${index}][suffix]" class="form-control" placeholder="Suffix">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-stat">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Remove items
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-value') || e.target.closest('.remove-value')) {
            e.target.closest('.value-item').remove();
        }
        if (e.target.classList.contains('remove-stat') || e.target.closest('.remove-stat')) {
            e.target.closest('.stat-item').remove();
        }
    });
</script>
@endpush
@endsection
