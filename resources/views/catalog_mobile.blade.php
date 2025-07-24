@extends('layouts.app')

@section('content')
<!-- Minimal Banner Section -->
<div class="relative mb-6 w-full overflow-hidden">
    @if($activeBanners->count() > 0)
    <div class="banner-container relative aspect-[16/9] w-full rounded-lg overflow-hidden shadow-md">
        @foreach($activeBanners as $index => $banner)
        <div class="banner-item absolute inset-0 w-full h-full transform transition-all duration-700 ease-in-out {{ $index === 0 ? 'active opacity-100 scale-100' : 'opacity-0 scale-105' }}">
            <div class="relative w-full h-full">
                <img src="{{ Storage::url($banner->banner_image) }}"
                     alt="{{ $banner->judul_iklan }}"
                     class="object-cover w-full h-full"
                     width="640" height="360"
                     loading="lazy">
                <div class="absolute right-0 bottom-0 left-0 p-4 bg-gradient-to-t to-transparent from-black/80">
                    <h3 class="mb-1 text-lg font-bold tracking-tight text-white">{{ $banner->judul_iklan }}</h3>
                    <p class="text-sm leading-snug text-white/90 line-clamp-1">{{ $banner->deskripsi }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Kategori Produk - Swipe Scroll -->
<div class="px-4 py-6 mb-2">
    <h2 class="mb-4 text-xl font-bold text-gray-900">Kategori Produk</h2>

    <div class="flex overflow-x-auto snap-x pb-4 -mx-2 hide-scrollbar">
        <div class="flex-shrink-0 w-24 mx-2 snap-start">
            <a href="{{ route('catalog.mobile') }}"
               class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm transition-colors h-full {{ !request()->category ? 'ring-2 ring-primary-500' : '' }}">
                <div class="flex justify-center items-center mb-2 w-12 h-12 bg-gradient-to-br rounded-xl from-primary-50 to-primary-100">
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-center text-gray-700">Semua</span>
            </a>
        </div>

        @foreach($categories as $cat)
        <div class="flex-shrink-0 w-24 mx-2 snap-start">
            <a href="{{ route('catalog.mobile.show', $cat->slug) }}"
               class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm transition-colors h-full {{ request()->category == $cat->slug ? 'ring-2 ring-primary-500' : '' }}">
                <div class="flex justify-center items-center mb-2 w-12 h-12 bg-gradient-to-br rounded-xl from-primary-50 to-primary-100">
                    @if($cat->icon)
                    {!! $cat->icon !!}
                    @else
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    @endif
                </div>
                <span class="text-xs font-medium text-center text-gray-700 line-clamp-1">{{ $cat->name }}</span>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Katalog Produk -->
<div class="px-4 py-4">
    <!-- Header dan Form Pencarian -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-900">
                @if(request()->category && $category)
                    {{ $category->name }}
                @else
                    Katalog Produk
                @endif
            </h1>
            
            <div class="flex items-center gap-2">
                @if(request()->search)
                    <a href="{{ request()->category ? route('catalog.mobile.show', request()->category) : route('catalog.mobile') }}" 
                       class="flex items-center text-sm text-primary-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif
                
                <a href="{{ request()->category 
                    ? route('catalog.show', request()->category) 
                    : route('catalog') }}" 
                   class="flex items-center text-xs text-gray-600 border border-gray-300 rounded px-2 py-1">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    Versi Desktop
                </a>
            </div>
        </div>

        <!-- Form Pencarian yang Dioptimalkan untuk Mobile -->
        <form action="{{ request()->category ? route('catalog.mobile.show', request()->category) : route('catalog.mobile') }}" 
              method="GET" 
              class="relative">
            @if(request()->category)
                <input type="hidden" name="category" value="{{ request()->category }}">
            @endif
            <div class="relative">
                <span class="flex absolute inset-y-0 left-0 items-center pl-3 text-gray-500 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input
                    type="search"
                    name="search"
                    value="{{ request()->search }}"
                    placeholder="Cari produk..."
                    class="py-2 pr-4 pl-10 w-full text-gray-700 bg-white rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                    autocomplete="off"
                    x-data
                    x-on:input.debounce.500ms="$el.form.requestSubmit()"
                >
                @if(request()->search)
                <button type="button" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        onclick="this.previousElementSibling.value = ''; this.form.requestSubmit();">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>

            <!-- Indikator Hasil Pencarian -->
            @if(request()->search)
                <div class="mt-2 text-xs text-gray-600">
                    {{ $products->total() }} hasil untuk "{{ request()->search }}"
                </div>
            @endif
        </form>
    </div>

    <!-- Tampilan Produk -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @forelse($products as $product)
            <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="w-1/3 h-24">
                        <img
                            src="{{ $product->image_url }}"
                            alt="{{ $product->name }}"
                            class="object-cover w-full h-full"
                            width="100" height="100"
                            loading="lazy"
                        >
                    </div>
                    <div class="w-2/3 p-3">
                        <h3 class="mb-1 text-base font-medium text-gray-800 line-clamp-1">{{ $product->name }}</h3>
                        <p class="mb-2 text-xs text-gray-600 line-clamp-1">{{ $product->description }}</p>                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <livewire:add-to-cart-mobile :product="$product" :wire:key="'cart-mobile-'.$product->id" />
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-6 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500">Tidak ada produk yang ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination - Simplified -->
    <div class="mt-6">
        @if ($products->hasPages())
            <div class="px-4 py-3 bg-white rounded-lg shadow-sm">
                <div class="flex justify-between">
                    <a href="{{ $products->previousPageUrl() }}" 
                       class="inline-flex items-center px-4 py-2 {{ !$products->onFirstPage() ? 'text-primary-600' : 'text-gray-400 cursor-not-allowed' }}"
                       @if(!$products->onFirstPage()) wire:navigate @endif>
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Sebelumnya
                    </a>
                    
                    <span class="text-sm text-gray-600">
                        {{ $products->currentPage() }} dari {{ $products->lastPage() }}
                    </span>
                    
                    <a href="{{ $products->nextPageUrl() }}" 
                       class="inline-flex items-center px-4 py-2 {{ $products->hasMorePages() ? 'text-primary-600' : 'text-gray-400 cursor-not-allowed' }}"
                       @if($products->hasMorePages()) wire:navigate @endif>
                        Selanjutnya
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Back to Top Button - Fixed -->
    <button 
        id="backToTopBtn"
        class="fixed bottom-16 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-primary-600 text-white shadow-lg opacity-0 transition-opacity duration-300 z-50"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
