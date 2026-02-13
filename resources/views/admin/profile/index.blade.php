@extends('layouts.admin')

@section('title', 'My Profile - Admin Panel')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    Profile Picture
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-4">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="{{ auth()->user()->name }}"
                             class="rounded-circle img-thumbnail border-3 border-primary"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto border-3 border-primary"
                             style="width: 150px; height: 150px; font-size: 64px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-2 border-white"
                          style="width: 20px; height: 20px;"></span>
                </div>

                <h4 class="fw-semibold">{{ auth()->user()->name }}</h4>
                <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                <span class="badge bg-primary px-3 py-2">
                    <i class="fas fa-shield-alt me-1"></i> Administrator
                </span>

                @if(auth()->user()->bio)
                    <p class="mt-4 mb-0 text-muted">{{ auth()->user()->bio }}</p>
                @endif

                <hr class="my-4">

                <div class="text-start">
                    <div class="d-flex mb-3">
                        <div class="text-muted" style="width: 100px;"><i class="fas fa-phone me-2"></i>Phone:</div>
                        <div class="fw-semibold">{{ auth()->user()->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="text-muted" style="width: 100px;"><i class="fas fa-calendar me-2"></i>Joined:</div>
                        <div class="fw-semibold">{{ auth()->user()->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="d-flex">
                        <div class="text-muted" style="width: 100px;"><i class="fas fa-clock me-2"></i>Last Login:</div>
                        <div class="fw-semibold">{{ auth()->user()->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Profile Information
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', auth()->user()->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', auth()->user()->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="avatar" class="form-label fw-semibold">Profile Picture</label>
                                <input type="file"
                                       class="form-control @error('avatar') is-invalid @enderror"
                                       id="avatar"
                                       name="avatar"
                                       accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="form-label fw-semibold">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror"
                                  id="bio"
                                  name="bio"
                                  rows="3"
                                  placeholder="Tell us a little about yourself...">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4" id="imagePreview" style="display: none;">
                        <label class="form-label fw-semibold">Preview</label>
                        <div class="border rounded-3 p-3 text-center bg-light">
                            <img src="" alt="Preview" style="max-width: 100%; max-height: 150px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-key text-warning me-2"></i>
                    Change Password
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label for="current_password" class="form-label fw-semibold">Current Password</label>
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="new_password" class="form-label fw-semibold">New Password</label>
                            <input type="password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password"
                                   name="new_password"
                                   required>
                            <div class="form-text">Minimum 8 characters</div>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="new_password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const previewImg = preview.querySelector('img');

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
            previewImg.src = '';
        }
    });
</script>
@endpush
@endsection
