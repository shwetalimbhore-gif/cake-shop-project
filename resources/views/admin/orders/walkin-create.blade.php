@extends('layouts.admin')

@section('title', 'Create Walk-in Order - Admin Panel')
@section('page-title', 'Create Walk-in Order')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-store text-warning me-2"></i>
                    New Walk-in Order
                </h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.orders.walkin.store') }}" method="POST" id="walkinForm">
                    @csrf

                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        Customer Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_name" class="form-control"
                                               value="{{ old('customer_name') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_phone" class="form-control"
                                               value="{{ old('customer_phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-credit-card text-primary me-2"></i>
                                        Payment Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Payment Method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="upi">UPI</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Payment Status</label>
                                        <select name="payment_status" class="form-select" required>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-box text-primary me-2"></i>
                                        Select Products
                                    </h6>
                                    <span class="badge bg-info" id="selectedCount">0 items</span>
                                </div>
                                <div class="card-body">
                                    <!-- Search and Filter -->
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <input type="text" id="productSearch" class="form-control"
                                                   placeholder="Search products...">
                                        </div>
                                        <div class="col-md-4">
                                            <select id="categoryFilter" class="form-select">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Products List -->
                                    <div class="products-list" style="max-height: 400px; overflow-y: auto;">
                                        @foreach($products as $product)
                                        <div class="product-item border rounded p-3 mb-2"
                                             data-product-id="{{ $product->id }}"
                                             data-product-name="{{ $product->name }}"
                                             data-product-price="{{ $product->sale_price ?? $product->regular_price }}"
                                             data-category-id="{{ $product->category_id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 fw-semibold">{{ $product->name }}</h6>
                                                    <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">${{ number_format($product->sale_price ?? $product->regular_price, 2) }}</span>
                                                        <span class="badge bg-info">Stock: {{ $product->stock_quantity }}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary add-product">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Items Table -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-shopping-cart text-primary me-2"></i>
                                Selected Items
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="selectedItemsTable">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Flavor</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedItemsBody">
                                        <!-- Selected items will appear here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                            <td><strong id="subtotal">$0.00</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Tax ({{ setting('tax_rate', 10) }}%):</strong></td>
                                            <td><strong id="tax">$0.00</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                            <td><strong id="total">$0.00</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-1"></i>Create Walk-in Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedItems = [];
    let itemCounter = 0;

    // Search and filter functionality
    document.getElementById('productSearch').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    function filterProducts() {
        const searchTerm = document.getElementById('productSearch').value.toLowerCase();
        const categoryId = document.getElementById('categoryFilter').value;

        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.productName.toLowerCase();
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !categoryId || item.dataset.categoryId === categoryId;

            item.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
        });
    }

    // Add product to cart
    document.querySelectorAll('.add-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const productItem = this.closest('.product-item');
            const productId = productItem.dataset.productId;
            const productName = productItem.dataset.productName;
            const productPrice = parseFloat(productItem.dataset.productPrice);

            // Check if product already added
            const existingItem = selectedItems.find(item => item.productId === productId && !item.size && !item.flavor);

            if (existingItem) {
                existingItem.quantity++;
                updateItemsTable();
            } else {
                // Show options modal
                showProductOptions(productId, productName, productPrice);
            }
        });
    });

    function showProductOptions(productId, productName, basePrice) {
        // You can implement a modal here for size/flavor selection
        // For simplicity, we'll add directly
        addItemToCart({
            id: itemCounter++,
            productId: productId,
            name: productName,
            price: basePrice,
            quantity: 1,
            size: '',
            flavor: ''
        });
    }

    function addItemToCart(item) {
        selectedItems.push(item);
        updateItemsTable();
    }

    function updateItemsTable() {
        const tbody = document.getElementById('selectedItemsBody');
        tbody.innerHTML = '';

        let subtotal = 0;

        selectedItems.forEach((item, index) => {
            const itemSubtotal = item.price * item.quantity;
            subtotal += itemSubtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <select class="form-select form-select-sm" onchange="updateItemSize(${index}, this.value)">
                        <option value="">Select Size</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm" onchange="updateItemFlavor(${index}, this.value)">
                        <option value="">Select Flavor</option>
                        <option value="Vanilla">Vanilla</option>
                        <option value="Chocolate">Chocolate</option>
                        <option value="Strawberry">Strawberry</option>
                    </select>
                </td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" style="width: 80px;"
                           value="${item.quantity}" min="1"
                           onchange="updateQuantity(${index}, this.value)">
                </td>
                <td>$${itemSubtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            // Add hidden inputs for form submission
            row.innerHTML += `
                <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}" class="item-quantity-${index}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
                <input type="hidden" name="items[${index}][size]" value="" class="item-size-${index}">
                <input type="hidden" name="items[${index}][flavor]" value="" class="item-flavor-${index}">
            `;

            tbody.appendChild(row);
        });

        // Update totals
        const taxRate = {{ setting('tax_rate', 10) }};
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + tax.toFixed(2);
        document.getElementById('total').textContent = '$' + total.toFixed(2);
        document.getElementById('selectedCount').textContent = selectedItems.length + ' items';
    }

    function updateQuantity(index, quantity) {
        selectedItems[index].quantity = parseInt(quantity);
        updateItemsTable();
    }

    function updateItemSize(index, size) {
        selectedItems[index].size = size;
        document.querySelector(`.item-size-${index}`).value = size;
    }

    function updateItemFlavor(index, flavor) {
        selectedItems[index].flavor = flavor;
        document.querySelector(`.item-flavor-${index}`).value = flavor;
    }

    function removeItem(index) {
        selectedItems.splice(index, 1);
        updateItemsTable();
    }

    // Form validation before submit
    document.getElementById('walkinForm').addEventListener('submit', function(e) {
        if (selectedItems.length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the order.');
            return;
        }

        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
    });
</script>
@endpush
@endsection
