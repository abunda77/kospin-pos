@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto min-h-[calc(100vh-4rem-20rem)]">
    <div class="mx-auto max-w-2xl">
        <div class="p-6 bg-white rounded-lg shadow-sm">
            <div class="text-center">
                <svg class="mx-auto w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="mt-4 text-2xl font-bold text-gray-900">Terima Kasih!</h1>
                <p class="mt-2 text-gray-600">Pesanan Anda telah berhasil diproses.</p>
                <p class="text-sm text-gray-500">Nomor Order: #{{ str_pad($order->no_order, 5, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="mt-8">
                <h2 class="pb-2 text-lg font-semibold border-b">Detail Pesanan</h2>

                <!-- Informasi Pemesan -->
                <div class="p-4 mt-4 bg-gray-50 rounded-lg">
                    <h3 class="mb-2 font-semibold text-gray-700">Informasi Pemesan</h3>
                    <div class="space-y-1 text-gray-600">
                        <p><span class="font-medium">Nama:</span> {{ $order->name }}</p>
                        <p><span class="font-medium">WhatsApp:</span> {{ $order->whatsapp }}</p>
                        <p><span class="font-medium">Alamat:</span> {{ $order->address }}</p>
                    </div>
                </div>

                <!-- Detail Produk -->
                <div class="mt-4 space-y-4">
                    @foreach($order->orderProducts as $item)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="object-cover w-12 h-12 rounded-lg">
                            <div>
                                <h3 class="font-medium">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach

                    <div class="pt-4 mt-4 space-y-2 border-t">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span class="flex items-center">
                                <svg class="mr-1 w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Diskon Voucher
                            </span>
                            <span class="text-green-600">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between pt-2 text-lg font-bold border-t">
                            <span>Total</span>
                            <span class="text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700">Informasi Pembayaran</h3>
                            <p class="mt-1 text-gray-600">Metode: {{ $order->paymentMethod->name }}</p>
                            <p class="text-gray-600">Status:
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-8 space-x-4">
                <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 text-white bg-blue-600 rounded-lg transition-colors hover:bg-blue-700">
                    <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('order.pdf', $order->id) }}" class="inline-flex items-center px-6 py-3 text-blue-600 bg-blue-100 rounded-lg transition-colors hover:bg-blue-200">
                    <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
