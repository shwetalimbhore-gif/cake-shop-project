<?php
// app/Http/Controllers/Front/HomeController.php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display homepage
     */
    public function index()
    {
        // Get featured products (those marked as featured)
        $featuredProducts = Product::with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        // Get all active categories
        $categories = Category::withCount(['products' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->get();

        // Get best selling products (you'll implement this logic later)
        $bestSelling = Product::with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->inRandomOrder() // Temporary - replace with actual best-selling logic
            ->take(4)
            ->get();

        return view('front.home', compact('featuredProducts', 'categories', 'bestSelling'));
    }

    /**
     * About Us page
     */
    public function about()
    {
        return view('front.about');
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return view('front.contact');
    }
}
