<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('sort')) {
            $direction = $request->get('direction', 'asc');
            $query->orderBy($request->sort, $direction);
        } else {
            $query->latest();
        }

        $products = $query->paginate(15)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_eggless' => 'sometimes|boolean',

        ]);

          // ===== IMPORTANT: Handle checkbox values =====
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_featured'] = $request->has('is_featured') ? true : false;
        $validated['is_eggless'] = $request->has('is_eggless') ? true : false;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('products', 'public');
            $validated['featured_image'] = $path;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_eggless' => 'sometimes|boolean',
        ]);

         // ===== IMPORTANT: Handle checkbox values =====
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_featured'] = $request->has('is_featured') ? true : false;
        $validated['is_eggless'] = $request->has('is_eggless') ? true : false;

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image) {
                Storage::disk('public')->delete($product->featured_image);
            }

            $path = $request->file('featured_image')->store('products', 'public');
            $validated['featured_image'] = $path;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->featured_image) {
            Storage::disk('public')->delete($product->featured_image);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if ($request->has('ids') && is_string($request->ids)) {
            $ids = explode(',', $request->ids);
        } else {
            $ids = $request->ids ?? [];
        }

        $validIds = Product::whereIn('id', $ids)->pluck('id')->toArray();

        if (empty($validIds)) {
            return redirect()->back()
                ->with('error', 'No valid products selected for deletion.');
        }

        foreach ($validIds as $id) {
            $product = Product::find($id);
            if ($product) {
                if ($product->featured_image) {
                    Storage::disk('public')->delete($product->featured_image);
                }

                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        Product::whereIn('id', $validIds)->delete();

        $count = count($validIds);
        return redirect()->route('admin.products.index')
            ->with('success', $count . ' products deleted successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->update([
            'is_featured' => !$product->is_featured
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $product->is_featured
        ]);
    }
}
