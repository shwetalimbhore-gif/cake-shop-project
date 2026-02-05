<form method="POST" action="{{ route('admin.products.store') }}">
    @csrf

    <input type="text" name="name" placeholder="Product name"><br>

    <select name="category_id">
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select><br>

    <input type="number" name="base_price" placeholder="Base price"><br>

    <textarea name="description" placeholder="Description"></textarea><br>

    <button type="submit">Save</button>
</form>
