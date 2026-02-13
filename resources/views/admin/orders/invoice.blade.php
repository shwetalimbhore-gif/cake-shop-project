<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            padding: 40px 20px;
        }
        .invoice-box {
            max-width: 1100px;
            margin: auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff6b8b;
        }
        .shop-info h2 {
            color: #ff6b8b;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 700;
            color: #ff6b8b;
            text-align: right;
        }
        .invoice-details {
            background: #f8fafc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border: none;
        }
        .table td {
            vertical-align: middle;
        }
        .totals {
            width: 350px;
            margin-left: auto;
            background: #f8fafc;
            border-radius: 15px;
            padding: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 30px;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff6b8b;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255,107,139,0.3);
            transition: all 0.3s;
            z-index: 1000;
        }
        .btn-print:hover {
            background: #ff5a7e;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,107,139,0.4);
        }
        @media print {
            .btn-print { display: none; }
            .invoice-box { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Print Invoice
    </button>

    <div class="invoice-box">
        <div class="header">
            <div class="shop-info">
                <h2>
                    @if(setting('site_logo'))
                        <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}" style="height: 50px;">
                    @else
                        <i class="fas fa-birthday-cake me-2"></i>{{ setting('site_name', 'MyCakeShop') }}
                    @endif
                </h2>
                <p class="text-muted mb-0">{{ setting('contact_address', '123 Bakery Street, Sweet City') }}</p>
                <p class="text-muted">{{ setting('contact_phone', '+1 234 567 8900') }} | {{ setting('contact_email', 'info@mycakeshop.com') }}</p>
            </div>
            <div class="text-end">
                <div class="invoice-title">INVOICE</div>
                <h5 class="mt-3 mb-1">#{{ $order->order_number }}</h5>
                <p class="text-muted mb-0">Date: {{ $order->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="invoice-details">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-danger me-2"></i>Bill To:</h6>
                    <p class="mb-1"><strong>{{ $order->billing_name ?? $order->shipping_name }}</strong></p>
                    <p class="mb-1">{{ $order->billing_email ?? $order->shipping_email }}</p>
                    <p class="mb-1">{{ $order->billing_phone ?? $order->shipping_phone }}</p>
                    <p class="mb-0">{{ $order->billing_address ?? $order->shipping_address }}</p>
                    <p>{{ $order->billing_city ?? $order->shipping_city }}, {{ $order->billing_state ?? $order->shipping_state }} {{ $order->billing_zip ?? $order->shipping_zip }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3"><i class="fas fa-truck text-success me-2"></i>Ship To:</h6>
                    <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                    <p class="mb-1">{{ $order->shipping_email }}</p>
                    <p class="mb-1">{{ $order->shipping_phone }}</p>
                    <p class="mb-0">{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                </div>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if(!empty($item->options))
                            @php $options = json_decode($item->options, true); @endphp
                            <br>
                            <small class="text-muted">
                                @foreach($options as $key => $value)
                                    {{ ucfirst($key) }}: {{ $value }}
                                @endforeach
                            </small>
                        @endif
                    </td>
                    <td>{{ $item->sku ?? 'N/A' }}</td>
                    <td class="text-center">{{ format_currency($item->price) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ format_currency($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table class="table table-borderless">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-end">{{ format_currency($order->subtotal) }}</td>
                </tr>
                @if($order->tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-end">{{ format_currency($order->tax) }}</td>
                </tr>
                @endif
                @if($order->shipping_cost > 0)
                <tr>
                    <td>Shipping:</td>
                    <td class="text-end">{{ format_currency($order->shipping_cost) }}</td>
                </tr>
                @endif
                @if($order->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-end text-danger">-{{ format_currency($order->discount) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2"><hr class="my-2"></td>
                </tr>
                <tr>
                    <td class="fw-bold fs-5">Total:</td>
                    <td class="text-end fw-bold fs-5 text-primary">{{ format_currency($order->total) }}</td>
                </tr>
            </table>
        </div>

        @if($order->notes)
        <div class="mt-4 p-3 bg-light rounded-3">
            <strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong>
            <p class="mb-0 mt-2">{{ $order->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p class="mb-1">Thank you for your business!</p>
            <p class="mb-0">{{ setting('site_name', 'MyCakeShop') }} - Where every cake tells a story</p>
        </div>
    </div>
</body>
</html>
