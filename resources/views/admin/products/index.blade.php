@extends('layouts.admin')

@section('title', 'Products - Admin Panel')
@section('page-title', 'Products')

@section('content')
<div class="container-fluid px-0 px-md-2 px-lg-3">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-box text-primary me-2"></i>
                Manage Products
            </h5>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="stock_status" class="form-select">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="col-6 col-sm-3 col-md-1">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i>
                    </a>
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
                            <th width="120">Actions</th>
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
                                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center rounded-3"
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
                                    <span class="text-decoration-line-through text-muted">${{ number_format($product->regular_price, 2) }}</span>
                                    <br>
                                    <span class="text-danger fw-bold">${{ number_format($product->sale_price, 2) }}</span>
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
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-3">
                                    Add New Product
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination with Beautiful Icons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                <div class="text-muted small mb-3 mb-md-0">
                    Showing <span class="fw-bold">{{ $products->firstItem() ?? 0 }}</span>
                    to <span class="fw-bold">{{ $products->lastItem() ?? 0 }}</span>
                    of <span class="fw-bold">{{ $products->total() }}</span> products
                </div>

                @if ($products->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($products->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="d-none d-sm-inline ms-1">Prev</span>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="d-none d-sm-inline ms-1">Prev</span>
                                    </a>
                                </li>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                                @if ($page == $products->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($products->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">
                                        <span class="d-none d-sm-inline me-1">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <span class="d-none d-sm-inline me-1">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
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
    // Initialize toastr if available
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    }

    // Show flash messages
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

    // ===== FIXED: Delete single product =====
    $(document).on('click', '.delete-product', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#deleteProductName').text(name);
        $('#deleteForm').attr('action', '/admin/products/' + id);
        $('#deleteModal').modal('show');
    });

    // ===== Select All checkbox =====
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteButton();
    });

    // ===== Individual checkbox change =====
    $(document).on('change', '.product-checkbox', function() {
        toggleBulkDeleteButton();
        updateSelectAll();
    });

    // ===== Toggle bulk delete button =====
    function toggleBulkDeleteButton() {
        var checkedCount = $('.product-checkbox:checked').length;
        $('#bulkDeleteBtn').prop('disabled', checkedCount === 0);

        if (checkedCount > 0) {
            $('#bulkDeleteBtn').removeClass('btn-secondary').addClass('btn-danger');
        } else {
            $('#bulkDeleteBtn').removeClass('btn-danger').addClass('btn-secondary');
        }
    }

    // ===== Update select all checkbox =====
    function updateSelectAll() {
        var totalCheckboxes = $('.product-checkbox').length;
        var checkedCheckboxes = $('.product-checkbox:checked').length;

        if (totalCheckboxes === 0) return;

        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    }

    // ===== Bulk delete button click =====
    $('#bulkDeleteBtn').click(function() {
        var ids = [];
        $('.product-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select at least one product to delete.');
            } else {
                alert('Please select at least one product to delete.');
            }
            return;
        }

        $('#selectedCount').text(ids.length);
        $('#bulkDeleteIds').val(ids.join(','));
        $('#bulkDeleteModal').modal('show');
    });

    // ===== Toggle product status with AJAX =====
    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var isChecked = checkbox.prop('checked');

        $.ajax({
            url: '/admin/products/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Product status updated successfully');
                    }
                }
            },
            error: function(xhr) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update product status');
                } else {
                    alert('Failed to update product status');
                }
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    // ===== Toggle featured status with AJAX =====
    $(document).on('change', '.featured-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var isChecked = checkbox.prop('checked');

        $.ajax({
            url: '/admin/products/' + id + '/toggle-featured',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Featured status updated successfully');
                    }

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
            error: function(xhr) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update featured status');
                } else {
                    alert('Failed to update featured status');
                }
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    // ===== Auto-submit filters =====
    $(document).on('change', 'select[name="category"], select[name="stock_status"], select[name="status"]', function() {
        $(this).closest('form').submit();
    });

    // ===== Search with debounce =====
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
        }, 800);
    });

    // ===== Initialize =====
    toggleBulkDeleteButton();
    updateSelectAll();
});
</script>
@endsection
