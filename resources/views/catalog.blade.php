@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold sm:text-3xl">Katalog Produk</h1>
        <a href="{{ route('catalog.download-pdf') }}" class="px-4 py-2 text-sm text-white bg-green-500 rounded hover:bg-green-600 sm:text-base">
            Download Catalog PDF
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($products as $product)
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-48">
            <div class="p-4">
                <h2 class="mb-2 text-xl font-semibold">{{ $product->name }}</h2>
                <p class="mb-2 text-gray-600">{{ $product->category->name }}</p>
                <p class="mb-4 font-bold text-gray-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="mb-2 text-gray-600">Stok : {{ $product->stock }} </p>
                <livewire:add-to-cart :product="$product" :wire:key="$product->id" />
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
