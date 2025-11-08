<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- QR Code Image -->
        <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
            @if($record->qris_image)
                <img 
                    src="{{ asset('storage/' . $record->qris_image) }}" 
                    alt="QRIS {{ $record->name }}"
                    class="max-w-full h-auto rounded-lg shadow-md"
                    style="max-height: 300px;"
                >
            @else
                <div class="text-center text-gray-400">
                    <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-sm">Tidak ada gambar QRIS</p>
                </div>
            @endif
        </div>

        <!-- Details -->
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ $record->name }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Merchant</label>
                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ $record->merchant_name ?? '-' }}</p>
            </div>

            @if($record->description)
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ $record->description }}</p>
            </div>
            @endif

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <p class="mt-1">
                    @if($record->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                            Tidak Aktif
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Dibuat</label>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $record->created_at->format('d M Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terakhir Diperbarui</label>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $record->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- QRIS String -->
    <div class="mt-6">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">String QRIS</label>
        <div class="mt-2 relative">
            <textarea 
                readonly 
                rows="4" 
                class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm font-mono text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
            >{{ $record->qris_string }}</textarea>
            <button 
                type="button"
                onclick="navigator.clipboard.writeText('{{ $record->qris_string }}'); alert('String QRIS berhasil disalin!');"
                class="absolute top-2 right-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Salin
            </button>
        </div>
    </div>
</div>
