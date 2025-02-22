@extends('layouts.app')

@section('content')
<!-- Banner Section -->
<div class="hidden overflow-hidden relative mb-12 w-full sm:block">
    @if($activeBanners->count() > 0)
    <div class="banner-container relative aspect-[21/9] w-full rounded-xl overflow-hidden shadow-2xl">
        @foreach($activeBanners as $index => $banner)
        <div class="banner-item absolute inset-0 w-full h-full transform transition-all duration-1000 ease-in-out {{ $index === 0 ? 'active opacity-100 scale-100' : 'opacity-0 scale-105' }}">
            <div class="relative w-full h-full">
                <img src="{{ Storage::url($banner->banner_image) }}"
                     alt="{{ $banner->judul_iklan }}"
                     class="object-cover w-full h-full transition-transform duration-1000 ease-in-out transform hover:scale-105"
                     loading="lazy">
                <div class="absolute right-0 bottom-0 left-0 p-8 bg-gradient-to-t to-transparent from-black/80">
                    <div class="container mx-auto">
                        <h3 class="mb-3 text-3xl font-bold tracking-tight text-white">{{ $banner->judul_iklan }}</h3>
                        <p class="text-lg leading-relaxed text-white/90 line-clamp-2">{{ $banner->deskripsi }}</p>
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
    <h2 class="mb-8 text-3xl font-bold text-center text-gray-900">Kategori Produk</h2>

    <div class="grid grid-cols-2 gap-6 mx-auto max-w-7xl sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <a href="{{ route('catalog') }}"
           class="flex flex-col items-center p-6 bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 group {{ !request()->category ? 'ring-2 ring-primary-500' : '' }}">
            <div class="flex justify-center items-center mb-4 w-20 h-20 bg-gradient-to-br rounded-2xl from-primary-50 to-primary-100">
                <svg class="w-10 h-10 transition-colors text-primary-600 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <span class="text-base font-semibold text-center text-gray-700 transition-colors group-hover:text-primary-600">Semua Produk</span>
        </a>

        @foreach($categories as $cat)
            <x-category-link :category="$cat" />
        @endforeach
    </div>
