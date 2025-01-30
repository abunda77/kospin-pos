@props(['category'])

<a href="{{ route('catalog.show', $category) }}"
   class="flex flex-col items-center p-4 bg-white rounded-lg shadow transition-all duration-300 hover:shadow-lg hover:scale-105">
    @if($category->image)
        <img src="{{ Storage::url($category->image) }}"
             alt="{{ $category->name }}"
             class="object-cover mb-2 w-16 h-16 rounded-lg"
             loading="lazy">
    @else
        <div class="flex justify-center items-center mb-2 w-16 h-16 bg-gray-100 rounded-lg">
            @switch(strtolower($category->name))
                @case('makanan')
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @break
                @case('minuman')
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19c-1.1 0-2-.9-2-2V9h4v8c0 1.1-.9 2-2 2zM8 3h8l1 6H7l1-6z"/>
                    </svg>
                    @break
                @case('atk')
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    @break
                @case('rumah tangga')
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @break
                @case('lainnya')
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    @break
                @default
                    <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
            @endswitch
        </div>
    @endif
    <span class="text-sm font-medium text-center text-gray-700 group-hover:text-primary-600">{{ $category->name }}</span>
</a>