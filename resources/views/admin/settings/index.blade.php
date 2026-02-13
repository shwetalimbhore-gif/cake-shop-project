@extends('layouts.admin')

@section('title', 'Site Settings - Admin Panel')
@section('page-title', 'Site Settings')

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
                    <a href="#contact" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-address-card text-success me-3" style="width: 20px;"></i>
                        <span>Contact Information</span>
                    </a>
                    <a href="#delivery" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-truck text-warning me-3" style="width: 20px;"></i>
                        <span>Delivery Settings</span>
                    </a>
                    <a href="#hours" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-clock text-info me-3" style="width: 20px;"></i>
                        <span>Opening Hours</span>
                    </a>
                    <a href="#social" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-share-alt text-danger me-3" style="width: 20px;"></i>
                        <span>Social Media</span>
                    </a>
                    <a href="#advanced" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-bs-toggle="pill">
                        <i class="fas fa-cogs text-secondary me-3" style="width: 20px;"></i>
                        <span>Advanced Settings</span>
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
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Site Name</label>
                                <input type="text" class="form-control" name="site_name"
                                       value="{{ setting('site_name') }}" required>
                                <div class="form-text">Your website name displayed in the header and browser tab</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Site Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="logo-preview bg-light rounded-3 p-3 text-center" style="width: 150px; height: 150px;">
                                        @if(setting('site_logo'))
                                            <img src="{{ asset('storage/' . setting('site_logo')) }}"
                                                 alt="Site Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" class="form-control" name="site_logo" accept="image/*">
                                        <div class="form-text mt-2">Recommended size: 200x50px (Max: 2MB)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Favicon</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="favicon-preview bg-light rounded-3 p-2" style="width: 32px; height: 32px;">
                                        @if(setting('site_favicon'))
                                            <img src="{{ asset('storage/' . setting('site_favicon')) }}"
                                                 alt="Favicon" style="width: 100%; height: 100%; object-fit: contain;">
                                        @else
                                            <i class="fas fa-star text-muted"></i>
                                        @endif
                                    </div>
                                    <input type="file" class="form-control" name="site_favicon" accept="image/x-icon,image/png">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea class="form-control" name="site_description" rows="3">{{ setting('site_description') }}</textarea>
                                <div class="form-text">Used for SEO - describe your website in 160 characters</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text" class="form-control" name="site_keywords"
                                       value="{{ setting('site_keywords') }}">
                                <div class="form-text">Comma-separated keywords for SEO</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Tax Rate (%)</label>
                                    <input type="number" class="form-control" name="tax_rate"
                                           value="{{ setting('tax_rate', '10') }}" step="0.01" min="0" max="100">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Currency</label>
                                    <select class="form-select" name="currency">
                                        <option value="USD" {{ setting('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ setting('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ setting('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="INR" {{ setting('currency') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save General Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="contact">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-address-card text-success me-2"></i>
                            Contact Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Contact Email</label>
                                    <input type="email" class="form-control" name="contact_email"
                                           value="{{ setting('contact_email') }}" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Contact Phone</label>
                                    <input type="text" class="form-control" name="contact_phone"
                                           value="{{ setting('contact_phone') }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea class="form-control" name="contact_address" rows="3">{{ setting('contact_address') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Google Maps Embed URL</label>
                                <input type="text" class="form-control" name="contact_map" value="{{ setting('contact_map') }}">
                                <div class="form-text">Paste the embed URL from Google Maps</div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Contact Info
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="delivery">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-truck text-warning me-2"></i>
                            Delivery Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">Delivery Charges</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                        <input type="number" step="0.01" class="form-control" name="delivery_charges"
                                               value="{{ setting('delivery_charges', '10.00') }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">Free Delivery Over</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                        <input type="number" step="0.01" class="form-control" name="free_delivery_threshold"
                                               value="{{ setting('free_delivery_threshold', '100.00') }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">Delivery Radius (km)</label>
                                    <input type="number" class="form-control" name="delivery_radius"
                                           value="{{ setting('delivery_radius', '20') }}" min="0">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Delivery Days</label>
                                <div class="row">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $selectedDays = json_decode(setting('delivery_days', '[]'), true) ?: $days;
                                    @endphp
                                    @foreach($days as $day)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="delivery_days[]"
                                                       value="{{ $day }}" id="day_{{ $day }}"
                                                       {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $day }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Delivery Time Slots</label>
                                <div id="timeSlots">
                                    @php
                                        $slots = json_decode(setting('delivery_time_slots', '[]'), true) ?: [
                                            '09:00-12:00', '12:00-15:00', '15:00-18:00', '18:00-21:00'
                                        ];
                                    @endphp
                                    @foreach($slots as $index => $slot)
                                        <div class="input-group mb-2 time-slot">
                                            <input type="text" class="form-control" name="delivery_time_slots[]"
                                                   value="{{ $slot }}" placeholder="HH:MM-HH:MM">
                                            <button class="btn btn-outline-danger remove-slot" type="button">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addTimeSlot">
                                    <i class="fas fa-plus me-2"></i>Add Time Slot
                                </button>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Delivery Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="hours">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-clock text-info me-2"></i>
                            Opening Hours
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            @php
                                $hours = json_decode(setting('opening_hours', '{}'), true);
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Opening Time</th>
                                            <th>Closing Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($days as $day)
                                            @php
                                                $time = $hours[$day] ?? '09:00-18:00';
                                                $parts = explode('-', $time);
                                                $open = $parts[0] ?? '09:00';
                                                $close = $parts[1] ?? '18:00';
                                                $isClosed = $time == 'Closed';
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $day }}</td>
                                                <td>
                                                    <input type="time" class="form-control"
                                                           name="opening_hours[{{ $day }}][open]"
                                                           value="{{ $open }}" {{ $isClosed ? 'disabled' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="time" class="form-control"
                                                           name="opening_hours[{{ $day }}][close]"
                                                           value="{{ $close }}" {{ $isClosed ? 'disabled' : '' }}>
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input day-toggle" type="checkbox"
                                                               data-day="{{ $day }}"
                                                               {{ !$isClosed ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            {{ $isClosed ? 'Closed' : 'Open' }}
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary px-4 mt-3">
                                <i class="fas fa-save me-2"></i>Save Opening Hours
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="social">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-share-alt text-danger me-2"></i>
                            Social Media Links
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                </label>
                                <input type="url" class="form-control" name="facebook_url"
                                       value="{{ setting('facebook_url') }}" placeholder="https://facebook.com/yourpage">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fab fa-instagram text-danger me-2"></i>Instagram URL
                                </label>
                                <input type="url" class="form-control" name="instagram_url"
                                       value="{{ setting('instagram_url') }}" placeholder="https://instagram.com/yourpage">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fab fa-twitter text-info me-2"></i>Twitter URL
                                </label>
                                <input type="url" class="form-control" name="twitter_url"
                                       value="{{ setting('twitter_url') }}" placeholder="https://twitter.com/yourpage">
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Social Links
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="advanced">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-cogs text-secondary me-2"></i>
                            Advanced Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Order Number Prefix</label>
                                <input type="text" class="form-control" name="order_prefix"
                                       value="{{ setting('order_prefix', 'ORD-') }}">
                                <div class="form-text">Example: ORD-2024-001</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_reviews"
                                           id="enable_reviews" value="1" {{ setting('enable_reviews', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="enable_reviews">
                                        Enable Product Reviews
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode"
                                           id="maintenance_mode" value="1" {{ setting('maintenance_mode', '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="maintenance_mode">
                                        Maintenance Mode
                                    </label>
                                </div>
                                <div class="form-text text-danger">When enabled, only admins can access the site</div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Advanced Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('addTimeSlot')?.addEventListener('click', function() {
        const container = document.getElementById('timeSlots');
        const newSlot = document.createElement('div');
        newSlot.className = 'input-group mb-2 time-slot';
        newSlot.innerHTML = `
            <input type="text" class="form-control" name="delivery_time_slots[]" placeholder="HH:MM-HH:MM">
            <button class="btn btn-outline-danger remove-slot" type="button">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newSlot);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slot') || e.target.closest('.remove-slot')) {
            const slot = e.target.closest('.time-slot');
            if (document.querySelectorAll('.time-slot').length > 1) {
                slot.remove();
            }
        }
    });

    document.querySelectorAll('.day-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const day = this.dataset.day;
            const openInput = document.querySelector(`[name="opening_hours[${day}][open]"]`);
            const closeInput = document.querySelector(`[name="opening_hours[${day}][close]"]`);
            const label = this.nextElementSibling;

            if (this.checked) {
                openInput.disabled = false;
                closeInput.disabled = false;
                label.textContent = 'Open';
            } else {
                openInput.disabled = true;
                closeInput.disabled = true;
                label.textContent = 'Closed';
            }
        });
    });
</script>
@endpush
@endsection
