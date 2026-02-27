@extends('layouts.front')

@section('title', 'Change Password - ' . setting('site_name'))
@section('page-title', 'Change Password')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-modern">
    <div class="container">
        <h1 class="fw-bold">Change Password</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}">My Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                    <a href="{{ route('account.profile') }}" class="sidebar-menu-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="{{ route('account.password') }}" class="sidebar-menu-item active">
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

        <!-- Password Content -->
        <div class="col-lg-9">
            <div class="profile-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="profile-header">
                    <h4 class="profile-title">Change Password</h4>
                    <p class="profile-subtitle">Ensure your account is secure with a strong password</p>
                </div>

                <form action="{{ route('account.password.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-4">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password"
                               class="form-control @error('new_password') is-invalid @enderror"
                               required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation"
                               class="form-control"
                               required>
                    </div>

                    <!-- Password Requirements -->
                    <div class="password-requirements mb-4">
                        <h6 class="requirements-title">Password Requirements:</h6>
                        <ul class="requirements-list">
                            <li><i class="fas fa-circle"></i> At least 8 characters long</li>
                            <li><i class="fas fa-circle"></i> Include at least one uppercase letter</li>
                            <li><i class="fas fa-circle"></i> Include at least one number</li>
                            <li><i class="fas fa-circle"></i> Include at least one special character</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-key me-2"></i>Update Password
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
.password-requirements {
    background: var(--cream);
    padding: 20px;
    border-radius: 12px;
}

.requirements-title {
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 10px;
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirements-list li {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.requirements-list li i {
    font-size: 0.5rem;
    color: var(--terracotta);
    margin-right: 8px;
    vertical-align: middle;
}
</style>
@endsection
