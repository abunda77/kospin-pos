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
                            @if($bankDetails)
                            <div class="mt-4">
                                <h3 class="font-semibold text-gray-700">Detail Rekening Bank</h3>
                                <p class="text-gray-600">Bank: <span class="font-bold">{{ $bankDetails['bank'] }}</span></p>
                                <p class="text-gray-600">Nomor Rekening: <span class="font-bold">{{ $bankDetails['account_number'] }}</span></p>
                                <p class="text-gray-600">Nama Pemilik Rekening: <span class="font-bold">{{ $bankDetails['account_name'] }}</span></p>
                                <div class="mt-3 p-3 bg-blue-50 rounded-md">
                                    <p class="text-gray-700">Setelah melakukan pembayaran, mohon konfirmasi melalui WhatsApp kami di:</p>
                                    <div class="flex items-center mt-2">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm6.958 17.394c-.293.636-1.045 1.137-1.809 1.319-1.1.262-2.411.179-3.824-.308-2.077-.712-3.858-1.888-5.577-3.617-1.719-1.729-2.885-3.52-3.587-5.607-.485-1.423-.558-2.744-.286-3.854.182-.774.673-1.537 1.299-1.839C4.863 3.589 5.45 3.483 6 3.483c.595 0 1.047.073 1.197.522.177.526.662 1.841.728 1.978.066.137.132.36-.066.569-.197.209-.329.345-.526.542-.197.197-.412.458-.177.881.235.423.989 1.785 2.145 2.885 1.488 1.346 2.674 1.726 3.111 1.923.386.173.674.16.923-.043.285-.232.667-.637.954-.927.215-.267.43-.309.729-.208.303.103 1.899.889 2.229 1.052.331.163.555.242.637.376.082.134.082.767-.214 1.514z"/>
                                        </svg>
                                        <span class="font-semibold text-gray-800">+62 877-7871-5788</span>
                                    </div>
                                </div>
                            </div>
                            @endif
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
