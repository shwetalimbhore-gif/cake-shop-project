<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LowStockExport implements FromCollection, WithHeadings, WithMapping
{
    protected $threshold;

    public function __construct($threshold)
    {
        $this->threshold = $threshold;
    }

    public function collection()
    {
        return Product::where('stock_quantity', '<=', $this->threshold)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->orderBy('stock_quantity')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['LOW STOCK REPORT'],
            ['Threshold: ≤ ' . $this->threshold . ' units'],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            ['Product', 'SKU', 'Category', 'Current Stock', 'Status']
        ];
    }

    public function map($product): array
    {
        $status = $product->stock_quantity <= 5 ? 'Critical' : 'Low';

        return [
            $product->name,
            $product->sku,
            $product->category->name ?? 'Uncategorized',
            $product->stock_quantity,
            $status,
        ];
    }
}
