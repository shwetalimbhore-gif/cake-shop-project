<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $deliveryCharge = setting('delivery_charges', 10);
        $freeThreshold = setting('free_delivery_threshold', 100);
        $deliveryFee = ($total >= $freeThreshold) ? 0 : $deliveryCharge;

        $grandTotal = $total + $deliveryFee;

        return view('front.checkout', compact('cart', 'total', 'deliveryFee', 'grandTotal'));
    }

    /**
 * Process checkout
 */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'payment_method' => 'required|in:credit_card,paypal,cash_on_delivery',
        ]);

        // Get cart from database
        $cart = Cart::getCart();
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cart->total_amount;

            $deliveryCharge = setting('delivery_charges', 10);
            $freeThreshold = setting('free_delivery_threshold', 100);
            $shippingCost = ($subtotal >= $freeThreshold) ? 0 : $deliveryCharge;

            $tax = $subtotal * (setting('tax_rate', 10) / 100);
            $total = $subtotal + $tax + $shippingCost;

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'billing_name' => $request->billing_name ?? $request->shipping_name,
                'billing_email' => $request->billing_email ?? $request->shipping_email,
                'billing_phone' => $request->billing_phone ?? $request->shipping_phone,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'billing_city' => $request->billing_city ?? $request->shipping_city,
                'billing_state' => $request->billing_state ?? $request->shipping_state,
                'billing_zip' => $request->billing_zip ?? $request->shipping_zip,
                'billing_country' => $request->billing_country ?? $request->shipping_country,
                'notes' => $request->notes,
            ]);

            // Create order items from cart
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'options' => json_encode($item->options),
                ]);

                // Update stock if needed
                // $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total_amount = 0;
            $cart->save();

            DB::commit();

            return redirect()->route('checkout.success', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('front.checkout-success', compact('order'));
    }
}
