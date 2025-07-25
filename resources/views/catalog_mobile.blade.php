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
        <div class="flex-shrink-0 w-36 mx-2 snap-start">
            <a href="{{ route('catalog.mobile') }}"
               class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow transition-all duration-300 hover:bg-primary-50 hover:shadow-md group {{ !request()->category ? 'ring-2 ring-primary-500 bg-primary-50 bg-gray-100' : 'bg-gray-100' }}">
                <div class="flex justify-center items-center min-w-[3rem] h-12 bg-gradient-to-br rounded-lg from-primary-50 to-primary-100">
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 whitespace-nowrap">Semua</span>
            </a>
        </div>
        @foreach($categories as $cat)
            @php
                $cardBg = match(strtolower($cat->name)) {
                    'makanan' => 'bg-yellow-100',
                    'minuman' => 'bg-blue-100',
                    'atk' => 'bg-pink-100',
                    'bahan pokok' => 'bg-green-100',
                    'elektronik' => 'bg-gray-200',
                    'kebutuhan tersier' => 'bg-purple-100',
                    'rumah tangga' => 'bg-orange-100',
                    'perawatan badan' => 'bg-red-100',
                    'lainnya' => 'bg-slate-100',
                    default => 'bg-gray-100',
                };
                $iconBg = match(strtolower($cat->name)) {
                    'makanan' => 'bg-yellow-300',
                    'minuman' => 'bg-blue-300',
                    'atk' => 'bg-pink-300',
                    'bahan pokok' => 'bg-green-300',
                    'elektronik' => 'bg-gray-300',
                    'kebutuhan tersier' => 'bg-purple-300',
                    'rumah tangga' => 'bg-orange-300',
                    'perawatan badan' => 'bg-red-300',
                    'lainnya' => 'bg-slate-300',
                    default => 'bg-primary-300',
                };
            @endphp
            <div class="flex-shrink-0 w-auto mx-2 snap-start">
                <a href="{{ route('catalog.mobile.show', $cat->slug) }}"
                   class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow transition-all duration-300 hover:bg-primary-50 hover:shadow-md group {{ $cardBg }} {{ request()->category == $cat->slug ? 'ring-2 ring-primary-500 bg-primary-50' : '' }}">
                    <div class="flex justify-center items-center min-w-[3rem] h-12 rounded-lg {{ $iconBg }}">
                        @if($cat->icon)
                            <img src="{{ Storage::url($cat->icon) }}" alt="{{ $cat->name }}" class="object-contain w-6 h-6">
                        @else
                            @switch(strtolower($cat->name))
                                @case('makanan')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    @break
                                @case('minuman')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19c-1.1 0-2-.9-2-2V9h4v8c0 1.1-.9 2-2 2zM8 3h8l1 6H7l1-6z"/>
                                    </svg>
                                    @break
                                @case('atk')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    @break
                                @case('bahan pokok')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    @break
                                @case('elektronik')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    @break
                                @case('kebutuhan tersier')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                    @break
                                @case('rumah tangga')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    @break
                                @case('perawatan badan')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 10-4 0v2a2 2 0 104 0V7zm-7 8a7 7 0 1114 0v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1z"/>
                                    </svg>
                                    @break
                                @case('lainnya')
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                            @endswitch
                        @endif
                    </div>
                    <span class="text-xs font-medium text-gray-700 whitespace-nowrap">{{ $cat->name }}</span>
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
                <div class="relative aspect-w-4 aspect-h-3">
                    @if($product->image)
                        <img src="{{ url('/storage/public/products/' . basename($product->image)) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                    @else
                        <div class="flex items-center justify-center w-full h-full bg-gray-100">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="mb-1 text-sm font-medium text-gray-900">{{ $product->name }}</h3>
                            <p class="text-sm font-medium text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @if($product->stock <= 5 && $product->stock > 0)
                                <p class="mt-1 text-xs text-orange-500">Stok tinggal {{ $product->stock }}</p>
                            @elseif($product->stock == 0)
                                <p class="mt-1 text-xs text-red-500">Stok habis</p>
                            @endif
                        </div>
                        <div class="ml-3">
                            <livewire:add-to-cart-mobile :product="$product" :wire:key="'cart-'.$product->id" />
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-6 text-center">
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
