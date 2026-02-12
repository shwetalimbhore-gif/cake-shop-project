@extends('layouts.admin')

@section('title', 'Edit Product - Admin Panel')
@section('page-title', 'Edit Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Product: {{ $product->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror"
                                          id="short_description" name="short_description" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
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
                                               id="regular_price" name="regular_price" value="{{ old('regular_price', $product->regular_price) }}" required>
                                        @error('regular_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price ($)</label>
                                        <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                               id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
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
                                               id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
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
                                    @php
                                        $selectedSizes = $product->sizes ?? [];
                                    @endphp
                                    <option value="small" {{ in_array('small', $selectedSizes) ? 'selected' : '' }}>Small (6 inch)</option>
                                    <option value="medium" {{ in_array('medium', $selectedSizes) ? 'selected' : '' }}>Medium (8 inch)</option>
                                    <option value="large" {{ in_array('large', $selectedSizes) ? 'selected' : '' }}>Large (10 inch)</option>
                                    <option value="xl" {{ in_array('xl', $selectedSizes) ? 'selected' : '' }}>Extra Large (12 inch)</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple sizes</small>
                            </div>

                            <div class="mb-3">
                                <label for="flavors" class="form-label">Available Flavors</label>
                                <select class="form-select" id="flavors" name="flavors[]" multiple>
                                    @php
                                        $selectedFlavors = $product->flavors ?? [];
                                    @endphp
                                    <option value="vanilla" {{ in_array('vanilla', $selectedFlavors) ? 'selected' : '' }}>Vanilla</option>
                                    <option value="chocolate" {{ in_array('chocolate', $selectedFlavors) ? 'selected' : '' }}>Chocolate</option>
                                    <option value="strawberry" {{ in_array('strawberry', $selectedFlavors) ? 'selected' : '' }}>Strawberry</option>
                                    <option value="red_velvet" {{ in_array('red_velvet', $selectedFlavors) ? 'selected' : '' }}>Red Velvet</option>
                                    <option value="lemon" {{ in_array('lemon', $selectedFlavors) ? 'selected' : '' }}>Lemon</option>
                                    <option value="coffee" {{ in_array('coffee', $selectedFlavors) ? 'selected' : '' }}>Coffee</option>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                            @if($product->featured_image)
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('storage/' . $product->featured_image) }}"
                                         alt="{{ $product->name }}"
                                         class="img-thumbnail mb-2"
                                         style="max-width: 100%; max-height: 200px; object-fit: contain;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remove_featured_image" name="remove_featured_image">
                                        <label class="form-check-label text-danger" for="remove_featured_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="featured_image" class="form-label">
                                    {{ $product->featured_image ? 'Change Image' : 'Upload Image' }}
                                </label>
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror"
                                       id="featured_image" name="featured_image" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max size: 2MB. JPG, PNG, GIF supported.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Images -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Gallery Images</h6>
                        </div>
                        <div class="card-body">
                            @if($product->gallery_images && count($product->gallery_images) > 0)
                                <div class="row g-2 mb-3" id="existing-gallery">
                                    @foreach($product->gallery_images as $index => $image)
                                        <div class="col-6" id="gallery-image-{{ $index }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     class="img-thumbnail"
                                                     style="width: 100%; height: 100px; object-fit: cover;">
                                                <button type="button"
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                        onclick="removeGalleryImage({{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_gallery_images[]" value="{{ $image }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mb-3">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="remove_all_gallery" name="remove_all_gallery">
                                        <span class="text-danger">Remove all gallery images</span>
                                    </label>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="gallery_images" class="form-label">Add More Images</label>
                                <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror"
                                       id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                                @error('gallery_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can select multiple images. Max size: 2MB each.</small>
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
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active (Visible in store)</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured Product</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Meta -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Product Information</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <strong>Created:</strong>
                                    {{ $product->created_at ? $product->created_at->format('M d, Y H:i') : 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>Last Updated:</strong>
                                    {{ $product->updated_at ? $product->updated_at->format('M d, Y H:i') : 'N/A' }}
                                </li>
                                <li>
                                    <strong>Product ID:</strong>
                                    {{ $product->id }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
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
                                <i class="fas fa-save me-2"></i>Update Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete confirmation for new gallery images -->
<div class="modal fade" id="removeGalleryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Gallery Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this image?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveImage">Remove</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// CKEditor for description
CKEDITOR.replace('description', {
    height: 300,
    toolbar: [
        { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
        { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
        { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
        '/',
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
        { name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
        '/',
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
        { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
        { name: 'about', items: ['About'] }
    ]
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

// Preview featured image before upload
document.getElementById('featured_image').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Create or update preview
            var preview = document.getElementById('featured-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'featured-preview';
                preview.className = 'img-thumbnail mb-2';
                preview.style.maxWidth = '100%';
                preview.style.maxHeight = '200px';
                preview.style.objectFit = 'contain';
                document.getElementById('featured_image').parentNode.insertBefore(preview, document.getElementById('featured_image'));
            }
            preview.src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Preview multiple gallery images
document.getElementById('gallery_images').addEventListener('change', function(e) {
    var previewContainer = document.getElementById('gallery-preview');
    if (!previewContainer) {
        previewContainer = document.createElement('div');
        previewContainer.id = 'gallery-preview';
        previewContainer.className = 'row g-2 mt-2';
        document.getElementById('gallery_images').parentNode.appendChild(previewContainer);
    }

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

// Remove gallery image
var imageIndexToRemove = null;

function removeGalleryImage(index) {
    imageIndexToRemove = index;
    $('#removeGalleryModal').modal('show');
}

document.getElementById('confirmRemoveImage').addEventListener('click', function() {
    if (imageIndexToRemove !== null) {
        var element = document.getElementById('gallery-image-' + imageIndexToRemove);
        if (element) {
            element.remove();

            // Add hidden input to mark this image for removal
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_gallery_images[]';
            input.value = imageIndexToRemove;
            document.querySelector('form').appendChild(input);
        }
    }
    $('#removeGalleryModal').modal('hide');
});

// Remove all gallery images checkbox
document.getElementById('remove_all_gallery')?.addEventListener('change', function() {
    var existingGallery = document.getElementById('existing-gallery');
    if (existingGallery) {
        if (this.checked) {
            existingGallery.style.opacity = '0.5';
        } else {
            existingGallery.style.opacity = '1';
        }
    }
});

// Remove featured image checkbox
document.getElementById('remove_featured_image')?.addEventListener('change', function() {
    var featuredPreview = document.querySelector('img[alt="{{ $product->name }}"]');
    if (featuredPreview) {
        if (this.checked) {
            featuredPreview.style.opacity = '0.5';
        } else {
            featuredPreview.style.opacity = '1';
        }
    }
});

// Confirm before leaving if form is dirty
var formChanged = false;

document.querySelector('form').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
});

document.querySelector('form').addEventListener('submit', function() {
    formChanged = false;
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endsection
