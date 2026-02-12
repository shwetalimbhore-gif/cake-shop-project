@extends('layouts.admin')

@section('title', 'Products - Admin Panel')
@section('page-title', 'Products')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Products</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock_status" class="form-select">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Bulk Actions -->
        <div class="mb-3">
            <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash me-2"></i>Delete Selected
            </button>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th width="80">Image</th>
                        <th>
                            <a href="{{ route('admin.products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                Name
                                @if(request('sort') == 'name')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>
                            @if($product->featured_image)
                                <img src="{{ asset('storage/' . $product->featured_image) }}"
                                     alt="{{ $product->name }}"
                                     class="img-thumbnail"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $product->name }}
                            @if($product->is_featured)
                                <span class="badge bg-warning ms-2">Featured</span>
                            @endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>
                            @if($product->sale_price)
                                <span class="text-decoration-line-through text-muted">${{ number_format($product->regular_price, 2) }}</span>
                                <br>
                                <span class="text-danger fw-bold">${{ number_format($product->sale_price, 2) }}</span>
                                <span class="badge bg-success ms-1">-{{ $product->discount_percentage }}%</span>
                            @else
                                ${{ number_format($product->regular_price, 2) }}
                            @endif
                        </td>
                        <td>
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success">{{ $product->stock_quantity }} in stock</span>
                            @else
                                <span class="badge bg-danger">Out of stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input status-toggle"
                                       data-id="{{ $product->id }}"
                                       {{ $product->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input featured-toggle"
                                       data-id="{{ $product->id }}"
                                       {{ $product->is_featured ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="btn btn-sm btn-info" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger delete-product"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No products found</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-3">
                                Add Your First Product
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
            </div>
            <div>
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="deleteProductName"></span>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Selected Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="selectedCount"></span> selected products?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.products.bulk-delete') }}">
                    @csrf
                    <input type="hidden" name="ids[]" id="bulkDeleteIds">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select All checkbox
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteButton();
    });

    // Individual checkbox change
    $(document).on('change', '.product-checkbox', function() {
        toggleBulkDeleteButton();
        updateSelectAll();
    });

    // Toggle bulk delete button
    function toggleBulkDeleteButton() {
        var checkedCount = $('.product-checkbox:checked').length;
        $('#bulkDeleteBtn').prop('disabled', checkedCount === 0);
    }

    // Update select all checkbox
    function updateSelectAll() {
        var totalCheckboxes = $('.product-checkbox').length;
        var checkedCheckboxes = $('.product-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    }

    // Bulk delete button click
    $('#bulkDeleteBtn').click(function() {
        var ids = [];
        $('.product-checkbox:checked').each(function() {
            ids.push($(this).val());
        });
        $('#selectedCount').text(ids.length);
        $('#bulkDeleteIds').val(ids);
        $('#bulkDeleteModal').modal('show');
    });

    // Delete single product
    $('.delete-product').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#deleteProductName').text(name);
        $('#deleteForm').attr('action', '{{ url("admin/products") }}/' + id);
        $('#deleteModal').modal('show');
    });

    // Toggle product status
    $('.status-toggle').change(function() {
        var id = $(this).data('id');
        var isChecked = $(this).prop('checked');

        $.ajax({
            url: '{{ url("admin/products") }}/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Product status updated successfully');
                }
            },
            error: function() {
                toastr.error('Failed to update product status');
                $(this).prop('checked', !isChecked);
            }
        });
    });

    // Toggle featured status
    $('.featured-toggle').change(function() {
        var id = $(this).data('id');
        var isChecked = $(this).prop('checked');

        $.ajax({
            url: '{{ url("admin/products") }}/' + id + '/toggle-featured',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Featured status updated successfully');
                }
            },
            error: function() {
                toastr.error('Failed to update featured status');
                $(this).prop('checked', !isChecked);
            }
        });
    });
});
</script>
@endsection
