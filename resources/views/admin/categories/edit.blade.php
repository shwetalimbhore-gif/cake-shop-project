<form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $category->name }}">
    <button type="submit">Update</button>
</form>
