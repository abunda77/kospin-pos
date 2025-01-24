@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-gradient-to-br from-slate-300 to-slate-100 rounded-lg shadow-md">
        <!-- Header -->
        <div class="text-center p-6 md:p-8 border-b">
            <svg class="w-16 h-16 md:w-20 md:h-20 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600 text-sm md:text-base">Pesanan Anda telah berhasil diproses</p>
            <p class="text-gray-800 text-xs md:text-sm font-bold">Silakan download invoice pesanan Anda pada tombol dibawah ini dan bawa invoice pesanan dan bukti transfer (jika ada) ke kasir di kantor Koperasi Sinara Artha.</p>
        </div>

        <!-- Order Details -->
        <div class="p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-lg md:text-xl font-semibold mb-2">Detail Pesanan</h2>
                <p class="text-gray-600 text-sm md:text-base">Nomor Order: # <span class="font-semibold">{{ $order->id }}</span></p>
                <p class="text-gray-600 text-sm md:text-base">Tanggal: <span class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</span></p>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h3 class="font-semibold mb-2 text-sm md:text-base">Informasi Penerima:</h3>
                <p class="text-gray-600 text-sm md:text-base">{{ $order->name }}</p>
                <p class="text-gray-600 text-sm md:text-base">{{ $order->whatsapp }}</p>
                <p class="text-gray-600 text-sm md:text-base">{{ $order->address }}</p>
            </div>

            <!-- Products -->
            <div class="mb-6">
                <h3 class="font-semibold mb-2 text-sm md:text-base">Produk:</h3>
                <div class="border rounded-lg divide-y">
                    @foreach($order->orderProducts as $item)
                    <div class="flex justify-between p-3 text-sm md:text-base">
                        <div>
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-gray-600">{{ $item->quantity }}x @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-medium">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Total -->
            <div class="mb-6 border-t pt-4">
                <div class="flex justify-between font-bold text-sm md:text-base">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <h3 class="font-semibold mb-2 text-sm md:text-base">Metode Pembayaran:</h3>
                <p class="text-gray-600 text-sm md:text-base">{{ $order->paymentMethod->name }}</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <a href="{{ route('order.pdf', $order->id) }}" target="_blank"
                   class="bg-blue-500 text-white text-center py-2 px-4 rounded hover:bg-blue-600 transition duration-200 text-sm md:text-base">
                    <i class="fas fa-print mr-2"></i>Cetak Struk
                </a>
                <a href="{{ route('catalog') }}"
                   class="bg-gray-500 text-white text-center py-2 px-4 rounded hover:bg-gray-600 transition duration-200 text-sm md:text-base">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
    }
</style>
@endpush
@endsection
