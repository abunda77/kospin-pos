<div>
    @if(count($cart) > 0)
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-8">
            <!-- Daftar Produk -->
            <div class="p-4 bg-white rounded-lg shadow-md md:col-span-2 md:p-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-sm text-left md:text-base">Produk</th>
                            <th class="py-2 text-sm text-center md:text-base">Jumlah</th>
                            <th class="py-2 text-sm text-right md:text-base">Harga</th>
                            <th class="py-2 text-sm text-right md:text-base">Subtotal</th>
                            <th class="py-2 text-sm text-right md:text-base">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                        <tr class="border-b">
                            <td class="py-2 md:py-4">
                                <div class="flex items-center">
                                    <img src="{{ $item['image'] }}" class="object-cover w-12 h-12 rounded md:w-16 md:h-16">
                                    <span class="ml-2 text-sm md:ml-4 md:text-base">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <button wire:click="decrementQuantity({{ $id }})"
                                            class="px-2 py-1 text-gray-600 hover:text-gray-800">
                                        -
                                    </button>
                                    <span class="text-sm md:text-base">{{ $item['quantity'] }}</span>
                                    <button wire:click="incrementQuantity({{ $id }})"
                                            class="px-2 py-1 text-gray-600 hover:text-gray-800">
                                        +
                                    </button>
                                </div>
                            </td>
                            <td class="text-sm text-right md:text-base">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                            <td class="text-sm text-right md:text-base">Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</td>
                            <td class="text-right">
                                <button wire:click="removeItem({{ $id }})" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-2 text-sm font-bold text-right md:py-4 md:text-base">Total:</td>
                            <td class="text-sm font-bold text-right md:text-base">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Metode Pembayaran -->
            <div class="p-4 bg-white rounded-lg shadow-md md:p-6">
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
            </div>
        </div>
    @else
        <div class="py-6 text-center md:py-8">
            <p class="text-sm text-gray-600 md:text-base">Keranjang belanja Anda kosong</p>
            <a href="{{ route('catalog') }}" class="text-sm text-blue-500 hover:underline md:text-base">Kembali ke Katalog</a>
        </div>
    @endif
</div>
