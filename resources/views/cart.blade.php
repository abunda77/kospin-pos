@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto min-h-[calc(100vh-4rem-20rem)]">
    <h1 class="mb-8 text-2xl font-bold md:text-3xl">Keranjang Belanja</h1>

    <div class="mx-auto max-w-6xl">
        <!-- Daftar Produk -->
        <div class="p-4 mb-4 bg-white rounded-lg shadow-md md:p-6">
            <livewire:cart-items />
        </div>

        <!-- Voucher -->
        <div class="p-4 mb-4 bg-white rounded-lg shadow-md md:p-6">
            <livewire:cart-voucher />
        </div>

        <!-- Metode Pembayaran -->
        <div class="p-4 bg-white rounded-lg shadow-md md:p-6">
            <h2 class="mb-4 text-lg font-semibold">Metode Pembayaran</h2>
            <form action="{{ route('checkout') }}" method="GET">
                @csrf
                <div class="space-y-3">
                    @foreach($paymentMethods as $method)
                    <label class="flex items-center p-4 rounded-lg border cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            {{ $loop->first ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $method->name }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="px-6 py-3 w-full text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Lanjut ke Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
