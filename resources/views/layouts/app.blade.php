<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div>
                    <a href="{{ route('catalog') }}">
                        <img src="{{ asset('images/logo_sinaraartha.png') }}" alt="Logo Sinar Aartha" class="h-12">
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('catalog') }}" class="text-gray-700 hover:text-gray-900">Katalog</a>
                    <a href="{{ route('cart') }}" class="text-gray-700 hover:text-gray-900 relative inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if(session()->has('cart') && count(session('cart')) > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-white mt-12 py-6">
        <div class="container mx-auto px-4 text-center text-gray-600">
            <p>&copy; {{ date('Y') }} Sinara Artha. All rights reserved.</p>
        </div>
    </footer>

    <!-- Content -->
    @livewireScripts
    @stack('scripts')
    <script>
        console.log('Layout script loaded');
    </script>
</body>
</html>
