<div class="mt-4">
    <div class="flex gap-4">
        <div class="flex-1">
            <input type="text"
                wire:model="kode_voucher"
                placeholder="Masukkan kode voucher"
                class="px-4 py-2 w-full rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>
        <button
            wire:click="applyVoucher"
            class="px-4 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
        >
            Gunakan Voucher
        </button>
    </div>

    @if($message)
        <div class="mt-2 text-sm {{ str_contains($message, 'berhasil') ? 'text-green-600' : 'text-red-600' }}">
            {{ $message }}
        </div>
    @endif

    @if($voucher)
        <div class="p-4 mt-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold">Voucher Aktif:</p>
                    <p class="text-sm text-gray-600">{{ $voucher->kode_voucher }}</p>
                    <p class="text-sm text-gray-600">
                        Diskon:
                        @if($voucher->jenis_discount === 'prosentase')
                            {{ $voucher->nilai_discount }}%
                        @else
                            Rp {{ number_format($voucher->nilai_discount, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <button
                    wire:click="removeVoucher"
                    class="text-red-500 hover:text-red-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