</div>
@endsection

@push('styles')
<style>
    /* Hide scrollbar for category slider */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Minimal banner styling */
    .banner-container {
        background-color: #f3f4f6;
    }
    
    .banner-item {
        opacity: 0;
        transform: scale(1.05);
        transition: all 0.7s ease;
        pointer-events: none;
    }
    
    .banner-item.active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }

    /* Touch-friendly styling */
    input, button, a {
        touch-action: manipulation;
    }

    /* Fixed position elements need to account for mobile browsers' UI */
    @supports (padding: max(0px)) {
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
        #backToTopBtn {
            bottom: max(1rem, env(safe-area-inset-bottom));
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simplified banner rotation for mobile
    const banners = document.querySelectorAll('.banner-item');
    if (banners.length > 1) {
        let currentIndex = 0;
        const DISPLAY_TIME = 4000; // 4 seconds per banner for mobile (faster than desktop)
        
        setInterval(() => {
            banners[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % banners.length;
            banners[currentIndex].classList.add('active');
        }, DISPLAY_TIME);
    }

    // Back to top button visibility
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.replace('opacity-0', 'opacity-100');
        } else {
            backToTopBtn.classList.replace('opacity-100', 'opacity-0');
        }
    });

    // Passive scroll event listeners for better performance
    const passiveIfSupported = false;
    try {
        window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
            get: () => passiveIfSupported = { passive: true }
        }));
    } catch(err) {}
    
    // Image lazy loading
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.loading = 'lazy';
        });
    } else {
        // Fallback for browsers that don't support native lazy loading
        // Could include a lazy loading library here
    }
});
</script>
@endpush
