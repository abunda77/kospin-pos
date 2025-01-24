<!DOCTYPE html>
<html>
<head>
    <title>Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .detail-section { margin-bottom: 20px; }
        .products-table { width: 100%; border-collapse: collapse; }
        .products-table th, .products-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detail Pesanan #{{ $order->id }}</h1>
            <p>Tanggal: {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>

        <div class="detail-section">
            <h3>Informasi Penerima:</h3>
            <p>{{ $order->name }}</p>
            <p>{{ $order->whatsapp }}</p>
            <p>{{ $order->address }}</p>
        </div>

        <div class="detail-section">
            <h3>Produk:</h3>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderProducts as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="total">
                Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
            </div>
        </div>

        <div class="detail-section">
            <h3>Metode Pembayaran:</h3>
            <p>{{ $order->paymentMethod->name }}</p>
        </div>
    </div>
</body>
</html>
