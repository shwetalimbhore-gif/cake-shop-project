<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportExport
{
    /**
     * Get profit data export
     */
    public static function getProfitData($startDate, $endDate)
    {
        $data = OrderItem::select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function($item) {
                // Assuming 60% cost for estimation
                $cost = $item->revenue * 0.6;
                $profit = $item->revenue - $cost;
                $margin = $item->revenue > 0 ? ($profit / $item->revenue) * 100 : 0;

                return [
                    'Product Name' => $item->product_name,
                    'Quantity Sold' => $item->quantity_sold,
                    'Revenue' => $item->revenue,
                    'Estimated Cost' => round($cost, 2),
                    'Estimated Profit' => round($profit, 2),
                    'Profit Margin' => round($margin, 2) . '%',
                ];
            });

        return $data;
    }

    /**
     * Get payment methods export data
     */
    public static function getPaymentMethods($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(function($item) {
                return [
                    'Payment Method' => ucfirst($item->payment_method ?? 'N/A'),
                    'Number of Orders' => $item->count,
                    'Total Amount' => $item->total,
                ];
            });
    }

    /**
     * Get discount impact export data
     */
    public static function getDiscountImpact($startDate, $endDate)
    {
        $withDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', '>', 0)
            ->count();

        $withoutDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', 0)
            ->count();

        $totalDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount');

        $revenueWithDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', '>', 0)
            ->sum('total');

        $revenueWithoutDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', 0)
            ->sum('total');

        return collect([
            [
                'Metric' => 'Orders with Discount',
                'Value' => $withDiscount,
            ],
            [
                'Metric' => 'Orders without Discount',
                'Value' => $withoutDiscount,
            ],
            [
                'Metric' => 'Total Discount Given',
                'Value' => '$' . number_format($totalDiscount, 2),
            ],
            [
                'Metric' => 'Revenue with Discount',
                'Value' => '$' . number_format($revenueWithDiscount, 2),
            ],
            [
                'Metric' => 'Revenue without Discount',
                'Value' => '$' . number_format($revenueWithoutDiscount, 2),
            ],
        ]);
    }
}
