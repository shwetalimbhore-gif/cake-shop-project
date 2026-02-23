<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show tracking form
     */
    public function index()
    {
        return view('front.tracking.index');
    }

    /**
     * Track order by order number
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email'
        ]);

        $order = Order::where('order_number', $request->order_number)
                      ->where('shipping_email', $request->email)
                      ->with('items')
                      ->first();

        if (!$order) {
            return back()->with('error', 'No order found with these details!');
        }

        return view('front.tracking.result', compact('order'));
    }

    /**
     * API endpoint for real-time tracking (AJAX)
     */
    public function getTrackingStatus($orderId)
    {
        $order = Order::findOrFail($orderId);

        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'estimated_delivery' => $order->estimated_delivery?->format('M d, Y h:i A'),
            'current_location' => $order->current_location,
            'tracking_history' => json_decode($order->tracking_history),
            'driver_latitude' => $order->driver_latitude,
            'driver_longitude' => $order->driver_longitude
        ]);
    }
}
