<?php

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
        // Start with active products
        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

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

        // EGGLESS FILTER
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

        // Get paginated products
        $products = $query->paginate(12)->appends($request->query());

        // ===== IMPORTANT: Get counts for filter sidebar =====
        $egglessCount = Product::where('is_active', true)
            ->where('is_eggless', true)
            ->count();

        $withEggCount = Product::where('is_active', true)
            ->where('is_eggless', false)
            ->count();

        // Get categories
        $categories = Category::where('is_active', true)->get();

        // Pass all variables to the view
        return view('front.shop', compact(
            'products',
            'categories',
            'egglessCount',
            'withEggCount'
        ));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images', 'reviews' => function($query) {
            $query->where('is_approved', true)->latest();
        }])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        // Increment view count
        $product->increment('views');

        // Get related products
        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('in_stock', true)
            ->limit(4)
            ->get();

        return view('front.product-details', compact('product', 'relatedProducts'));
    }
}
