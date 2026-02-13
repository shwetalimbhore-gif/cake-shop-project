<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('shipping_name', 'like', '%' . $search . '%')
                  ->orWhere('shipping_email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'processing_orders' => Order::where('status', Order::STATUS_PROCESSING)->count(),
            'delivered_orders' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'today_revenue' => Order::where('payment_status', 'paid')
                ->whereDate('created_at', today())
                ->sum('total')
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items.product', 'user');
        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUNDED
        ];

        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,confirmed,shipped,delivered,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
        ]);

        if ($request->status == 'shipped' && !$order->shipped_at) {
            $validated['shipped_at'] = now();
        }

        if ($request->status == 'delivered' && !$order->delivered_at) {
            $validated['delivered_at'] = now();
        }

        if ($request->status == 'cancelled' && !$order->cancelled_at) {
            $validated['cancelled_at'] = now();
            $validated['cancellation_reason'] = $request->cancellation_reason ?? 'Cancelled by admin';
        }

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order #' . $order->order_number . ' updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,confirmed,shipped,delivered,cancelled,refunded'
        ]);

        $data = ['status' => $request->status];

        if ($request->status == 'shipped' && !$order->shipped_at) {
            $data['shipped_at'] = now();
        }

        if ($request->status == 'delivered' && !$order->delivered_at) {
            $data['delivered_at'] = now();
        }

        if ($request->status == 'cancelled' && !$order->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        $order->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'status' => $request->status,
            'badge_class' => $order->status_badge
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $order->update([
            'payment_status' => $request->payment_status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'payment_status' => $request->payment_status,
            'badge_class' => $order->payment_status_badge
        ]);
    }

    public function printInvoice(Order $order)
    {
        $order->load('items', 'user');
        return view('admin.orders.invoice', compact('order'));
    }

    public function export(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order Number', 'Date', 'Customer Name', 'Customer Email',
                'Total', 'Status', 'Payment Status', 'Items Count'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->shipping_name,
                    $order->shipping_email,
                    $order->total,
                    ucfirst($order->status),
                    ucfirst($order->payment_status),
                    $order->items->count()
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
