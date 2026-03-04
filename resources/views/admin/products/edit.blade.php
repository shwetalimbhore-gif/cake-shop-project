@extends('layouts.admin')

@section('title', 'Edit Product - Admin Panel')
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
<div class="container-fluid px-0 px-md-2 px-lg-3">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Product: {{ $product->name }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
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
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('PUT')

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
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Product Name</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name', $product->name) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">SKU</label>
                                        <input type="text" name="sku" class="form-control"
                                               value="{{ old('sku', $product->sku) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Category</label>
                                        <select name="category_id" class="form-select">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Short Description</label>
                                        <textarea name="short_description" class="form-control"
                                                  rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Full Description</label>
                                        <textarea name="description" class="form-control"
                                                  rows="4">{{ old('description', $product->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sizes Section with Working Delete Buttons -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-arrows-alt me-2 text-primary"></i>Sizes with Prices
                                </h6>
                                <span class="badge bg-info">Required</span>
                            </div>
                            <div class="card-body">
                                <div id="sizes-container">
                                    @php
                                        $sizes = json_decode($product->sizes, true) ?? ['6 inch (6-8 servings)', '8 inch (10-12 servings)', '10 inch (14-16 servings)'];
                                        $sizePrices = json_decode($product->size_prices, true) ?? [29.99, 39.99, 49.99];
                                    @endphp
                                    @foreach($sizes as $index => $size)
                                    <div class="row g-2 mb-3 size-row align-items-end">
                                        <div class="col-md-5 col-12">
                                            <label class="form-label small">Size Name</label>
                                            <input type="text" name="sizes[]" class="form-control"
                                                   value="{{ $size }}" placeholder="e.g., 6 inch" required>
                                        </div>
                                        <div class="col-md-4 col-8">
                                            <label class="form-label small">Price (₹)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" name="size_prices[]" class="form-control"
                                                       value="{{ $sizePrices[$index] ?? 29.99 }}" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-4">
                                            <label class="form-label small">&nbsp;</label>
                                            @if($loop->first)
                                                <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger w-100 remove-size">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-size">
                                    <i class="fas fa-plus me-2"></i>Add Size
                                </button>
                            </div>
                        </div>

                        <!-- Flavors Section with Working Delete Buttons -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-ice-cream me-2 text-primary"></i>Flavors (Optional)
                                </h6>
                                <span class="badge bg-secondary">Optional</span>
                            </div>
                            <div class="card-body">
                                <div id="flavors-container">
                                    @php
                                        $flavors = json_decode($product->flavors, true) ?? ['Chocolate', 'Double Chocolate', 'Strawberry'];
                                        $flavorPrices = json_decode($product->flavor_prices, true) ?? [0, 2.00, 1.50];
                                    @endphp
                                    @foreach($flavors as $index => $flavor)
                                    <div class="row g-2 mb-3 flavor-row align-items-end">
                                        <div class="col-md-5 col-12">
                                            <label class="form-label small">Flavor Name</label>
                                            <input type="text" name="flavors[]" class="form-control"
                                                   value="{{ $flavor }}" placeholder="e.g., Chocolate">
                                        </div>
                                        <div class="col-md-4 col-8">
                                            <label class="form-label small">Extra Price (₹)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" name="flavor_prices[]" class="form-control"
                                                       value="{{ $flavorPrices[$index] ?? 0 }}" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-4">
                                            <label class="form-label small">&nbsp;</label>
                                            @if($loop->first)
                                                <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger w-100 remove-flavor">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-flavor">
                                    <i class="fas fa-plus me-2"></i>Add Flavor
                                </button>
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
                                    <label class="form-label fw-semibold">Regular Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" name="regular_price"
                                               class="form-control" value="{{ old('regular_price', $product->regular_price) }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sale Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" name="sale_price"
                                               class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
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
                                           class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Image Card with Working Upload -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-image me-2 text-primary"></i>Product Image
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($product->featured_image)
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('storage/' . $product->featured_image) }}"
                                             alt="Current" class="img-fluid rounded border" style="max-height: 150px;">
                                        <div class="mt-2">
                                            <small class="text-muted">Current Image</small>
                                        </div>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Upload New Image</label>
                                    <input type="file" name="featured_image" class="form-control" accept="image/*" id="featured_image">
                                    <small class="text-muted">Leave empty to keep current image. Max: 2MB</small>
                                </div>
                                <div class="mt-3 text-center" id="imagePreview" style="display: none;">
                                    <h6 class="fw-semibold">Preview</h6>
                                    <img src="" alt="Preview" class="img-fluid rounded border" style="max-height: 150px;">
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
                                <div class="mb-3">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1"
                                            {{ old('is_eggless', $product->is_eggless) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_eggless">Eggless</label>
                                    </div>
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
                                <i class="fas fa-save me-2"></i>Update Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    }

    // ===== ADD NEW SIZE =====
    $('#add-size').click(function(e) {
        e.preventDefault();

        const newRow = `
            <div class="row g-2 mb-3 size-row align-items-end">
                <div class="col-md-5 col-12">
                    <label class="form-label small">Size Name</label>
                    <input type="text" name="sizes[]" class="form-control" placeholder="e.g., New Size" required>
                </div>
                <div class="col-md-4 col-8">
                    <label class="form-label small">Price (₹)</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="size_prices[]" class="form-control" placeholder="Price" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-3 col-4">
                    <label class="form-label small">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger w-100 remove-size">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        `;

        $('#sizes-container').append(newRow);

        if (typeof toastr !== 'undefined') {
            toastr.success('New size option added');
        }
    });

    // ===== ADD NEW FLAVOR =====
    $('#add-flavor').click(function(e) {
        e.preventDefault();

        const newRow = `
            <div class="row g-2 mb-3 flavor-row align-items-end">
                <div class="col-md-5 col-12">
                    <label class="form-label small">Flavor Name</label>
                    <input type="text" name="flavors[]" class="form-control" placeholder="e.g., New Flavor">
                </div>
                <div class="col-md-4 col-8">
                    <label class="form-label small">Extra Price ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="flavor_prices[]" class="form-control" placeholder="Extra Price" step="0.01">
                    </div>
                </div>
                <div class="col-md-3 col-4">
                    <label class="form-label small">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger w-100 remove-flavor">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        `;

        $('#flavors-container').append(newRow);

        if (typeof toastr !== 'undefined') {
            toastr.success('New flavor option added');
        }
    });

    // ===== DELETE SIZE =====
    $(document).on('click', '.remove-size', function(e) {
        e.preventDefault();

        const row = $(this).closest('.size-row');

        if ($('.size-row').length <= 1) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('You need at least one size option');
            } else {
                alert('You need at least one size option');
            }
            return;
        }

        if (confirm('Are you sure you want to delete this size option?')) {
            row.fadeOut(300, function() {
                $(this).remove();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Size option removed');
                }
            });
        }
    });

    // ===== DELETE FLAVOR =====
    $(document).on('click', '.remove-flavor', function(e) {
        e.preventDefault();

        const row = $(this).closest('.flavor-row');

        if ($('.flavor-row').length <= 1) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('You need at least one flavor option');
            } else {
                alert('You need at least one flavor option');
            }
            return;
        }

        if (confirm('Are you sure you want to delete this flavor option?')) {
            row.fadeOut(300, function() {
                $(this).remove();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Flavor option removed');
                }
            });
        }
    });

    // ===== IMAGE PREVIEW =====
    $('#featured_image').change(function() {
        const file = this.files[0];
        const preview = $('#imagePreview');
        const previewImg = preview.find('img');

        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('File size must be less than 2MB');
                } else {
                    alert('File size must be less than 2MB');
                }
                this.value = '';
                preview.hide();
                return;
            }

            // Validate file type
            const fileType = file.type;
            if (!fileType.match('image.*')) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select an image file');
                } else {
                    alert('Please select an image file');
                }
                this.value = '';
                preview.hide();
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.attr('src', e.target.result);
                preview.fadeIn();
            }
            reader.readAsDataURL(file);
        } else {
            preview.fadeOut();
        }
    });

    // ===== FORM VALIDATION BEFORE SUBMIT =====
    $('#productForm').submit(function(e) {
        let isValid = true;
        let errorMessage = '';

        // Check if at least one size exists
        if ($('.size-row').length === 0) {
            isValid = false;
            errorMessage = 'Please add at least one size option';
        }

        // Check if all size names are filled
        $('input[name="sizes[]"]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                errorMessage = 'Please fill in all size names';
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Check if all size prices are filled
        $('input[name="size_prices[]"]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                errorMessage = 'Please fill in all size prices';
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMessage);
            } else {
                alert(errorMessage);
            }
        }
    });

    // ===== SHOW FLASH MESSAGES =====
    @if(session('success'))
        if (typeof toastr !== 'undefined') {
            toastr.success('{{ session('success') }}');
        } else {
            alert('{{ session('success') }}');
        }
    @endif

    @if(session('error'))
        if (typeof toastr !== 'undefined') {
            toastr.error('{{ session('error') }}');
        } else {
            alert('{{ session('error') }}');
        }
    @endif
});
</script>
@endsection
