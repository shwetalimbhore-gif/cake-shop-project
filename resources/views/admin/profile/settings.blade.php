@extends('layouts.admin')

@section('title', 'Settings - Admin Panel')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-3">
                    <a href="#general" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-globe text-primary me-3" style="width: 20px;"></i>
                        <span>General Settings</span>
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-bell text-success me-3" style="width: 20px;"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-shield-alt text-warning me-3" style="width: 20px;"></i>
                        <span>Security</span>
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-palette text-info me-3" style="width: 20px;"></i>
                        <span>Preferences</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="general">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-globe text-primary me-2"></i>
                            General Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="timezone" class="form-label fw-semibold">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    @foreach($timezones as $timezone)
                                        <option value="{{ $timezone }}"
                                            {{ ($user->settings['timezone'] ?? 'America/New_York') == $timezone ? 'selected' : '' }}>
                                            {{ $timezone }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select your preferred timezone for date/time display</div>
                            </div>

                            <div class="mb-4">
                                <label for="language" class="form-label fw-semibold">Language</label>
                                <select class="form-select" id="language" name="language">
                                    @foreach($languages as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ ($user->settings['language'] ?? 'en') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="date_format" class="form-label fw-semibold">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="M d, Y" {{ ($user->settings['date_format'] ?? 'M d, Y') == 'M d, Y' ? 'selected' : '' }}>Jan 15, 2024</option>
                                    <option value="d/m/Y" {{ ($user->settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>15/01/2024</option>
                                    <option value="m/d/Y" {{ ($user->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>01/15/2024</option>
                                    <option value="Y-m-d" {{ ($user->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>2024-01-15</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="notifications">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bell text-success me-2"></i>
                            Notification Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="notification_email" class="form-label fw-semibold">Notification Email</label>
                                <input type="email"
                                       class="form-control"
                                       id="notification_email"
                                       name="notification_email"
                                       value="{{ $user->settings['notification_email'] ?? $user->email }}">
                                <div class="form-text">Email address for order notifications and alerts</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Email Notifications</label>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="email_orders"
                                           name="email_orders"
                                           value="1"
                                           {{ isset($user->settings['email_orders']) && $user->settings['email_orders'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_orders">
                                        New Order Notifications
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="email_low_stock"
                                           name="email_low_stock"
                                           value="1"
                                           {{ isset($user->settings['email_low_stock']) && $user->settings['email_low_stock'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_low_stock">
                                        Low Stock Alerts
                                    </label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="email_customer"
                                           name="email_customer"
                                           value="1"
                                           {{ isset($user->settings['email_customer']) && $user->settings['email_customer'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_customer">
                                        Customer Messages
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="security">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-shield-alt text-warning me-2"></i>
                            Security Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="new_password" class="form-label fw-semibold">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                       id="new_password" name="new_password" required>
                                <div class="form-text">Minimum 8 characters with at least one number and one uppercase letter</div>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" class="form-control"
                                       id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="two_factor" name="two_factor"
                                           {{ isset($user->settings['two_factor']) && $user->settings['two_factor'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="two_factor">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>
                                <div class="form-text">Add an extra layer of security to your account</div>
                            </div>

                            <button type="submit" class="btn btn-warning px-4">
                                <i class="fas fa-key me-2"></i>Update Security
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="preferences">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-palette text-info me-2"></i>
                            Preferences
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Dashboard Layout</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="dashboard_layout"
                                                   id="layout_default"
                                                   value="default"
                                                   {{ ($user->settings['dashboard_layout'] ?? 'default') == 'default' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layout_default">
                                                Default
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="dashboard_layout"
                                                   id="layout_compact"
                                                   value="compact"
                                                   {{ ($user->settings['dashboard_layout'] ?? '') == 'compact' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layout_compact">
                                                Compact
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="dashboard_layout"
                                                   id="layout_expanded"
                                                   value="expanded"
                                                   {{ ($user->settings['dashboard_layout'] ?? '') == 'expanded' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layout_expanded">
                                                Expanded
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="dark_mode"
                                           name="dark_mode"
                                           value="1"
                                           {{ isset($user->settings['dark_mode']) && $user->settings['dark_mode'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="dark_mode">
                                        Enable Dark Mode
                                    </label>
                                </div>
                                <div class="form-text">Switch to dark theme for better night viewing</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="compact_sidebar"
                                           name="compact_sidebar"
                                           value="1"
                                           {{ isset($user->settings['compact_sidebar']) && $user->settings['compact_sidebar'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="compact_sidebar">
                                        Compact Sidebar
                                    </label>
                                </div>
                                <div class="form-text">Show smaller icons and less padding in sidebar</div>
                            </div>

                            <div class="mb-4">
                                <label for="items_per_page" class="form-label fw-semibold">Items Per Page</label>
                                <select class="form-select" id="items_per_page" name="items_per_page">
                                    <option value="15" {{ ($user->settings['items_per_page'] ?? 15) == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ ($user->settings['items_per_page'] ?? '') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($user->settings['items_per_page'] ?? '') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($user->settings['items_per_page'] ?? '') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div class="form-text">Number of items to show per page in tables</div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
