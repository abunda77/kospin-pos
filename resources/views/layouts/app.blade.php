<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Styles -->
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="container px-4 mx-auto">
            <div class="flex justify-between items-center h-16">
                <div>
                    <a href="{{ route('catalog') }}">
                        <img src="{{ asset('images/logo_sinaraartha.png') }}" alt="Logo Sinar Artha" class="h-12">
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('catalog') }}" class="hidden text-gray-700 sm:block hover:text-gray-900">Beranda</a>
                    <a href="{{ route('about') }}" class="hidden text-gray-700 sm:block hover:text-gray-900">Tentang Kami</a>
                    <a href="{{ route('contact') }}" class="hidden text-gray-700 sm:block hover:text-gray-900">Kontak</a>
                    <a href="{{ route('catalog.download-pdf') }}" class="px-3 py-1 text-sm text-center text-white bg-green-500 rounded-lg transition-colors duration-300 hover:bg-green-600">
                        Download Katalog PDF
                    </a>
                    <a href="{{ route('cart') }}" class="inline-flex relative items-center text-gray-700 hover:text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <livewire:cart-counter />
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="container px-4 mx-auto">
            @if(session('success'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 rounded border border-green-400" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="py-8 mt-auto text-white bg-gray-800 shadow-lg">
        <div class="container px-4 mx-auto">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="text-center md:text-left">
                    <h3 class="mb-4 text-lg font-semibold text-green-400">Tentang Kami</h3>
                    <p class="text-gray-300">
                        Koperasi Serba Usaha yang terpercaya melayani kebutuhan anggota sejak tahun 2021.
                    </p>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="mb-4 text-lg font-semibold text-green-400">Alamat Kantor</h3>
                    <p class="text-gray-300">
                        Eastern Park Residence Blok B No. 7,<br>
                        Sukolilo, Surabaya 60111
                    </p>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="mb-4 text-lg font-semibold text-green-400">Hubungi Kami</h3>
                    <p class="text-gray-300">
                        Email: <a href="mailto:cs@kospinsinaraartha.co.id" class="text-green-400 hover:text-green-300">cs@kospinsinaraartha.co.id</a><br>
                        Telepon: (+62) 87778715788<br>
                        WhatsApp: +62 87778715788
                    </p>
                </div>

                <div class="text-center md:text-left">
                    <h3 class="mb-4 text-lg font-semibold text-green-400">Jam Operasional</h3>
                    <p class="text-gray-300">
                        Senin - Jumat: 08.00 - 17.00<br>
                        Sabtu: 08.00 - 52.00<br>
                        Minggu / Hari Libur: Tutup
                    </p>
                </div>
            </div>

            <div class="pt-6 mt-6 text-center border-t border-gray-700">
                <p class="text-gray-400">&copy; {{ date('Y') }} Sinara Artha. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Content -->
    @livewireScripts
    @stack('scripts')

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        console.log('Layout script loaded');

        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('notify', function(event) {
                Swal.fire({
                    title: event.detail.type === 'success' ? 'Berhasil!' : 'Error!',
                    text: event.detail.message,
                    icon: event.detail.type,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            window.addEventListener('cart-updated', function() {
                // Reload halaman untuk memperbarui tampilan cart
                window.location.reload();
            });
        });
    </script>
</body>
</html>
