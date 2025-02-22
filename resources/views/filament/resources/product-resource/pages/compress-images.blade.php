<x-filament-panels::page>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', ({ status, message }) => {
                Filament.notify({
                    status: status,
                    message: message,
                });
            });

            Livewire.on('progress-updated', ({ progress }) => {
                document.querySelector('.progress-bar').style.width = `${progress}%`;
            });
        });
    </script>

    <div x-data="{
        processing: @entangle('processing'),
        progress: @entangle('progress'),
        currentImage: @entangle('currentImage'),
        selectAll: false
    }">
        <x-filament::card>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-medium">Kompresi Gambar Produk</h2>
                    <x-filament::button
                        wire:click="compressSelected"
                        :disabled="count($selectedImages) === 0 || processing"
                    >
                        Kompres {{ count($selectedImages) }} Gambar Terpilih
                    </x-filament::button>
                </div>

                <!-- Select All / Unselect All -->
                <div>
                    <button @click="selectAll = !selectAll; $wire.set('selectedImages', selectAll ? @json($this->productImages->pluck('id')) : [])" class="text-sm text-blue-600">
                        {{ selectAll ? 'Unselect All' : 'Select All' }}
                    </button>
                </div>

                <!-- Progress Bar -->
                <div x-show="processing" class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span x-text="currentImage"></span>
                        <span x-text="`${Math.round(progress)}%`"></span>
                    </div>
                    <div class="w-full h-2.5 bg-gray-200 rounded-full dark:bg-gray-700">
                        <div class="h-2.5 rounded-full transition-all duration-300 progress-bar bg-primary-600"
                             x-bind:style="`width: ${progress}%`">
                        </div>
                    </div>
                </div>

                <!-- Image List -->
                <div class="space-y-2">
                    @foreach($this->productImages as $image)
                        <label class="flex items-center p-2 space-x-3 rounded border transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700">
                            <input type="checkbox"
                                wire:model.live="selectedImages"
                                value="{{ $image['id'] }}"
                                class="rounded border-gray-300 shadow-sm text-primary-600 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700"
                                {{ $processing ? 'disabled' : '' }}
                            >
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="overflow-hidden flex-shrink-0 w-16 h-16 rounded-lg shadow-sm">
                                        <img
                                            src="{{ $image['url'] }}"
                                            alt="{{ $image['name'] }}"
                                            class="object-cover w-full h-full"
                                            loading="lazy"
                                        >
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $image['name'] }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $image['size'] }} KB
                                        </div>
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
