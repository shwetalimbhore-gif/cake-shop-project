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

        <!-- Password Change Content -->
        <div class="col-lg-9">
            <div class="profile-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="profile-header">
                    <h4 class="profile-title">Change Password</h4>
                    <p class="profile-subtitle">Ensure your account is secure with a strong password</p>
                </div>

                <form action="{{ route('account.password.update') }}" method="POST" class="profile-form" id="passwordForm">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="current_password"
                                   id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter your current password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="new_password"
                                   id="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   placeholder="Enter new password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="new_password_confirmation"
                                   id="new_password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm new password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength Meter -->
                    <div class="password-strength mb-4">
                        <div class="strength-meter">
                            <div class="strength-meter-fill" id="password-strength-fill"></div>
                        </div>
                        <div class="strength-text" id="password-strength-text">
                            Enter a password
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="password-requirements mb-4">
                        <h6 class="requirements-title">
                            <i class="fas fa-shield-alt me-2" style="color: var(--terracotta);"></i>
                            Password Requirements:
                        </h6>
                        <ul class="requirements-list">
                            <li id="req-length">
                                <i class="fas fa-circle"></i> At least 8 characters long
                            </li>
                            <li id="req-uppercase">
                                <i class="fas fa-circle"></i> Include at least one uppercase letter
                            </li>
                            <li id="req-lowercase">
                                <i class="fas fa-circle"></i> Include at least one lowercase letter
                            </li>
                            <li id="req-number">
                                <i class="fas fa-circle"></i> Include at least one number
                            </li>
                            <li id="req-special">
                                <i class="fas fa-circle"></i> Include at least one special character
                            </li>
                        </ul>
                    </div>

                    <!-- Password Match Indicator -->
                    <div class="password-match mb-4" id="password-match" style="display: none;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="password-match-text">Passwords match</span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save" id="submitBtn">
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
    width: 100%;
    text-align: left;
}