</div>
<!-- Katalog Produk -->
<div class="container px-4 py-12 mx-auto">
    <!-- Header dan Form Pencarian -->
    <div class="flex flex-col gap-6 mb-10 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-center text-gray-900 md:text-3xl md:text-left">Katalog Produk</h1>

        <!-- Form Pencarian yang Ditingkatkan -->
        <div class="w-full md:max-w-lg">
            <form action="{{ route('catalog') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:gap-2">
                @if(request()->category)
                    <input type="hidden" name="category" value="{{ request()->category }}">
                @endif
                <div class="relative flex-1">
                    <span class="flex absolute inset-y-0 left-0 items-center pl-3 text-gray-500 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request()->search }}"
                        placeholder="Cari produk..."
                        class="py-3 pr-4 pl-10 w-full text-gray-700 bg-white rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                        autocomplete="off"
                        x-data
                        x-on:input.debounce.300ms="$el.form.requestSubmit()"
                    >
                    @if(request()->search)
                        <button
                            type="button"
                            onclick="window.location.href='{{ route('catalog', request()->except('search')) }}'"
                            class="flex absolute inset-y-0 right-0 items-center pr-3 text-gray-500 hover:text-gray-700"
                            title="Hapus pencarian">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
                <button type="submit" class="flex gap-2 justify-center items-center px-6 py-3 w-full text-white rounded-lg transition duration-150 ease-in-out bg-primary-600 hover:bg-primary-700 active:bg-primary-800 focus:ring-2 focus:ring-primary-200 focus:outline-none sm:w-auto touch-manipulation">
                    <span class="hidden sm:inline">Cari</span>
                    <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>

            <!-- Indikator Hasil Pencarian -->
            @if(request()->search)
                <div class="mt-3 text-sm text-gray-600">
                    Menampilkan hasil pencarian untuk "{{ request()->search }}"
                    <span class="text-primary-600">({{ $products->total() }} hasil)</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Tampilan Produk -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($products as $product)
            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="object-cover w-full h-48"
                >
                <div class="p-4">
                    <h3 class="mb-2 text-xl font-semibold text-gray-800">{{ $product->name }}</h3>
                    <p class="mb-4 text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-bold text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <div class="flex flex-col items-end">
                            <span class="text-sm text-gray-500">Kategori: {{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                            <livewire:product-stock :product="$product" :wire:key="'stock-'.$product->id" />
                        </div>
                    </div>
                    <livewire:add-to-cart :product="$product" :wire:key="'cart-'.$product->id" />
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p class="text-gray-500">Tidak ada produk yang ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        @if ($products->hasPages())
            <div class="px-4 py-3 bg-white rounded-lg shadow-sm">
                <div class="flex flex-col items-center">
                    <!-- Info halaman -->
                    <div class="mb-4 text-sm text-gray-700">
                        Menampilkan {{ $products->firstItem() }} sampai {{ $products->lastItem() }}
                        dari {{ $products->total() }} data
                    </div>

                    <!-- Links Pagination -->
                    <div class="flex justify-center">
                        {{-- Previous Page Link --}}
                        @if ($products->onFirstPage())
                            <span class="px-3 py-1 text-gray-400 bg-gray-100 border rounded-l cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border rounded-l hover:bg-gray-100">
                                Previous
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $start = $products->currentPage() - 2;
                            $end = $products->currentPage() + 2;
                            if ($start < 1) {
                                $start = 1;
                                $end = min(5, $products->lastPage());
                            }
                            if ($end > $products->lastPage()) {
                                $end = $products->lastPage();
                                $start = max(1, $end - 4);
                            }
                        @endphp

                        {{-- First Page + Dots --}}
                        @if($start > 1)
                            <a href="{{ $products->url(1) }}" class="px-3 py-1 text-gray-700 bg-white border-t border-b hover:bg-gray-100">1</a>
                            @if($start > 2)
                                <span class="px-3 py-1 text-gray-700 bg-white border-t border-b">...</span>
                            @endif
                        @endif

                        {{-- Pagination Elements --}}
                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $products->currentPage())
                                <span class="px-3 py-1 text-white bg-primary-600 border-t border-b">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $products->url($i) }}" class="px-3 py-1 text-gray-700 bg-white border-t border-b hover:bg-gray-100">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Last Page + Dots --}}
                        @if($end < $products->lastPage())
                            @if($end < $products->lastPage() - 1)
                                <span class="px-3 py-1 text-gray-700 bg-white border-t border-b">...</span>
                            @endif
                            <a href="{{ $products->url($products->lastPage()) }}" class="px-3 py-1 text-gray-700 bg-white border-t border-b hover:bg-gray-100">
                                {{ $products->lastPage() }}
                            </a>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border rounded-r hover:bg-gray-100">
                                Next
                            </a>
                        @else
                            <span class="px-3 py-1 text-gray-400 bg-gray-100 border rounded-r cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
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

    /* Styling untuk pagination */
    nav[role="navigation"] {
        width: 100%;
        display: block;
    }

    nav[role="navigation"] > div {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
    }

    nav[role="navigation"] .flex.justify-between,
    nav[role="navigation"] .hidden {
        display: flex !important;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    nav[role="navigation"] span[aria-current="page"] span,
    nav[role="navigation"] a {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 200ms;
    }

    /* Halaman aktif */
    nav[role="navigation"] span[aria-current="page"] span {
        background-color: var(--primary-600);
        color: white;
    }

    /* Link halaman */
    nav[role="navigation"] a {
        color: #374151;
        background-color: white;
        border: 1px solid #e5e7eb;
    }

    nav[role="navigation"] a:hover {
        background-color: #f3f4f6;
    }

    /* Tombol disabled */
    nav[role="navigation"] span[aria-disabled="true"] {
        color: #9ca3af;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        cursor: not-allowed;
    }

    /* Responsif */
    @media (max-width: 640px) {
        nav[role="navigation"] .sm\:hidden {
            display: block !important;
            width: 100%;
        }

        nav[role="navigation"] .sm\:flex-1 {
            width: 100%;
            text-align: center;
        }
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
        banners[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % banners.length;
        banners[currentIndex].classList.add('active');
    }

    function startRotation() {
        interval = setInterval(showNextBanner, DISPLAY_TIME);
    }

    function pauseRotation() {
        clearInterval(interval);
    }

    startRotation();

    const bannerContainer = document.querySelector('.banner-container');
    bannerContainer.addEventListener('mouseenter', pauseRotation);
    bannerContainer.addEventListener('mouseleave', startRotation);
});
</script>
@endpush
