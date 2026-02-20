<?php
// app/Http/Controllers/Front/ProductController.php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display all products
     */
    public function index(Request $request)
    {
        // Start with active products
        $query = Product::with(['category', 'primaryImage', 'images'])
            ->where('is_active', true);

        // Filter by category if provided
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search if provided
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $products = $query->paginate(12);

        // Get categories for filter sidebar
        $categories = Category::where('is_active', true)->get();

        return view('front.products.index', compact('products', 'categories'));
    }

    /**
     * Display products by category
     */
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $products = Product::with(['category', 'primaryImage'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->paginate(12);

        return view('front.products.category', compact('products', 'category'));
    }

    /**
     * Display single product
     */
    public function show($slug)
    {
        die("innn");
        $product = Product::with(['category', 'images', 'options' => function($query) {
            $query->where('is_active', true)->orderBy('type')->orderBy('order');
        }])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        // Get related products (same category)
        $relatedProducts = Product::with(['primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Group options by type
        $groupedOptions = [];
        foreach ($product->options as $option) {
            $groupedOptions[$option->type][] = $option;
        }

        return view('front.products.show', compact('product', 'relatedProducts', 'groupedOptions'));
    }
}
