<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function byCategory(Category $category)
    {
       $products = $category->products()
            ->where('is_active', 1)
            ->get();

        return view('frontend.products.by-category', compact('category', 'products'));
    }

    public function show(Product $product)
    {
        return view('frontend.products.show', compact('product'));
    }
}
