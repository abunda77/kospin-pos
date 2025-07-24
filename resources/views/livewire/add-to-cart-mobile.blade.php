<div>
    <button
        wire:click="addToCart"
        wire:loading.attr="disabled"
        class="inline-flex items-center justify-center p-1 w-8 h-8 text-white bg-primary-600 rounded-full shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 touch-manipulation disabled:opacity-70"
    >
        <span wire:loading.remove wire:target="addToCart" aria-hidden="true">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="sr-only">Tambah ke Keranjang</span>
        </span>
        <span wire:loading wire:target="addToCart" aria-hidden="true">
            <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="sr-only">Menambahkan...</span>
        </span>
    </button>
</div>
