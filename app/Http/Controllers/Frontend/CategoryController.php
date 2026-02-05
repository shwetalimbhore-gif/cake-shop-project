<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // Get only active categories (adjust if needed)
        $categories = Category::orderBy('name')->get();

        return view('frontend.categories.index', compact('categories'));
    }
}
