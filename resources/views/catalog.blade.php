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

    <!-- Navigation Hint untuk Mobile -->
    <div class="block mb-4 text-center sm:hidden">
        <p class="text-sm text-gray-600">Geser ke kanan untuk melihat kategori lainnya</p>
    </div>

    <!-- Category Grid - Improved for mobile navigation -->
    <div class="overflow-x-auto pb-4 sm:overflow-visible">
        <div class="flex gap-4 mx-auto min-w-max sm:grid sm:grid-cols-2 sm:min-w-0 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 max-w-7xl">
            <a href="{{ route('catalog') }}"
               class="flex gap-3 items-center px-4 py-3 bg-white rounded-xl shadow-md transition-all duration-300 hover:bg-primary-50 hover:shadow-lg hover:scale-105 group whitespace-nowrap sm:whitespace-normal {{ !request()->category ? 'ring-2 ring-primary-500 bg-primary-50 shadow-lg' : '' }}">
                <div class="flex justify-center items-center min-w-[3rem] h-12 bg-gradient-to-br rounded-lg from-primary-50 to-primary-100 group-hover:from-primary-100 group-hover:to-primary-200 transition-all duration-300">
                    <svg class="w-6 h-6 transition-colors text-primary-600 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <span class="text-base font-semibold text-gray-700 transition-colors group-hover:text-primary-600">Semua Produk</span>
            </a>

            @foreach($categories as $cat)
                <x-category-link :category="$cat" />
            @endforeach
        </div>
    </div>
