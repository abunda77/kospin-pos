<x-filament-panels::page>
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
                        <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300"
                             x-bind:style="`width: ${progress}%`"></div>
                    </div>
                </div>

                <!-- Image List -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-2">
                                    <x-filament::input.checkbox
                                        wire:model.live="selectedImages"
                                        value="all"
                                    />
                                </th>
                                <th class="p-2 text-left">Gambar</th>
                                <th class="p-2 text-left">Nama Produk</th>
                                <th class="p-2 text-right">Ukuran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->productImages as $image)
                            <tr class="border-t">
                                <td class="p-2">
                                    <x-filament::input.checkbox
                                        wire:model.live="selectedImages"
                                        value="{{ $image['id'] }}"
                                    />
                                </td>
                                <td class="p-2">
                                    <img src="{{ $image['url'] }}" 
                                         alt="{{ $image['name'] }}"
                                         class="w-16 h-16 object-cover rounded">
                                </td>
                                <td class="p-2">{{ $image['name'] }}</td>
                                <td class="p-2 text-right">{{ $image['size'] }} KB</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>