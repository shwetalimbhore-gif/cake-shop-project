<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportHelpers
{
    /**
     * Validate and parse date range
     */
    public static function validateDateRange($request, $defaultStart = null, $defaultEnd = null)
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
     * Format currency
     */
    public static function formatCurrency($amount)
    {
        return '$' . number_format($amount, 2);
    }

    /**
     * Calculate percentage
     */
    public static function calculatePercentage($value, $total)
    {
        return $total > 0 ? round(($value / $total) * 100, 2) : 0;
    }

    /**
     * Get date range string for display
     */
    public static function getDateRangeString($startDate, $endDate)
    {
        return $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');
    }

    /**
     * Prepare chart data
     */
    public static function prepareChartData($labels, $data, $type = 'line')
    {
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'borderColor' => 'rgba(60,141,188,0.8)',
                    'backgroundColor' => 'rgba(60,141,188,0.1)',
                ]
            ]
        ];
    }

    /**
     * Get month name from number
     */
    public static function getMonthName($month)
    {
        return Carbon::create()->month($month)->format('F');
    }

    /**
     * Get day name from date
     */
    public static function getDayName($date)
    {
        return Carbon::parse($date)->format('l');
    }
}
