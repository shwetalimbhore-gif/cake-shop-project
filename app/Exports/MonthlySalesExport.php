<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class MonthlySalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, $this->month, 1)->endOfMonth();

        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('items')
            ->get();
    }

    public function headings(): array
    {
        $monthName = Carbon::create($this->year, $this->month, 1)->format('F Y');

        return [
            ['MONTHLY SALES REPORT - ' . $monthName],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            [
                'Date',
                'Order #',
                'Customer',
                'Order Type',
                'Items',
                'Total',
                'Status',
                'Payment'
            ]
        ];
    }

    public function map($order): array
    {
        return [
            $order->created_at->format('Y-m-d'),
            $order->order_number,
            $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name,
            ucfirst($order->order_type),
            $order->items->count(),
            '$' . number_format($order->total, 2),
            ucfirst($order->status),
            ucfirst($order->payment_status),
        ];
    }
}
