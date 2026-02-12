<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', 1)->get();

        // $categories = Category::all();

        return view('frontend.categories.index', compact('categories'));
    }
}
