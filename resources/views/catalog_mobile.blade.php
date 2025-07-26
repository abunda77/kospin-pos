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

<!-- Kategori Produk - Enhanced Swipe Scroll -->
<div class="px-4 py-6 mb-2">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900">Kategori Produk</h2>
        <div class="text-xs text-gray-500 sm:hidden">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
            </svg>
            Geser untuk melihat lainnya
        </div>
    </div>

    <div class="flex overflow-x-auto snap-x pb-4 -mx-2 scrollbar-hide category-container">
        <!-- Semua Produk Button -->
        <div class="flex-shrink-0 w-auto min-w-[140px] mx-2 snap-start category-item">
            <a href="{{ route('catalog.mobile') }}"
               class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 group {{ !request()->category ? 'bg-primary-50 shadow-lg' : 'bg-gray-100 hover:bg-gray-50' }}">
                <div class="flex justify-center items-center min-w-[3rem] h-12 bg-gradient-to-br rounded-lg from-primary-50 to-primary-100 group-hover:from-primary-100 group-hover:to-primary-200 transition-all duration-300">
                    <svg class="w-6 h-6 text-primary-600 transition-colors group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap transition-colors group-hover:text-primary-600">Semua Produk</span>
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
            <div class="flex-shrink-0 w-auto min-w-[140px] mx-2 snap-start category-item">
                <a href="{{ route('catalog.mobile.show', $cat->slug) }}"
                   class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 group {{ $cardBg }} {{ request()->category == $cat->slug ? 'ring-2 ring-primary-500 bg-primary-50 shadow-lg' : 'hover:bg-primary-50' }}">
                    <div class="flex justify-center items-center min-w-[3rem] h-12 rounded-lg {{ $iconBg }} group-hover:scale-110 transition-transform duration-300">
                        @if($cat->icon)
                            <img src="{{ Storage::url($cat->icon) }}" alt="{{ $cat->name }}" class="object-contain w-6 h-6 transition-transform duration-300 group-hover:scale-110">
                        @else
                            @switch(strtolower($cat->name))
                                @case('makanan')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    @break
                                @case('minuman')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    @break
                                @case('atk')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    @break
                                @case('bahan pokok')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    @break
                                @case('elektronik')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    @break
                                @case('kebutuhan tersier')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                    @break
                                @case('rumah tangga')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    </svg>
                                    @break
                                @case('perawatan badan')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    @break
                                @case('lainnya')
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                            @endswitch
                        @endif
                    </div>
                    <span class="text-sm font-semibold text-gray-700 whitespace-nowrap transition-colors duration-300 group-hover:text-primary-600">{{ $cat->name }}</span>
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
              <div class="flex items-center gap-3">
                @if(request()->search)
                    <a href="{{ request()->category ? route('catalog.mobile.show', request()->category) : route('catalog.mobile') }}" 
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif                <!-- Desktop Version Button - Improved Styling -->
                @php
                    $currentCategory = request()->category;
                    $currentParams = request()->query();
                    $desktopRoute = $currentCategory 
                        ? route('catalog.show', array_merge([$currentCategory], $currentParams))
                        : route('catalog', $currentParams);
                @endphp
                <a href="{{ $desktopRoute }}" 
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-100"
                   onclick="sessionStorage.setItem('view_preference', 'desktop');">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <span>Versi Desktop</span>
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
    </div>    <!-- Tampilan Produk - Enhanced Mobile Layout -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @forelse($products as $product)
            <div class="overflow-hidden bg-white rounded-xl shadow-md border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-105 group product-card">
                <!-- Product Image with Enhanced Loading State -->
                <div class="relative aspect-w-4 aspect-h-3 overflow-hidden">
                    @if($product->image)
                        <img src="{{ url('/storage/public/products/' . basename($product->image)) }}" 
                             alt="{{ $product->name }}" 
                             class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110"
                             loading="lazy"
                             onload="this.classList.add('loaded')"
                             onerror="this.parentElement.querySelector('.fallback-placeholder').classList.remove('hidden'); this.style.display='none';">
                    @endif
                    
                    <!-- Fallback Placeholder -->
                    <div class="fallback-placeholder {{ $product->image ? 'hidden' : '' }} flex items-center justify-center w-full h-full bg-gray-100">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Tidak ada gambar</p>
                        </div>
                    </div>

                    <!-- Product Overlay dengan Quick Actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 transition-all duration-300 group-hover:bg-opacity-10 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="text-white text-xs font-medium px-2 py-1 bg-black bg-opacity-50 rounded-full">
                            Tap untuk detail
                        </div>
                    </div>

                    <!-- Stock Badge -->
                    <div class="absolute top-2 left-2">
                        <livewire:product-stock :product="$product" :wire:key="'stock-'.$product->id" />
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 pr-3">
                            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors duration-200">{{ $product->name }}</h3>
                            <p class="text-lg font-bold text-primary-600 mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <livewire:add-to-cart-mobile :product="$product" :wire:key="'cart-'.$product->id" />
                        </div>
                    </div>
                    
                    <!-- Category Info -->
                    <div class="flex justify-between items-center">
                        <span class="inline-block px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                            {{ $product->category?->name ?? 'Tanpa Kategori' }}
                        </span>
                        
                        <!-- Product Rating or Additional Info (if available) -->
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>4.5</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <div class="max-w-sm mx-auto">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada produk ditemukan</h3>
                    <p class="text-gray-500 text-sm mb-4">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                    @if(request()->search || request()->category)
                        <a href="{{ route('catalog.mobile') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>    <!-- Pagination - Enhanced Mobile Experience -->
    @if ($products->hasPages())
        <div class="mt-8 mb-6">
            <div class="flex items-center justify-center gap-3 px-4">
                <!-- Previous Button -->
                @if (!$products->onFirstPage())
                    <a href="{{ $products->previousPageUrl() }}" 
                       class="pagination-btn flex items-center justify-center w-12 h-12 rounded-full bg-white border border-gray-200 text-primary-600 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @else
                    <div class="pagination-btn flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                @endif
                
                <!-- Page Info dengan Progress Bar -->
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center min-w-[100px] px-4 py-2 bg-white rounded-full border border-gray-200 shadow-sm">
                        <span class="text-sm font-semibold text-gray-900">{{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-20 h-1 mt-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-500 rounded-full transition-all duration-300" 
                             style="width: {{ ($products->currentPage() / $products->lastPage()) * 100 }}%"></div>
                    </div>
                </div>
                
                <!-- Next Button -->
                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" 
                       class="pagination-btn flex items-center justify-center w-12 h-12 rounded-full bg-white border border-gray-200 text-primary-600 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <div class="pagination-btn flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Enhanced Product Info -->
            <div class="text-center mt-4 space-y-1">
                <span class="text-sm font-medium text-gray-700">
                    {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ number_format($products->total()) }} produk
                </span>
                @if(request()->search)
                    <div class="text-xs text-gray-500">
                        Hasil pencarian untuk: "<span class="font-medium text-primary-600">{{ request()->search }}</span>"
                    </div>
                @endif
                @if(request()->category && isset($category))
                    <div class="text-xs text-gray-500">
                        Kategori: <span class="font-medium text-primary-600">{{ $category->name }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Enhanced Back to Top Button -->
    <button 
        id="backToTopBtn"
        class="fixed bottom-20 right-4 w-12 h-12 flex items-center justify-center rounded-full bg-primary-600 text-white shadow-xl opacity-0 transition-all duration-300 z-50 hover:bg-primary-700 hover:scale-110 active:scale-95"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        aria-label="Kembali ke atas"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    <!-- Floating Action Menu (optional) -->
    <div id="floatingMenu" class="fixed bottom-4 left-4 z-40">
        <div class="relative">
            <!-- Filter Toggle Button -->
            <button 
                id="filterToggle"
                class="w-12 h-12 bg-white border border-gray-200 rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105"
                onclick="toggleFilterMenu()"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>
            
            <!-- Filter Options (initially hidden) -->
            <div id="filterOptions" class="absolute bottom-16 left-0 bg-white rounded-lg shadow-xl border border-gray-200 p-3 opacity-0 pointer-events-none transition-all duration-200 min-w-[200px]">
                <div class="text-xs font-semibold text-gray-900 mb-2">Quick Actions</div>
                <div class="space-y-2">
                    <a href="{{ route('catalog.mobile') }}" class="flex items-center px-2 py-1 text-sm text-gray-700 hover:bg-gray-50 rounded">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Semua Filter
                    </a>
                    <button onclick="window.scrollTo({top: document.querySelector('.scrollbar-hide').offsetTop, behavior: 'smooth'})" class="w-full text-left flex items-center px-2 py-1 text-sm text-gray-700 hover:bg-gray-50 rounded">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Pilih Kategori
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Enhanced scrollbar hiding untuk category slider */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Enhanced banner styling with better transitions */
    .banner-container {
        background-color: #f3f4f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .banner-item {
        opacity: 0;
        transform: scale(1.05);
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }
    
    .banner-item.active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }

    /* Product image loading animation */
    img {
        transition: opacity 0.3s ease, transform 0.3s ease;
        opacity: 0;
    }

    img.loaded {
        opacity: 1;
    }

    /* Enhanced hover and touch effects */
    .group:hover .group-hover\:scale-105 {
        transform: scale(1.05);
    }

    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }

    /* Mobile-optimized touch targets */
    input, button, a {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    /* Enhanced pagination styling */
    .pagination-btn {
        min-height: 48px;
        min-width: 48px;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        user-select: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pagination-btn:active {
        transform: scale(0.95);
    }

    /* Improved loading skeleton */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Enhanced category scroll indicators */
    .category-scroll-indicator {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
        transition: all 0.2s ease;
    }

    .category-scroll-indicator:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .category-scroll-indicator.left {
        left: 8px;
    }

    .category-scroll-indicator.right {
        right: 8px;
    }

    /* Floating menu animations */
    #filterOptions.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    #filterOptions {
        transform: translateY(10px);
    }

    /* Enhanced focus styles for accessibility */
    a:focus-visible, button:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 0.375rem;
    }

    /* Safe area adjustments for modern mobile browsers */
    @supports (padding: max(0px)) {
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
        #backToTopBtn {
            bottom: max(5rem, calc(1rem + env(safe-area-inset-bottom)));
        }
        #floatingMenu {
            bottom: max(1rem, env(safe-area-inset-bottom));
        }
    }

    /* Prevent zoom on input focus (iOS Safari) */
    @media screen and (-webkit-min-device-pixel-ratio: 0) {
        input[type="search"] {
            font-size: 16px;
        }
    }

    /* Enhanced ripple effect */
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(59, 130, 246, 0.3);
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

    /* Improved scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Category item active state enhancement */
    .category-item.active {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .category-item.active svg {
        color: white;
    }

    /* Product card enhanced hover state */
    .product-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Loading states */
    .btn-loading {
        position: relative;
        color: transparent;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Dark mode considerations (if needed in future) */
    @media (prefers-color-scheme: dark) {
        .dark-mode-support {
            /* Reserved for dark mode styles */
        }
    }

    /* Reduce motion for users who prefer it */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        
        html {
            scroll-behavior: auto;
        }
    }

    /* Enhanced touch feedback */
    @media (hover: none) and (pointer: coarse) {
        .touch-feedback:active {
            background-color: rgba(59, 130, 246, 0.1);
            transform: scale(0.98);
        }
    }

    /* Improved category scroll snap */
    .category-container {
        scroll-snap-type: x mandatory;
        scroll-padding: 1rem;
    }

    .category-item {
        scroll-snap-align: start;
    }

    /* Better visual hierarchy for mobile */
    @media (max-width: 640px) {
        .mobile-optimized {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .mobile-tap-target {
            min-height: 44px;
            min-width: 44px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced banner rotation for mobile
    const banners = document.querySelectorAll('.banner-item');
    if (banners.length > 1) {
        let currentIndex = 0;
        let interval;
        const DISPLAY_TIME = 4000; // 4 seconds per banner for mobile
        let isPaused = false;
        
        function showNextBanner() {
            if (isPaused) return;
            
            banners[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % banners.length;
            banners[currentIndex].classList.add('active');
        }
        
        function startRotation() {
            interval = setInterval(showNextBanner, DISPLAY_TIME);
        }
        
        function pauseRotation() {
            isPaused = true;
            clearInterval(interval);
        }
        
        function resumeRotation() {
            isPaused = false;
            startRotation();
        }
        
        startRotation();
        
        // Pause on touch/interaction
        const bannerContainer = document.querySelector('.banner-container');
        if (bannerContainer) {
            bannerContainer.addEventListener('touchstart', pauseRotation);
            bannerContainer.addEventListener('touchend', () => {
                setTimeout(resumeRotation, 2000); // Resume after 2 seconds
            });
        }
    }

    // Enhanced back to top button with scroll progress
    const backToTopBtn = document.getElementById('backToTopBtn');
    let ticking = false;
    
    function updateScrollProgress() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrollTop / scrollHeight) * 100;
        
        // Show/hide button
        if (scrollTop > 300) {
            backToTopBtn.classList.replace('opacity-0', 'opacity-100');
        } else {
            backToTopBtn.classList.replace('opacity-100', 'opacity-0');
        }
        
        // Update progress ring (if we want to add one)
        // backToTopBtn.style.setProperty('--progress', progress + '%');
        
        ticking = false;
    }
    
    function requestScrollUpdate() {
        if (!ticking) {
            requestAnimationFrame(updateScrollProgress);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestScrollUpdate, { passive: true });    // Enhanced category scroll with indicators
    const categoryContainer = document.querySelector('.scrollbar-hide');
    if (categoryContainer) {
        let isScrolling = false;
        let scrollTimeout;
        
        // Add scroll indicators
        function updateScrollIndicators() {
            const container = categoryContainer;
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;
            
            // You can add visual indicators here
            // console.log('Scroll progress:', (scrollLeft / maxScroll) * 100 + '%');
        }
        
        categoryContainer.addEventListener('scroll', function() {
            isScrolling = true;
            clearTimeout(scrollTimeout);
            
            scrollTimeout = setTimeout(() => {
                isScrolling = false;
                updateScrollIndicators();
            }, 150);
        }, { passive: true });
        
        // Smooth scroll for category navigation
        const categoryItems = categoryContainer.querySelectorAll('a');
        categoryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Add active state animation
                this.classList.add('scale-95');
                setTimeout(() => {
                    this.classList.remove('scale-95');
                }, 150);
            });
        });
    }

    // Enhanced image loading with progressive enhancement
    const productImages = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const parent = img.parentElement;
                
                // Add loading skeleton
                if (!img.complete) {
                    parent.classList.add('loading-skeleton');
                }
                
                img.addEventListener('load', function() {
                    parent.classList.remove('loading-skeleton');
                    this.classList.add('loaded');
                    observer.unobserve(this);
                });
                
                img.addEventListener('error', function() {
                    parent.classList.remove('loading-skeleton');
                    const fallback = parent.querySelector('.fallback-placeholder');
                    if (fallback) {
                        fallback.classList.remove('hidden');
                    }
                    this.style.display = 'none';
                    observer.unobserve(this);
                });
            }
        });
    }, {
        rootMargin: '50px'
    });
    
    productImages.forEach(img => imageObserver.observe(img));

    // Enhanced search functionality with loading states
    const searchForm = document.querySelector('form[action*="catalog"]');
    const searchInput = document.querySelector('input[name="search"]');
    
    if (searchForm && searchInput) {
        let searchTimeout;
        
        // Add loading state to search
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
            }
        });

        // Debounced search with visual feedback
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Visual feedback
            this.style.borderColor = '#f59e0b';
            
            searchTimeout = setTimeout(() => {
                this.style.borderColor = '';
                if (this.value.length > 2 || this.value.length === 0) {
                    searchForm.requestSubmit();
                }
            }, 600);
        });
    }

    // Floating action menu functionality
    window.toggleFilterMenu = function() {
        const filterOptions = document.getElementById('filterOptions');
        const filterToggle = document.getElementById('filterToggle');
        
        if (filterOptions.classList.contains('show')) {
            filterOptions.classList.remove('show');
            filterToggle.style.transform = 'rotate(0deg)';
        } else {
            filterOptions.classList.add('show');
            filterToggle.style.transform = 'rotate(45deg)';
        }
    };
    
    // Close floating menu when clicking outside
    document.addEventListener('click', function(e) {
        const floatingMenu = document.getElementById('floatingMenu');
        const filterOptions = document.getElementById('filterOptions');
        
        if (floatingMenu && !floatingMenu.contains(e.target)) {
            filterOptions.classList.remove('show');
            document.getElementById('filterToggle').style.transform = 'rotate(0deg)';
        }
    });

    // Add ripple effect to interactive elements
    function createRipple(event) {
        if (event.target.closest('.no-ripple')) return;
        
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = diameter + 'px';
        circle.style.left = event.clientX - button.offsetLeft - radius + 'px';
        circle.style.top = event.clientY - button.offsetTop - radius + 'px';
        circle.classList.add('ripple');

        const ripple = button.querySelector('.ripple');
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
        
        setTimeout(() => {
            circle.remove();
        }, 600);
    }

    // Apply ripple effect to buttons and links
    const interactiveElements = document.querySelectorAll('a, button, .group');
    interactiveElements.forEach(element => {
        element.addEventListener('click', createRipple);
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
    });    // Enhanced pagination with keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            @if($products->hasPages())
            const currentPage = {{ $products->currentPage() }};
            const lastPage = {{ $products->lastPage() }};
            
            if (e.key === 'ArrowLeft' && currentPage > 1) {
                const prevLink = document.querySelector('a[href*="page=' + (currentPage - 1) + '"]');
                if (prevLink) {
                    e.preventDefault();
                    prevLink.click();
                }
            } else if (e.key === 'ArrowRight' && currentPage < lastPage) {
                const nextLink = document.querySelector('a[href*="page=' + (currentPage + 1) + '"]');
                if (nextLink) {
                    e.preventDefault();
                    nextLink.click();
                }
            }
            @endif
        }
        
        // ESC to clear search or close menus
        if (e.key === 'Escape') {
            const searchInput = document.querySelector('input[name="search"]');
            const filterOptions = document.getElementById('filterOptions');
            
            if (filterOptions && filterOptions.classList.contains('show')) {
                toggleFilterMenu();
            } else if (searchInput && searchInput.value) {
                searchInput.value = '';
                searchInput.form.requestSubmit();
            }
        }
    });

    // Touch gestures for category navigation
    if ('ontouchstart' in window) {
        const categoryScroll = document.querySelector('.scrollbar-hide');
        if (categoryScroll) {
            let startX, scrollLeft, isDown = false;

            categoryScroll.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - categoryScroll.offsetLeft;
                scrollLeft = categoryScroll.scrollLeft;
            }, { passive: true });

            categoryScroll.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.touches[0].pageX - categoryScroll.offsetLeft;
                const walk = (x - startX) * 2;
                categoryScroll.scrollLeft = scrollLeft - walk;
            });

            categoryScroll.addEventListener('touchend', () => {
                isDown = false;
            });
        }
    }

    // Intersection Observer for fade-in animations
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                fadeObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe product cards for fade-in animation
    const productCards = document.querySelectorAll('.grid > div');
    productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.05}s, transform 0.6s ease ${index * 0.05}s`;
        fadeObserver.observe(card);
    });    // Performance monitoring (optional)
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const loadTime = performance.now();
            // console.log('Page loaded in:', Math.round(loadTime), 'ms');
        });
    }    // Service Worker registration for offline support (if available)
    if ('serviceWorker' in navigator && 'serviceWorker' in window) {
        window.addEventListener('load', () => {
            // Service worker implementation would go here
            // navigator.serviceWorker.register('/sw.js')
        });
    }
});
</script>
@endpush
