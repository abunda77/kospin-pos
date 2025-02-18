<div>
    <!-- Products Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @forelse($products as $product)
        <div class="overflow-hidden bg-white rounded-2xl shadow-lg transition-all duration-300 transform hover:shadow-xl group hover:-translate-y-2">
            <div class="overflow-hidden relative aspect-square">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         class="object-cover w-full h-full transition-transform duration-500 transform group-hover:scale-105">
                @else
                    <div class="flex justify-center items-center w-full h-full bg-gray-200">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <h2 class="mb-2 text-lg font-semibold text-gray-800 truncate">{{ $product->name }}</h2>
                <div class="flex items-center mb-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full text-primary-600 bg-primary-50">{{ $product->category->name }}</span>
                </div>
                <p class="mb-3 text-xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <div class="flex items-center mb-4">
                    <span class="text-sm text-gray-600">Stok:</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium {{ $product->stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} rounded-full">
                        {{ $product->stock }}
                    </span>
                </div>
                <div>
                    <livewire:add-to-cart :product="$product" :wire:key="'cart-'.$product->id" />
                </div>
            </div>
        </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada produk yang tersedia saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
