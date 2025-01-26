@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto min-h-[calc(100vh-4rem-20rem)]">
    <h1 class="mb-8 text-2xl font-bold md:text-3xl">Keranjang Belanja</h1>
    <livewire:cart-items />
</div>
@endsection
