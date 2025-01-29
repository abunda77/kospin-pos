@extends('layouts.app')

@section('content')
<!-- Banner Section -->
<div class="relative mb-8 w-full">
    @if($activeBanners->count() > 0)
    <div class="banner-container relative aspect-[21/9] w-full">
        @foreach($activeBanners as $index => $banner)
        <div class="banner-item absolute inset-0 w-full h-full {{ $index === 0 ? 'active' : '' }}">
            <div class="relative w-full h-full">
                <img src="{{ Storage::url($banner->banner_image) }}"
                     alt="{{ $banner->judul_iklan }}"
                     class="object-cover w-full h-full">
                <div class="absolute right-0 bottom-0 left-0 p-6 bg-gradient-to-t to-transparent from-black/70">
                    <div class="container mx-auto">
                        <h3 class="mb-2 text-2xl font-bold text-white">{{ $banner->judul_iklan }}</h3>
                        <p class="text-white/90 line-clamp-2">{{ $banner->deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
<!-- Kategori Produk -->
<div class="container px-4 py-8 mx-auto">
    <h2 class="mb-6 text-2xl font-bold">Kategori Produk</h2>

    <div class="grid grid-cols-2 gap-4 mx-auto max-w-5xl md:grid-cols-4 lg:grid-cols-6">
        <a href="{{ route('catalog') }}"
           class="flex flex-col items-center p-4 bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg hover:scale-105">
            <div class="flex justify-center items-center mb-2 w-16 h-16 bg-gray-100 rounded-lg">
                <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-center text-gray-700 group-hover:text-primary-600">Semua Produk</span>
        </a>

        @foreach($categories as $category)
            <a href="{{ route('catalog.show', $category) }}"
               class="flex flex-col items-center p-4 bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg hover:scale-105">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}"
                         alt="{{ $category->name }}"
                         class="object-cover mb-2 w-16 h-16 rounded-lg">
                @else
                    <div class="flex justify-center items-center mb-2 w-16 h-16 bg-gray-100 rounded-lg">
                        @switch(strtolower($category->name))
                            @case('makanan')
                                <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                @break
                            @case('minuman')
                                <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19c-1.1 0-2-.9-2-2V9h4v8c0 1.1-.9 2-2 2zM8 3h8l1 6H7l1-6z"/>
                                </svg>
                                @break
                            @case('atk')
                                <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                        @endswitch
                    </div>
                @endif
                <span class="text-sm font-medium text-center text-gray-700 group-hover:text-primary-600">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</div>
<!-- Katalog Produk -->
<div class="container px-4 py-8 mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold sm:text-3xl">Katalog Produk</h1>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach($products as $product)
        <div class="overflow-hidden bg-gray-200 rounded-lg shadow-md transition-all duration-300 transform group hover:shadow-xl hover:-translate-y-1">
            <div class="overflow-hidden relative">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="object-cover w-full h-40 transition-transform duration-500 transform group-hover:scale-110">
                <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-300 group-hover:opacity-10"></div>
            </div>
            <div class="p-3">
                <h2 class="mb-1 text-base font-semibold text-gray-800 truncate transition-colors duration-300 group-hover:text-green-600">{{ $product->name }}</h2>
                <div class="flex items-center mb-1">
                    <span class="px-2 py-0.5 text-xs text-green-600 bg-green-100 rounded-full">{{ $product->category->name }}</span>
                </div>
                <p class="mb-2 text-lg font-bold text-gray-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex items-center mb-2">
                    <span class="text-sm text-gray-600">Stok:</span>
                    <span class="ml-1 px-2 py-0.5 text-xs {{ $product->stock > 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} rounded-full">
                        {{ $product->stock }}
                    </span>
                </div>
                <livewire:add-to-cart :product="$product" :wire:key="$product->id" />
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner-container {
        background-color: #f3f4f6;
    }

    .banner-item {
        opacity: 0;
        transition: opacity 1s ease-in-out;
        pointer-events: none;
    }

    .banner-item.active {
        opacity: 1;
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
    const DISPLAY_TIME = 5000; // 5 detik per banner

    function showNextBanner() {
        // Sembunyikan banner aktif saat ini
        banners[currentIndex].classList.remove('active');

        // Hitung index banner berikutnya
        currentIndex = (currentIndex + 1) % banners.length;

        // Tampilkan banner berikutnya
        banners[currentIndex].classList.add('active');
    }

    // Mulai rotasi banner
    setInterval(showNextBanner, DISPLAY_TIME);
});
</script>
@endpush
