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

                    <!-- Basic Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 10) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- REAL-WORLD PRICING CARD -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Pricing (Real Bakery Style)</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Set prices like real bakeries - different sizes have different prices, flavors can have extra charges.
                            </div>

                            <!-- Size Pricing (REAL-WORLD EXAMPLE) -->
                            <h6 class="fw-bold mb-3">Size Options & Pricing</h6>
                            <p class="text-muted small mb-3">Example: Small 6" = $29, Medium 8" = $39, Large 10" = $49</p>

                            <div id="sizes-container">
                                <!-- Default size rows like real bakery websites -->
                                <div class="row mb-3 size-row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Size Name</label>
                                        <input type="text" name="sizes[]" class="form-control" value="6 inch (Serves 4-6)" placeholder="e.g., 6 inch">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" name="size_prices[]" class="form-control" value="29.99" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Servings</label>
                                        <input type="text" name="size_servings[]" class="form-control" value="4-6" placeholder="e.g., 4-6">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-danger remove-size" type="button" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3 size-row align-items-end">
                                    <div class="col-md-4">
                                        <input type="text" name="sizes[]" class="form-control" value="8 inch (Serves 8-10)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="size_prices[]" class="form-control" value="39.99" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="size_servings[]" class="form-control" value="8-10">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-danger remove-size" type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3 size-row align-items-end">
                                    <div class="col-md-4">
                                        <input type="text" name="sizes[]" class="form-control" value="10 inch (Serves 12-14)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="size_prices[]" class="form-control" value="49.99" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="size_servings[]" class="form-control" value="12-14">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-danger remove-size" type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-size">
                                <i class="fas fa-plus me-2"></i>Add Another Size
                            </button>

                            <hr class="my-4">

                            <!-- Flavor Pricing (REAL-WORLD EXAMPLE) -->
                            <h6 class="fw-bold mb-3">Flavor Options & Surcharges</h6>
                            <p class="text-muted small mb-3">Example: Vanilla = $0, Chocolate = $2 extra, Premium flavors = $5 extra</p>

                            <div id="flavors-container">
                                <div class="row mb-3 flavor-row align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Flavor Name</label>
                                        <input type="text" name="flavors[]" class="form-control" value="Vanilla" placeholder="e.g., Vanilla">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Extra Cost ($)</label>
                                        <input type="number" name="flavor_prices[]" class="form-control" value="0" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-outline-danger remove-flavor w-100" type="button" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3 flavor-row align-items-end">
                                    <div class="col-md-5">
                                        <input type="text" name="flavors[]" class="form-control" value="Chocolate">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="flavor_prices[]" class="form-control" value="2.00" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-danger remove-flavor w-100" type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3 flavor-row align-items-end">
                                    <div class="col-md-5">
                                        <input type="text" name="flavors[]" class="form-control" value="Red Velvet (Premium)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="flavor_prices[]" class="form-control" value="5.00" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-danger remove-flavor w-100" type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-flavor">
                                <i class="fas fa-plus me-2"></i>Add Another Flavor
                            </button>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Description</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Image Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Product Image</h6>
                        </div>
                        <div class="card-body">
                            <input type="file" name="featured_image" class="form-control" accept="image/*">
                            <div class="mt-3" id="imagePreview" style="display: none;">
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Active (Visible on website)</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                <label class="form-check-label" for="is_featured">Featured (Show on homepage)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_eggless" name="is_eggless" value="1">
                                <label class="form-check-label" for="is_eggless">Eggless Cake</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add Size
    document.getElementById('add-size').addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 size-row align-items-end';
        newRow.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Size Name</label>
                <input type="text" name="sizes[]" class="form-control" placeholder="e.g., 6 inch">
            </div>
            <div class="col-md-3">
                <label class="form-label">Price ($)</label>
                <input type="number" name="size_prices[]" class="form-control" step="0.01" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Servings</label>
                <input type="text" name="size_servings[]" class="form-control" placeholder="e.g., 4-6">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-danger remove-size w-100" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    });

    // Add Flavor
    document.getElementById('add-flavor').addEventListener('click', function() {
        const container = document.getElementById('flavors-container');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 flavor-row align-items-end';
        newRow.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Flavor Name</label>
                <input type="text" name="flavors[]" class="form-control" placeholder="e.g., Chocolate">
            </div>
            <div class="col-md-4">
                <label class="form-label">Extra Cost ($)</label>
                <input type="number" name="flavor_prices[]" class="form-control" step="0.01" min="0">
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-danger remove-flavor w-100" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    });

    // Remove Size
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-size')) {
            const row = e.target.closest('.size-row');
            if (document.querySelectorAll('.size-row').length > 1) {
                row.remove();
            }
        }
    });

    // Remove Flavor
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-flavor')) {
            const row = e.target.closest('.flavor-row');
            if (document.querySelectorAll('.flavor-row').length > 1) {
                row.remove();
            }
        }
    });

    // Image Preview
    document.querySelector('input[name="featured_image"]').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endpush
@endsection
