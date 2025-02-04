

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Aturan @page untuk mengatur ukuran kertas menjadi A6 */
        @page {
            size: 105mm 148.5mm; /* Dimensi A6 */
            margin: 10mm;
        }

        /* Menggunakan reset CSS untuk memastikan konsistensi penampilan */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #333;
        }

        .page-container {
            width: 100%;
            height: 100%;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #2563eb;
            font-size: 18px;
        }

        .header img {
            width: 80px;
            height: auto;
            margin: 5px 0;
        }

        .header p {
            margin: 3px 0;
        }

        .info-section {
            background-color: #f3f4f6;
            border-radius: 3px;
            padding: 8px;
            margin-bottom: 8px;
        }

        .info-section h2 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #374151;
        }

        .info-item {
            margin: 3px 0;
            color: #4b5563;
        }

        .info-item strong {
            color: #374151;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            padding: 8px 4px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 10px;
        }

        .total-section {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            color: #4b5563;
            font-size: 10px;
        }

        .total-row.final {
            font-weight: bold;
            color: #2563eb;
            font-size: 11px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            color: #6b7280;
            font-size: 8px;
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="header">
            <h1>INVOICE</h1>
            @if(file_exists(public_path('images/logo_sinaraartha.png')))
                <img src="{{ asset('images/logo_sinaraartha.png') }}" alt="Pembelian Barang">
            @else
                <div style="height: 80px;"></div>
            @endif
            <p>No: <strong>#{{ str_pad($order->no_order, 5, '0', STR_PAD_LEFT) }}</strong></p>
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

            @if($order->paymentMethod && $order->paymentMethod->name === "Transfer")
            <div style="margin-top: 5px; padding-top: 5px; border-top: 1px solid #e5e7eb;">
                <div class="info-item">
                    <strong>Bank:</strong> BCA
                </div>
                <div class="info-item">
                    <strong>Nomor Rekening:</strong> 0889333288
                </div>
                <div class="info-item">
                    <strong>Atas Nama:</strong> KOPERASI SINARA ARTHA
                </div>
            </div>
            @endif
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
                @forelse($order->orderProducts as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produk tidak tersedia' }}</td>
                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align: right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center">Tidak ada produk</td>
                </tr>
                @endforelse
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
            <p>Setelah melakukan pembayaran, mohon konfirmasi melalui WhatsApp kami di:</p>
            <p>+62 877-7871-5788</p>
        </div>
    </div>
</body>

</html>

