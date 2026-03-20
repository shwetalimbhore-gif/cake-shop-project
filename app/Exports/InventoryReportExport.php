<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class InventoryReportExport
{
    /**
     * Get inventory usage export data
     */
    public static function getInventoryUsage($startDate, $endDate)
    {
        return OrderItem::select(
                'products.name as product_name',
                'products.sku',
                'products.stock_quantity as current_stock',
                DB::raw('SUM(order_items.quantity) as total_used'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as times_used')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity')
            ->orderBy('total_used', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Product Name' => $item->product_name,
                    'SKU' => $item->sku,
                    'Current Stock' => $item->current_stock,
                    'Total Used' => $item->total_used,
                    'Times Used' => $item->times_used,
                    'Usage Rate' => $item->times_used > 0
                        ? round($item->total_used / $item->times_used, 2) . '/order'
                        : '0/order',
                ];
            });
    }

    /**
     * Get low stock products export data
     */
    public static function getLowStock()
    {
        return Product::where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get()
            ->map(function($product) {
                return [
                    'Product Name' => $product->name,
                    'SKU' => $product->sku,
                    'Current Stock' => $product->stock_quantity,
                    'Status' => $product->stock_quantity <= 5 ? 'Critical' : 'Low',
                ];
            });
    }
}
