<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products
        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->take(8)
            ->get();

        // Get categories for display
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        // Get latest products
        $latestProducts = Product::where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        return view('front.home', compact('featuredProducts', 'categories', 'latestProducts'));
    }

    public function shop(Request $request)
    {

        $query = Product::where('is_active', true)->with('category');

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', $request->max_price);
        }

        // Sorting
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
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::with(relations: 'products')->where('is_active', true)->get();

        return view('front.shop', compact('products', 'categories'));
    }

    public function productDetails($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        // Increment views
        $product->increment('views');

        // Get related products (same category)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('front.product-details', compact('product', 'relatedProducts'));
    }

    public function about()
    {
        return view('front.about');
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        // Send email or save to database
        // Mail::to(setting('contact_email'))->send(new ContactMail($request->all()));

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
