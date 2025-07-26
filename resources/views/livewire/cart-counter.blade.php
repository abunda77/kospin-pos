<div>
    @if($cartCount > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-semibold text-white bg-red-500 rounded-full border-2 border-white shadow-sm z-10">
            {{ $cartCount > 99 ? '99+' : $cartCount }}
        </span>
    @endif
</div>
