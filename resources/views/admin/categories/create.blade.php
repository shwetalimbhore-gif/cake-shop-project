<form method="POST" action="{{ route('admin.categories.store') }}">
    @csrf
    <input type="text" name="name" placeholder="Category name">
    <button type="submit">Save</button>
</form>
