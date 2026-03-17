<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopCustomersExport implements FromCollection, WithHeadings, WithMapping
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
        return User::where('is_admin', false)
            ->withCount(['orders' => function($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->withSum(['orders as total_spent' => function($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }], 'total')
            ->having('orders_count', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(100)
            ->get();
    }

    public function headings(): array
    {
        return [
            ['TOP CUSTOMERS REPORT'],
            ['Period: ' . $this->startDate . ' to ' . $this->endDate],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            [],
            ['Rank', 'Customer Name', 'Email', 'Orders', 'Total Spent', 'Average Order']
        ];
    }

    public function map($customer): array
    {
        static $rank = 0;
        $rank++;

        $avgOrder = $customer->orders_count > 0
            ? $customer->total_spent / $customer->orders_count
            : 0;

        return [
            $rank,
            $customer->name,
            $customer->email,
            $customer->orders_count,
            '₹' . number_format($customer->total_spent ?? 0, 2),
            '₹' . number_format($avgOrder, 2),
        ];
    }
}
