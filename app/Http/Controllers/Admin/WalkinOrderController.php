<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalkinOrderController extends Controller
{
    /**
     * Show walk-in order creation page
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->get();

        $categories = Category::where('is_active', true)->get();

        return view('admin.orders.walkin-create', compact('products', 'categories'));
    }

    /**
     * Store walk-in order
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,card,upi',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'items.*.flavor' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            $totalItems = 0;

            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
                $totalItems += $item['quantity'];
            }

            $tax = $subtotal * (setting('tax_rate', 10) / 100);
            $total = $subtotal + $tax;

            // Create order
            $order = Order::create([
                'order_number' => 'WALKIN-' . strtoupper(uniqid()),
                'order_type' => 'walkin',
                'status' => 'processing', // Walk-in orders are immediately processing
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => 0, // No shipping for walk-in
                'discount' => 0,
                'total' => $total,

                // Walk-in specific fields
                'walkin_customer_name' => $request->customer_name,
                'walkin_customer_phone' => $request->customer_phone,
                'walkin_notes' => $request->notes,
                'created_by_admin' => Auth::id(),

                // Shipping fields (not used for walk-in but required)
                'shipping_name' => $request->customer_name,
                'shipping_email' => 'walkin@localhost.com',
                'shipping_phone' => $request->customer_phone,
                'shipping_address' => 'Walk-in Customer',
                'shipping_city' => 'Store',
                'shipping_state' => 'Store',
                'shipping_zip' => '00000',
                'shipping_country' => 'Store',

                // Billing same as shipping
                'billing_name' => $request->customer_name,
                'billing_email' => 'walkin@localhost.com',
                'billing_phone' => $request->customer_phone,
                'billing_address' => 'Walk-in Customer',
                'billing_city' => 'Store',
                'billing_state' => 'Store',
                'billing_zip' => '00000',
                'billing_country' => 'Store',
            ]);

            // Create order items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Decrement stock
                $product->decrement('stock_quantity', $item['quantity']);

                $options = [];
                if (!empty($item['size'])) $options['size'] = $item['size'];
                if (!empty($item['flavor'])) $options['flavor'] = $item['flavor'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'options' => !empty($options) ? json_encode($options) : null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.walkin.receipt', $order)
                ->with('success', 'Walk-in order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show walk-in order receipt
     */
    public function receipt(Order $order)
    {
        if (!$order->isWalkin()) {
            abort(404);
        }

        $order->load('items.product');

        return view('admin.orders.walkin-receipt', compact('order'));
    }

    /**
     * Get product details via AJAX
     */
    public function getProductDetails(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->regular_price,
            'stock' => $product->stock_quantity,
            'sizes' => json_decode($product->sizes, true) ?? [],
            'flavors' => json_decode($product->flavors, true) ?? [],
        ]);
    }

    /**
     * Walk-in orders report
     */
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get statistics
        $stats = [
            'online' => Order::where('order_type', 'online')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'walkin' => Order::where('order_type', 'walkin')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        $stats['total'] = $stats['online'] + $stats['walkin'];

        $stats['online_percentage'] = $stats['total'] > 0
            ? round(($stats['online'] / $stats['total']) * 100, 2)
            : 0;

        $stats['walkin_percentage'] = $stats['total'] > 0
            ? round(($stats['walkin'] / $stats['total']) * 100, 2)
            : 0;

        // Revenue stats
        $revenue = [
            'online' => Order::where('order_type', 'online')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total'),
            'walkin' => Order::where('order_type', 'walkin')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total'),
        ];

        $revenue['total'] = $revenue['online'] + $revenue['walkin'];

        $revenue['online_percentage'] = $revenue['total'] > 0
            ? round(($revenue['online'] / $revenue['total']) * 100, 2)
            : 0;

        $revenue['walkin_percentage'] = $revenue['total'] > 0
            ? round(($revenue['walkin'] / $revenue['total']) * 100, 2)
            : 0;

        // Get orders for table
        $orders = Order::with('items')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.walkin-report', compact('stats', 'revenue', 'orders', 'startDate', 'endDate'));
    }
}
