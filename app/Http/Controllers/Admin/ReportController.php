<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailySalesExport;
use App\Exports\MonthlySalesExport;
use App\Exports\ProductSalesExport;
use App\Exports\TopSellingExport;
use App\Exports\LowStockExport;
use App\Exports\WalkinVsOnlineExport;
use App\Exports\OrderSummaryExport;
use App\Exports\TopCustomersExport;

class ReportController extends Controller
{
    /**
     * Show reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Daily Sales Report
     */
    public function dailySales(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $sales = Order::whereDate('created_at', $date)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as paid_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "pending" THEN total ELSE 0 END) as pending_revenue')
            )
            ->first();

        $paymentMethods = Order::whereDate('created_at', $date)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        $hourlySales = Order::whereDate('created_at', $date)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();

        return view('admin.reports.daily-sales', compact('sales', 'paymentMethods', 'hourlySales', 'date'));
    }

    /**
     * Monthly Sales Report
     */
    public function monthlySales(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as average_order_value'),
                DB::raw('COUNT(DISTINCT user_id) as unique_customers')
            )
            ->first();

        $dailyBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        $growthData = $this->calculateGrowth($year, $month);

        return view('admin.reports.monthly-sales', compact(
            'sales', 'dailyBreakdown', 'statusBreakdown', 'growthData', 'year', 'month'
        ));
    }

    /**
     * Product-wise Sales Report
     */
    public function productSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $products = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity')
            ->paginate(20);

        $summary = OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(quantity) as total_items_sold'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT product_id) as unique_products')
            )
            ->first();

        return view('admin.reports.product-sales', compact('products', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Category Sales Report
     */
    public function categorySales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $categories = Category::withCount(['products'])
            ->with(['products' => function($query) use ($startDate, $endDate) {
                $query->withCount(['orderItems as sold_quantity' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->select(DB::raw('SUM(quantity)'));
                }])->withSum(['orderItems as revenue' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->select(DB::raw('SUM(subtotal)'));
                }]);
            }])
            ->get();

        $categoryStats = [];
        foreach ($categories as $category) {
            $totalSold = 0;
            $totalRevenue = 0;
            foreach ($category->products as $product) {
                $totalSold += $product->sold_quantity ?? 0;
                $totalRevenue += $product->revenue ?? 0;
            }
            $categoryStats[] = [
                'name' => $category->name,
                'total_sold' => $totalSold,
                'total_revenue' => $totalRevenue,
                'product_count' => $category->products_count
            ];
        }

        // Sort by revenue
        usort($categoryStats, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return view('admin.reports.category-sales', compact('categoryStats', 'startDate', 'endDate'));
    }

    /**
     * Top Selling Products Report
     */
    public function topSelling(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, year, all

        $query = OrderItem::query();

        switch($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $topByQuantity = (clone $query)
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $topByRevenue = (clone $query)
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('admin.reports.top-selling', compact('topByQuantity', 'topByRevenue', 'period'));
    }

    /**
     * Low Selling Products Report
     */
    public function lowSelling(Request $request)
    {
        $threshold = $request->get('threshold', 5);
        $period = $request->get('period', 30); // days

        $cutoffDate = now()->subDays($period);

        $products = Product::withCount(['orderItems as sold_quantity' => function($query) use ($cutoffDate) {
                $query->where('created_at', '>=', $cutoffDate);
            }])
            ->withSum(['orderItems as revenue' => function($query) use ($cutoffDate) {
                $query->where('created_at', '>=', $cutoffDate);
            }], 'subtotal')
            ->having('sold_quantity', '<=', $threshold)
            ->orHavingNull('sold_quantity')
            ->orderBy('sold_quantity')
            ->paginate(20);

        return view('admin.reports.low-selling', compact('products', 'threshold', 'period'));
    }

    /**
     * Order Summary Report
     */
    public function orderSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $statusCounts = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');

        return view('admin.reports.order-summary', compact('statusCounts', 'totalOrders', 'totalRevenue', 'startDate', 'endDate'));
    }

    /**
     * Custom Cake Orders Report
     */
    public function customCakeOrders(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $customCategory = Category::where('name', 'LIKE', '%custom%')->first();

        if (!$customCategory) {
            $customOrders = collect([]);
        } else {
            $customOrders = Order::whereHas('items.product', function($query) use ($customCategory) {
                    $query->where('category_id', $customCategory->id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['items.product', 'user'])
                ->orderByDesc('created_at')
                ->paginate(20);
        }

        return view('admin.reports.custom-cake-orders', compact('customOrders', 'startDate', 'endDate'));
    }

    /**
     * Delivery vs Pickup Report
     */
    public function deliveryVsPickup(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Assuming delivery orders have shipping_cost > 0 or specific flag
        $deliveryOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('shipping_cost', '>', 0)
            ->count();

        $pickupOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('shipping_cost', '<=', 0)
            ->count();

        $deliveryRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('shipping_cost', '>', 0)
            ->sum('total');

        $pickupRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('shipping_cost', '<=', 0)
            ->sum('total');

        $trendData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN shipping_cost > 0 THEN 1 ELSE 0 END) as delivery_count'),
                DB::raw('SUM(CASE WHEN shipping_cost <= 0 THEN 1 ELSE 0 END) as pickup_count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.reports.delivery-vs-pickup', compact(
            'deliveryOrders', 'pickupOrders', 'deliveryRevenue', 'pickupRevenue',
            'trendData', 'startDate', 'endDate'
        ));
    }

    /**
     * Top Customers Report
     */
    public function topCustomers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $topCustomers = User::where('is_admin', false)
            ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        return view('admin.reports.top-customers', compact('topCustomers', 'startDate', 'endDate'));
    }

    /**
     * Customer Frequency Report
     */
    public function customerFrequency(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $frequencyData = User::where('is_admin', false)
            ->whereHas('orders', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->withCount(['orders as order_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->groupBy(function($user) {
                if ($user->order_count >= 10) return '10+ orders';
                if ($user->order_count >= 5) return '5-9 orders';
                if ($user->order_count >= 2) return '2-4 orders';
                return '1 order';
            })
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'percentage' => 0 // Will calculate later
                ];
            });

        $total = $frequencyData->sum('count');
        foreach ($frequencyData as &$data) {
            $data['percentage'] = $total > 0 ? round(($data['count'] / $total) * 100, 2) : 0;
        }

        return view('admin.reports.customer-frequency', compact('frequencyData', 'total', 'startDate', 'endDate'));
    }

    /**
     * Low Stock Report
     */
    public function lowStock(Request $request)
    {
        $threshold = $request->get('threshold', 10);

        $products = Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->paginate(20);

        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();

        return view('admin.reports.low-stock', compact('products', 'threshold', 'outOfStock'));
    }

    /**
     * Payment Method Report
     */
    public function paymentMethods(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $payments = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as paid_amount')
            )
            ->groupBy('payment_method')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = $payments->sum('total_revenue');

        return view('admin.reports.payment-methods', compact('payments', 'totalRevenue', 'startDate', 'endDate'));
    }

    /**
     * Calculate growth compared to previous month
     */
    private function calculateGrowth($year, $month)
    {
        $currentStart = Carbon::create($year, $month, 1)->startOfMonth();
        $currentEnd = Carbon::create($year, $month, 1)->endOfMonth();

        $previousStart = Carbon::create($year, $month, 1)->subMonth()->startOfMonth();
        $previousEnd = Carbon::create($year, $month, 1)->subMonth()->endOfMonth();

        $currentRevenue = Order::whereBetween('created_at', [$currentStart, $currentEnd])->sum('total');
        $previousRevenue = Order::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total');

        $currentOrders = Order::whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previousOrders = Order::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $revenueGrowth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $ordersGrowth = $previousOrders > 0 ? (($currentOrders - $previousOrders) / $previousOrders) * 100 : 0;

        return [
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'revenue_growth' => round($revenueGrowth, 2),
            'current_orders' => $currentOrders,
            'previous_orders' => $previousOrders,
            'orders_growth' => round($ordersGrowth, 2)
        ];
    }

    /**
     * Export Daily Sales
     */
    public function exportDailySales($format, Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        if ($format == 'pdf') {
            $sales = Order::whereDate('created_at', $date)->get();
            $pdf = Pdf::loadView('admin.reports.exports.daily-sales-pdf', [
                'sales' => $sales,
                'date' => $date
            ]);
            return $pdf->download('daily-sales-' . $date . '.pdf');
        }

        return Excel::download(
            new DailySalesExport($date),
            'daily-sales-' . $date . '.xlsx'
        );
    }

    /**
     * Export Monthly Sales
     */
    public function exportMonthlySales($format, Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        if ($format == 'pdf') {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            $sales = Order::whereBetween('created_at', [$startDate, $endDate])->get();

            $pdf = Pdf::loadView('admin.reports.exports.monthly-sales-pdf', [
                'sales' => $sales,
                'year' => $year,
                'month' => $month
            ]);
            return $pdf->download('monthly-sales-' . $year . '-' . $month . '.pdf');
        }

        return Excel::download(
            new MonthlySalesExport($year, $month),
            'monthly-sales-' . $year . '-' . $month . '.xlsx'
        );
    }

    /**
     * Export Product Sales
     */
    public function exportProductSales($format, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($format == 'pdf') {
            $products = OrderItem::whereBetween('created_at', [$startDate, $endDate])
                ->select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
                ->groupBy('product_name')
                ->orderByDesc('total_revenue')
                ->get();

            $pdf = Pdf::loadView('admin.reports.exports.product-sales-pdf', [
                'products' => $products,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->download('product-sales-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        return Excel::download(
            new ProductSalesExport($startDate, $endDate),
            'product-sales-' . $startDate . '-to-' . $endDate . '.xlsx'
        );
    }

    /**
     * Export Top Selling
     */
    public function exportTopSelling($format, Request $request)
    {
        $period = $request->get('period', 'month');

        if ($format == 'pdf') {
            $query = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
                ->groupBy('product_name')
                ->orderByDesc('total_revenue')
                ->limit(50);

            switch($period) {
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }

            $products = $query->get();

            $pdf = Pdf::loadView('admin.reports.exports.top-selling-pdf', [
                'products' => $products,
                'period' => $period
            ]);
            return $pdf->download('top-selling-' . $period . '.pdf');
        }

        return Excel::download(
            new TopSellingExport($period),
            'top-selling-' . $period . '.xlsx'
        );
    }

    /**
     * Export Low Stock
     */
    public function exportLowStock($format, Request $request)
    {
        $threshold = $request->get('threshold', 10);

        if ($format == 'pdf') {
            $products = Product::where('stock_quantity', '<=', $threshold)
                ->where('stock_quantity', '>', 0)
                ->with('category')
                ->orderBy('stock_quantity')
                ->get();

            $pdf = Pdf::loadView('admin.reports.exports.low-stock-pdf', [
                'products' => $products,
                'threshold' => $threshold
            ]);
            return $pdf->download('low-stock-report.pdf');
        }

        return Excel::download(
            new LowStockExport($threshold),
            'low-stock-report.xlsx'
        );
    }

    /**
     * Export Walk-in vs Online
     */
    public function exportWalkinVsOnline($format, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($format == 'pdf') {
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->with('items')
                ->orderBy('order_type')
                ->orderBy('created_at', 'desc')
                ->get();

            $pdf = Pdf::loadView('admin.reports.exports.walkin-vs-online-pdf', [
                'orders' => $orders,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->download('walkin-vs-online.pdf');
        }

        return Excel::download(
            new WalkinVsOnlineExport($startDate, $endDate),
            'walkin-vs-online.xlsx'
        );
    }

    /**
     * Export Order Summary
     */
    public function exportOrderSummary($format, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($format == 'pdf') {
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->with('items')
                ->orderBy('status')
                ->orderBy('created_at', 'desc')
                ->get();

            $pdf = Pdf::loadView('admin.reports.exports.order-summary-pdf', [
                'orders' => $orders,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->download('order-summary.pdf');
        }

        return Excel::download(
            new OrderSummaryExport($startDate, $endDate),
            'order-summary.xlsx'
        );
    }

    /**
     * Export Top Customers
     */
    public function exportTopCustomers($format, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($format == 'pdf') {
            $customers = User::where('is_admin', false)
                ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }], 'total')
                ->having('orders_count', '>', 0)
                ->orderByDesc('total_spent')
                ->limit(100)
                ->get();

            $pdf = Pdf::loadView('admin.reports.exports.top-customers-pdf', [
                'customers' => $customers,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->download('top-customers.pdf');
        }

        return Excel::download(
            new TopCustomersExport($startDate, $endDate),
            'top-customers.xlsx'
        );

    }

}
