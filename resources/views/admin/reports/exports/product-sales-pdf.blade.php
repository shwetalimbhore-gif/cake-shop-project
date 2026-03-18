<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Product Sales Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff6b8b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #ff6b8b; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Sales Report</h1>
        <p>{{ $startDate }} to {{ $endDate }}</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
                <th>Orders</th>
                <th>Avg Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->product_name }}</td>
                <td>{{ number_format($product->total_quantity) }}</td>
                <td>${{ number_format($product->total_revenue, 2) }}</td>
                <td>{{ $product->order_count ?? 'N/A' }}</td>
                <td>${{ $product->total_quantity > 0 ? number_format($product->total_revenue / $product->total_quantity, 2) : '0.00' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report. © {{ date('Y') }} Cozy Cravings</p>
    </div>
</body>
</html>