</div>
<!-- Katalog Produk -->
<div class="container px-4 py-12 mx-auto">
    <!-- Header dan Form Pencarian -->
    <div class="flex flex-col gap-6 mb-10 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full md:w-auto">
            <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Katalog Produk</h1>
            
            <!-- Mobile Version Button - Improved Styling -->
            @php
                $currentCategory = request()->category;
                $currentParams = request()->query();
                $mobileRoute = $currentCategory 
                    ? route('catalog.mobile.show', array_merge([$currentCategory], $currentParams))
                    : route('catalog.mobile', $currentParams);
            @endphp
            <a href="{{ $mobileRoute }}" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-100"
                onclick="sessionStorage.setItem('view_preference', 'mobile');">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>Versi Mobile</span>
            </a>
        </div>

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
            <div class="overflow-hidden bg-white rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 group">
                <!-- Product Image with Loading State -->
                <div class="relative overflow-hidden aspect-square">
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110"
                        loading="lazy"
                        onload="this.classList.add('loaded')"
                        onerror="this.src='/images/placeholder-product.jpg'"
                    >
                    <!-- Overlay dengan quick action -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 transition-all duration-300 group-hover:bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="text-white text-sm font-medium px-3 py-1 bg-black bg-opacity-50 rounded-full">
                            Lihat Detail
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-3">
                        <h3 class="text-lg font-bold text-gray-800 line-clamp-2 group-hover:text-primary-600 transition-colors duration-200">{{ $product->name }}</h3>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                    </div>
                    
                    <!-- Price and Category Info -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xl font-bold text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                                {{ $product->category?->name ?? 'Tanpa Kategori' }}
                            </span>
                            <div class="mt-1">
                                <livewire:product-stock :product="$product" :wire:key="'stock-'.$product->id" />
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add to Cart Button -->
                    <div class="mt-4">
                        <livewire:add-to-cart :product="$product" :wire:key="'cart-'.$product->id" />
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada produk ditemukan</h3>
                    <p class="text-gray-500">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                    @if(request()->search || request()->category)
                        <a href="{{ route('catalog') }}" class="inline-flex items-center mt-4 px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>
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
                    <div class="flex gap-1 justify-center items-center my-4">
                        {{-- Previous Page Link --}}
                        @if ($products->onFirstPage())
                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-md border border-gray-200 transition-colors duration-150 ease-in-out cursor-not-allowed">
                                <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Previous
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-200 transition-colors duration-150 ease-in-out hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:bg-primary-100">
                                <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
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
                            <a href="{{ $products->url(1) }}" class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:bg-primary-100 transition-colors duration-150 ease-in-out">1</a>
                            @if($start > 2)
                                <span class="inline-flex justify-center items-center px-3 py-2 text-sm text-gray-500">...</span>
                            @endif
                        @endif

                        {{-- Pagination Elements --}}
                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $products->currentPage())
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-2 text-sm font-semibold text-gray-200 bg-primary-600 border border-primary-600 rounded-md shadow-sm ring-2 ring-primary-600 ring-offset-2">
                                    {{ $i }}
                                </span>
                            @else
                                @php
                                    $url = request()->has('category') ? $products->url($i) . '&category=' . request()->get('category') : $products->url($i);
                                @endphp
                                <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:bg-primary-100 transition-colors duration-150 ease-in-out">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Last Page + Dots --}}
                        @if($end < $products->lastPage())
                            @if($end < $products->lastPage() - 1)
                                <span class="inline-flex justify-center items-center px-3 py-2 text-sm text-gray-500">...</span>
                            @endif
                            <a href="{{ $products->url($products->lastPage()) }}" class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:bg-primary-100 transition-colors duration-150 ease-in-out">
                                {{ $products->lastPage() }}
                            </a>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($products->hasMorePages())
                            @php
                                $url = request()->has('category') ? $products->nextPageUrl() . '&category=' . request()->get('category') : $products->nextPageUrl();
                            @endphp
                            <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-200 transition-colors duration-150 ease-in-out hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:bg-primary-100">
                                Next
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-md border border-gray-200 transition-colors duration-150 ease-in-out cursor-not-allowed">
                                Next
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
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

    /* Enhanced scrollbar untuk category horizontal scroll */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
        transition: background 0.2s;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Product image loading animation */
    img {
        transition: opacity 0.3s ease;
        opacity: 0;
    }

    img.loaded {
        opacity: 1;
    }

    /* Enhanced hover effects for better UX */
    .group:hover .group-hover\:scale-105 {
        transform: scale(1.05);
    }

    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }

    /* Smooth transitions for all interactive elements */
    a, button, .transition-all {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Focus styles for accessibility */
    a:focus-visible, button:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 0.375rem;
    }

    /* Mobile touch optimization */
    @media (max-width: 768px) {
        .touch-callout-none {
            -webkit-touch-callout: none;
        }
        
        .select-none {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
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
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Link halaman */
    nav[role="navigation"] a {
        color: #374151;
        background-color: white;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    nav[role="navigation"] a:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    /* Tombol disabled */
    nav[role="navigation"] span[aria-disabled="true"] {
        color: #9ca3af;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Loading skeleton untuk gambar produk */
    .image-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
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

        /* Optimized tap targets for mobile */
        .mobile-tap-target {
            min-height: 44px;
            min-width: 44px;
        }
    }

    /* Dark mode considerations */
    @media (prefers-color-scheme: dark) {
        .dark-mode-support {
            /* Add dark mode styles if needed */
        }
    }

    /* Reduce motion for users who prefer it */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Banner rotation functionality
    const banners = document.querySelectorAll('.banner-item');
    if (banners.length > 1) {
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
        if (bannerContainer) {
            bannerContainer.addEventListener('mouseenter', pauseRotation);
            bannerContainer.addEventListener('mouseleave', startRotation);
        }
    }

    // Enhanced image loading with skeleton effect
    const productImages = document.querySelectorAll('img[loading="lazy"]');
    productImages.forEach(img => {
        const parent = img.parentElement;
        
        // Add skeleton while loading
        if (!img.complete) {
            parent.classList.add('image-skeleton');
        }
        
        img.addEventListener('load', function() {
            parent.classList.remove('image-skeleton');
            this.classList.add('loaded');
        });
        
        img.addEventListener('error', function() {
            parent.classList.remove('image-skeleton');
            this.src = '/images/placeholder-product.jpg';
            this.classList.add('loaded');
        });
    });

    // Smooth scroll for category navigation on mobile
    const categoryContainer = document.querySelector('.overflow-x-auto');
    if (categoryContainer) {
        let isScrolling = false;
        
        categoryContainer.addEventListener('scroll', function() {
            if (!isScrolling) {
                isScrolling = true;
                requestAnimationFrame(function() {
                    isScrolling = false;
                });
            }
        });
    }

    // Enhanced search functionality with loading state
    const searchForm = document.querySelector('form[action*="catalog"]');
    const searchInput = document.querySelector('input[name="search"]');
    const searchButton = document.querySelector('button[type="submit"]');
    
    if (searchForm && searchInput && searchButton) {
        let searchTimeout;
        
        searchForm.addEventListener('submit', function(e) {
            // Add loading state
            searchButton.disabled = true;
            searchButton.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        });

        // Debounced search for better UX
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length > 2 || this.value.length === 0) {
                    searchForm.requestSubmit();
                }
            }, 500);
        });
    }

    // Add ripple effect to buttons and links
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = diameter + 'px';
        circle.style.left = event.clientX - button.offsetLeft - radius + 'px';
        circle.style.top = event.clientY - button.offsetTop - radius + 'px';
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
        
        setTimeout(() => {
            circle.remove();
        }, 600);
    }

    // Apply ripple effect to interactive elements
    const interactiveElements = document.querySelectorAll('a, button, .group');
    interactiveElements.forEach(element => {
        element.addEventListener('click', createRipple);
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
    });

    // Add CSS for ripple effect
    const rippleCSS = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    
    const style = document.createElement('style');
    style.textContent = rippleCSS;
    document.head.appendChild(style);

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe product cards for fade-in animation
    const productCards = document.querySelectorAll('.grid > div');
    productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Touch gestures for mobile category navigation
    if ('ontouchstart' in window) {
        const categoryScroll = document.querySelector('.overflow-x-auto');
        if (categoryScroll) {
            let startX, scrollLeft;

            categoryScroll.addEventListener('touchstart', e => {
                startX = e.touches[0].pageX - categoryScroll.offsetLeft;
                scrollLeft = categoryScroll.scrollLeft;
            });

            categoryScroll.addEventListener('touchmove', e => {
                if (!startX) return;
                e.preventDefault();
                const x = e.touches[0].pageX - categoryScroll.offsetLeft;
                const walk = (x - startX) * 2;
                categoryScroll.scrollLeft = scrollLeft - walk;
            });
        }
    }

    // Keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close any open modals or clear search
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                searchForm.requestSubmit();
            }
        }
    });

    // Performance optimization: Lazy load images when they're about to enter viewport
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });

        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
</script>
@endpush
