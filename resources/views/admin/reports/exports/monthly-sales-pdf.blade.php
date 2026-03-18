<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Monthly Sales Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff6b8b; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #ff6b8b; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .summary-box { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Sales Report</h1>
        <p>{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span><strong>Total Orders:</strong> {{ $sales->count() }}</span>
            <span><strong>Total Revenue:</strong> ${{ number_format($sales->sum('total'), 2) }}</span>
            <span><strong>Average Order:</strong> ${{ number_format($sales->avg('total'), 2) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $order)
            <tr>
                <td>{{ $order->created_at->format('M d') }}</td>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name }}</td>
                <td>{{ ucfirst($order->order_type) }}</td>
                <td>{{ $order->items->count() }}</td>
                <td>${{ number_format($order->total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report. © {{ date('Y') }} Cozy Cravings</p>
    </div>
</body>
</html>
