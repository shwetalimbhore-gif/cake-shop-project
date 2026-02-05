{{-- <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image">
    <button type="submit">Upload Image</button>
</form>

<h4>Images</h4>
@foreach($product->images as $image)
    <img src="{{ asset('storage/' . $image->image_path) }}" width="100">
@endforeach --}}

<h3>Add Option</h3>

<form method="POST" action="{{ route('admin.products.options.store', $product) }}">
    @csrf

    <select name="type">
        <option value="size">Size</option>
        <option value="flavor">Flavor</option>
        <option value="message">Message</option>
        <option value="slot">Delivery Slot</option>
    </select>

    <input type="text" name="value" placeholder="Option value">
    <input type="number" name="extra_price" placeholder="Extra price">

    <button type="submit">Add Option</button>
</form>

<h4>Options</h4>
<ul>
@foreach($product->options as $option)
    <li>
        {{ $option->type }} - {{ $option->value }} (+₹{{ $option->extra_price }})
    </li>
@endforeach
</ul>
