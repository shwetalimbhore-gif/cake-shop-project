@extends('layouts.admin')

@section('title', 'Create Product - Admin Panel')
@section('page-title', 'Create New Product')

@section('content')
<div class="container-fluid px-0 px-md-2 px-lg-3">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Create New Product
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Form -->
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                <!-- Responsive Grid -->
                <div class="row g-4">
                    <!-- Left Column - Main Details (Full width on mobile, 8 cols on desktop) -->
                    <div class="col-xl-8 col-lg-7">
                        <!-- Basic Information Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Basic Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Product Name - Full width -->
                                    <div class="col-12">
                                        <label for="name" class="form-label fw-semibold">
                                            Product Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="Enter product name"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- SKU and Category - Stack on mobile, side by side on tablet+ -->
                                    <div class="col-md-6">
                                        <label for="sku" class="form-label fw-semibold">
                                            SKU <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('sku') is-invalid @enderror"
                                               id="sku"
                                               name="sku"
                                               value="{{ old('sku') }}"
                                               placeholder="e.g., CAKE-001"
                                               required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label fw-semibold">Category</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                                id="category_id"
                                                name="category_id">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Short Description -->
                                    <div class="col-12">
                                        <label for="short_description" class="form-label fw-semibold">Short Description</label>
                                        <textarea class="form-control @error('short_description') is-invalid @enderror"
                                                  id="short_description"
                                                  name="short_description"
                                                  rows="2"
                                                  placeholder="Brief description">{{ old('short_description') }}</textarea>
                                        @error('short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Full Description -->
                                    <div class="col-12">
                                        <label for="description" class="form-label fw-semibold">Full Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description"
                                                  name="description"
                                                  rows="4"
                                                  placeholder="Detailed product description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sizes Section -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-arrows-alt me-2 text-primary"></i>Sizes with Prices
                                </h6>
                                <span class="badge bg-info">Required</span>
                            </div>
                            <div class="card-body">
                                <div id="sizes-container">
                                    <!-- Size Row 1 - Responsive layout -->
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control"
                                                   placeholder="Size" value="6 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="29.99" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Size Row 2 -->
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control"
                                                   placeholder="Size" value="8 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="39.99" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Size Row 3 -->
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control"
                                                   placeholder="Size" value="10 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="49.99" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-size">
                                    <i class="fas fa-plus me-2"></i>Add Size
                                </button>
                            </div>
                        </div>

                        <!-- Flavors Section -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-ice-cream me-2 text-primary"></i>Flavors (Optional)
                                </h6>
                                <span class="badge bg-secondary">Optional</span>
                            </div>
                            <div class="card-body">
                                <div id="flavors-container">
                                    <!-- Flavor Row 1 -->
                                    <div class="row g-2 mb-2 flavor-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="flavors[]" class="form-control"
                                                   placeholder="Flavor" value="Chocolate">
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       placeholder="Extra Price" value="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Flavor Row 2 -->
                                    <div class="row g-2 mb-2 flavor-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="flavors[]" class="form-control"
                                                   placeholder="Flavor" value="Vanilla">
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       placeholder="Extra Price" value="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-flavor">
                                    <i class="fas fa-plus me-2"></i>Add Flavor
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Pricing & Settings (Full width on mobile, 4 cols on desktop) -->
                    <div class="col-xl-4 col-lg-5">
                        <!-- Pricing Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-tag me-2 text-primary"></i>Pricing
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Regular Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="regular_price"
                                               class="form-control" value="{{ old('regular_price') }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sale Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="sale_price"
                                               class="form-control" value="{{ old('sale_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-boxes me-2 text-primary"></i>Inventory
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Stock Quantity</label>
                                    <input type="number" name="stock_quantity"
                                           class="form-control" value="{{ old('stock_quantity', 0) }}" min="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Image Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-image me-2 text-primary"></i>Product Image
                                </h6>
                            </div>
                            <div class="card-body">
                                <input type="file" name="featured_image" class="form-control" accept="image/*">
                                <div class="mt-3 text-center" id="imagePreview" style="display: none;">
                                    <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-toggle-on me-2 text-primary"></i>Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1">
                                    <label class="form-check-label" for="is_eggless">Eggless</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary order-2 order-sm-1">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary order-1 order-sm-2">
                                <i class="fas fa-save me-2"></i>Save Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add Size
    document.getElementById('add-size')?.addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 mb-2 size-row align-items-center';
        newRow.innerHTML = `
            <div class="col-sm-5 col-12">
                <input type="text" name="sizes[]" class="form-control" placeholder="Size" required>
            </div>
            <div class="col-sm-5 col-8">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="size_prices[]" class="form-control" placeholder="Price" step="0.01" required>
                </div>
            </div>
            <div class="col-sm-2 col-4">
                <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    });

    // Add Flavor
    document.getElementById('add-flavor')?.addEventListener('click', function() {
        const container = document.getElementById('flavors-container');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 mb-2 flavor-row align-items-center';
        newRow.innerHTML = `
            <div class="col-sm-5 col-12">
                <input type="text" name="flavors[]" class="form-control" placeholder="Flavor">
            </div>
            <div class="col-sm-5 col-8">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="flavor_prices[]" class="form-control" placeholder="Extra Price" step="0.01">
                </div>
            </div>
            <div class="col-sm-2 col-4">
                <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    });

    // Remove handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-size')) {
            const row = e.target.closest('.size-row');
            if (document.querySelectorAll('.size-row').length > 1) {
                row.remove();
            }
        }
        if (e.target.closest('.remove-flavor')) {
            const row = e.target.closest('.flavor-row');
            if (document.querySelectorAll('.flavor-row').length > 1) {
                row.remove();
            }
        }
    });

    // Image Preview
    document.querySelector('input[name="featured_image"]')?.addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });
</script>
@endpush
@endsection
