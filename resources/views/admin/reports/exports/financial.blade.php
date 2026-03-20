<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { color: #2c3e50; font-size: 24px; text-align: center; }
        h3 { color: #34495e; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #e74c3c; color: white; padding: 8px; border: 1px solid #c0392b; }
        td { padding: 6px; border: 1px solid #bdc3c7; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { margin: 20px 0; padding: 15px; background-color: #f8f9fa; }
        .amount { text-align: right; }
        .text-success { color: #27ae60; }
        .text-danger { color: #e74c3c; }
        .footer { margin-top: 30px; font-size: 10px; color: #7f8c8d; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
        <p>Period: {{ request('start_date') }} to {{ request('end_date') }}</p>
    </div>

    @if(!empty($pdfData))
        @php
            $totalRevenue = collect($pdfData)->sum('Revenue');
            $totalCost = collect($pdfData)->sum('Estimated Cost');
            $totalProfit = collect($pdfData)->sum('Estimated Profit');
            $profitMargin = ($totalRevenue > 0) ? ($totalProfit / $totalRevenue) * 100 : 0;
        @endphp

        <div class="summary">
            <h3>Financial Summary</h3>
            <p><strong>Total Revenue:</strong> ${{ number_format($totalRevenue, 2) }}</p>
            <p><strong>Total Cost:</strong> ${{ number_format($totalCost, 2) }}</p>
            <p><strong>Total Profit:</strong> <span class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($totalProfit, 2) }}</span></p>
            <p><strong>Profit Margin:</strong> <span class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profitMargin, 1) }}%</span></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th class="amount">Revenue ($)</th>
                    <th class="amount">Estimated Cost ($)</th>
                    <th class="amount">Estimated Profit ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pdfData as $item)
                <tr>
                    <td>{{ $item['id'] ?? 'N/A' }}</td>
                    <td>{{ $item['description'] ?? $item['name'] ?? 'N/A' }}</td>
                    <td>{{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $item['category'] ?? $item['type'] ?? 'N/A' }}</td>
                    <td class="amount">${{ number_format($item['Revenue'] ?? 0, 2) }}</td>
                    <td class="amount">${{ number_format($item['Estimated Cost'] ?? 0, 2) }}</td>
                    <td class="amount {{ (($item['Estimated Profit'] ?? 0) >= 0) ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($item['Estimated Profit'] ?? 0, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right;">Totals:</th>
                    <th class="amount">${{ number_format($totalRevenue, 2) }}</th>
                    <th class="amount">${{ number_format($totalCost, 2) }}</th>
                    <th class="amount">${{ number_format($totalProfit, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <div style="text-align: center; padding: 50px; background-color: #f8f9fa;">
            <h3>No Data Available</h3>
            <p>There is no financial data to display for the selected period.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically. For any questions, please contact the finance department.</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
