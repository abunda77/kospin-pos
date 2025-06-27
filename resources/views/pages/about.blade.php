@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header Section -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Tentang Kami</h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    <strong>Koperasi Sinar Artha </strong> adalah koperasi serba usaha yang telah melayani kebutuhan anggota dengan dedikasi tinggi sejak tahun 2021.
                    </p>
                </div>

                <!-- Company Info Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Sejarah Perusahaan</h2>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                Koperasi Sinar Artha didirikan pada tahun 2010 dengan visi menjadi koperasi terdepan dalam melayani kebutuhan anggota. 
                                Berawal dari sebuah koperasi kecil, kami telah berkembang menjadi koperasi serba usaha yang terpercaya.
                            </p>
                            <p>
                                Selama lebih dari satu dekade, kami telah konsisten memberikan pelayanan terbaik kepada anggota dengan 
                                menyediakan berbagai produk berkualitas dengan harga yang kompetitif.
                            </p>
                            <p>
                                Komitmen kami adalah terus berinovasi dan mengembangkan layanan untuk memenuhi kebutuhan anggota 
                                yang semakin beragam di era digital ini.
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Visi & Misi</h2>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-green-600 mb-2">Visi</h3>
                                <p class="text-gray-700">
                                    Menjadi koperasi serba usaha terdepan yang memberikan nilai tambah dan kesejahteraan 
                                    bagi seluruh anggota melalui pelayanan yang berkualitas dan inovatif.
                                </p>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-green-600 mb-2">Misi</h3>
                                <ul class="text-gray-700 space-y-2">
                                    <li>• Menyediakan produk dan layanan berkualitas tinggi dengan harga yang kompetitif</li>
                                    <li>• Memberikan pelayanan prima kepada seluruh anggota koperasi</li>
                                    <li>• Mengembangkan teknologi untuk meningkatkan efisiensi dan kemudahan akses</li>
                                    <li>• Memberdayakan anggota melalui program-program koperasi yang berkelanjutan</li>
                                    <li>• Berkontribusi positif terhadap perekonomian masyarakat sekitar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Values Section -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-8 text-center">Nilai-Nilai Kami</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kepercayaan</h3>
                            <p class="text-gray-600">
                                Membangun hubungan yang didasari kepercayaan dengan seluruh anggota dan mitra bisnis.
                            </p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Inovasi</h3>
                            <p class="text-gray-600">
                                Terus berinovasi dalam mengembangkan produk dan layanan untuk kepuasan anggota.
                            </p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kekeluargaan</h3>
                            <p class="text-gray-600">
                                Menjalankan prinsip koperasi dengan semangat kekeluargaan dan gotong royong.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact CTA Section -->
                <div class="bg-green-50 rounded-lg p-8 text-center">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Bergabung Bersama Kami</h2>
                    <p class="text-gray-700 mb-6 max-w-2xl mx-auto">
                        Ingin menjadi bagian dari keluarga besar Koperasi Sinar Artha? 
                        Hubungi kami untuk informasi lebih lanjut tentang keanggotaan dan layanan kami.
                    </p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-300">
                        Hubungi Kami
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
