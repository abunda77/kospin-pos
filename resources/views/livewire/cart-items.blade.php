<div>
    @if(count($cart) > 0)
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-8">
            <!-- Daftar Produk -->
            <div class="p-4 bg-white rounded-lg shadow-md md:col-span-2 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Subtotal</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cart as $id => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <img class="w-10 h-10 rounded-full" src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button wire:click="decrementQuantity({{ $id }})" class="text-gray-500 hover:text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="text-sm text-gray-900">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementQuantity({{ $id }})" class="text-gray-500 hover:text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-500 whitespace-nowrap">
                                    Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-500 whitespace-nowrap">
                                    Rp {{ number_format($item['quantity'] * $item['unit_price'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <button wire:click="removeItem({{ $id }})" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            {{-- <div class="p-4 bg-white rounded-lg shadow-md md:p-6">
                <h2 class="mb-4 text-lg font-semibold md:text-xl">Metode Pembayaran</h2>
                <form action="{{ route('checkout') }}" method="GET">
                    <div class="space-y-3 md:space-y-4">
                        @foreach($paymentMethods as $method)
                        <label class="flex items-center p-3 rounded-lg border cursor-pointer md:p-4 hover:bg-gray-50">
                            <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                   class="w-4 h-4 text-blue-600 form-radio" required>
                            <div class="flex items-center ml-3 md:ml-4">
                                <span class="text-sm font-medium md:text-base">{{ $method->name }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-6 md:mt-8">
                        <button type="submit"
                                class="px-4 py-2 w-full text-sm text-white bg-green-500 rounded hover:bg-green-600 md:text-base">
                            Lanjut ke Pembayaran
                        </button>
                    </div>
                </form>
            </div> --}}
        </div>

        <div class="mt-8">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex flex-col space-y-4">
                    <!-- Subtotal -->
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center pt-4 border-t">
                        <span class="text-lg font-semibold">Total</span>
                        <span class="text-lg font-bold text-blue-600">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="py-6 text-center md:py-8">
            <p class="text-sm text-gray-600 md:text-base">Keranjang belanja Anda kosong</p>
            <a href="{{ route('catalog') }}" class="text-sm text-blue-500 hover:underline md:text-base">Kembali ke Katalog</a>
        </div>
    @endif
</div>
