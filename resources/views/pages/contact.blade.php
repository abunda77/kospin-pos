@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header Section -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Hubungi Kami</h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Kami siap membantu Anda. Jangan ragu untuk menghubungi kami melalui berbagai cara di bawah ini.
                    </p>
                </div>

                <!-- Contact Information Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
                    <!-- Contact Details -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-8">Informasi Kontak</h2>
                        
                        <!-- Address -->
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Alamat Kantor</h3>
                                <p class="text-gray-700">
                                    Eastern Park Residence Blok B No. 7<br>
                                    Sukolilo, Surabaya 60111<br>
                                    Jawa Timur, Indonesia
                                </p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Telepon</h3>
                                <p class="text-gray-700">
                                    <a href="tel:+6287778715788" class="text-green-600 hover:text-green-700 transition-colors">
                                        (+62) 87778715788
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.700"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                                <p class="text-gray-700">
                                    <a href="https://wa.me/6287778715788" target="_blank" class="text-green-600 hover:text-green-700 transition-colors">
                                        +62 87778715788
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                                <p class="text-gray-700">
                                    <a href="mailto:cs@kospinsinaraartha.co.id" class="text-green-600 hover:text-green-700 transition-colors">
                                        cs@kospinsinaraartha.co.id
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-8">Jam Operasional</h2>
                        
                        <div class="bg-gray-50 rounded-lg p-6 mb-8">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-gray-900">Senin - Jumat</span>
                                    <span class="text-gray-700">08:00 - 17:00</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="font-medium text-gray-900">Sabtu</span>
                                    <span class="text-gray-700">08:00 - 12:00</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-medium text-gray-900">Minggu / Hari Libur</span>
                                    <span class="text-red-600 font-medium">Tutup</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Hubungi Kami Sekarang</h3>
                            
                            <a href="https://wa.me/6287778715788" target="_blank" 
                               class="flex items-center justify-center w-full px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-300">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.700"/>
                                </svg>
                                Chat via WhatsApp
                            </a>
                            
                            <a href="mailto:cs@kospinsinaraartha.co.id" 
                               class="flex items-center justify-center w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Kirim Email
                            </a>
                            
                            <a href="tel:+6287778715788" 
                               class="flex items-center justify-center w-full px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Telepon Langsung
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Map Section (Optional - you can add Google Maps embed here) -->
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Kunjungi Kantor Kami</h2>
                    <p class="text-gray-700 mb-6">
                        Kami dengan senang hati menyambut kunjungan Anda di kantor kami. 
                        Pastikan untuk menghubungi kami terlebih dahulu untuk membuat janji.
                    </p>
                    <div class="bg-white rounded-lg p-6 border border-gray-200">
                        <p class="text-gray-600 italic">
                            "Kami berkomitmen memberikan pelayanan terbaik untuk semua anggota koperasi. 
                            Tim customer service kami siap membantu Anda dengan segala kebutuhan dan pertanyaan."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
