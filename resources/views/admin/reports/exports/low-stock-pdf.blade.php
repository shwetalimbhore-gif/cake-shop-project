<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Low Stock Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #ff6b8b; }
        .warning { color: #ff6b8b; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #ff6b8b; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .critical { background: #ffebee; color: #c62828; font-weight: bold; }
        .low { background: #fff3e0; color: #ef6c00; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Low Stock Report</h1>
        <p>Threshold: ≤ {{ $threshold }} units</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="{{ $product->stock_quantity <= 5 ? 'critical' : 'low' }}">
                <td>{{ $product->name }}</td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td>{{ $product->stock_quantity <= 5 ? 'Critical' : 'Low' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report. © {{ date('Y') }} Cozy Cravings</p>
    </div>
</body>
</html>
