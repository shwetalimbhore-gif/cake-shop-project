<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DailySalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return Order::whereDate('created_at', $this->date)
            ->with('items')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['DAILY SALES REPORT - ' . Carbon::parse($this->date)->format('F d, Y')],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            [
                'Order #',
                'Time',
                'Customer',
                'Order Type',
                'Items',
                'Subtotal',
                'Tax',
                'Shipping',
                'Discount',
                'Total',
                'Status',
                'Payment'
            ]
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('h:i A'),
            $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name,
            ucfirst($order->order_type),
            $order->items->count(),
            '$' . number_format($order->subtotal, 2),
            '$' . number_format($order->tax, 2),
            '$' . number_format($order->shipping_cost, 2),
            '$' . number_format($order->discount, 2),
            '$' . number_format($order->total, 2),
            ucfirst($order->status),
            ucfirst($order->payment_status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6B8B']]],
        ];
    }
}
