<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class TopSellingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $period;

    public function __construct($period)
    {
        $this->period = $period;
    }

    public function collection()
    {
        $query = OrderItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->limit(50);

        switch($this->period) {
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

        return $query->get();
    }

    public function headings(): array
    {
        $periodText = [
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
            'all' => 'All Time'
        ][$this->period] ?? 'All Time';

        return [
            ['TOP SELLING PRODUCTS - ' . $periodText],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            ['Rank', 'Product Name', 'Quantity Sold', 'Revenue']
        ];
    }

    public function map($product): array
    {
        static $rank = 0;
        $rank++;

        return [
            $rank,
            $product->product_name,
            $product->total_quantity,
            '$' . number_format($product->total_revenue, 2),
        ];
    }
}
