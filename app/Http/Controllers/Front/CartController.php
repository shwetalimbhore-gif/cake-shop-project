<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('front.cart', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'flavor' => 'nullable|string',
        ]);

        $cart = Session::get('cart', []);

        // Create unique ID for cart item (based on product + options)
        $itemId = $product->id . '-' . $request->size . '-' . $request->flavor;

        if (isset($cart[$itemId])) {
            // Update quantity if item exists
            $cart[$itemId]['quantity'] += $request->quantity;
        } else {
            // Add new item
            $cart[$itemId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->getDisplayPriceAttribute(),
                'quantity' => $request->quantity,
                'size' => $request->size,
                'flavor' => $request->flavor,
                'image' => $product->featured_image,
                'slug' => $product->slug,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found!'
        ], 404);
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
