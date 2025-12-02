@extends('layouts.app')

@section('content')
<style>
    .countdown-container {
        display: flex;
        justify-content: center;
        margin-top: 12px;
        gap: 10px;
    }
    .countdown-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: linear-gradient(145deg, #ff6b6b, #f53b57);
        color: white;
        border-radius: 8px;
        padding: 8px 12px;
        min-width: 60px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .countdown-value {
        font-size: 24px;
        font-weight: bold;
        line-height: 1;
    }
    .countdown-label {
        font-size: 12px;
        text-transform: uppercase;
        margin-top: 4px;
        opacity: 0.8;
    }
    .countdown-separator {
        font-size: 24px;
        font-weight: bold;
        align-self: center;
        color: #f53b57;
    }
    .countdown-expired {
        background-color: #e74c3c;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        text-align: center;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.7; }
        50% { opacity: 1; }
        100% { opacity: 0.7; }
    }
</style>

<div class="container py-8 mx-auto">
    <div class="p-6 mx-auto max-w-3xl bg-white rounded-lg shadow-md">
        <div class="mb-6 text-center">
            <h1 class="mb-2 text-2xl font-bold text-green-600">Terima Kasih Atas Pesanan Anda!</h1>
            <p class="text-gray-600">Nomor Pesanan: <span class="font-semibold">{{ $order->no_order ?? $order->id }}</span></p>
        </div>

        <div class="py-4 mb-4 border-t border-b border-gray-200">
            <h2 class="mb-3 text-lg font-semibold">Detail Pesanan</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Nama:</p>
                    <p class="font-medium">{{ $order->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">WhatsApp:</p>
                    <p class="font-medium">{{ $order->whatsapp }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-600">Alamat:</p>
                    <p class="font-medium">{{ $order->address }}</p>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="mb-2 font-medium">Produk yang Dipesan:</h3>
                <div class="space-y-2">
                    @foreach($order->orderProducts as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <span>Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal_amount ?? $order->total_price, 0, ',', '.') }}</span>
                </div>
                @if(($order->discount_amount ?? 0) > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon</span>
                        <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between mt-2 font-semibold">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_amount ?? $order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="mb-3 text-lg font-semibold">Status Pembayaran</h2>

            <div class="flex items-center mb-4">
                @php
                    $statusColor = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                        'expired' => 'bg-red-100 text-red-800',
                    ][$order->status] ?? 'bg-gray-100 text-gray-800';

                    $statusText = [
                        'pending' => 'Menunggu Pembayaran',
                        'processing' => 'Sedang Diproses',
                        'completed' => 'Pembayaran Berhasil',
                        'failed' => 'Pembayaran Gagal',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Waktu Pembayaran Habis',
                    ][$order->status] ?? ucfirst($order->status);
                @endphp

                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    {{ $statusText }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-600">Metode Pembayaran:</p>
                <p class="font-medium">{{ $order->paymentMethod->name }}</p>
            </div>

            @if($order->payment_url)
                <div class="mt-4">
                    <a href="{{ $order->payment_url }}" target="_blank" class="inline-block px-4 py-2 font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Lanjutkan Pembayaran
                    </a>
                </div>
            @endif

            @if($order->status === 'pending' && $order->paymentMethod && $order->paymentMethod->gateway === 'midtrans' && !$order->payment_url)
                <div class="mt-4">
                    <a href="{{ route('checkout.check-status', $order->id) }}" class="inline-block px-4 py-2 font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                        Cek Status Pembayaran
                    </a>
                </div>
            @endif

            @if($bankDetails && $order->status === 'pending')
                <div class="p-4 mt-4 bg-gray-50 rounded-md">
                    <h3 class="mb-2 font-medium">Informasi Transfer Bank:</h3>
                    <p>Bank: {{ $bankDetails['bank'] }}</p>
                    <p>No. Rekening: {{ $bankDetails['account_number'] }}</p>
                    <p>Atas Nama: {{ $bankDetails['account_name'] }}</p>
                    <p class="mt-2 text-sm text-gray-600">Silakan transfer sejumlah <span class="font-semibold">Rp {{ number_format($order->total_amount ?? $order->total_price, 0, ',', '.') }}</span> ke rekening di atas.</p>
                </div>
            @endif

            @if($order->qrisDynamic && $order->status === 'pending')
                <div class="p-4 mt-4 bg-blue-50 rounded-md">
                    <h3 class="mb-3 font-medium text-center">Scan QRIS untuk Pembayaran</h3>
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('storage/' . $order->qrisDynamic->qr_image_path) }}" 
                             alt="QRIS Code" 
                             class="mb-3 w-64 h-64 border-2 border-gray-300 rounded">
                        <p class="mb-2 text-lg font-semibold">Total: Rp {{ number_format($order->total_amount ?? $order->total_price, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600 text-center">Scan QR code dengan aplikasi pembayaran Anda</p>
                        <p class="mt-2 text-xs text-gray-500">Merchant: {{ $order->qrisDynamic->merchant_name }}</p>
                    </div>
                </div>
            @endif

            @php
                $paymentDetails = json_decode($order->payment_details ?? '{}');
                $vaNumbers = $paymentDetails->va_numbers ?? [];
            @endphp

            @if(!empty($vaNumbers) && $order->status === 'pending' && $order->paymentMethod->gateway === 'midtrans')
                <div class="p-4 mt-4 bg-blue-50 rounded-md">
                    <h3 class="mb-2 font-medium">Informasi Virtual Account:</h3>
                    @foreach($vaNumbers as $va)
                        <div class="mb-2">
                            <p class="font-semibold">Bank: {{ strtoupper($va->bank ?? '') }}</p>
                            <p class="font-mono text-lg">Nomor VA: {{ $va->va_number ?? '' }}</p>
                        </div>
                    @endforeach
                    <p class="mt-2 text-sm text-gray-600">Silakan transfer sejumlah <span class="font-semibold">Rp {{ number_format($order->total_amount ?? $order->total_price, 0, ',', '.') }}</span> ke virtual account di atas.</p>
                    @if(isset($paymentDetails->expiry_time))
                        <p class="mt-2 text-sm text-red-600">Batas waktu pembayaran: {{ \Carbon\Carbon::parse($paymentDetails->expiry_time)->format('d M Y H:i') }}</p>
                        
                        <div id="countdown-timer" class="mt-3" data-expiry="{{ $paymentDetails->expiry_time }}">
                            <!-- Countdown HTML -->
                            <div class="countdown-container">
                                <div class="countdown-box">
                                    <span id="days" class="countdown-value">00</span>
                                    <span class="countdown-label">Hari</span>
                                </div>
                                <span class="countdown-separator">:</span>
                                <div class="countdown-box">
                                    <span id="hours" class="countdown-value">00</span>
                                    <span class="countdown-label">Jam</span>
                                </div>
                                <span class="countdown-separator">:</span>
                                <div class="countdown-box">
                                    <span id="minutes" class="countdown-value">00</span>
                                    <span class="countdown-label">Menit</span>
                                </div>
                                <span class="countdown-separator">:</span>
                                <div class="countdown-box">
                                    <span id="seconds" class="countdown-value">00</span>
                                    <span class="countdown-label">Detik</span>
                                </div>
                            </div>
                            <div id="expired-message" class="mt-2 countdown-expired" style="display: none;">
                                Waktu pembayaran telah berakhir!
                            </div>
                        </div>
                    @endif
                </div>
            @elseif(($paymentDetails->payment_type ?? '') === 'gopay' && $order->status === 'pending')
                <div class="p-4 mt-4 bg-blue-50 rounded-md">
                    <h3 class="mb-3 font-medium text-center">Scan QRIS / GoPay</h3>
                    <div class="flex flex-col items-center">
                        @php
                            $qrUrl = null;
                            $deeplinkUrl = null;
                            if (isset($paymentDetails->actions)) {
                                foreach ($paymentDetails->actions as $action) {
                                    if ($action->name === 'generate-qr-code') {
                                        $qrUrl = $action->url;
                                    } elseif ($action->name === 'deeplink-redirect') {
                                        $deeplinkUrl = $action->url;
                                    }
                                }
                            }
                        @endphp

                        @if($qrUrl)
                            <img src="{{ $qrUrl }}" alt="GoPay QR Code" class="mb-3 w-64 h-64 border-2 border-gray-300 rounded">
                        @endif
                        
                        <p class="mb-2 text-lg font-semibold">Total: Rp {{ number_format($order->total_amount ?? $order->total_price, 0, ',', '.') }}</p>
                        <p class="text-sm text-center text-gray-600">Scan QR code dengan aplikasi GoPay, Gojek, atau aplikasi QRIS lainnya</p>
                        
                        @if($deeplinkUrl)
                            <div class="mt-3">
                                <a href="{{ $deeplinkUrl }}" class="px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                    Buka GoPay App
                                </a>
                            </div>
                        @endif

                        @if(isset($paymentDetails->expiry_time))
                            <p class="mt-4 text-sm text-red-600">Batas waktu pembayaran: {{ \Carbon\Carbon::parse($paymentDetails->expiry_time)->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block px-4 py-2 font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                Kembali ke Beranda
            </a>

            <a href="{{ route('order.pdf', $order->id) }}" class="inline-flex items-center px-6 py-3 text-blue-600 bg-blue-100 rounded-lg transition-colors hover:bg-blue-200">
                Download Invoice
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElement = document.getElementById('countdown-timer');
        if (countdownElement) {
            const expiryTime = new Date(countdownElement.dataset.expiry).getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                if (distance < 0) {
                    // Waktu telah habis
                    document.getElementById('days').textContent = '00';
                    document.getElementById('hours').textContent = '00';
                    document.getElementById('minutes').textContent = '00';
                    document.getElementById('seconds').textContent = '00';
                    document.getElementById('expired-message').style.display = 'block';
                    clearInterval(countdownInterval);
                    return;
                }

                // Hitung waktu
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Tampilkan hasil
                document.getElementById('days').textContent = String(days).padStart(2, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }

            // Update countdown setiap 1 detik
            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
        }
    });
</script>
@endpush



