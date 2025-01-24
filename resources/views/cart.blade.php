@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-8">Keranjang Belanja</h1>

    @if(count($cart) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8">
            <!-- Daftar Produk -->
            <div class="md:col-span-2 bg-white rounded-lg shadow-md p-4 md:p-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm md:text-base">Produk</th>
                            <th class="text-center py-2 text-sm md:text-base">Jumlah</th>
                            <th class="text-right py-2 text-sm md:text-base">Harga</th>
                            <th class="text-right py-2 text-sm md:text-base">Subtotal</th>
                            <th class="text-right py-2 text-sm md:text-base">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                        <tr class="border-b">
                            <td class="py-2 md:py-4">
                                <div class="flex items-center">
                                    <img src="{{ $item['image'] }}" class="w-12 h-12 md:w-16 md:h-16 object-cover rounded">
                                    <span class="ml-2 md:ml-4 text-sm md:text-base">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center text-sm md:text-base">{{ $item['quantity'] }}</td>
                            <td class="text-right text-sm md:text-base">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                            <td class="text-right text-sm md:text-base">Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</td>
                            <td class="text-right">
                                <form action="{{ route('cart.delete', $id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold py-2 md:py-4 text-sm md:text-base">Total:</td>
                            <td class="text-right font-bold text-sm md:text-base">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-semibold mb-4">Metode Pembayaran</h2>
                <form action="{{ route('checkout') }}" method="GET">
                    <div class="space-y-3 md:space-y-4">
                        @foreach($paymentMethods as $method)
                        <label class="flex items-center p-3 md:p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                   class="form-radio h-4 w-4 text-blue-600" required>
                            <div class="ml-3 md:ml-4 flex items-center">
                                <span class="font-medium text-sm md:text-base">{{ $method->name }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-6 md:mt-8">
                        <button type="submit"
                                class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 text-sm md:text-base">
                            Lanjut ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="text-center py-6 md:py-8">
            <p class="text-gray-600 text-sm md:text-base">Keranjang belanja Anda kosong</p>
            <a href="{{ route('catalog') }}" class="text-blue-500 hover:underline text-sm md:text-base">Kembali ke Katalog</a>
        </div>
    @endif
</div>
@endsection
