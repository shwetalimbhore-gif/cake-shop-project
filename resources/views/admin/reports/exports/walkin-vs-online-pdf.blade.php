<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Walk-in vs Online Orders</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff6b8b; }
        .walkin { background: #fff3e0; }
        .online { background: #e3f2fd; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #ff6b8b; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .summary-box { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .summary-item { text-align: center; padding: 10px; background: #f8f9fa; border-radius: 5px; width: 45%; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Walk-in vs Online Orders</h1>
        <p>{{ $startDate }} to {{ $endDate }}</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item walkin">
            <h3>Walk-in Orders</h3>
            <p style="font-size: 24px; font-weight: bold;">{{ $orders->where('order_type', 'walkin')->count() }}</p>
            <p>Revenue: ${{ number_format($orders->where('order_type', 'walkin')->sum('total'), 2) }}</p>
        </div>
        <div class="summary-item online">
            <h3>Online Orders</h3>
            <p style="font-size: 24px; font-weight: bold;">{{ $orders->where('order_type', 'online')->count() }}</p>
            <p>Revenue: ${{ number_format($orders->where('order_type', 'online')->sum('total'), 2) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Type</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr class="{{ $order->order_type }}">
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ ucfirst($order->order_type) }}</td>
                <td>{{ $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name }}</td>
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
