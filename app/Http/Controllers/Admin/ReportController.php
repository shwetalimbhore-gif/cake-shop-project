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
     * Orders Report
     */
    public function orders(Request $request)
    {
        [$startDate, $endDate] = ReportHelpers::validateDateRange($request);

        $orderSummary = [
            'total' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'pending' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'cancelled' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
            'processing' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'processing')->count(),
        ];

        $customCakes = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_custom_cake', true)
            ->with('user')
            ->latest()
            ->paginate(20);

        $customCakesStats = [
            'total' => $customCakes->total(),
            'avg_price' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->avg('total') ?? 0,
            'with_message' => Order::whereBetween('created_at', [$startDate, $endDate])->where('is_custom_cake', true)->whereNotNull('custom_message')->count(),
        ];

        $preorderVsWalkin = [
            'pre_order' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('pre_order_date')->count(),
            'walk_in' => Order::whereBetween('created_at', [$startDate, $endDate])->whereNull('pre_order_date')->count(),
        ];

        $deliveryVsPickup = [
            'delivery' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'delivery')->count(),
            'pickup' => Order::whereBetween('created_at', [$startDate, $endDate])->where('order_type', 'pickup')->count(),
        ];

        $occasionOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('occasion')
            ->select('occasion', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->groupBy('occasion')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.reports.orders', compact(
            'orderSummary', 'customCakes', 'customCakesStats', 'preorderVsWalkin',
            'deliveryVsPickup', 'occasionOrders', 'startDate', 'endDate'
        ));
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

        // Get export data based on type
        $exportData = $this->getExportData($type, $startDate, $endDate, $request);
        $filename = $this->getExportFilename($type, $startDate, $endDate);
        $view = "admin.reports.exports.{$type}";
        $title = $this->getReportTitle($type);

        if ($request->format === 'excel') {
            return ReportExportService::toExcel($exportData, $filename, $title);
        } else {
            return ReportExportService::toPdf($exportData, $filename, $view, $title);
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
