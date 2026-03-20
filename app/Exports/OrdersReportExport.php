<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;

class OrdersReportExport
{
    /**
     * Get orders export data
     */
    public static function getOrders($startDate, $endDate)
    {
        return Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($order) {
                return [
                    'Order ID' => $order->id,
                    'Date' => $order->created_at->format('Y-m-d H:i'),
                    'Customer' => $order->user->name ?? ($order->walkin_customer_name ?? 'Guest'),
                    'Total' => $order->total,
                    'Status' => ucfirst($order->status),
                    'Order Type' => ucfirst($order->order_type ?? 'N/A'),
                    'Is Custom' => $order->is_custom_cake ? 'Yes' : 'No',
                    'Occasion' => ucfirst($order->occasion ?? 'N/A'),
                    'Payment Method' => ucfirst($order->payment_method ?? 'N/A'),
                ];
            });
    }

    /**
     * Get custom cakes export data
     */
    public static function getCustomCakes($startDate, $endDate)
    {
        return Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->get()
            ->map(function($order) {
                return [
                    'Order ID' => $order->id,
                    'Date' => $order->created_at->format('Y-m-d'),
                    'Customer' => $order->user->name ?? ($order->walkin_customer_name ?? 'Guest'),
                    'Cake Design' => $order->cake_design ?? 'Standard',
                    'Has Message' => $order->custom_message ? 'Yes' : 'No',
                    'Occasion' => ucfirst($order->occasion ?? 'N/A'),
                    'Total' => $order->total,
                    'Status' => ucfirst($order->status),
                ];
            });
    }
}