.sidebar-menu-item i {
    width: 24px;
    color: var(--taupe);
    transition: all 0.3s;
    margin-right: 10px;
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

/* ===== FORM STYLES ===== */
.form-label {
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
}

.input-group {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    transition: all 0.3s;
}

.input-group:focus-within {
    box-shadow: 0 5px 15px rgba(201, 124, 93, 0.1);
}

.input-group-text {
    background: white;
    border: 1px solid var(--sand);
    border-right: none;
    color: var(--taupe);
    padding: 0.75rem 1.2rem;
}

.form-control {
    border: 1px solid var(--sand);
    border-left: none;
    border-right: none;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: var(--terracotta);
    box-shadow: none;
    outline: none;
}

.toggle-password {
    border: 1px solid var(--sand);
    border-left: none;
    background: white;
    color: var(--taupe);
    padding: 0.75rem 1.2rem;
}

.toggle-password:hover {
    color: var(--terracotta);
    background: white;
}

/* ===== PASSWORD STRENGTH METER ===== */
.password-strength {
    margin-top: 10px;
}

.strength-meter {
    height: 4px;
    background: var(--sand);
    border-radius: 2px;
    margin-bottom: 5px;
    overflow: hidden;
}

.strength-meter-fill {
    height: 100%;
    width: 0;
    border-radius: 2px;
    transition: all 0.3s;
}

.strength-text {
    font-size: 0.85rem;
    color: var(--taupe);
}

/* ===== PASSWORD REQUIREMENTS ===== */
.password-requirements {
    background: var(--cream);
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
}

.requirements-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 15px;
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirements-list li {
    color: var(--taupe);
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
}

.requirements-list li i {
    font-size: 0.5rem;
    color: var(--taupe);
    transition: all 0.3s;
}

.requirements-list li.valid {
    color: #2e7d32;
}

.requirements-list li.valid i {
    color: #2e7d32;
    content: "\f00c";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    font-size: 0.8rem;
}

/* ===== PASSWORD MATCH ===== */
.password-match {
    padding: 10px 15px;
    background: #e8f5e9;
    color: #2e7d32;
    border-radius: 8px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.password-match i {
    font-size: 1rem;
}

/* ===== ALERTS ===== */
.alert {
    border: none;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
}

.alert-danger {
    background: #ffebee;
    color: #c62828;
}

/* ===== FORM ACTIONS ===== */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
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
    display: inline-flex;
    align-items: center;
}

.btn-save:hover {
    background: #b86a4a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-save:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
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
    display: inline-flex;
    align-items: center;
}

.btn-cancel:hover {
    background: var(--cream);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .profile-content {
        padding: 20px;
    }

    .profile-title {
        font-size: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-save, .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const password = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password Strength Checker
    const newPassword = document.getElementById('new_password');
    const strengthFill = document.getElementById('password-strength-fill');
    const strengthText = document.getElementById('password-strength-text');

    // Requirement elements
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    // Password match elements
    const confirmPassword = document.getElementById('new_password_confirmation');
    const passwordMatch = document.getElementById('password-match');
    const passwordMatchText = document.getElementById('password-match-text');

    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirement indicators
        reqLength.classList.toggle('valid', requirements.length);
        reqUppercase.classList.toggle('valid', requirements.uppercase);
        reqLowercase.classList.toggle('valid', requirements.lowercase);
        reqNumber.classList.toggle('valid', requirements.number);
        reqSpecial.classList.toggle('valid', requirements.special);

        // Calculate strength
        if (requirements.length) strength += 20;
        if (requirements.uppercase) strength += 20;
        if (requirements.lowercase) strength += 20;
        if (requirements.number) strength += 20;
        if (requirements.special) strength += 20;

        // Update strength meter
        strengthFill.style.width = strength + '%';

        if (strength === 0) {
            strengthFill.style.background = '#B7A69A';
            strengthText.textContent = 'Enter a password';
            strengthText.style.color = '#B7A69A';
        } else if (strength <= 40) {
            strengthFill.style.background = '#dc3545';
            strengthText.textContent = 'Weak password';
            strengthText.style.color = '#dc3545';
        } else if (strength <= 60) {
            strengthFill.style.background = '#ffc107';
            strengthText.textContent = 'Fair password';
            strengthText.style.color = '#b45f06';
        } else if (strength <= 80) {
            strengthFill.style.background = '#17a2b8';
            strengthText.textContent = 'Good password';
            strengthText.style.color = '#0d47a1';
        } else {
            strengthFill.style.background = '#28a745';
            strengthText.textContent = 'Strong password';
            strengthText.style.color = '#1b5e20';
        }

        return requirements;
    }

    // Check password match
    function checkPasswordMatch() {
        if (confirmPassword.value) {
            if (newPassword.value === confirmPassword.value) {
                passwordMatch.style.display = 'flex';
                passwordMatch.className = 'password-match';
                passwordMatchText.textContent = 'Passwords match';
                return true;
            } else {
                passwordMatch.style.display = 'flex';
                passwordMatch.className = 'password-match';
                passwordMatch.style.background = '#ffebee';
                passwordMatch.style.color = '#c62828';
                passwordMatchText.textContent = 'Passwords do not match';
                return false;
            }
        } else {
            passwordMatch.style.display = 'none';
            return false;
        }
    }

    // Event listeners
    newPassword.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        if (confirmPassword.value) {
            checkPasswordMatch();
        }
    });

    confirmPassword.addEventListener('input', checkPasswordMatch);

    // Form submission validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const requirements = checkPasswordStrength(newPassword.value);
        const allValid = Object.values(requirements).every(Boolean);
        const passwordsMatch = newPassword.value === confirmPassword.value;

        if (!allValid || !passwordsMatch) {
            e.preventDefault();

            if (!allValid) {
                toastr.error('Please meet all password requirements');
            } else if (!passwordsMatch) {
                toastr.error('Passwords do not match');
            }
        } else {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;
        }
    });
</script>
@endsection
