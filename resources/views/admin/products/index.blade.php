<h2>Products</h2>
<a href="{{ route('admin.products.create') }}">Add Product</a>

<ul>
@foreach($products as $product)
    <li>
        {{ $product->name }} -
        {{ $product->category->name }} -
        ₹{{ $product->base_price }}
    </li>
@endforeach
</ul>
