<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportExport
{
    /**
     * Get daily sales export data
     */
    public static function getDailySales($startDate, $endDate)
    {
        return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 0 THEN 1 ELSE 0 END) as standard_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'day')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'Date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'Day' => $item->day,
                    'Total Orders' => $item->orders,
                    'Custom Cakes' => $item->custom_cakes,
                    'Standard Cakes' => $item->standard_cakes,
                    'Total Revenue' => $item->revenue,
                    'Avg Order Value' => $item->orders > 0 ? round($item->revenue / $item->orders, 2) : 0,
                ];
            });
    }

    /**
     * Get monthly overview export data
     */
    public static function getMonthlyOverview($startDate, $endDate)
    {
        return Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('AVG(total) as avg_order_value'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'Year' => $item->year,
                    'Month' => Carbon::create()->month($item->month)->format('F'),
                    'Total Orders' => $item->orders,
                    'Custom Cakes' => $item->custom_cakes,
                    'Total Revenue' => $item->revenue,
                    'Avg Order Value' => round($item->avg_order_value, 2),
                ];
            });
    }

    /**
     * Get product-wise sales export data
     */
    public static function getProductWise($startDate, $endDate)
    {
        return OrderItem::select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(order_items.subtotal / order_items.quantity) as avg_price')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name', 'products.sku')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Product Name' => $item->product_name,
                    'SKU' => $item->sku,
                    'Quantity Sold' => $item->quantity,
                    'Order Count' => $item->order_count,
                    'Total Revenue' => $item->revenue,
                    'Average Price' => round($item->avg_price, 2),
                ];
            });
    }

    /**
     * Get top cakes export data
     */
    public static function getTopCakes($startDate, $endDate, $limit = 10)
    {
        return OrderItem::select(
                'products.name as product_name',
                'order_items.flavor',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name', 'order_items.flavor')
            ->orderBy('quantity', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item, $index) {
                return [
                    'Rank' => $index + 1,
                    'Product Name' => $item->product_name,
                    'Flavor' => ucfirst($item->flavor) ?? 'N/A',
                    'Quantity Sold' => $item->quantity,
                    'Times Ordered' => $item->order_count,
                    'Total Revenue' => $item->revenue,
                    'Average Price' => $item->quantity > 0 ? round($item->revenue / $item->quantity, 2) : 0,
                ];
            });
    }

    /**
     * Get flavor trends export data
     */
    public static function getFlavorTrends($startDate, $endDate)
    {
        return OrderItem::whereNotNull('flavor')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'order_items.flavor',
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->groupBy('order_items.flavor', 'year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('quantity', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Flavor' => ucfirst($item->flavor),
                    'Year' => $item->year,
                    'Month' => Carbon::create()->month($item->month)->format('F'),
                    'Quantity Sold' => $item->quantity,
                    'Order Count' => $item->order_count,
                    'Avg per Order' => $item->order_count > 0 ? round($item->quantity / $item->order_count, 2) : 0,
                ];
            });
    }

    /**
     * Get category-wise sales export data
     */
    public static function getCategoryWise($startDate, $endDate)
    {
        return OrderItem::select(
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('AVG(order_items.subtotal / order_items.quantity) as avg_price')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Category Name' => $item->category_name,
                    'Total Orders' => $item->order_count,
                    'Quantity Sold' => $item->quantity,
                    'Total Revenue' => $item->revenue,
                    'Average Price' => round($item->avg_price, 2),
                ];
            });
    }
}
