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
     * Display all products with filters and sorting
     */
    public function index(Request $request)
    {
        // Start with active AND in-stock products
        $query = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('in_stock', true);  // Only show in-stock products

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('short_description', 'like', '%' . $search . '%');
            });
        }

        // Eggless filter
        if ($request->filled('eggless')) {
            if ($request->eggless == 'yes') {
                $query->where('is_eggless', true);
            } elseif ($request->eggless == 'no') {
                $query->where('is_eggless', false);
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', $request->max_price);
        }

        // Sorting options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('regular_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('regular_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination
       $products = $query->paginate(12)->appends(request()->query());

        // Get categories for filter sidebar
        $categories = Category::where('is_active', true)->get();

        return view('front.shop', compact('products', 'categories'));
    }

    /**
     * Display single product
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'images', 'reviews' => function($query) {
            $query->where('is_approved', true)->latest();
        }])
        ->where('slug', $slug)
        ->where('is_active', true)  // Must be active
        ->firstOrFail();

        // Increment view count
        $product->increment('views');

        // Get related products (only in-stock)
        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('in_stock', true)  // Only show in-stock related products
            ->limit(4)
            ->get();

        return view('front.product-details', compact('product', 'relatedProducts'));
    }
}
