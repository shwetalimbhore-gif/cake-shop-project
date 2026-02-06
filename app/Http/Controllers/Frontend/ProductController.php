<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function byCategory(Category $category)
    {
        $products = $category->products; // relationship already exists from Phase 1

        return view('frontend.products.index', compact('category', 'products'));
    }
}
