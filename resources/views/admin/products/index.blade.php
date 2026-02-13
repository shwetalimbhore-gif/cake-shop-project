@extends('layouts.admin')

@section('title', 'Products - Admin Panel')
@section('page-title', 'Products')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-box text-primary me-2"></i>
            Manage Products
        </h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>
    <div class="card-body">
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

        <div class="mb-3">
            <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash me-2"></i>Delete Selected
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th width="80">Image</th>
                        <th>Name</th>
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
                                     class="img-thumbnail rounded-3"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-soft-secondary d-flex align-items-center justify-content-center rounded-3"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">
                            {{ $product->name }}
                            @if($product->is_featured)
                                <span class="badge bg-warning ms-2">Featured</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark">{{ $product->sku }}</span></td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>
                            @if($product->sale_price && $product->sale_price < $product->regular_price)
                                <span class="text-decoration-line-through text-muted">{{ format_currency($product->regular_price) }}</span>
                                <br>
                                <span class="text-danger fw-bold">{{ format_currency($product->sale_price) }}</span>
                                <span class="badge bg-success ms-1">-{{ $product->discount_percentage }}%</span>
                            @else
                                {{ format_currency($product->regular_price) }}
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
                            <div class="btn-group" role="group">
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Products Found</h5>
                            <p class="text-muted mb-3">Get started by adding your first product</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New Product
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
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
                <p class="mb-2">Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Product
                    </button>
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
                <p class="mb-2">Are you sure you want to delete <strong id="selectedCount"></strong> selected products?</p>
                <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.products.bulk-delete') }}">
                    @csrf
                    <input type="hidden" name="ids" id="bulkDeleteIds">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Selected
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteButton();
    });

    $(document).on('change', '.product-checkbox', function() {
        toggleBulkDeleteButton();
        updateSelectAll();
    });

    function toggleBulkDeleteButton() {
        var checkedCount = $('.product-checkbox:checked').length;
        $('#bulkDeleteBtn').prop('disabled', checkedCount === 0);

        if (checkedCount > 0) {
            $('#bulkDeleteBtn').removeClass('btn-secondary').addClass('btn-danger');
        } else {
            $('#bulkDeleteBtn').removeClass('btn-danger').addClass('btn-secondary');
        }
    }

    function updateSelectAll() {
        var totalCheckboxes = $('.product-checkbox').length;
        var checkedCheckboxes = $('.product-checkbox:checked').length;

        if (totalCheckboxes === 0) return;

        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    }

    $('#bulkDeleteBtn').click(function() {
        var ids = [];
        $('.product-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Please select at least one product to delete.');
            return;
        }

        $('#selectedCount').text(ids.length);
        $('#bulkDeleteIds').val(ids.join(','));
        $('#bulkDeleteModal').modal('show');
    });

    $(document).on('click', '.delete-product', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#deleteProductName').text(name);
        $('#deleteForm').attr('action', '/admin/products/' + id);
        $('#deleteModal').modal('show');
    });

    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var isChecked = checkbox.prop('checked');

        $.ajax({
            url: '/admin/products/' + id + '/toggle-status',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success('Product status updated successfully');
                }
            },
            error: function() {
                toastr.error('Failed to update product status');
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    $(document).on('change', '.featured-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var isChecked = checkbox.prop('checked');

        $.ajax({
            url: '/admin/products/' + id + '/toggle-featured',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success('Featured status updated successfully');
                    var row = checkbox.closest('tr');
                    var nameCell = row.find('td:eq(2)');
                    var featuredBadge = nameCell.find('.badge.bg-warning');

                    if (response.is_featured) {
                        if (featuredBadge.length === 0) {
                            nameCell.append('<span class="badge bg-warning ms-2">Featured</span>');
                        }
                    } else {
                        featuredBadge.remove();
                    }
                }
            },
            error: function() {
                toastr.error('Failed to update featured status');
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    $(document).on('change', 'select[name="category"], select[name="stock_status"], select[name="status"]', function() {
        $(this).closest('form').submit();
    });

    var searchTimer;
    $(document).on('keyup', 'input[name="search"]', function(e) {
        clearTimeout(searchTimer);
        var form = $(this).closest('form');

        if (e.key === 'Enter') {
            form.submit();
            return;
        }

        searchTimer = setTimeout(function() {
            form.submit();
        }, 500);
    });

    toggleBulkDeleteButton();
    updateSelectAll();
});
</script>
@endsection
