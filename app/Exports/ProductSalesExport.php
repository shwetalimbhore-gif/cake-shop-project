<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ProductSalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['PRODUCT SALES REPORT'],
            ['Period: ' . $this->startDate . ' to ' . $this->endDate],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            [
                'Product Name',
                'Quantity Sold',
                'Revenue',
                'Orders',
                'Average Price'
            ]
        ];
    }

    public function map($product): array
    {
        $avgPrice = $product->total_quantity > 0
            ? $product->total_revenue / $product->total_quantity
            : 0;

        return [
            $product->product_name,
            $product->total_quantity,
            '$' . number_format($product->total_revenue, 2),
            $product->order_count,
            '$' . number_format($avgPrice, 2),
        ];
    }
}
