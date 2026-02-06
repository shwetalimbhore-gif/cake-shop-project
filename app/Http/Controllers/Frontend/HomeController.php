<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', 1)
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.home', compact('products'));
    }
}
