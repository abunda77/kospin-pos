<div>
    <!-- Search Section -->
    <div class="mb-8">
        <div class="max-w-3xl mx-auto">
            <div class="relative" x-data="{ isSearching: false }">
                <!-- Search Icon -->
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                
                <!-- Search Input -->
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari produk yang Anda inginkan..." 
                    class="w-full py-4 pl-12 pr-32 text-base text-gray-900 bg-white border border-gray-200 rounded-full focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                    x-on:input="isSearching = true"
                    x-on:input.debounce.300ms="isSearching = false"
                >
                
                <!-- Loading Indicator -->
                <div class="absolute inset-y-0 right-24 flex items-center" x-show="isSearching">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Clear Button -->
                @if($search)
                <div class="absolute inset-y-0 right-2 flex items-center">
                    <button 
                        wire:click="resetSearch"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                    >
                        <span>Reset</span>
                    </button>
                </div>
                @endif
            </div>

            <!-- Search Status -->
            @if($search)
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    Menampilkan hasil pencarian untuk: 
                    <span class="font-medium text-gray-900">"{{ $search }}"</span>
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <img src="{{ $product->image_url }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <div class="mb-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full text-primary-600 bg-primary-50">
                            {{ $product->category->name }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-xl font-bold text-gray-900 mb-3">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center mb-4">
                        <span class="text-sm text-gray-600">Stok:</span>
                        <span class="ml-2 px-2 py-1 text-xs font-medium {{ $product->stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} rounded-full">
                            {{ $product->stock }}
                        </span>
                    </div>
                    <livewire:add-to-cart :product="$product" :wire:key="$product->id" />
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Tidak ada produk yang sesuai dengan pencarian Anda.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>