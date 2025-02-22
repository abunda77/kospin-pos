<div wire:poll.5s>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock === 0 ? 'bg-red-100 text-red-800' : ($stock <= 5 ? 'bg-yellow-100 text-yellow-800' : ($stock <= 10 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8 4-8-4m16 0l-8 4m8 4l-8 4m8-4l-8 4m8-4v4m-16-4l8 4m-8-4v4m0-8l8 4m-8-4v4m0-4l8 4" />
        </svg>
        Stok: {{ $stock }}
    </span>
</div>
