@extends('layouts.front')

@section('title', 'My Profile - ' . setting('site_name'))
@section('page-title', 'My Profile')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">My Profile</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}">My Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="account-sidebar">
                <div class="user-info text-center p-4">
                    <div class="user-avatar-wrapper mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="user-avatar">
                        @else
                            <div class="user-avatar-placeholder">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="user-name">{{ auth()->user()->name }}</h5>
                    <p class="user-email">{{ auth()->user()->email }}</p>
                    <span class="user-badge">Customer</span>
                </div>

                <div class="sidebar-menu">
                    <a href="{{ route('account.dashboard') }}" class="sidebar-menu-item">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}" class="sidebar-menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        My Orders
                    </a>
                    <a href="{{ route('account.profile') }}" class="sidebar-menu-item active">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="{{ route('account.password') }}" class="sidebar-menu-item">
                        <i class="fas fa-lock"></i>
                        Change Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-block">
                        @csrf
                        <button type="submit" class="sidebar-menu-item text-danger w-100 text-start border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="col-lg-9">
            <div class="profile-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="profile-header">
                    <h4 class="profile-title">Profile Information</h4>
                    <p class="profile-subtitle">Update your personal information</p>
                </div>

                <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Avatar Upload -->
                        <div class="col-12 mb-4">
                            <div class="avatar-upload-section">
                                <div class="current-avatar">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                             alt="Avatar"
                                             id="avatarPreview">
                                    @else
                                        <div class="avatar-placeholder" id="avatarPreview">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="avatar-upload-controls">
                                    <label for="avatar" class="btn-upload">
                                        <i class="fas fa-camera me-2"></i>Change Photo
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                                    <p class="upload-hint">JPG, PNG or GIF (Max. 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $user->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- State -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                                   value="{{ old('state', $user->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ZIP Code -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror"
                                   value="{{ old('zip', $user->zip) }}">
                            @error('zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                   value="{{ old('country', $user->country ?? 'USA') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('account.dashboard') }}" class="btn-cancel">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ACCOUNT SIDEBAR ===== */
.account-sidebar {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.user-info {
    background: linear-gradient(135deg, var(--cream), var(--sand));
}

.user-avatar-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.user-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-sm);
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--terracotta);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 600;
    border: 4px solid white;
    box-shadow: var(--shadow-sm);
}

.user-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.user-email {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.user-badge {
    display: inline-block;
    padding: 4px 12px;
    background: var(--cream);
    color: var(--terracotta);
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 500;
}

.sidebar-menu {
    padding: 15px;
}

.sidebar-menu-item {
    display: block;
    padding: 12px 15px;
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s;
    margin-bottom: 5px;
}

.sidebar-menu-item i {
    width: 24px;
    color: var(--taupe);
    transition: all 0.3s;
}

.sidebar-menu-item:hover {
    background: var(--cream);
    color: var(--terracotta);
    transform: translateX(5px);
}

.sidebar-menu-item:hover i {
    color: var(--terracotta);
}

.sidebar-menu-item.active {
    background: var(--terracotta);
    color: white;
}

.sidebar-menu-item.active i {
    color: white;
}

/* ===== PROFILE CONTENT ===== */
.profile-content {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: var(--shadow-sm);
}

.profile-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--sand);
}

.profile-title {
    font-family: 'Prata', serif;
    font-size: 1.8rem;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.profile-subtitle {
    color: var(--taupe);
    font-size: 1rem;
}

/* ===== AVATAR UPLOAD ===== */
.avatar-upload-section {
    display: flex;
    align-items: center;
    gap: 30px;
    padding: 20px;
    background: var(--cream);
    border-radius: 16px;
}

.current-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
}

.current-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: var(--terracotta);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 600;
}

.btn-upload {
    display: inline-block;
    padding: 10px 25px;
    background: var(--terracotta);
    color: white;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}

.btn-upload:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.upload-hint {
    color: var(--taupe);
    font-size: 0.8rem;
    margin-top: 10px;
}

/* ===== FORM STYLES ===== */
.profile-form .form-label {
    font-weight: 500;
    color: var(--charcoal);
    margin-bottom: 5px;
}

.profile-form .form-control {
    padding: 12px 15px;
    border: 1px solid var(--sand);
    border-radius: 12px;
    transition: all 0.3s;
}

.profile-form .form-control:focus {
    border-color: var(--terracotta);
    box-shadow: 0 0 0 3px rgba(201, 124, 93, 0.1);
    outline: none;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--sand);
}

.btn-save {
    padding: 12px 30px;
    background: var(--terracotta);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-cancel {
    padding: 12px 30px;
    background: transparent;
    color: var(--charcoal);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
    border: 1px solid var(--sand);
}

.btn-cancel:hover {
    background: var(--cream);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .avatar-upload-section {
        flex-direction: column;
        text-align: center;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-save, .btn-cancel {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
    // Preview avatar before upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.id = 'avatarPreview';
                    img.className = 'user-avatar';
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endsection
