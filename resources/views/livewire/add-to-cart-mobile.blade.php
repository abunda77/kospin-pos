<div class="flex justify-end">
    <button 
        wire:click="addToCart"
        wire:loading.attr="disabled"
        class="py-2 px-3 rounded relative text-white text-sm {{ $product->stock > 0 ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed' }}"
        {{ $product->stock <= 0 ? 'disabled' : '' }}>
        <span wire:loading.remove>
            {{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
        </span>
        <span wire:loading>
            <svg class="inline mr-2 w-4 h-4 animate-spin" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menambahkan...
        </span>
    </button>
</div>
