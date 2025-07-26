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
    
    // Determine route based on current mode
    $isMobileMode = session('view_preference') === 'mobile' ||
                   request()->routeIs('catalog.mobile*') || 
                   str_contains(request()->path(), 'm/catalog');
    $categoryRoute = $isMobileMode 
        ? route('catalog.mobile.show', $category->slug)
        : route('catalog', ['category' => $category->slug]);
@endphp

<a href="{{ $categoryRoute }}"
   class="flex gap-3 items-center px-4 py-3 w-full rounded-xl shadow-md transition-all duration-300 hover:bg-primary-50 hover:shadow-lg hover:scale-105 group whitespace-nowrap sm:whitespace-normal {{ $cardBg }} {{ request()->category === $category->slug ? 'ring-2 ring-primary-500 bg-primary-50 shadow-lg' : '' }}">
    <div class="flex justify-center items-center min-w-[3rem] h-12 rounded-lg {{ $iconBg }} group-hover:scale-110 transition-transform duration-300">
        @if($category->icon)
            <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}" class="object-contain w-6 h-6 transition-transform duration-300 group-hover:scale-110">
        @else
            @switch(strtolower($category->name))
                @case('makanan')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @break
                @case('minuman')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    @break
                @case('atk')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    @break
                @case('bahan pokok')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @break
                @case('elektronik')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @break
                @case('kebutuhan tersier')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    @break
                @case('rumah tangga')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    </svg>
                    @break
                @case('perawatan badan')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @break
                @case('lainnya')
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    @break
                @default
                    <svg class="w-6 h-6 text-primary-600 transition-all duration-300 group-hover:text-primary-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
            @endswitch
        @endif
    </div>
    <span class="text-base font-semibold text-gray-700 transition-colors duration-300 group-hover:text-primary-600 leading-tight">{{ $category->name }}</span>
</a>
