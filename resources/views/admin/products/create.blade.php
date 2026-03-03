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
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
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

                <div class="row g-4">
                    <!-- Left Column - Main Details -->
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
                                    <!-- Product Name -->
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

                                    <!-- SKU and Category -->
                                    <div class="col-md-6">
                                        <label for="sku" class="form-label fw-semibold">
                                            SKU <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('sku') is-invalid @enderror"
                                               id="sku"
                                               name="sku"
                                               value="{{ old('sku') }}"
                                               placeholder="e.g., CAKE-BDAY-001"
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
                                                  placeholder="Brief description (max 500 characters)">{{ old('short_description') }}</textarea>
                                        @error('short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">This will appear in product listings</small>
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
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control form-control-sm"
                                                   placeholder="Size (e.g., 6 inch)" value="6 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="29.99" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button" disabled>
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control form-control-sm"
                                                   placeholder="Size (e.g., 8 inch)" value="8 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="39.99" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2 size-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="sizes[]" class="form-control form-control-sm"
                                                   placeholder="Size (e.g., 10 inch)" value="10 inch" required>
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       placeholder="Price" value="49.99" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="add-size">
                                    <i class="fas fa-plus me-2"></i>Add Another Size
                                </button>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Each size must have a unique price. The first size will be default.
                                </small>
                            </div>
                        </div>

                        <!-- Flavors Section -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-ice-cream me-2 text-primary"></i>Flavors with Prices (Optional)
                                </h6>
                                <span class="badge bg-secondary">Optional</span>
                            </div>
                            <div class="card-body">
                                <div id="flavors-container">
                                    <div class="row g-2 mb-2 flavor-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="flavors[]" class="form-control form-control-sm"
                                                   placeholder="Flavor (e.g., Chocolate)" value="Chocolate">
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       placeholder="Extra Price" value="0" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button" disabled>
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2 flavor-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="flavors[]" class="form-control form-control-sm"
                                                   placeholder="Flavor (e.g., Vanilla)" value="Vanilla">
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       placeholder="Extra Price" value="0" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2 flavor-row align-items-center">
                                        <div class="col-sm-5 col-12">
                                            <input type="text" name="flavors[]" class="form-control form-control-sm"
                                                   placeholder="Flavor (e.g., Strawberry)" value="Strawberry">
                                        </div>
                                        <div class="col-sm-5 col-8">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       placeholder="Extra Price" value="2.00" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="add-flavor">
                                    <i class="fas fa-plus me-2"></i>Add Another Flavor
                                </button>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Leave price as 0 if no extra cost. The first flavor will be default.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Pricing & Settings -->
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
                                    <label for="regular_price" class="form-label fw-semibold">
                                        Regular Price <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                        <input type="number" step="0.01"
                                               class="form-control @error('regular_price') is-invalid @enderror"
                                               id="regular_price"
                                               name="regular_price"
                                               value="{{ old('regular_price') }}"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    @error('regular_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sale_price" class="form-label fw-semibold">Sale Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                                        <input type="number" step="0.01"
                                               class="form-control @error('sale_price') is-invalid @enderror"
                                               id="sale_price"
                                               name="sale_price"
                                               value="{{ old('sale_price') }}"
                                               placeholder="0.00">
                                    </div>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Discounted price (leave empty if not on sale)</small>
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
                                    <label for="stock_quantity" class="form-label fw-semibold">
                                        Stock Quantity <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control @error('stock_quantity') is-invalid @enderror"
                                           id="stock_quantity"
                                           name="stock_quantity"
                                           value="{{ old('stock_quantity', 0) }}"
                                           min="0"
                                           required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Product Image Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-image me-2 text-primary"></i>Product Image
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label fw-semibold">Featured Image</label>
                                    <input type="file"
                                           class="form-control @error('featured_image') is-invalid @enderror"
                                           id="featured_image"
                                           name="featured_image"
                                           accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)
                                    </small>
                                </div>

                                <!-- Image Preview -->
                                <div class="mb-3 text-center" id="imagePreview" style="display: none;">
                                    <label class="form-label fw-semibold">Preview</label>
                                    <div class="border rounded-3 p-3 bg-light">
                                        <img src="" alt="Preview" class="img-fluid" style="max-height: 150px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Toggles Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-toggle-on me-2 text-primary"></i>Product Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">
                                        <i class="fas fa-check-circle text-success me-1"></i>Active
                                    </label>
                                    <small class="text-muted d-block">Product will be visible on the website</small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                    <label class="form-check-label fw-semibold" for="is_featured">
                                        <i class="fas fa-crown text-warning me-1"></i>Featured Product
                                    </label>
                                    <small class="text-muted d-block">Show on homepage and featured sections</small>
                                </div>

                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1">
                                    <label class="form-check-label fw-semibold" for="is_eggless">
                                        <i class="fas fa-leaf me-1" style="color: #2e7d32;"></i> Eggless Cake
                                    </label>
                                    <small class="text-muted d-block">Check if cake contains no eggs</small>
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
    // ===== SIZES MANAGEMENT =====
    document.getElementById('add-size')?.addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const newSizeRow = document.createElement('div');
        newSizeRow.className = 'row g-2 mb-2 size-row align-items-center';
        newSizeRow.innerHTML = `
            <div class="col-sm-5 col-12">
                <input type="text" name="sizes[]" class="form-control form-control-sm" placeholder="Size (e.g., New Size)" required>
            </div>
            <div class="col-sm-5 col-8">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                    <input type="number" name="size_prices[]" class="form-control" placeholder="Price" step="0.01" min="0" required>
                </div>
            </div>
            <div class="col-sm-2 col-4">
                <button class="btn btn-outline-danger btn-sm w-100 remove-size" type="button">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </div>
        `;
        container.appendChild(newSizeRow);
    });

    // ===== FLAVORS MANAGEMENT =====
    document.getElementById('add-flavor')?.addEventListener('click', function() {
        const container = document.getElementById('flavors-container');
        const newFlavorRow = document.createElement('div');
        newFlavorRow.className = 'row g-2 mb-2 flavor-row align-items-center';
        newFlavorRow.innerHTML = `
            <div class="col-sm-5 col-12">
                <input type="text" name="flavors[]" class="form-control form-control-sm" placeholder="Flavor (e.g., New Flavor)">
            </div>
            <div class="col-sm-5 col-8">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">{{ setting('currency_symbol', '₹') }}</span>
                    <input type="number" name="flavor_prices[]" class="form-control" placeholder="Extra Price" step="0.01" min="0">
                </div>
            </div>
            <div class="col-sm-2 col-4">
                <button class="btn btn-outline-danger btn-sm w-100 remove-flavor" type="button">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </div>
        `;
        container.appendChild(newFlavorRow);
    });

    // ===== REMOVE SIZE =====
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-size')) {
            const sizeRow = e.target.closest('.size-row');
            if (document.querySelectorAll('.size-row').length > 1) {
                sizeRow.remove();
            } else {
                alert('You need at least one size option');
            }
        }
    });

    // ===== REMOVE FLAVOR =====
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-flavor')) {
            const flavorRow = e.target.closest('.flavor-row');
            if (document.querySelectorAll('.flavor-row').length > 1) {
                flavorRow.remove();
            } else {
                alert('You need at least one flavor option');
            }
        }
    });

    // ===== IMAGE PREVIEW =====
    document.getElementById('featured_image').addEventListener('change', function(e) {
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

    // ===== VALIDATE SALE PRICE =====
    document.getElementById('sale_price').addEventListener('input', function() {
        const regularPrice = parseFloat(document.getElementById('regular_price').value) || 0;
        const salePrice = parseFloat(this.value) || 0;

        if (salePrice > regularPrice) {
            this.setCustomValidity('Sale price cannot be greater than regular price');
        } else {
            this.setCustomValidity('');
        }
    });

    document.getElementById('regular_price').addEventListener('input', function() {
        const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
        const regularPrice = parseFloat(this.value) || 0;

        if (salePrice > regularPrice) {
            document.getElementById('sale_price').setCustomValidity('Sale price cannot be greater than regular price');
        } else {
            document.getElementById('sale_price').setCustomValidity('');
        }
    });
</script>
@endpush
@endsection
