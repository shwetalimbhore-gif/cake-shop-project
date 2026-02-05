<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductOption;

class ProductOptionController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required',
            'value' => 'required',
            'extra_price' => 'nullable|numeric|min:0',
        ]);

        ProductOption::create([
            'product_id' => $product->id,
            'type' => $request->type,
            'value' => $request->value,
            'extra_price' => $request->extra_price ?? 0,
        ]);

        return back();
    }
}
