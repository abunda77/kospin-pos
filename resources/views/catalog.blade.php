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

<div class="container px-4 py-8 mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold sm:text-3xl">Katalog Produk</h1>
        <a href="{{ route('catalog.download-pdf') }}" class="px-4 py-2 text-sm text-white bg-green-500 rounded-lg transition-colors duration-300 hover:bg-green-600 sm:text-base hover:shadow-lg">
            Download Catalog PDF
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($products as $product)
        <div class="overflow-hidden bg-white rounded-xl shadow-md transition-all duration-300 transform group hover:shadow-xl hover:-translate-y-1">
            <div class="overflow-hidden relative">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="object-cover w-full h-56 transition-transform duration-500 transform group-hover:scale-110">
                <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-300 group-hover:opacity-10"></div>
            </div>
            <div class="p-5">
                <h2 class="mb-2 text-xl font-semibold text-gray-800 transition-colors duration-300 group-hover:text-green-600">{{ $product->name }}</h2>
                <div class="flex items-center mb-2">
                    <span class="px-3 py-1 text-sm text-green-600 bg-green-100 rounded-full">{{ $product->category->name }}</span>
                </div>
                <p class="mb-4 text-2xl font-bold text-gray-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex items-center mb-3">
                    <span class="text-gray-600">Stok:</span>
                    <span class="ml-2 px-3 py-1 text-sm {{ $product->stock > 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} rounded-full">
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
