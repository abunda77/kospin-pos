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

        <!-- Tombol Lanjut ke Pembayaran -->
        <div class="p-4 bg-white rounded-lg shadow-md md:p-6">
            <a href="{{ route('checkout') }}"
               x-data="{ loading: false }"
               x-on:click="loading = true"
               class="block px-6 py-3 w-full text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 touch-manipulation"
               x-bind:class="{ 'opacity-70 cursor-wait': loading }">
                <span x-show="!loading">Lanjut ke Pembayaran</span>
                <span x-show="loading" class="flex justify-center items-center">
                    <svg class="inline mr-2 w-4 h-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </a>
        </div>
    </div>
</div>
@endsection


