@extends('layouts.admin')

@section('title', 'Categories - Admin Panel')
@section('page-title', 'Categories')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-tags text-primary me-2"></i>
                    Manage Categories
                </h5>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Category
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th width="80">Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="img-thumbnail rounded-3"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-soft-secondary d-flex align-items-center justify-content-center rounded-3"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-tag text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td><span class="badge bg-light text-dark">{{ $category->slug }}</span></td>
                                <td>{{ Str::limit($category->description, 30) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $category->products_count ?? $category->products->count() }}</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input status-toggle"
                                               data-id="{{ $category->id }}"
                                               {{ $category->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>{{ $category->order ?? 0 }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-category"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Categories Found</h5>
                                    <p class="text-muted mb-3">Get started by creating your first category</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add New Category
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
                    </div>
                    <div>
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete <strong id="deleteCategoryName"></strong>?</p>
                <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone! Products in this category will be uncategorized.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Category
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
    $(document).on('click', '.delete-category', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#deleteCategoryName').text(name);
        $('#deleteForm').attr('action', '/admin/categories/' + id);
        $('#deleteModal').modal('show');
    });

    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var isChecked = checkbox.prop('checked');

        $.ajax({
            url: '/admin/categories/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Category status updated successfully');
                }
            },
            error: function() {
                toastr.error('Failed to update category status');
                checkbox.prop('checked', !isChecked);
            }
        });
    });
});
</script>
@endsection
