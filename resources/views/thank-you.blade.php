@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto">
    <div class="mx-auto max-w-2xl bg-gradient-to-br rounded-lg shadow-md from-slate-300 to-slate-100">
        <!-- Header -->
        <div class="p-6 text-center border-b md:p-8">
            <svg class="mx-auto mb-4 w-16 h-16 text-green-500 md:w-20 md:h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="mb-2 text-2xl font-bold text-gray-800 md:text-3xl">Terima Kasih!</h1>
            <p class="text-sm text-gray-600 md:text-base">Pesanan Anda telah berhasil diproses</p>
            <p class="text-xs font-bold text-gray-800 md:text-sm">Silakan download invoice pesanan Anda pada tombol dibawah ini dan bawa invoice pesanan dan bukti transfer (jika ada) ke kasir di kantor Koperasi Sinara Artha.</p>
        </div>

        <!-- Order Details -->
        <div class="p-6 md:p-8">
            <div class="mb-6">
                <h2 class="mb-2 text-lg font-semibold md:text-xl">Detail Pesanan</h2>
                <p class="text-sm text-gray-600 md:text-base">Nomor Order: <strong>#{{ $order->no_order }}</strong></p>
                <p class="text-sm text-gray-600 md:text-base">Tanggal: <span class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</span></p>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h3 class="mb-2 text-sm font-semibold md:text-base">Informasi Penerima:</h3>
                <p class="text-sm text-gray-600 md:text-base">{{ $order->name }}</p>
                <p class="text-sm text-gray-600 md:text-base">{{ $order->whatsapp }}</p>
                <p class="text-sm text-gray-600 md:text-base">{{ $order->address }}</p>
            </div>

            <!-- Products -->
            <div class="mb-6">
                <h3 class="mb-2 text-sm font-semibold md:text-base">Produk:</h3>
                <div class="rounded-lg border divide-y">
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
            <div class="pt-4 mb-6 border-t">
                <div class="flex justify-between text-sm font-bold md:text-base">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <h3 class="mb-2 text-sm font-semibold md:text-base">Metode Pembayaran:</h3>
                <p class="text-sm text-gray-600 md:text-base">{{ $order->paymentMethod->name }}</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-4 justify-center items-center md:flex-row">
                <a href="{{ route('order.pdf', $order->id) }}" target="_blank"
                   class="px-4 py-2 text-sm text-center text-white bg-blue-500 rounded transition duration-200 hover:bg-blue-600 md:text-base">
                    <i class="mr-2 fas fa-print"></i>Cetak Struk
                </a>
                <a href="{{ route('catalog') }}"
                   class="px-4 py-2 text-sm text-center text-white bg-gray-500 rounded transition duration-200 hover:bg-gray-600 md:text-base">
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