<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .info-section h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #374151;
        }
        .info-item {
            margin: 5px 0;
            color: #4b5563;
        }
        .info-item strong {
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
        }
        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            color: #4b5563;
        }
        .total-row.final {
            font-weight: bold;
            color: #2563eb;
            font-size: 1.1em;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <img src="{{ asset('images/logo_sinaraartha.png') }}" alt="Pembelian Barang" style="width: 150px; height: auto;">
        <p>No: #{{ str_pad($order->no_order, 5, '0', STR_PAD_LEFT) }}</p>
        <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Informasi Pemesan -->
    <div class="info-section">
        <h2>Informasi Pemesan</h2>
        <div class="info-item">
            <strong>Nama:</strong> {{ $order->name }}
        </div>
        <div class="info-item">
            <strong>WhatsApp:</strong> {{ $order->whatsapp }}
        </div>
        <div class="info-item">
            <strong>Alamat:</strong> {{ $order->address }}
        </div>
    </div>

    <!-- Informasi Pembayaran -->
    <div class="info-section">
        <h2>Informasi Pembayaran</h2>
        <div class="info-item">
            <strong>Metode Pembayaran:</strong> {{ $order->paymentMethod->name }}
        </div>
        <div class="info-item">
            <strong>Status:</strong> {{ ucfirst($order->status) }}
        </div>
    </div>

    <!-- Detail Produk -->
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th style="text-align: right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderProducts as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align: right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-section">
        <div class="total-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div class="total-row">
            <span>Diskon Voucher</span>
            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row final">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Koperasi Sinara Artha</p>
        <p>Jika ada pertanyaan, silakan hubungi kami di nomor yang tertera</p>
    </div>
</body>
</html>
