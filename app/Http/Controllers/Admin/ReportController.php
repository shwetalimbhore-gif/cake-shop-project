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
     * Export Daily Sales Report
     */
    public function exportDailySales(Request $request, $format = 'csv')
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

        $data = [
            'date' => $date,
            'formatted_date' => Carbon::parse($date)->format('F d, Y'),
            'sales' => $sales,
            'paymentMethods' => $paymentMethods,
            'hourlySales' => $hourlySales,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format == 'pdf') {
            return $this->exportDailySalesPDF($data);
        }

        return $this->exportDailySalesCSV($data);
    }

    /**
     * Export Daily Sales as CSV
     */
    private function exportDailySalesCSV($data)
    {
        $filename = 'daily-sales-' . $data['date'] . '.csv';
        $handle = fopen('php://memory', 'r+');

        // Add headers
        fputcsv($handle, ['Daily Sales Report - ' . $data['formatted_date']]);
        fputcsv($handle, ['Generated at: ' . $data['generated_at']]);
        fputcsv($handle, []); // Empty line

        // Summary
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Orders', 'Total Revenue', 'Paid Revenue', 'Pending Revenue', 'Average Order']);
        fputcsv($handle, [
            $data['sales']->total_orders ?? 0,
            $data['sales']->total_revenue ?? 0,
            $data['sales']->paid_revenue ?? 0,
            $data['sales']->pending_revenue ?? 0,
            $data['sales']->total_orders > 0 ? round($data['sales']->total_revenue / $data['sales']->total_orders, 2) : 0
        ]);
        fputcsv($handle, []); // Empty line

        // Payment Methods
        fputcsv($handle, ['PAYMENT METHODS']);
        fputcsv($handle, ['Method', 'Orders', 'Revenue']);
        foreach ($data['paymentMethods'] as $method) {
            fputcsv($handle, [
                $method->payment_method ?? 'cash_on_delivery',
                $method->count,
                $method->total
            ]);
        }
        fputcsv($handle, []); // Empty line

        // Hourly Breakdown
        fputcsv($handle, ['HOURLY BREAKDOWN']);
        fputcsv($handle, ['Hour', 'Orders', 'Revenue']);

        $hourlyArray = [];
        for($i = 0; $i < 24; $i++) {
            $hourlyArray[$i] = ['orders' => 0, 'revenue' => 0];
        }
        foreach ($data['hourlySales'] as $sale) {
            $hourlyArray[$sale->hour] = [
                'orders' => $sale->orders,
                'revenue' => $sale->revenue
            ];
        }

        foreach ($hourlyArray as $hour => $stats) {
            if ($stats['orders'] > 0 || $stats['revenue'] > 0) {
                fputcsv($handle, [
                    sprintf('%02d:00 - %02d:00', $hour, $hour+1),
                    $stats['orders'],
                    $stats['revenue']
                ]);
            }
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export Daily Sales as PDF
     */
    private function exportDailySalesPDF($data)
    {
        $pdf = Pdf::loadView('admin.reports.exports.daily-sales-pdf', $data);
        return $pdf->download('daily-sales-' . $data['date'] . '.pdf');
    }

    /**
     * Export Monthly Sales Report
     */
    public function exportMonthlySales(Request $request, $format = 'csv')
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

        $data = [
            'year' => $year,
            'month' => $month,
            'month_name' => Carbon::create($year, $month, 1)->format('F Y'),
            'sales' => $sales,
            'dailyBreakdown' => $dailyBreakdown,
            'statusBreakdown' => $statusBreakdown,
            'growthData' => $growthData,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format == 'pdf') {
            return $this->exportMonthlySalesPDF($data);
        }

        return $this->exportMonthlySalesCSV($data);
    }

    /**
     * Export Monthly Sales as CSV
     */
    private function exportMonthlySalesCSV($data)
    {
        $filename = 'monthly-sales-' . $data['year'] . '-' . $data['month'] . '.csv';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, ['Monthly Sales Report - ' . $data['month_name']]);
        fputcsv($handle, ['Generated at: ' . $data['generated_at']]);
        fputcsv($handle, []);

        // Summary
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Orders', 'Total Revenue', 'Avg Order Value', 'Unique Customers']);
        fputcsv($handle, [
            $data['sales']->total_orders ?? 0,
            $data['sales']->total_revenue ?? 0,
            $data['sales']->average_order_value ?? 0,
            $data['sales']->unique_customers ?? 0
        ]);
        fputcsv($handle, []);

        // Growth
        fputcsv($handle, ['GROWTH VS PREVIOUS MONTH']);
        fputcsv($handle, ['Metric', 'Current', 'Previous', 'Growth %']);
        fputcsv($handle, [
            'Revenue',
            $data['growthData']['current_revenue'],
            $data['growthData']['previous_revenue'],
            $data['growthData']['revenue_growth'] . '%'
        ]);
        fputcsv($handle, [
            'Orders',
            $data['growthData']['current_orders'],
            $data['growthData']['previous_orders'],
            $data['growthData']['orders_growth'] . '%'
        ]);
        fputcsv($handle, []);

        // Status Breakdown
        fputcsv($handle, ['ORDER STATUS BREAKDOWN']);
        fputcsv($handle, ['Status', 'Count', 'Total']);
        foreach ($data['statusBreakdown'] as $status) {
            fputcsv($handle, [$status->status, $status->count, $status->total]);
        }
        fputcsv($handle, []);

        // Daily Breakdown
        fputcsv($handle, ['DAILY BREAKDOWN']);
        fputcsv($handle, ['Date', 'Orders', 'Revenue', 'Trend']);
        foreach ($data['dailyBreakdown'] as $day) {
            fputcsv($handle, [$day->date, $day->orders, $day->revenue, '']);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export Monthly Sales as PDF
     */
    private function exportMonthlySalesPDF($data)
    {
        $pdf = Pdf::loadView('admin.reports.exports.monthly-sales-pdf', $data);
        return $pdf->download('monthly-sales-' . $data['year'] . '-' . $data['month'] . '.pdf');
    }

    /**
     * Export Product Sales Report
     */
    public function exportProductSales(Request $request, $format = 'csv')
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
            ->orderByDesc('total_revenue')
            ->get();

        $summary = OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(quantity) as total_items_sold'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT product_id) as unique_products')
            )
            ->first();

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'formatted_start' => Carbon::parse($startDate)->format('M d, Y'),
            'formatted_end' => Carbon::parse($endDate)->format('M d, Y'),
            'products' => $products,
            'summary' => $summary,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format == 'pdf') {
            return $this->exportProductSalesPDF($data);
        }

        return $this->exportProductSalesCSV($data);
    }

    /**
     * Export Product Sales as CSV
     */
    private function exportProductSalesCSV($data)
    {
        $filename = 'product-sales-' . $data['start_date'] . '-to-' . $data['end_date'] . '.csv';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, ['Product Sales Report - ' . $data['formatted_start'] . ' to ' . $data['formatted_end']]);
        fputcsv($handle, ['Generated at: ' . $data['generated_at']]);
        fputcsv($handle, []);

        // Summary
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Items Sold', 'Total Revenue', 'Unique Products']);
        fputcsv($handle, [
            $data['summary']->total_items_sold ?? 0,
            $data['summary']->total_revenue ?? 0,
            $data['summary']->unique_products ?? 0
        ]);
        fputcsv($handle, []);

        // Product List
        fputcsv($handle, ['PRODUCT SALES DETAILS']);
        fputcsv($handle, ['Product', 'Quantity Sold', 'Revenue', 'Order Count', 'Avg Price', '% of Sales']);

        foreach ($data['products'] as $product) {
            $avgPrice = $product->total_quantity > 0 ? $product->total_revenue / $product->total_quantity : 0;
            $percentage = $data['summary']->total_revenue > 0 ? ($product->total_revenue / $data['summary']->total_revenue) * 100 : 0;

            fputcsv($handle, [
                $product->product_name,
                $product->total_quantity,
                $product->total_revenue,
                $product->order_count,
                round($avgPrice, 2),
                round($percentage, 2) . '%'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export Product Sales as PDF
     */
    private function exportProductSalesPDF($data)
    {
        $pdf = Pdf::loadView('admin.reports.exports.product-sales-pdf', $data);
        return $pdf->download('product-sales-' . $data['start_date'] . '-to-' . $data['end_date'] . '.pdf');
    }

    /**
     * Export Low Stock Report
     */
    public function exportLowStock(Request $request, $format = 'csv')
    {
        $threshold = $request->get('threshold', 10);

        $products = Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->get();

        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();

        $data = [
            'threshold' => $threshold,
            'products' => $products,
            'outOfStock' => $outOfStock,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.low-stock-pdf', $data);
            return $pdf->download('low-stock-report.pdf');
        }

        return $this->exportLowStockCSV($data);
    }

    /**
     * Export Low Stock as CSV
     */
    private function exportLowStockCSV($data)
    {
        $filename = 'low-stock-report.csv';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, ['LOW STOCK REPORT']);
        fputcsv($handle, ['Threshold: ≤ ' . $data['threshold'] . ' units']);
        fputcsv($handle, ['Out of Stock: ' . $data['outOfStock']]);
        fputcsv($handle, ['Generated at: ' . $data['generated_at']]);
        fputcsv($handle, []);

        fputcsv($handle, ['PRODUCTS BELOW THRESHOLD']);
        fputcsv($handle, ['Product', 'SKU', 'Category', 'Current Stock', 'Status', 'Last Updated']);

        foreach ($data['products'] as $product) {
            $status = $product->stock_quantity <= 0 ? 'Out of Stock' : ($product->stock_quantity <= 5 ? 'Critical' : 'Low');

            fputcsv($handle, [
                $product->name,
                $product->sku,
                $product->category->name ?? 'Uncategorized',
                $product->stock_quantity,
                $status,
                $product->updated_at->format('Y-m-d H:i')
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export Top Customers Report
     */
    public function exportTopCustomers(Request $request, $format = 'csv')
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $customers = User::where('is_admin', false)
            ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->orderByDesc('total_spent')
            ->limit(50)
            ->get();

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'formatted_start' => Carbon::parse($startDate)->format('M d, Y'),
            'formatted_end' => Carbon::parse($endDate)->format('M d, Y'),
            'customers' => $customers,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.top-customers-pdf', $data);
            return $pdf->download('top-customers.pdf');
        }

        return $this->exportTopCustomersCSV($data);
    }

    /**
     * Export Top Customers as CSV
     */
    private function exportTopCustomersCSV($data)
    {
        $filename = 'top-customers.csv';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, ['TOP CUSTOMERS REPORT']);
        fputcsv($handle, ['Period: ' . $data['formatted_start'] . ' to ' . $data['formatted_end']]);
        fputcsv($handle, ['Generated at: ' . $data['generated_at']]);
        fputcsv($handle, []);

        fputcsv($handle, ['Customer', 'Email', 'Orders Count', 'Total Spent', 'Average per Order', 'Joined']);

        foreach ($data['customers'] as $customer) {
            $avgOrder = $customer->orders_count > 0 ? $customer->total_spent / $customer->orders_count : 0;

            fputcsv($handle, [
                $customer->name,
                $customer->email,
                $customer->orders_count,
                $customer->total_spent ?? 0,
                round($avgOrder, 2),
                $customer->created_at->format('Y-m-d')
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
