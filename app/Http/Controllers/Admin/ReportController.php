<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\InventoryLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    /**
     * Display the main reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Sales & Revenue Reports
     */
    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Apply filters
        $orderQuery = Order::with('items.product', 'user')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('cake_type')) {
            $orderQuery->where('is_custom_cake', $request->cake_type === 'custom');
        }

        if ($request->filled('occasion')) {
            $orderQuery->where('occasion', $request->occasion);
        }

        if ($request->filled('order_type')) {
            $orderQuery->where('order_type', $request->order_type);
        }

        // 1. Daily Sales
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 0 THEN 1 ELSE 0 END) as standard_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // 2. Monthly Overview
        $monthlyData = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->whereBetween('created_at', [$startDate->copy()->subMonths(6), $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // 3. Product-wise Sales
        $productSales = OrderItem::select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->when($request->flavor, function($query, $flavor) {
                return $query->where('order_items.flavor', $flavor);
            })
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // 4. Category-wise Sales
        $categorySales = OrderItem::select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // 5. Top Cakes (Best Sellers)
        $topCakes = OrderItem::select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                'order_items.flavor',
                DB::raw('COUNT(DISTINCT order_items.order_id) as times_ordered')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'order_items.flavor')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // 6. Low Selling Cakes
        $lowSellingCakes = Product::select(
                'products.id',
                'products.name',
                'products.stock_quantity',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
            )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->groupBy('products.id', 'products.name', 'products.stock_quantity')
            ->having('total_sold', '<', 5)
            ->orHavingNull('total_sold')
            ->orderBy('total_sold', 'asc')
            ->limit(20)
            ->get();

        // 7. Flavor Trends
        $flavorTrends = OrderItem::select(
                'order_items.flavor',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('MONTH(orders.created_at) as month')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('order_items.flavor')
            ->groupBy('order_items.flavor', 'month')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Summary Cards
        $summary = [
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_cakes_sold' => OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('quantity'),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])->avg('total_amount') ?? 0,
            'custom_cakes_count' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_custom_cake', true)
                ->count(),
            'top_flavor' => OrderItem::whereNotNull('flavor')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->groupBy('flavor')
                ->orderByRaw('SUM(quantity) DESC')
                ->first()->flavor ?? 'N/A',
            'top_occasion' => Order::whereNotNull('occasion')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('occasion')
                ->orderByRaw('COUNT(*) DESC')
                ->first()->occasion ?? 'N/A',
        ];

        $occasions = Order::whereNotNull('occasion')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('occasion', DB::raw('COUNT(*) as total'))
            ->groupBy('occasion')
            ->pluck('total', 'occasion')
            ->toArray();

        $flavors = OrderItem::whereNotNull('flavor')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('flavor', DB::raw('SUM(quantity) as total'))
            ->groupBy('flavor')
            ->pluck('total', 'flavor')
            ->toArray();

        return view('admin.reports.sales', compact(
            'dailySales',
            'monthlyData',
            'productSales',
            'categorySales',
            'topCakes',
            'lowSellingCakes',
            'flavorTrends',
            'summary',
            'occasions',
            'flavors',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Orders & Operations Reports
     */
    public function orders(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Order Summary
        $orderSummary = [
            'total' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'pending' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')->count(),
            'cancelled' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')->count(),
            'processing' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'processing')->count(),
        ];

        // Custom Cake Orders
        $customCakes = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->with('user')
            ->latest()
            ->paginate(20);

        $customCakesStats = [
            'total' => $customCakes->total(),
            'avg_price' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_custom_cake', true)
                ->avg('total_amount') ?? 0,
            'with_message' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_custom_cake', true)
                ->whereNotNull('custom_message')
                ->count(),
        ];

        // Pre-order vs Walk-in
        $preorderVsWalkin = [
            'pre_order' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('pre_order_date')
                ->count(),
            'walk_in' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereNull('pre_order_date')
                ->count(),
        ];

        // Delivery vs Pickup
        $deliveryVsPickup = [
            'delivery' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('order_type', 'delivery')
                ->count(),
            'pickup' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('order_type', 'pickup')
                ->count(),
        ];

        // Occasion-based Orders
        $occasionOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('occasion')
            ->select('occasion', DB::raw('COUNT(*) as total'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('occasion')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.reports.orders', compact(
            'orderSummary',
            'customCakes',
            'customCakesStats',
            'preorderVsWalkin',
            'deliveryVsPickup',
            'occasionOrders',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Customer Reports
     */
    public function customers(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Top Customers by Spending
        $topCustomers = User::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('AVG(orders.total_amount) as avg_order_value'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(20)
            ->get();

        // Customer Order History (paginated)
        $customerOrders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        // New vs Returning Customers
        $customers = User::whereHas('orders', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        $newCustomers = $customers->filter(function($customer) {
            return $customer->orders_count == 1;
        })->count();

        $returningCustomers = $customers->filter(function($customer) {
            return $customer->orders_count > 1;
        })->count();

        // Order Frequency
        $orderFrequency = User::select(
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('COUNT(DISTINCT users.id) as user_count')
            )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->having('order_count', '>', 0)
            ->get()
            ->groupBy('order_count')
            ->map(function($group) {
                return $group->sum('user_count');
            });

        // Special Date Customers (birthday/anniversary)
        $specialDateCustomers = User::whereHas('orders', function($query) {
                $query->whereNotNull('occasion');
            })
            ->with(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->whereNotNull('occasion');
            }])
            ->get()
            ->filter(function($user) {
                return $user->orders->isNotEmpty();
            });

        return view('admin.reports.customers', compact(
            'topCustomers',
            'customerOrders',
            'newCustomers',
            'returningCustomers',
            'orderFrequency',
            'specialDateCustomers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Inventory Reports
     */
    public function inventory(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Raw Material Usage (simplified - you may have an ingredients table)
        $rawMaterialUsage = OrderItem::select(
                'products.name as product_name',
                'order_items.flavor',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as times_used')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name', 'order_items.flavor')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Low Stock Alerts
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->orWhere('stock_quantity', '<=', DB::raw('reorder_level'))
            ->orderBy('stock_quantity', 'asc')
            ->get();

        // Stock Movement (daily usage trend)
        $stockMovement = OrderItem::select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_items.quantity) as total_used')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.reports.inventory', compact(
            'rawMaterialUsage',
            'lowStockProducts',
            'stockMovement',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Financial Reports
     */
    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Revenue vs Cost (simplified cost estimation)
        $profitData = OrderItem::select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(order_items.quantity * products.cost_price) as total_cost')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->get()
            ->map(function($item) {
                $item->profit = $item->revenue - $item->total_cost;
                $item->margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                return $item;
            });

        $totalRevenue = $profitData->sum('revenue');
        $totalCost = $profitData->sum('total_cost');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Discount Impact
        $discountImpact = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(CASE WHEN discount_amount > 0 THEN 1 END) as orders_with_discount'),
                DB::raw('SUM(discount_amount) as total_discount_given'),
                DB::raw('AVG(discount_amount) as avg_discount'),
                DB::raw('SUM(total_amount) as revenue_with_discount'),
                DB::raw('(SELECT SUM(total_amount) FROM orders WHERE discount_amount = 0 AND created_at BETWEEN ? AND ?) as revenue_without_discount')
            )
            ->setBindings([$startDate, $endDate, $startDate, $endDate])
            ->first();

        // Payment Methods
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Seasonal Revenue (festival spikes)
        $seasonalRevenue = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('DAYOFYEAR(created_at) as day_of_year')
            )
            ->whereBetween('created_at', [$startDate->copy()->subYear(), $endDate])
            ->groupBy('date', 'day_of_year')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        return view('admin.reports.financial', compact(
            'profitData',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'discountImpact',
            'paymentMethods',
            'seasonalRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports to CSV/Excel/PDF
     */
    public function export(Request $request, $type, $report)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel,pdf'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($report) {
            case 'sales':
                $data = $this->getSalesExportData($startDate, $endDate);
                $filename = "sales_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                break;
            case 'orders':
                $data = $this->getOrdersExportData($startDate, $endDate);
                $filename = "orders_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                break;
            case 'customers':
                $data = $this->getCustomersExportData($startDate, $endDate);
                $filename = "customers_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                break;
            default:
                abort(404);
        }

        if ($request->format === 'csv') {
            return $this->exportToCsv($data, $filename);
        } elseif ($request->format === 'excel') {
            return $this->exportToExcel($data, $filename);
        } else {
            return $this->exportToPdf($data, $filename, $report);
        }
    }

    private function getSalesExportData($startDate, $endDate)
    {
        return Order::with('items.product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($order) {
                return [
                    'Order ID' => $order->id,
                    'Date' => $order->created_at->format('Y-m-d'),
                    'Customer' => $order->user->name ?? 'Guest',
                    'Total' => $order->total_amount,
                    'Items' => $order->items->sum('quantity'),
                    'Type' => $order->is_custom_cake ? 'Custom' : 'Standard',
                    'Occasion' => $order->occasion ?? 'N/A',
                ];
            });
    }

    private function exportToCsv($data, $filename)
    {
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()->toArray()));

                // Add rows
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }

            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        return response()->stream($callback, 200, $headers);
    }

    private function exportToExcel($data, $filename)
    {
        // You'll need to install maatwebsite/excel package for this
        // For now, fallback to CSV
        return $this->exportToCsv($data, $filename);
    }

    private function exportToPdf($data, $filename, $report)
    {
        // You'll need to install barryvdh/laravel-dompdf package for this
        $pdf = PDF::loadView("admin.reports.exports.{$report}", compact('data'));
        return $pdf->download("{$filename}.pdf");
    }
}
