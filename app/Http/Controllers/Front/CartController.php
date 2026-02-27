<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $cart = Cart::getCart();
        $cart->load('items.product');

        $cart->calculateTotal();

        return view('front.cart', compact('cart'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'flavor' => 'nullable|string',
            'calculated_price' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $cart = Cart::getCart();

            $price = $product->sale_price ?? $product->regular_price;

            $options = [
                'size' => $request->size,
                'flavor' => $request->flavor,
            ];

            // Check if item already exists with same options
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('options', json_encode($options))
                ->first();

            if ($existingItem) {
                // Update quantity
                $existingItem->quantity += $request->quantity;
                $existingItem->unit_price = $request->calculated_price;
                $existingItem->save();
                $message = 'Cart updated successfully!';
            } else {
                // Create new cart item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'unit_price' => $price,
                    'options' => $options,
                ]);
                $message = 'Product added to cart!';
            }

            // Update cart total
            $cart->calculateTotal();

            DB::commit();

            // Get updated cart count
            $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => $cartCount,
                    'cart_total' => $cart->total_amount
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to cart.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to add product to cart.');
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $cartItem = CartItem::findOrFail($itemId);

            // Verify cart belongs to current user/session
            $cart = Cart::getCart();
            if ($cartItem->cart_id != $cart->id) {
                abort(403);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            // Update cart total
            $cart->calculateTotal();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated!',
                    'item_subtotal' => $cartItem->subtotal,
                    'cart_total' => $cart->total_amount
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Cart updated!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update cart.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update cart.');
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        DB::beginTransaction();

        try {
            $cartItem = CartItem::findOrFail($itemId);

            // Verify cart belongs to current user/session
            $cart = Cart::getCart();
            if ($cartItem->cart_id != $cart->id) {
                abort(403);
            }

            $cartItem->delete();

            // Update cart total
            $cart->calculateTotal();

            DB::commit();

            return redirect()->route('cart.index')->with('success', 'Item removed from cart!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove item.');
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        DB::beginTransaction();

        try {
            $cart = Cart::getCart();

            CartItem::where('cart_id', $cart->id)->delete();

            $cart->total_amount = 0;
            $cart->save();

            DB::commit();

            return redirect()->route('cart.index')->with('success', 'Cart cleared!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to clear cart.');
        }
    }

    /**
     * Get cart count (for AJAX)
     */
    public function getCount()
    {
        $cart = Cart::getCart();
        $count = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'count' => $count
        ]);
    }
}
