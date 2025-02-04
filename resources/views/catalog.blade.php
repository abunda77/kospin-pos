@extends('layouts.app')

@section('content')
<!-- Banner Section -->
<div class="relative mb-12 w-full overflow-hidden">
    @if($activeBanners->count() > 0)
    <div class="banner-container relative aspect-[21/9] w-full rounded-xl overflow-hidden shadow-2xl">
        @foreach($activeBanners as $index => $banner)
        <div class="banner-item absolute inset-0 w-full h-full transform transition-all duration-1000 ease-in-out {{ $index === 0 ? 'active opacity-100 scale-100' : 'opacity-0 scale-105' }}">
            <div class="relative w-full h-full">
                <img src="{{ Storage::url($banner->banner_image) }}"
                     alt="{{ $banner->judul_iklan }}"
                     class="object-cover w-full h-full transform transition-transform duration-1000 ease-in-out hover:scale-105"
                     loading="lazy">
                <div class="absolute right-0 bottom-0 left-0 p-8 bg-gradient-to-t from-black/80 to-transparent">
                    <div class="container mx-auto">
                        <h3 class="mb-3 text-3xl font-bold text-white tracking-tight">{{ $banner->judul_iklan }}</h3>
                        <p class="text-white/90 line-clamp-2 text-lg leading-relaxed">{{ $banner->deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
<!-- Kategori Produk -->
<div class="container px-4 py-12 mx-auto">
    <h2 class="mb-8 text-3xl font-bold text-gray-900 text-center">Kategori Produk</h2>

    <div class="grid grid-cols-2 gap-6 mx-auto max-w-7xl sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <a href="{{ route('catalog') }}"
           class="flex flex-col items-center p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group">
            <div class="flex justify-center items-center mb-4 w-20 h-20 bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl">
                <svg class="w-10 h-10 text-primary-600 group-hover:text-primary-700 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <span class="text-base font-semibold text-center text-gray-700 group-hover:text-primary-600 transition-colors">Semua Produk</span>
        </a>

        @foreach($categories as $category)
            @include('components.category-link', ['category' => $category])
        @endforeach
    </div>
</div>
<!-- Katalog Produk -->
<div class="container px-4 py-12 mx-auto">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Katalog Produk</h1>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach($products as $product)
        <div class="overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform group hover:-translate-y-2">
            <div class="overflow-hidden relative aspect-square">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="object-cover w-full h-full transition-transform duration-500 transform group-hover:scale-105">
                <div class="absolute inset-0 bg-black/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
            </div>
            <div class="p-4">
                <h2 class="mb-2 text-lg font-semibold text-gray-800 truncate transition-colors duration-300 group-hover:text-primary-600">{{ $product->name }}</h2>
                <div class="flex items-center mb-2">
                    <span class="px-2 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded-full">{{ $product->category->name }}</span>
                </div>
                <p class="mb-3 text-xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex items-center mb-4">
                    <span class="text-sm text-gray-600">Stok:</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium {{ $product->stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} rounded-full">
                        {{ $product->stock }}
                    </span>
                </div>
                <livewire:add-to-cart :product="$product" :wire:key="$product->id" />
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $products->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner-container {
        background-color: #f3f4f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .banner-item {
        opacity: 0;
        transform: scale(1.05);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    .banner-item.active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const banners = document.querySelectorAll('.banner-item');
    if (banners.length <= 1) return;

    let currentIndex = 0;
    let interval;
    const DISPLAY_TIME = 5000; // 5 detik per banner

    function showNextBanner() {
        // Sembunyikan banner aktif saat ini
        banners[currentIndex].classList.remove('active');

        // Hitung index banner berikutnya
        currentIndex = (currentIndex + 1) % banners.length;

        // Tampilkan banner berikutnya
        banners[currentIndex].classList.add('active');
    }

    function startRotation() {
        interval = setInterval(showNextBanner, DISPLAY_TIME);
    }

    function pauseRotation() {
        clearInterval(interval);
    }

    // Mulai rotasi banner
    startRotation();

    // Pause saat hover
    const bannerContainer = document.querySelector('.banner-container');
    bannerContainer.addEventListener('mouseenter', pauseRotation);
    bannerContainer.addEventListener('mouseleave', startRotation);
});
</script>
@endpush
