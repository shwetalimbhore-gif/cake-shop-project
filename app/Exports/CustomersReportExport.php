<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomersReportExport
{
    /**
     * Get top customers export data
     */
    public static function getTopCustomers($startDate, $endDate)
    {
        return User::select(
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('AVG(orders.total) as avg_order_value'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(50)
            ->get()
            ->map(function($customer, $index) {
                return [
                    'Rank' => $index + 1,
                    'Customer Name' => $customer->name,
                    'Email' => $customer->email,
                    'Total Orders' => $customer->total_orders,
                    'Total Spent' => $customer->total_spent,
                    'Avg Order Value' => round($customer->avg_order_value, 2),
                    'Last Order' => \Carbon\Carbon::parse($customer->last_order_date)->format('Y-m-d'),
                ];
            });
    }

    /**
     * Get customer order history export data
     */
    public static function getCustomerHistory($startDate, $endDate)
    {
        return \App\Models\Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($order) {
                return [
                    'Order ID' => $order->id,
                    'Customer Name' => $order->user->name ?? ($order->walkin_customer_name ?? 'Guest'),
                    'Customer Email' => $order->user->email ?? 'N/A',
                    'Order Date' => $order->created_at->format('Y-m-d'),
                    'Total' => $order->total,
                    'Status' => ucfirst($order->status),
                    'Order Type' => $order->order_type ?? 'N/A',
                ];
            });
    }
}
