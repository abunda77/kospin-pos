<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <div class="hidden p-6 bg-white rounded-lg shadow-md md:col-span-1 dark:bg-gray-800 md:block">
        <button wire:click="resetOrder" class="py-2 mt-2 mb-4 w-full h-12 text-white bg-red-500 rounded-lg">Reset</button>
        @foreach($order_items as $item)
        <div class="mb-4">
            <div class="flex justify-between items-center p-4 bg-gray-100 rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center">
                    <img src="{{$item['image_url']}}" alt="Product Image"
                    class="object-cover mr-2 w-10 h-10 rounded-lg">
                    <div class="px-2">
                        <h3 class="text-sm font-semibold">{{$item['name']}}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp {{number_format($item['price'], 0, ',', '.')}}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <x-filament::button color="warning" wire:click="decreaseQuantity({{$item['product_id']}})">-</x-filament::button>
                    <input
                        type="number"
                        wire:model.blur="order_items.{{array_search($item, $order_items)}}.quantity"
                        min="1"
                        class="px-2 py-1 mx-2 w-16 text-center text-gray-900 bg-white rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                    <x-filament::button color="success" wire:click="increaseQuantity({{$item['product_id']}})">+</x-filament::button>
                </div>
            </div>
        </div>
        @endforeach
        @if(count($order_items) > 0)
        <div class="py-4 bg-gray-50 border-t border-gray-100 dark:bg-gray-700">
            <h3 class="text-lg font-semibold text-center">Subtotal: Rp {{number_format($this->calculateTotal() * 100 / (100 - $discount), 0, ',', '.')}}</h3>
            @if($discount > 0)
                <h3 class="text-center text-gray-600 text-md">Diskon ({{$discount}}%): Rp {{number_format(($this->calculateTotal() * 100 / (100 - $discount)) * $discount / 100, 0, ',', '.')}}</h3>
            @endif
            <h3 class="text-xl font-bold text-center text-primary-600">Total: Rp {{number_format($this->calculateTotal(), 0, ',', '.')}}</h3>
        </div>
        @endif

        <div class="mt-2">

        </div>
    </div>
    <div class="p-6 bg-white rounded-lg shadow-md md:col-span-2 dark:bg-gray-800">
        <form wire:submit="checkout">
            <!-- Form Section -->
            <div class="mb-6 space-y-4">
                <!-- Anggota Section -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-1">
                        {{$this->form}}
                    </div>
                    <div class="flex col-span-1 justify-start">
                        <x-filament::button
                            type="button"
                            color="success"
                            wire:click="$set('showAnggotaModal', true)"
                            class="flex gap-2 items-center px-4 h-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </x-filament::button>

                    </div>
                </div>

                <!-- Checkout Button -->
                <x-filament::button
                    type="submit"
                    class="py-2 w-full h-12 text-white rounded-lg transition-colors duration-200 bg-primary hover:bg-primary-600">
                    Checkout
                </x-filament::button>
            </div>
        </form>

        <!-- Barcode Scanner Section -->
        <div class="flex gap-2 items-center mb-8">
            <div class="flex-1">
                <input
                    wire:model.live='barcode'
                    type="text"
                    placeholder="Scan Barcode..."
                    autofocus
                    id="barcode"
                    class="px-4 w-full h-12 placeholder-gray-400 text-gray-900 bg-white rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent dark:placeholder-gray-500">
            </div>
            <x-filament::button
                x-data=""
                x-on:click="$dispatch('toggle-scanner')"
                class="flex justify-center items-center w-12 h-12 transition-colors duration-200 bg-primary hover:bg-primary-600">
                <i class="text-2xl fa fa-barcode"></i>
            </x-filament::button>
            <livewire:scanner-modal-component>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @foreach($products as $item)
            <div wire:click="addToOrder({{$item->id}})"
                 class="p-4 bg-gray-100 rounded-lg shadow-sm transition-shadow duration-200 cursor-pointer dark:bg-gray-700 hover:shadow-md">
                <img src="{{$item->image_url}}"
                    alt="Product Image"
                    class="object-cover mb-3 w-full h-24 rounded-lg">
                <h3 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white">{{$item->name}}</h3>
                <p class="mb-1 text-sm text-gray-600 dark:text-gray-300">
                    Rp. {{number_format($item->price, 0, ',', '.')}}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400" wire:poll.5s>
                    Stok: {{$item->stock}}
                </p>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <div class="flex justify-center">
                <style>
                    /* Styling untuk pagination */
                    .pagination-container {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        gap: 0.5rem;
                        margin-top: 1rem;
                        background-color: white;
                        padding: 1rem;
                        border-radius: 0.5rem;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    }

                    .dark .pagination-container {
                        background-color: #1f2937;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
                    }

                    .pagination-item {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        min-width: 2.5rem;
                        height: 2.5rem;
                        padding: 0 0.75rem;
                        background-color: white;
                        color: #374151;
                        font-size: 0.875rem;
                        font-weight: 500;
                        border-radius: 0.375rem;
                        border: 1px solid #e5e7eb;
                        transition: all 200ms;
                        text-decoration: none;
                    }

                    .dark .pagination-item {
                        background-color: #374151;
                        color: #e5e7eb;
                        border: 1px solid #4b5563;
                    }

                    .pagination-item:hover {
                        background-color: #f3f4f6;
                        color: #6366f1;
                        border-color: #c7d2fe;
                    }

                    .dark .pagination-item:hover {
                        background-color: #4b5563;
                        color: #a5b4fc;
                        border-color: #6366f1;
                    }

                    .pagination-active {
                        background-color: #6366f1;
                        color: white;
                        border: 1px solid #6366f1;
                        font-weight: 600;
                    }

                    .dark .pagination-active {
                        background-color: #6366f1;
                        color: white;
                        border: 1px solid #818cf8;
                    }

                    .pagination-disabled {
                        color: #9ca3af;
                        background-color: #f3f4f6;
                        border: 1px solid #e5e7eb;
                        cursor: not-allowed;
                    }

                    .dark .pagination-disabled {
                        color: #6b7280;
                        background-color: #374151;
                        border: 1px solid #4b5563;
                    }
                </style>
                <div class="pagination-container">
                    <!-- Previous page -->
                    @if ($products->onFirstPage())
                        <span class="pagination-item pagination-disabled">«</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="pagination-item">«</a>
                    @endif

                    <!-- Numbered pages -->
                    @php
                        $start = max(1, $products->currentPage() - 2);
                        $end = min($start + 4, $products->lastPage());
                        if ($end < $products->lastPage() - 1) {
                            $end = min($end, $products->currentPage() + 2);
                        }
                        if ($start > 2) {
                            $start = max(1, $end - 4);
                        }
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $products->url(1) }}" class="pagination-item">1</a>
                        @if ($start > 2)
                            <span class="pagination-item pagination-disabled">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $products->currentPage())
                            <span class="pagination-item pagination-active">{{ $i }}</span>
                        @else
                            <a href="{{ $products->url($i) }}" class="pagination-item">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($end < $products->lastPage())
                        @if ($end < $products->lastPage() - 1)
                            <span class="pagination-item pagination-disabled">...</span>
                        @endif
                        <a href="{{ $products->url($products->lastPage()) }}" class="pagination-item">{{ $products->lastPage() }}</a>
                    @endif

                    <!-- Next page -->
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="pagination-item">»</a>
                    @else
                        <span class="pagination-item pagination-disabled">»</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="block p-6 bg-white rounded-lg shadow-md md:col-span-1 dark:bg-gray-800 md:hidden">
        <button wire:click="resetOrder" class="py-2 mt-2 mb-4 w-full h-12 text-white bg-red-500 rounded-lg">Reset</button>
        @foreach($order_items as $item)
        <div class="mb-4">
            <div class="flex justify-between items-center p-4 bg-gray-100 rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center">
                    <img src="{{$item['image_url']}}" alt="Product Image"
                    class="object-cover mr-2 w-10 h-10 rounded-lg">
                    <div class="px-2">
                        <h3 class="text-sm font-semibold">{{$item['name']}}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp {{number_format($item['price'], 0, ',', '.')}}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <x-filament::button color="warning" wire:click="decreaseQuantity({{$item['product_id']}})">-</x-filament::button>
                    <input
                        type="number"
                        wire:model.blur="order_items.{{array_search($item, $order_items)}}.quantity"
                        min="1"
                        class="px-2 py-1 mx-2 w-16 text-center text-gray-900 bg-white rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                    <x-filament::button color="success" wire:click="increaseQuantity({{$item['product_id']}})">+</x-filament::button>
                </div>
            </div>
        </div>
        @endforeach
        @if(count($order_items) > 0)
        <div class="py-4">
            <h3 class="text-lg font-semibold text-center">Subtotal: Rp {{number_format($this->calculateTotal() * 100 / (100 - $discount), 0, ',', '.')}}</h3>
            @if($discount > 0)
                <h3 class="text-center text-gray-600 text-md">Diskon ({{$discount}}%): Rp {{number_format(($this->calculateTotal() * 100 / (100 - $discount)) * $discount / 100, 0, ',', '.')}}</h3>
            @endif
            <h3 class="text-xl font-bold text-center text-primary-600">Total: Rp {{number_format($this->calculateTotal(), 0, ',', '.')}}</h3>
        </div>
        @endif

        <div class="mt-2">

        </div>
    </div>
    <div>
        @if ($showConfirmationModal)
        <div class="flex fixed inset-0 z-50 justify-center items-center bg-gray-800 bg-opacity-50">
            <!-- Modal Content -->
            <div class="w-11/12 bg-white rounded-lg shadow-lg sm:w-96">
                <!-- Modal Header -->
                <div class="px-6 py-4 text-white bg-purple-500 rounded-t-lg">
                    <h2 class="text-xl font-semibold text-center">PRINT STRUK</h2>
                </div>
                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <p class="text-gray-800">
                        Apakah Anda ingin mencetak struk untuk pesanan ini?
                    </p>
                </div>
                <!-- Modal Footer -->
                <div class="flex justify-center px-6 py-4 space-x-4">
                    <button
                        wire:click="$set('showConfirmationModal', false)"
                        class="px-4 py-2 text-gray-700 bg-gray-300 rounded-full hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">
                        Tidak
                    </button>
                    @if ($print_via_mobile == true)
                    <button
                        wire:click="confirmPrint2"
                        class="px-4 py-2 text-white bg-purple-500 rounded-full hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
                        Cetak
                    </button>
                    @else
                    <button
                        wire:click="confirmPrint1"
                        class="px-4 py-2 text-white bg-purple-500 rounded-full hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
                        Cetak
                    </button>

                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal Tambah Anggota -->
    @if($showAnggotaModal)
    <div class="flex fixed inset-0 z-50 justify-center items-center bg-gray-800/50 dark:bg-gray-950/75">
        <div class="w-11/12 bg-white rounded-lg shadow-lg dark:bg-gray-800 sm:w-96">
            <div class="px-6 py-4 text-white bg-purple-500 rounded-t-lg dark:bg-purple-600">
                <h2 class="text-xl font-semibold text-center">Tambah Anggota Baru</h2>
            </div>
            <div class="px-6 py-4">
                <form wire:submit.prevent="addAnggota" x-data="{ closeModal() { $wire.set('showAnggotaModal', false) } }" @anggota-added.window="closeModal()">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-white">
                            Nama Lengkap
                        </label>
                        <input type="text"
                            wire:model="newAnggota.nama_lengkap"
                            class="px-3 py-2 w-full placeholder-gray-400 text-gray-900 bg-white rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 dark:placeholder-gray-300"
                            required>
                        @error('newAnggota.nama_lengkap')
                            <span class="text-xs text-red-500 dark:text-red-300">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-white">
                            NIK
                        </label>
                        <input type="text"
                            wire:model="newAnggota.nik"
                            class="px-3 py-2 w-full placeholder-gray-400 text-gray-900 bg-white rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 dark:placeholder-gray-300"
                            required>
                        @error('newAnggota.nik')
                            <span class="text-xs text-red-500 dark:text-red-300">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button"
                            wire:click="$set('showAnggotaModal', false)"
                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded-lg transition duration-150 dark:bg-gray-600 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-500">
                            Tutup
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-white bg-purple-500 rounded-lg transition duration-150 dark:bg-purple-600 hover:bg-purple-600 dark:hover:bg-purple-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

