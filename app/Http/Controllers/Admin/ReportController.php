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
        // Get some basic stats for the dashboard
        $totalRevenue = Order::sum('total') ?? 0;
        $totalOrders = Order::count() ?? 0;
        $totalCustomers = User::whereHas('orders')->count() ?? 0;
        $lowStockCount = Product::where('stock_quantity', '<=', 10)->count() ?? 0;

        // Get quick insights
        $topFlavor = OrderItem::whereNotNull('flavor')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('order_items.flavor', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('order_items.flavor')
            ->orderBy('total', 'DESC')
            ->first()->flavor ?? 'Chocolate';

        $topOccasion = Order::whereNotNull('occasion')
            ->select('occasion', DB::raw('COUNT(*) as total'))
            ->groupBy('occasion')
            ->orderBy('total', 'DESC')
            ->first()->occasion ?? 'Birthday';

        $avgOrderValue = Order::avg('total') ?? 0;

        // Get peak order day (simplified)
        $peakDay = Order::select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('total', 'DESC')
            ->first()->day ?? 'Saturday';

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'lowStockCount',
            'topFlavor',
            'topOccasion',
            'avgOrderValue',
            'peakDay'
        ));
    }

    /**
     * Daily Sales Report
     */
    public function dailySales(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Daily Sales Data
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 0 THEN 1 ELSE 0 END) as standard_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'day_name')
            ->orderBy('date', 'desc')
            ->get();

        // Summary Stats
        $summary = [
            'total_revenue' => $dailySales->sum('total_revenue'),
            'total_orders' => $dailySales->sum('total_orders'),
            'total_custom' => $dailySales->sum('custom_cakes'),
            'total_standard' => $dailySales->sum('standard_cakes'),
            'avg_daily_revenue' => $dailySales->avg('total_revenue'),
            'avg_daily_orders' => round($dailySales->avg('total_orders')),
            'best_day' => $dailySales->sortByDesc('total_revenue')->first(),
        ];

        // Chart Data
        $chartData = [
            'dates' => $dailySales->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'revenues' => $dailySales->pluck('total_revenue'),
            'orders' => $dailySales->pluck('total_orders'),
        ];

        return view('admin.reports.daily-sales', compact('dailySales', 'summary', 'chartData', 'startDate', 'endDate'));
    }

    /**
     * Monthly Overview Report
     */
    public function monthlyOverview(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $monthlyData = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as avg_order_value'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $summary = [
            'total_revenue' => $monthlyData->sum('total_revenue'),
            'total_orders' => $monthlyData->sum('total_orders'),
            'total_months' => $monthlyData->count(),
            'avg_monthly_revenue' => $monthlyData->avg('total_revenue'),
            'best_month' => $monthlyData->sortByDesc('total_revenue')->first(),
        ];

        return view('admin.reports.monthly-overview', compact('monthlyData', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Product-wise Sales Report
     */
    public function productWise(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $productSales = OrderItem::select(
                'products.id',
                'products.name',
                'products.stock_quantity',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.stock_quantity')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $summary = [
            'total_revenue' => $productSales->sum('total_revenue'),
            'total_quantity' => $productSales->sum('total_quantity'),
            'total_products' => $productSales->count(),
            'avg_product_revenue' => $productSales->avg('total_revenue'),
        ];

        return view('admin.reports.product-wise', compact('productSales', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Top Selling Cakes Report
     */
    public function topCakes(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $limit = $request->limit ?? 10;

        $topCakes = OrderItem::select(
                'products.id',
                'products.name',
                'order_items.flavor',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as times_ordered')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'order_items.flavor')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();

        $summary = [
            'total_revenue' => $topCakes->sum('total_revenue'),
            'total_quantity' => $topCakes->sum('total_quantity'),
            'total_items' => $topCakes->count(),
            'avg_price' => $topCakes->sum('total_revenue') / $topCakes->sum('total_quantity'),
        ];

        return view('admin.reports.top-cakes', compact('topCakes', 'summary', 'startDate', 'endDate', 'limit'));
    }

    /**
     * Flavor Trends Report
     */
    public function flavorTrends(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $flavorTrends = OrderItem::select(
                'order_items.flavor',
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('order_items.flavor')
            ->groupBy('order_items.flavor', DB::raw('YEAR(orders.created_at)'), DB::raw('MONTH(orders.created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('total_quantity', 'desc')
            ->get();

        $flavorSummary = OrderItem::whereNotNull('flavor')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('order_items.flavor', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('order_items.flavor')
            ->orderBy('total', 'desc')
            ->get();

        $summary = [
            'total_flavors' => $flavorSummary->count(),
            'top_flavor' => $flavorSummary->first()->flavor ?? 'N/A',
            'top_flavor_quantity' => $flavorSummary->first()->total ?? 0,
            'total_quantity' => $flavorSummary->sum('total'),
        ];

        return view('admin.reports.flavor-trends', compact('flavorTrends', 'flavorSummary', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Category-wise Sales Report
     */
    public function categoryWise(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $categorySales = OrderItem::select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return view('admin.reports.category-wise', compact('categorySales', 'startDate', 'endDate'));
    }

    /**
     * Export Report
     */
    public function exportReport(Request $request, $type)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get data based on report type
        switch ($type) {
            case 'daily-sales':
                $data = $this->getDailySalesExport($startDate, $endDate);
                $filename = "daily_sales_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.daily-sales';
                break;
            case 'monthly-overview':
                $data = $this->getMonthlyExport($startDate, $endDate);
                $filename = "monthly_overview_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.monthly';
                break;
            case 'product-wise':
                $data = $this->getProductWiseExport($startDate, $endDate);
                $filename = "product_sales_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.product-wise';
                break;
            case 'top-cakes':
                $data = $this->getTopCakesExport($startDate, $endDate);
                $filename = "top_cakes_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.top-cakes';
                break;
            case 'flavor-trends':
                $data = $this->getFlavorTrendsExport($startDate, $endDate);
                $filename = "flavor_trends_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.flavor-trends';
                break;
            default:
                abort(404);
        }

        if ($request->format === 'excel') {
            return $this->exportToExcel($data, $filename);
        } else {
            return $this->exportToPdf($data, $filename, $view);
        }
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
                ->avg('total') ?? 0,
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
            ->select('occasion', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
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

        // Top Customers by Spending - FIXED: using 'total' instead of 'total_amount'
        $topCustomers = User::select(
                'users.id',
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

        // Raw Material Usage
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

        // Low Stock Alerts - FIXED: removed reorder_level reference
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
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
     * Financial Reports - COMPLETELY REWRITTEN to match database schema
     */
    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Simplified profit data (without cost_price since it doesn't exist)
        $profitData = OrderItem::select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->get()
            ->map(function($item) {
                // Since we don't have cost_price, we'll estimate cost as 60% of revenue (example)
                // You can adjust this percentage based on your actual costs
                $item->total_cost = $item->revenue * 0.6; // 60% cost assumption
                $item->profit = $item->revenue - $item->total_cost;
                $item->margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                return $item;
            });

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $totalCost = $profitData->sum('total_cost');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Discount Impact - using 'discount' column
        $discountImpact = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(CASE WHEN discount > 0 THEN 1 END) as orders_with_discount'),
                DB::raw('SUM(discount) as total_discount_given'),
                DB::raw('AVG(discount) as avg_discount'),
                DB::raw('SUM(total) as revenue_with_discount')
            )
            ->first();

        // Add revenue without discount
        $revenueWithoutDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', 0)
            ->sum('total');

        $discountImpact->revenue_without_discount = $revenueWithoutDiscount;

        // Payment Methods - using 'total' instead of 'total_amount'
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Seasonal Revenue - using 'total' instead of 'total_amount'
        $seasonalRevenue = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [$startDate->copy()->subYear(), $endDate])
            ->groupBy('date')
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
 * Export reports to Excel/PDF
 */
public function export(Request $request, $type, $report)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'format' => 'required|in:excel,pdf',
        'report' => 'nullable|string'
    ]);

    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);
    $reportType = $request->report ?? 'daily';

    // Get data based on report type
    switch ($report) {
        case 'sales':
            $data = $this->getSalesExportData($startDate, $endDate, $reportType);
            $filename = "sales_report_{$reportType}_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
            $view = 'admin.reports.exports.sales';
            break;
        case 'orders':
            $data = $this->getOrdersExportData($startDate, $endDate);
            $filename = "orders_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
            $view = 'admin.reports.exports.orders';
            break;
        case 'customers':
            $data = $this->getCustomersExportData($startDate, $endDate);
            $filename = "customers_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
            $view = 'admin.reports.exports.customers';
            break;
        default:
            abort(404);
    }

    if ($request->format === 'excel') {
        return $this->exportToExcel($data, $filename);
    } else {
        return $this->exportToPdf($data, $filename, $view);
    }
}

/**
 * Get sales data for export
 */
private function getSalesExportData($startDate, $endDate, $reportType = 'daily')
{
    if ($reportType == 'daily') {
        return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 1 THEN 1 ELSE 0 END) as custom_cakes'),
                DB::raw('SUM(CASE WHEN is_custom_cake = 0 THEN 1 ELSE 0 END) as standard_cakes')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'day_name')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'Date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'Day' => $item->day_name,
                    'Total Orders' => $item->total_orders,
                    'Custom Cakes' => $item->custom_cakes,
                    'Standard Cakes' => $item->standard_cakes,
                    'Total Revenue' => $item->total_revenue,
                    'Avg per Order' => $item->total_orders > 0 ? round($item->total_revenue / $item->total_orders, 2) : 0,
                ];
            });
    } elseif ($reportType == 'monthly') {
        return Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as avg_order_value')
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
                    'Total Orders' => $item->total_orders,
                    'Total Revenue' => $item->total_revenue,
                    'Avg Order Value' => round($item->avg_order_value, 2),
                ];
            });
    } elseif ($reportType == 'product') {
        return OrderItem::select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Product Name' => $item->product_name,
                    'Quantity Sold' => $item->total_quantity,
                    'Total Revenue' => $item->total_revenue,
                    'Order Count' => $item->order_count,
                    'Avg Price' => $item->total_quantity > 0 ? round($item->total_revenue / $item->total_quantity, 2) : 0,
                ];
            });
    } elseif ($reportType == 'top') {
        return OrderItem::select(
                'products.name as product_name',
                'order_items.flavor',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as times_ordered')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name', 'order_items.flavor')
            ->orderBy('total_quantity', 'desc')
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'Product Name' => $item->product_name,
                    'Flavor' => ucfirst($item->flavor) ?? 'N/A',
                    'Quantity Sold' => $item->total_quantity,
                    'Total Revenue' => $item->total_revenue,
                    'Times Ordered' => $item->times_ordered,
                ];
            });
    }

    return collect([]);
}

    /**
     * Export to Excel
     */
    private function exportToExcel($data, $filename)
    {
        // If you have Maatwebsite Excel installed
        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection,
                                        \Maatwebsite\Excel\Concerns\WithHeadings {
                private $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    if ($this->data->isNotEmpty()) {
                        return array_keys($this->data->first()->toArray());
                    }
                    return [];
                }
            }, $filename . '.xlsx');
        }

        // Fallback to CSV
        return $this->exportToCsv($data, $filename);
    }

    /**
     * Export to PDF
     */
    private function exportToPdf($data, $filename, $view)
    {
        // If you have DomPDF installed
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = PDF::loadView($view, compact('data'));
            return $pdf->download($filename . '.pdf');
        }

        // Fallback to CSV
        return $this->exportToCsv($data, $filename);
    }

    /**
     * Export to CSV (fallback)
     */
    private function exportToCsv($data, $filename)
    {
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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

}
