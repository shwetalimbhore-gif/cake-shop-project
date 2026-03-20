<?php

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

// Import Helpers and Exports
use App\Helpers\ReportHelpers;
use App\Exports\ReportExportService;
use App\Exports\SalesReportExport;
use App\Exports\OrdersReportExport;
use App\Exports\CustomersReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\FinancialReportExport;

class ReportController extends Controller
{
    // Add this method inside your ReportController class
    private function validateDateRange($request, $defaultStart = null, $defaultEnd = null)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)
            : ($defaultStart ?? Carbon::now()->startOfMonth());

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)
            : ($defaultEnd ?? Carbon::now()->endOfDay());

        return [$startDate, $endDate];
    }

    /**
     * Display the main reports dashboard
     */
    public function index()
    {
        $totalRevenue = Order::sum('total') ?? 0;
        $totalOrders = Order::count() ?? 0;
        $totalCustomers = User::whereHas('orders')->count() ?? 0;
        $lowStockCount = Product::where('stock_quantity', '<=', 10)->count() ?? 0;

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

        $peakDay = Order::select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('total', 'DESC')
            ->first()->day ?? 'Saturday';

        return view('admin.reports.index', compact(
            'totalRevenue', 'totalOrders', 'totalCustomers', 'lowStockCount',
            'topFlavor', 'topOccasion', 'avgOrderValue', 'peakDay'
        ));
    }

    /**
     * Daily Sales Report
     */
    public function dailySales(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

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

        $summary = [
            'total_revenue' => $dailySales->sum('total_revenue'),
            'total_orders' => $dailySales->sum('total_orders'),
            'total_custom' => $dailySales->sum('custom_cakes'),
            'total_standard' => $dailySales->sum('standard_cakes'),
            'avg_daily_revenue' => $dailySales->avg('total_revenue'),
            'avg_daily_orders' => round($dailySales->avg('total_orders')),
            'best_day' => $dailySales->sortByDesc('total_revenue')->first(),
        ];

        $chartData = [
            'dates' => $dailySales->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
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
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request, Carbon::now()->startOfYear());

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
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

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
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);
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
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

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
            ->groupBy('order_items.flavor', 'year', 'month')
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
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        $categorySales = OrderItem::select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('AVG(order_items.subtotal / order_items.quantity) as avg_price')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $summary = [
            'total_categories' => $categorySales->count(),
            'total_revenue' => $categorySales->sum('total_revenue'),
            'total_quantity' => $categorySales->sum('total_quantity'),
            'total_orders' => $categorySales->sum('order_count'),
            'avg_category_revenue' => $categorySales->count() > 0 ? $categorySales->avg('total_revenue') : 0,
            'top_category' => $categorySales->first()->name ?? 'N/A',
            'top_category_revenue' => $categorySales->first()->total_revenue ?? 0,
        ];

        $chartData = [
            'labels' => $categorySales->pluck('name'),
            'revenues' => $categorySales->pluck('total_revenue'),
            'quantities' => $categorySales->pluck('total_quantity'),
        ];

        return view('admin.reports.category-wise', compact('categorySales', 'summary', 'chartData', 'startDate', 'endDate'));
    }

    /**
     * Orders Main Dashboard with Status Breakdown
     */
    public function orders(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // Get all possible order statuses from database
        $allStatuses = Order::select('status')->distinct()->pluck('status')->toArray();

        // Get order status breakdown from database
        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->get();

        // Calculate order summary with all statuses
        $orderSummary = [
            'total' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'confirmed' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'confirmed')->count(),
            'delivered' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'delivered')->count(),
            'pending' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'processing' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'processing')->count(),
            'cancelled' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])->avg('total') ?? 0,
            'custom' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->count(),
        ];

        // Get recent orders with status
        $recentOrders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.orders', compact(
            'orderSummary',
            'recentOrders',
            'statusBreakdown',
            'allStatuses',
            'startDate',
            'endDate'
        ));
    }

   /**
     * Order Summary Report with Detailed Status Analysis
     */
    public function orderSummary(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // First, get the order summary
        $orderData = [
            'total' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])->avg('total') ?? 0,
            'completed' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'confirmed' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'confirmed')->count(),
            'delivered' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'delivered')->count(),
            'pending' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'processing' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'processing')->count(),
            'cancelled' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
        ];

        // Order status breakdown
        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        // Hourly order distribution with status
        $hourlyDistribution = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_count'),
                DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing_count'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Weekly trend with status breakdown
        $weeklyTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_count'),
                DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing_count'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count')
            )
            ->groupBy('day')
            ->orderByRaw('FIELD(day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
            ->get();

        // Calculate summary statistics
        $summary = [
            'total_orders' => $orderData['total'],
            'total_revenue' => $orderData['total_revenue'],
            'avg_order' => $orderData['avg_order_value'],
            'completed_orders' => $orderData['completed'],
            'confirmed_orders' => $orderData['confirmed'],
            'delivered_orders' => $orderData['delivered'],
            'pending_orders' => $orderData['pending'],
            'processing_orders' => $orderData['processing'],
            'cancelled_orders' => $orderData['cancelled'],
            'peak_hour' => $hourlyDistribution->sortByDesc('count')->first()->hour ?? 12,
            'peak_day' => $weeklyTrend->sortByDesc('count')->first()->day ?? 'Saturday',
            'completion_rate' => $orderData['total'] > 0
                ? (($orderData['completed'] + $orderData['delivered']) / $orderData['total']) * 100
                : 0,
            'cancellation_rate' => $orderData['total'] > 0
                ? ($orderData['cancelled'] / $orderData['total']) * 100
                : 0,
        ];

        return view('admin.reports.orders-summary', compact(
            'statusBreakdown',
            'hourlyDistribution',
            'weeklyTrend',
            'summary',
            'startDate',
            'endDate'
        ));
    }
    /**
     * Custom Cake Orders Report
     */
    public function customCakes(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // Custom cakes query
        $customCakes = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->count(),
            'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->sum('total'),
            'avg_price' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->avg('total') ?? 0,
            'with_message' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->whereNotNull('custom_message')->count(),
            'with_design' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->whereNotNull('cake_design')->count(),
        ];

        // Popular designs
        $popularDesigns = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->whereNotNull('cake_design')
            ->select('cake_design', DB::raw('COUNT(*) as count'))
            ->groupBy('cake_design')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Monthly trend
        $monthlyTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.reports.orders-custom', compact(
            'customCakes', 'stats', 'popularDesigns', 'monthlyTrend', 'startDate', 'endDate'
        ));
    }

    /**
     * Delivery vs Pickup Report
     */
    public function deliveryVsPickup(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // Overall comparison
        $comparison = [
            'delivery' => [
                'count' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'delivery')->count(),
                'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'delivery')->sum('total'),
            ],
            'pickup' => [
                'count' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'pickup')->count(),
                'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'pickup')->sum('total'),
            ],
        ];

        // Daily trend
        $dailyTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN order_type = "delivery" THEN 1 ELSE 0 END) as delivery_count'),
                DB::raw('SUM(CASE WHEN order_type = "pickup" THEN 1 ELSE 0 END) as pickup_count'),
                DB::raw('SUM(CASE WHEN order_type = "delivery" THEN total ELSE 0 END) as delivery_revenue'),
                DB::raw('SUM(CASE WHEN order_type = "pickup" THEN total ELSE 0 END) as pickup_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Hourly distribution for each type
        $hourlyDelivery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('order_type', 'delivery')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hourlyPickup = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('order_type', 'pickup')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Day of week preference
        $dayPreference = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('SUM(CASE WHEN order_type = "delivery" THEN 1 ELSE 0 END) as delivery_count'),
                DB::raw('SUM(CASE WHEN order_type = "pickup" THEN 1 ELSE 0 END) as pickup_count')
            )
            ->groupBy('day')
            ->orderByRaw('FIELD(day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
            ->get();

        return view('admin.reports.orders-delivery-pickup', compact(
            'comparison', 'dailyTrend', 'hourlyDelivery', 'hourlyPickup', 'dayPreference', 'startDate', 'endDate'
        ));
    }

    /**
     * Occasion-based Orders Report
     */
    public function occasionBased(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // Occasion breakdown
        $occasionBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('occasion')
            ->select('occasion', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('occasion')
            ->orderBy('count', 'desc')
            ->get();

        // Monthly occasion trends
        $monthlyOccasion = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('occasion')
            ->select(
                'occasion',
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('occasion', 'year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Top occasions by revenue
        $topOccasions = $occasionBreakdown->take(5);

        // Occasion vs regular orders comparison
        $comparison = [
            'occasion_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('occasion')->count(),
            'regular_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNull('occasion')->count(),
            'occasion_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('occasion')->sum('total'),
            'regular_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNull('occasion')->sum('total'),
        ];

        return view('admin.reports.orders-occasion', compact(
            'occasionBreakdown', 'monthlyOccasion', 'topOccasions', 'comparison', 'startDate', 'endDate'
        ));
    }

    /**
     * Pre-order vs Walk-in Report
     */
    public function preorderVsWalkin(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        // Overall comparison
        $comparison = [
            'preorder' => [
                'count' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('pre_order_date')->count(),
                'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('pre_order_date')->sum('total'),
            ],
            'walkin' => [
                'count' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNull('pre_order_date')->count(),
                'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNull('pre_order_date')->sum('total'),
            ],
        ];

        // Pre-order lead time analysis
        $leadTime = Order::whereNotNull('pre_order_date')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('AVG(DATEDIFF(created_at, pre_order_date)) as avg_lead_days'),
                DB::raw('MIN(DATEDIFF(created_at, pre_order_date)) as min_lead_days'),
                DB::raw('MAX(DATEDIFF(created_at, pre_order_date)) as max_lead_days')
            )
            ->first();

        // Pre-order by day of week
        $preorderByDay = Order::whereNotNull('pre_order_date')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->orderByRaw('FIELD(day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
            ->get();

        // Monthly trend
        $monthlyTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN pre_order_date IS NOT NULL THEN 1 ELSE 0 END) as preorder_count'),
                DB::raw('SUM(CASE WHEN pre_order_date IS NULL THEN 1 ELSE 0 END) as walkin_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.reports.orders-preorder', compact(
            'comparison', 'leadTime', 'preorderByDay', 'monthlyTrend', 'startDate', 'endDate'
        ));
    }

    /**
     * Get Orders Export Data
     */
    private function getOrdersExportData($startDate, $endDate, $type = 'summary')
    {
        switch ($type) {
            case 'summary':
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
                    })->toArray();

            case 'custom-cakes':
                return Order::whereBetween('created_at', [$startDate, $endDate])
                    ->where('is_custom_cake', true)
                    ->with('user')
                    ->get()
                    ->map(function($order) {
                        return [
                            'Order ID' => $order->id,
                            'Date' => $order->created_at->format('Y-m-d'),
                            'Customer' => $order->user->name ?? ($order->walkin_customer_name ?? 'Guest'),
                            'Cake Design' => $order->cake_design ?? 'Standard',
                            'Message' => $order->custom_message ? 'Yes' : 'No',
                            'Occasion' => ucfirst($order->occasion ?? 'N/A'),
                            'Total' => $order->total,
                            'Status' => ucfirst($order->status),
                        ];
                    })->toArray();

            default:
                return [];
        }
    }

    /**
     * Customers Report
     */
    public function customers(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        $topCustomers = User::select(
                'users.id', 'users.name', 'users.email',
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

        $customerOrders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        $customers = User::whereHas('orders', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->withCount(['orders' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])])
            ->get();

        $newCustomers = $customers->filter(fn($c) => $c->orders_count == 1)->count();
        $returningCustomers = $customers->filter(fn($c) => $c->orders_count > 1)->count();

        $orderFrequency = User::select(DB::raw('COUNT(orders.id) as order_count'), DB::raw('COUNT(DISTINCT users.id) as user_count'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->having('order_count', '>', 0)
            ->get()
            ->groupBy('order_count')
            ->map(fn($group) => $group->sum('user_count'));

        $specialDateCustomers = User::whereHas('orders', fn($q) => $q->whereNotNull('occasion'))
            ->with(['orders' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])->whereNotNull('occasion')])
            ->get()
            ->filter(fn($user) => $user->orders->isNotEmpty());

        return view('admin.reports.customers', compact(
            'topCustomers', 'customerOrders', 'newCustomers', 'returningCustomers',
            'orderFrequency', 'specialDateCustomers', 'startDate', 'endDate'
        ));
    }

    /**
     * Inventory Report
     */
    public function inventory(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

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

        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get();

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
            'rawMaterialUsage', 'lowStockProducts', 'stockMovement', 'startDate', 'endDate'
        ));
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        $profitData = OrderItem::select(
                'products.id', 'products.name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->get()
            ->map(function($item) {
                $item->total_cost = $item->revenue * 0.6;
                $item->profit = $item->revenue - $item->total_cost;
                $item->margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                return $item;
            });

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $totalCost = $profitData->sum('total_cost');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $discountImpact = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(CASE WHEN discount > 0 THEN 1 END) as orders_with_discount'),
                DB::raw('SUM(discount) as total_discount_given'),
                DB::raw('AVG(discount) as avg_discount'),
                DB::raw('SUM(total) as revenue_with_discount')
            )
            ->first();

        $revenueWithoutDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('discount', 0)
            ->sum('total');

        $discountImpact->revenue_without_discount = $revenueWithoutDiscount;

        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        $seasonalRevenue = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->whereBetween('created_at', [$startDate->copy()->subYear(), $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        return view('admin.reports.financial', compact(
            'profitData', 'totalRevenue', 'totalCost', 'totalProfit', 'profitMargin',
            'discountImpact', 'paymentMethods', 'seasonalRevenue', 'startDate', 'endDate'
        ));
    }

    /**
     * Update the exportReport method to handle orders type
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
        $exportType = $request->get('type', 'summary');

        // Get data based on report type
        switch ($type) {
            case 'daily-sales':
                $data = $this->getDailySalesExport($startDate, $endDate);
                $filename = "daily_sales_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.daily-sales';
                $title = 'Daily Sales Report';
                break;

            case 'orders':
                $data = $this->getOrdersExportData($startDate, $endDate, $exportType);
                $filename = "orders_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
                $view = 'admin.reports.exports.orders';
                $title = 'Orders Report';
                break;

            // ... other cases ...

            default:
                abort(404);
        }

        if ($request->format === 'excel') {
            return ReportExportService::toExcel($data, $filename, $title);
        } else {
            return ReportExportService::toPdf($data, $filename, $view, $title);
        }
    }

    /**
     * Get export data based on report type
     */
    private function getExportData($type, $startDate, $endDate, $request)
    {
        switch ($type) {
            case 'daily-sales':
                return $this->getDailySalesExport($startDate, $endDate);

            case 'monthly-overview':
                return $this->getMonthlyExport($startDate, $endDate);

            case 'product-wise':
                return $this->getProductWiseExport($startDate, $endDate);

            case 'top-cakes':
                $limit = $request->limit ?? 10;
                return $this->getTopCakesExport($startDate, $endDate, $limit);

            case 'flavor-trends':
                return $this->getFlavorTrendsExport($startDate, $endDate);

            case 'category-wise':
                return $this->getCategoryWiseExport($startDate, $endDate);

            case 'orders':
                return $this->getOrdersExportData($startDate, $endDate);

            case 'customers':
                return $this->getCustomersExportData($startDate, $endDate);

            case 'inventory':
                return $this->getInventoryExportData($startDate, $endDate);

            case 'financial':
                return $this->getFinancialExportData($startDate, $endDate);

            default:
                abort(404);
        }
    }

    /**
     * Get daily sales export data
     */
    private function getDailySalesExport($startDate, $endDate)
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
            })
            ->toArray(); // Important: Convert to array
    }

    /**
     * Get monthly export data
     */
    private function getMonthlyExport($startDate, $endDate)
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
            })
            ->toArray();
    }

    /**
     * Get product wise export data
     */
    private function getProductWiseExport($startDate, $endDate)
    {
        return OrderItem::select(
                'products.name as product_name',
                'products.sku',
                'products.stock_quantity',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name', 'products.sku', 'products.stock_quantity')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'Product Name' => $item->product_name,
                    'SKU' => $item->sku,
                    'Current Stock' => $item->stock_quantity,
                    'Quantity Sold' => $item->quantity,
                    'Order Count' => $item->order_count,
                    'Total Revenue' => $item->revenue,
                    'Average Price' => $item->quantity > 0 ? round($item->revenue / $item->quantity, 2) : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get top cakes export data
     */
    private function getTopCakesExport($startDate, $endDate, $limit = 10)
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
            })
            ->toArray();
    }

    /**
     * Get category wise export data
     */
    private function getCategoryWiseExport($startDate, $endDate)
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
            })
            ->toArray();
    }

    /**
     * Get export filename
     */
    private function getExportFilename($type, $startDate, $endDate)
    {
        $typeMap = [
            'daily-sales' => 'daily_sales',
            'monthly-overview' => 'monthly_overview',
            'product-wise' => 'product_sales',
            'top-cakes' => 'top_cakes',
            'flavor-trends' => 'flavor_trends',
            'category-wise' => 'category_sales',
            'orders' => 'orders_report',
            'customers' => 'customers_report',
            'inventory' => 'inventory_report',
            'financial' => 'financial_report',
        ];

        $prefix = $typeMap[$type] ?? str_replace('-', '_', $type);
        return "{$prefix}_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";
    }

    /**
     * Get report title
     */
    private function getReportTitle($type)
    {
        $titles = [
            'daily-sales' => 'Daily Sales Report',
            'monthly-overview' => 'Monthly Overview Report',
            'product-wise' => 'Product-wise Sales Report',
            'top-cakes' => 'Top Selling Cakes Report',
            'flavor-trends' => 'Flavor Trends Report',
            'category-wise' => 'Category-wise Sales Report',
            'orders' => 'Orders Report',
            'customers' => 'Customers Report',
            'inventory' => 'Inventory Report',
            'financial' => 'Financial Report',
        ];

        return $titles[$type] ?? ucfirst(str_replace('-', ' ', $type)) . ' Report';
    }
}
