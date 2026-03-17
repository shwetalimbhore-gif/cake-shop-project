<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderSummaryExport implements FromCollection, WithHeadings, WithMapping
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
        return Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with('items')
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['ORDER SUMMARY REPORT'],
            ['Period: ' . $this->startDate . ' to ' . $this->endDate],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            [
                'Order #',
                'Date',
                'Customer',
                'Type',
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
            $order->created_at->format('Y-m-d H:i'),
            $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name,
            ucfirst($order->order_type),
            '$' . number_format($order->total, 2),
            ucfirst($order->status),
            ucfirst($order->payment_status),
        ];
    }
}
