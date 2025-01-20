<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 hidden md:block">
        <button wire:click="resetOrder" class="w-full h-12 bg-red-500 mt-2 text-white py-2 rounded-lg mb-4">Reset</button>
        @foreach($order_items as $item)
        <div class="mb-4 ">
            <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <img src="{{$item['image_url']}}" alt="Product Image"
                    class="w-10 h-10 object-cover rounded-lg mr-2">
                    <div class="px-2">
                        <h3 class="text-sm font-semibold">{{$item['name']}}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">Rp {{number_format($item['price'], 0, ',', '.')}}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <x-filament::button color="warning" wire:click="decreaseQuantity({{$item['product_id']}})">-</x-filament::button>
                    <span class="px-4">{{$item['quantity']}}</span>
                    <x-filament::button color="success" wire:click="increaseQuantity({{$item['product_id']}})">+</x-filament::button>
                </div>
            </div>
        </div>
        @endforeach
        @if(count($order_items) > 0)
        <div class="py-4 border-t border-gray-100 bg-gray-50 dark:bg-gray-700 ">
            <h3 class="text-lg font-semibold text-center">Subtotal: Rp {{number_format($this->calculateTotal() * 100 / (100 - $discount), 0, ',', '.')}}</h3>
            @if($discount > 0)
                <h3 class="text-md text-center text-gray-600">Diskon ({{$discount}}%): Rp {{number_format(($this->calculateTotal() * 100 / (100 - $discount)) * $discount / 100, 0, ',', '.')}}</h3>
            @endif
            <h3 class="text-xl font-bold text-center text-primary-600">Total: Rp {{number_format($this->calculateTotal(), 0, ',', '.')}}</h3>
        </div>
        @endif

        <div class="mt-2">

        </div>
    </div>
    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form wire:submit="checkout">
            <!-- Form Section -->
            <div class="space-y-4 mb-6">
                <!-- Anggota Section -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-1">
                        {{$this->form}}
                    </div>
                    <div class="col-span-1 flex justify-start">
                        <x-filament::button
                            type="button"
                            color="success"
                            wire:click="$set('showAnggotaModal', true)"
                            class="h-10 px-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </x-filament::button>

                    </div>
                </div>

                <!-- Checkout Button -->
                <x-filament::button
                    type="submit"
                    class="w-full h-12 bg-primary text-white py-2 rounded-lg
                           hover:bg-primary-600 transition-colors duration-200">
                    Checkout
                </x-filament::button>
            </div>
        </form>

        <!-- Barcode Scanner Section -->
        <div class="flex items-center gap-2 mb-8">
            <div class="flex-1">
                <input
                    wire:model.live='barcode'
                    type="text"
                    placeholder="Scan Barcode..."
                    autofocus
                    id="barcode"
                    class="w-full h-12 px-4 border border-gray-300 dark:border-gray-600
                           rounded-lg bg-white dark:bg-gray-900
                           text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-primary focus:border-transparent
                           placeholder-gray-400 dark:placeholder-gray-500">
            </div>
            <x-filament::button
                x-data=""
                x-on:click="$dispatch('toggle-scanner')"
                class="h-12 w-12 flex items-center justify-center bg-primary hover:bg-primary-600
                       transition-colors duration-200">
                <i class="fa fa-barcode text-2xl"></i>
            </x-filament::button>
            <livewire:scanner-modal-component>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($products as $item)
            <div wire:click="addToOrder({{$item->id}})"
                 class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm
                        hover:shadow-md transition-shadow duration-200 cursor-pointer">
                <img src="{{$item->image_url}}"
                    alt="Product Image"
                    class="w-full h-24 object-cover rounded-lg mb-3">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">{{$item->name}}</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-1">
                    Rp. {{number_format($item->price, 0, ',', '.')}}
                </p>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Stok: {{$item->stock}}
                </p>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 block md:hidden">
        <button wire:click="resetOrder" class="w-full h-12 bg-red-500 mt-2 text-white py-2 rounded-lg mb-4 ">Reset</button>
        @foreach($order_items as $item)
        <div class="mb-4 ">
            <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <img src="{{$item['image_url']}}" alt="Product Image"
                    class="w-10 h-10 object-cover rounded-lg mr-2">
                    <div class="px-2">
                        <h3 class="text-sm font-semibold">{{$item['name']}}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">Rp {{number_format($item['price'], 0, ',', '.')}}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <x-filament::button color="warning" wire:click="decreaseQuantity({{$item['product_id']}})">-</x-filament::button>
                    <span class="px-4">{{$item['quantity']}}</span>
                    <x-filament::button color="success" wire:click="increaseQuantity({{$item['product_id']}})">+</x-filament::button>
                </div>
            </div>
        </div>
        @endforeach
        @if(count($order_items) > 0)
        <div class="py-4 ">
            <h3 class="text-lg font-semibold text-center">Subtotal: Rp {{number_format($this->calculateTotal() * 100 / (100 - $discount), 0, ',', '.')}}</h3>
            @if($discount > 0)
                <h3 class="text-md text-center text-gray-600">Diskon ({{$discount}}%): Rp {{number_format(($this->calculateTotal() * 100 / (100 - $discount)) * $discount / 100, 0, ',', '.')}}</h3>
            @endif
            <h3 class="text-xl font-bold text-center text-primary-600">Total: Rp {{number_format($this->calculateTotal(), 0, ',', '.')}}</h3>
        </div>
        @endif

        <div class="mt-2">

        </div>
    </div>
    <div>
        @if ($showConfirmationModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <!-- Modal Content -->
            <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-96">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-purple-500 text-white rounded-t-lg">
                    <h2 class="text-xl text-center font-semibold">PRINT STRUK</h2>
                </div>
                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <p class="text-gray-800">
                        Apakah Anda ingin mencetak struk untuk pesanan ini?
                    </p>
                </div>
                <!-- Modal Footer -->
                <div class="px-6 py-4 flex justify-center space-x-4">
                    <button
                        wire:click="$set('showConfirmationModal', false)"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">
                        Tidak
                    </button>
                    @if ($print_via_mobile == true)
                    <button
                        wire:click="confirmPrint2"
                        class="px-4 py-2 bg-purple-500 text-white rounded-full hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
                        Cetak
                    </button>
                    @else
                    <button
                        wire:click="confirmPrint1"
                        class="px-4 py-2 bg-purple-500 text-white rounded-full hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
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
    <div class="fixed inset-0 bg-gray-800/50 dark:bg-gray-950/75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-11/12 sm:w-96">
            <div class="px-6 py-4 bg-purple-500 dark:bg-purple-600 text-white rounded-t-lg">
                <h2 class="text-xl text-center font-semibold">Tambah Anggota Baru</h2>
            </div>
            <div class="px-6 py-4">
                <form wire:submit.prevent="addAnggota" x-data="{ closeModal() { $wire.set('showAnggotaModal', false) } }" @anggota-added.window="closeModal()">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-white text-sm font-bold mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text"
                            wire:model="newAnggota.nama_lengkap"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                            bg-white dark:bg-gray-700
                            text-gray-900 dark:text-white
                            focus:outline-none focus:ring-2 focus:ring-purple-500
                            placeholder-gray-400 dark:placeholder-gray-300"
                            required>
                        @error('newAnggota.nama_lengkap')
                            <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-white text-sm font-bold mb-2">
                            NIK
                        </label>
                        <input type="text"
                            wire:model="newAnggota.nik"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                            bg-white dark:bg-gray-700
                            text-gray-900 dark:text-white
                            focus:outline-none focus:ring-2 focus:ring-purple-500
                            placeholder-gray-400 dark:placeholder-gray-300"
                            required>
                        @error('newAnggota.nik')
                            <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button"
                            wire:click="$set('showAnggotaModal', false)"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600
                            text-gray-700 dark:text-white
                            rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500
                            transition duration-150">
                            Tutup
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-500 dark:bg-purple-600
                            text-white rounded-lg
                            hover:bg-purple-600 dark:hover:bg-purple-700
                            transition duration-150">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

