<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .receipt {
            auto;
            background: #fff;
        }

        .receipt .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt .logo img {
            width: 150px;
            height: 150px;
        }

        .receipt .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 300;
        }

        .receipt .header p {
            margin: 0;
            font-size: 20px;
            font-weight: 100;
        }

        .receipt .content {
            font-size: 20px;
        }

        .receipt .content p {
            margin: 4px;
            letter-spacing: 0.5px;
            /* Atur jarak antar huruf */
            font-weight: 100;
            /* Huruf lebih tipis */
        }

        .receipt .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt .content table th,
        .receipt .content table td {
            text-align: left;
            padding: 5px 0;
        }

        .receipt .content table td {
            font-weight: 100;
            /* Huruf lebih tipis */
        }


        .receipt .content table th {
            border-bottom: 1px dashed #ccc;
            border-top: 1px dashed #ccc;
            font-weight: 300;
        }

        .receipt .footer {
            text-align: center;
            margin-top: 2px;
            font-size: 20px;
            margin-bottom: 2px;
            height: 50px;
        }

        .receipt .footer p {
            margin: 0;
            margin-bottom: 5px;
            height: 100px;
            line-height: 100px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="logo">
            {{-- @if($setting && $setting->image)
                <img src="{{ asset('storage/' . $setting->image) }}" alt="Logoxxxx"> --}}
            @if($setting && $setting->image)
                <img src="{{ public_path('images/logo_sinaraartha.png') }}" alt="Logo Default" style="max-height: 100px;">
            @endif
        </div>
        <div class="header">
            <h1>{{$setting->shop}}</h1>
            <p>{{$setting->address}}</p>
            <p>{{$setting->phone}}</p>
        </div>
        <div class="content">
            <p>=============================</p>
            <p>No. Order: {{ $order->no_order }}</p>
            <p>Tanggal :{{ date('d-m-Y, H:i') }}</p>
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order_items as $item)
                    @php $product = \App\Models\Product::find($item->product_id); @endphp
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: center;">{{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                @endforeach
                <tfoot>
                    <tr>
                        @php
                        $subtotal = 0;
                        foreach ($order_items as $item) {
                            $subtotal += $item->quantity * $item->unit_price;
                        }
                        $discount_amount = ($subtotal * $order->discount) / 100;
                        $total = $subtotal - $discount_amount;
                        @endphp
                        <th>Subtotal</th>
                        <th></th>
                        <th colspan="2" style="text-align: center;">{{ number_format($subtotal, 0, ',', '.') }}</th>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <th>Diskon ({{ $order->discount }}%)</th>
                        <th></th>
                        <th colspan="2" style="text-align: center;">{{ number_format($discount_amount, 0, ',', '.') }}</th>
                    </tr>
                    @endif
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th colspan="2" style="text-align: center;">{{ number_format($total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
            <p>=============================</p>
            <p>Metode Pembayaran: {{ $payment_method->name }}</p>
            @if($payment_method->account_number)
            <p>No. Rekening: {{ $payment_method->account_number }}</p>
            @endif
            @if($payment_method->account_name)
            <p>Atas Nama: {{ $payment_method->account_name }}</p>
            @endif
            <p>=============================</p>
        </div>
        <div class="footer">
            <p style="font-weight: bold;">Terima Kasih!</p>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>
