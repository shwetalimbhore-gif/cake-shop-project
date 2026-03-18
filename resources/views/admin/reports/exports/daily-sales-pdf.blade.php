<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Sales Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff6b8b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #ff6b8b; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .total-row { font-weight: bold; background: #f8f9fa; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Sales Report</h1>
        <p>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Time</th>
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
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('h:i A') }}</td>
                <td>{{ $order->order_type == 'walkin' ? $order->walkin_customer_name : $order->shipping_name }}</td>
                <td>{{ ucfirst($order->order_type) }}</td>
                <td>{{ $order->items->count() }}</td>
                <td>${{ number_format($order->total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>${{ number_format($sales->sum('total'), 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a system-generated report. © {{ date('Y') }} Cozy Cravings</p>
    </div>
</body>
</html>
