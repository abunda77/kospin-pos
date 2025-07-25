@props(['category'])

@php
    $cardBg = match(strtolower($category->name)) {
        'makanan' => 'bg-yellow-100',
        'minuman' => 'bg-blue-100',
        'atk' => 'bg-pink-100',
        'bahan pokok' => 'bg-green-100',
        'elektronik' => 'bg-gray-200',
        'kebutuhan tersier' => 'bg-purple-100',
        'rumah tangga' => 'bg-orange-100',
        'perawatan badan' => 'bg-red-100',
        'lainnya' => 'bg-slate-100',
        default => 'bg-gray-100',
    };
    $iconBg = match(strtolower($category->name)) {
        'makanan' => 'bg-yellow-300',
        'minuman' => 'bg-blue-300',
        'atk' => 'bg-pink-300',
        'bahan pokok' => 'bg-green-300',
        'elektronik' => 'bg-gray-300',
        'kebutuhan tersier' => 'bg-purple-300',
        'rumah tangga' => 'bg-orange-300',
        'perawatan badan' => 'bg-red-300',
        'lainnya' => 'bg-slate-300',
        default => 'bg-primary-300',
    };
@endphp

<a href="{{ route('catalog', ['category' => $category->slug]) }}"
   class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow transition-all duration-300 hover:bg-primary-50 hover:shadow-md group {{ $cardBg }} {{ request()->category === $category->slug ? 'ring-2 ring-primary-500 bg-primary-50' : '' }}">
    <div class="flex justify-center items-center min-w-[3rem] h-12 rounded-lg {{ $iconBg }}">
        @if($category->icon)
            <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}" class="object-contain w-6 h-6">
        @else
            @switch(strtolower($category->name))
                @case('makanan')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @break
                @case('minuman')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19c-1.1 0-2-.9-2-2V9h4v8c0 1.1-.9 2-2 2zM8 3h8l1 6H7l1-6z"/>
                    </svg>
                    @break
                @case('atk')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    @break
                @case('bahan pokok')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @break
                @case('elektronik')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @break
                @case('kebutuhan tersier')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    @break
                @case('rumah tangga')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @break
                @case('perawatan badan')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 10-4 0v2a2 2 0 104 0V7zm-7 8a7 7 0 1114 0v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1z"/>
                    </svg>
                    @break
                @case('lainnya')
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    @break
                @default
                    <svg class="w-6 h-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
            @endswitch
        @endif
    </div>
    <span class="text-base font-semibold text-gray-700 transition-colors group-hover:text-primary-600 sm:text-base text-sm truncate">{{ $category->name }}</span>
</a>
