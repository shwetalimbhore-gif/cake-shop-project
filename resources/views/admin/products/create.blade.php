@extends('layouts.admin')

@section('title', 'Create Product - Admin Panel')
@section('page-title', 'Create New Product')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    Create New Product
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SKU and Category -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sku" class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                           id="sku" name="sku" value="{{ old('sku') }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
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

                            <!-- Short Description -->
                            <div class="mb-3">
                                <label for="short_description" class="form-label fw-semibold">Short Description</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror"
                                          id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Full Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Full Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Prices -->
                            <div class="mb-3">
                                <label for="regular_price" class="form-label fw-semibold">Regular Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                    <input type="number" step="0.01" class="form-control @error('regular_price') is-invalid @enderror"
                                           id="regular_price" name="regular_price" value="{{ old('regular_price') }}" required>
                                </div>
                                @error('regular_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sale_price" class="form-label fw-semibold">Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ setting('currency_symbol', '$') }}</span>
                                    <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                           id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                                </div>
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Featured Image -->
                            <div class="mb-3">
                                <label for="featured_image" class="form-label fw-semibold">Featured Image</label>
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror"
                                       id="featured_image" name="featured_image" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                            </div>

                            <!-- Image Preview -->
                            <div class="mb-3" id="imagePreview" style="display: none;">
                                <label class="form-label fw-semibold">Preview</label>
                                <div class="border rounded-3 p-3 text-center bg-light">
                                    <img src="" alt="Preview" style="max-width: 100%; max-height: 150px;">
                                </div>
                            </div>

                            <!-- ===== EGG-EGGLESS TOGGLES ===== -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Dietary Options</label>

                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                    <label class="form-check-label fw-semibold" for="is_featured">Featured Product</label>
                                </div>

                                <!-- EGG-EGGLESS OPTION -->
                                <div class="form-check form-switch mt-3 pt-2 border-top">
                                    <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1">
                                    <label class="form-check-label fw-semibold" for="is_eggless">
                                        <i class="fas fa-leaf me-1" style="color: #2e7d32;"></i>
                                        <span style="color: #2e7d32;">Eggless Cake</span>
                                    </label>
                                    <small class="text-muted d-block mt-1">Check this if the cake contains NO eggs</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
</script>
@endpush
@endsection
