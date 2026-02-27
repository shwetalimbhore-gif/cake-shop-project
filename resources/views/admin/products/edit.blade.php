@extends('layouts.admin')

@section('title', 'Edit Product - Admin Panel')
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Product
                </h5>
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Left Column - Main Details -->
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Product Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">
                                            Product Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $product->name) }}"
                                               placeholder="Enter product name"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- SKU and Category -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sku" class="form-label fw-semibold">
                                                SKU <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control @error('sku') is-invalid @enderror"
                                                   id="sku"
                                                   name="sku"
                                                   value="{{ old('sku', $product->sku) }}"
                                                   placeholder="e.g., CAKE-BDAY-001"
                                                   required>
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="category_id" class="form-label fw-semibold">Category</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror"
                                                    id="category_id"
                                                    name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="mb-3">
                                        <label for="short_description" class="form-label fw-semibold">Short Description</label>
                                        <textarea class="form-control @error('short_description') is-invalid @enderror"
                                                  id="short_description"
                                                  name="short_description"
                                                  rows="2"
                                                  placeholder="Brief description (max 500 characters)">{{ old('short_description', $product->short_description) }}</textarea>
                                        @error('short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Full Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-semibold">Full Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description"
                                                  name="description"
                                                  rows="5"
                                                  placeholder="Detailed product description">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Sizes Section -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-arrows-alt me-2 text-primary"></i>Sizes with Prices
                                    </h6>
                                    <span class="badge bg-info">Required</span>
                                </div>
                                <div class="card-body">
                                    @php
                                        $sizes = json_decode($product->sizes, true) ?? ['6 inch', '8 inch', '10 inch'];
                                        $sizePrices = json_decode($product->size_prices, true) ?? [29.99, 39.99, 49.99];
                                    @endphp
                                    <div id="sizes-container">
                                        @foreach($sizes as $index => $size)
                                        <div class="row mb-2 size-row align-items-center">
                                            <div class="col-md-5">
                                                <input type="text" name="sizes[]" class="form-control"
                                                       placeholder="Size (e.g., 6 inch)"
                                                       value="{{ old('sizes.' . $index, $size) }}" required>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                                    <input type="number" name="size_prices[]" class="form-control"
                                                           placeholder="Price"
                                                           value="{{ old('size_prices.' . $index, $sizePrices[$index] ?? 29.99) }}"
                                                           step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                @if($loop->first)
                                                    <button class="btn btn-outline-danger remove-size" type="button" disabled>
                                                        <i class="fas fa-minus-circle"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-danger remove-size" type="button">
                                                        <i class="fas fa-minus-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-size">
                                        <i class="fas fa-plus me-2"></i>Add Another Size
                                    </button>
                                </div>
                            </div>

                            <!-- Flavors Section -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-ice-cream me-2 text-primary"></i>Flavors with Prices (Optional)
                                    </h6>
                                    <span class="badge bg-secondary">Optional</span>
                                </div>
                                <div class="card-body">
                                    @php
                                        $flavors = json_decode($product->flavors, true) ?? ['Chocolate', 'Vanilla', 'Strawberry'];
                                        $flavorPrices = json_decode($product->flavor_prices, true) ?? [0, 0, 2.00];
                                    @endphp
                                    <div id="flavors-container">
                                        @foreach($flavors as $index => $flavor)
                                        <div class="row mb-2 flavor-row align-items-center">
                                            <div class="col-md-5">
                                                <input type="text" name="flavors[]" class="form-control"
                                                       placeholder="Flavor (e.g., Chocolate)"
                                                       value="{{ old('flavors.' . $index, $flavor) }}">
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                                    <input type="number" name="flavor_prices[]" class="form-control"
                                                           placeholder="Extra Price"
                                                           value="{{ old('flavor_prices.' . $index, $flavorPrices[$index] ?? 0) }}"
                                                           step="0.01" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                @if($loop->first)
                                                    <button class="btn btn-outline-danger remove-flavor" type="button" disabled>
                                                        <i class="fas fa-minus-circle"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-danger remove-flavor" type="button">
                                                        <i class="fas fa-minus-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-flavor">
                                        <i class="fas fa-plus me-2"></i>Add Another Flavor
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Pricing & Settings -->
                        <div class="col-md-4">
                            <!-- Pricing Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Pricing</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="regular_price" class="form-label fw-semibold">
                                            Regular Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                            <input type="number" step="0.01"
                                                   class="form-control @error('regular_price') is-invalid @enderror"
                                                   id="regular_price"
                                                   name="regular_price"
                                                   value="{{ old('regular_price', $product->regular_price) }}"
                                                   required>
                                        </div>
                                        @error('regular_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label fw-semibold">Sale Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                            <input type="number" step="0.01"
                                                   class="form-control @error('sale_price') is-invalid @enderror"
                                                   id="sale_price"
                                                   name="sale_price"
                                                   value="{{ old('sale_price', $product->sale_price) }}">
                                        </div>
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Inventory</h6>
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
                                               value="{{ old('stock_quantity', $product->stock_quantity) }}"
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
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Product Image</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Current Image -->
                                    @if($product->featured_image)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Current Image</label>
                                        <div class="border rounded-3 p-3 text-center bg-light">
                                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="img-fluid"
                                                 style="max-height: 150px;">
                                        </div>
                                    </div>
                                    @endif

                                    <!-- New Image Upload -->
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label fw-semibold">New Image</label>
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
                                            Leave empty to keep current image. Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)
                                        </small>
                                    </div>

                                    <!-- New Image Preview -->
                                    <div class="mb-3" id="imagePreview" style="display: none;">
                                        <label class="form-label fw-semibold">New Image Preview</label>
                                        <div class="border rounded-3 p-3 text-center bg-light">
                                            <img src="" alt="Preview" class="img-fluid" style="max-height: 150px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Toggles Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">Product Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_active">
                                            <i class="fas fa-check-circle text-success me-1"></i>Active
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_featured">
                                            <i class="fas fa-crown text-warning me-1"></i>Featured Product
                                        </label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1"
                                            {{ old('is_eggless', $product->is_eggless) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_eggless">
                                            <i class="fas fa-leaf me-1" style="color: #2e7d32;"></i> Eggless Cake
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ===== SIZES MANAGEMENT =====
    document.getElementById('add-size')?.addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const newSizeRow = document.createElement('div');
        newSizeRow.className = 'row mb-2 size-row align-items-center';
        newSizeRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="sizes[]" class="form-control" placeholder="Size (e.g., New Size)" required>
            </div>
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                    <input type="number" name="size_prices[]" class="form-control" placeholder="Price" step="0.01" min="0" required>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-danger remove-size" type="button">
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
        newFlavorRow.className = 'row mb-2 flavor-row align-items-center';
        newFlavorRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="flavors[]" class="form-control" placeholder="Flavor (e.g., New Flavor)">
            </div>
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                    <input type="number" name="flavor_prices[]" class="form-control" placeholder="Extra Price" step="0.01" min="0">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-danger remove-flavor" type="button">
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
