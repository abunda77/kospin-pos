@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Katalog Produk</h1>
        <a href="{{ route('catalog.download-pdf') }}" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
            Download Catalog PDF
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h2 class="text-xl font-semibold mb-2">{{ $product->name }}</h2>
                <p class="text-gray-600 mb-2">{{ $product->category->name }}</p>
                <p class="text-gray-800 font-bold mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="text-gray-600 mb-2">Stok : {{ $product->stock }} </p>
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full {{ $product->stock > 0 ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed' }} text-white py-2 px-4 rounded"
                        {{ $product->stock == 0 ? 'disabled' : '' }}>
                        Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
