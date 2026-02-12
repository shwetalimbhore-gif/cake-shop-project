@extends('layouts.admin')

@section('title', 'Add New Product - Admin Panel')
@section('page-title', 'Add New Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Product Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror"
                                          id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Stock -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Pricing & Stock</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="regular_price" class="form-label">Regular Price ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('regular_price') is-invalid @enderror"
                                               id="regular_price" name="regular_price" value="{{ old('regular_price') }}" required>
                                        @error('regular_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price ($)</label>
                                        <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                               id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                               id="sku" name="sku" value="{{ old('sku') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required>
                                        @error('stock_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cake Options -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Cake Options</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="sizes" class="form-label">Available Sizes</label>
                                <select class="form-select" id="sizes" name="sizes[]" multiple>
                                    <option value="small">Small (6 inch)</option>
                                    <option value="medium">Medium (8 inch)</option>
                                    <option value="large">Large (10 inch)</option>
                                    <option value="xl">Extra Large (12 inch)</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple sizes</small>
                            </div>

                            <div class="mb-3">
                                <label for="flavors" class="form-label">Available Flavors</label>
                                <select class="form-select" id="flavors" name="flavors[]" multiple>
                                    <option value="vanilla">Vanilla</option>
                                    <option value="chocolate">Chocolate</option>
                                    <option value="strawberry">Strawberry</option>
                                    <option value="red_velvet">Red Velvet</option>
                                    <option value="lemon">Lemon</option>
                                    <option value="coffee">Coffee</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple flavors</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Category -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Category</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id" required>
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
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Featured Image</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Upload Image</label>
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror"
                                       id="featured_image" name="featured_image" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max size: 2MB. JPG, PNG, GIF supported.</small>
                                <div id="featured-preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Images -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Gallery Images</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="gallery_images" class="form-label">Upload Multiple Images</label>
                                <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror"
                                       id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                                @error('gallery_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can select multiple images. Max size: 2MB each.</small>
                                <div id="gallery-preview" class="row g-2 mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Status -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Product Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active (Visible in store)</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                    <label class="form-check-label" for="is_featured">Featured Product</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM ACTIONS - THIS ADDS THE SAVE BUTTON -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
                        </a>
                        <div>
                            <button type="reset" class="btn btn-warning me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END FORM ACTIONS -->

        </form>
    </div>
</div>

<!-- Image Preview Script -->
@section('scripts')
<script>
// Preview featured image before upload
document.getElementById('featured_image').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('featured-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 100%; max-height: 200px; object-fit: contain;">';
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Preview multiple gallery images
document.getElementById('gallery_images').addEventListener('change', function(e) {
    var previewContainer = document.getElementById('gallery-preview');
    previewContainer.innerHTML = '';

    for (var i = 0; i < e.target.files.length; i++) {
        (function(file) {
            var reader = new FileReader();
            var col = document.createElement('div');
            col.className = 'col-4';

            reader.onload = function(e) {
                col.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">';
            }
            reader.readAsDataURL(file);
            previewContainer.appendChild(col);
        })(e.target.files[i]);
    }
});

// Price validation
document.getElementById('sale_price').addEventListener('change', function() {
    var regularPrice = parseFloat(document.getElementById('regular_price').value) || 0;
    var salePrice = parseFloat(this.value) || 0;

    if (salePrice >= regularPrice) {
        alert('Sale price must be less than regular price');
        this.value = '';
    }
});
</script>
@endsection

@endsection
