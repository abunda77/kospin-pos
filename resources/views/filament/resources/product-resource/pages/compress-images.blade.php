<x-filament-panels::page>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                Filament.notify({
                    status: data[0].status,
                    message: data[0].message,
                });
            });
        });
    </script>

    <div x-data="{ 
        processing: @entangle('processing'),
        progress: @entangle('progress'),
        currentImage: @entangle('currentImage')
    }">
        <x-filament::card>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-medium">Kompresi Gambar Produk</h2>
                    <x-filament::button
                        wire:click="compressSelected"
                        :disabled="count($selectedImages) === 0 || $processing"
                    >
                        Kompres {{ count($selectedImages) }} Gambar Terpilih
                    </x-filament::button>
                </div>

                <!-- Progress Bar -->
                <div x-show="processing" class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span x-text="currentImage"></span>
                        <span x-text="`${Math.round(progress)}%`"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary-600 h-2.5 rounded-full" x-bind:style="`width: ${progress}%`"></div>
                    </div>
                </div>

                <!-- Image List -->
                <div class="space-y-2">
                    @foreach($this->productImages as $image)
                        <label class="flex items-center space-x-3 p-2 border rounded hover:bg-gray-50">
                            <input type="checkbox" 
                                wire:model.live="selectedImages" 
                                value="{{ $image['id'] }}"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                {{ $processing ? 'disabled' : '' }}
                            >
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="w-12 h-12 object-cover rounded">
                                    <div>
                                        <div class="font-medium">{{ $image['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $image['size'] }} KB</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
