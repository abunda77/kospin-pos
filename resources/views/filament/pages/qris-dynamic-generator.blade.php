<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Form Section --}}
        <x-filament::card>
            <form wire:submit.prevent="generate">
                {{ $this->form }}

                <div class="mt-6 flex gap-3">
                    <x-filament::button type="submit" color="primary">
                        <x-heroicon-o-sparkles class="w-5 h-5 mr-2" />
                        Generate Dynamic QRIS
                    </x-filament::button>

                    <x-filament::button type="button" wire:click="resetForm" color="gray">
                        <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" />
                        Reset
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>

        {{-- Result Section --}}
        @if($dynamicQris)
            <x-filament::card>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Generated Dynamic QRIS
                        </h3>
                        <x-filament::badge color="success">
                            Ready
                        </x-filament::badge>
                    </div>

                    {{-- Merchant Info --}}
                    @if($merchantName)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Merchant Name</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $merchantName }}</p>
                        </div>
                    @endif

                    {{-- QR Code Display --}}
                    <div class="flex justify-center p-6 bg-white dark:bg-gray-900 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                        @php
                            $filename = session('last_generated_qr');
                            $qrImageUrl = $filename ? asset('storage/qris-generated/' . $filename) : null;
                        @endphp

                        @if($qrImageUrl)
                            <img src="{{ $qrImageUrl }}" alt="Dynamic QRIS" class="max-w-sm w-full h-auto">
                        @else
                            <div class="text-center text-gray-500">
                                <x-heroicon-o-qr-code class="w-24 h-24 mx-auto mb-2" />
                                <p>QR Code will appear here</p>
                            </div>
                        @endif
                    </div>

                    {{-- QRIS String --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dynamic QRIS String
                        </label>
                        <div class="relative">
                            <textarea 
                                readonly 
                                rows="4" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white font-mono text-sm"
                            >{{ $dynamicQris }}</textarea>
                            <button 
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ $dynamicQris }}'); 
                                         window.$wireui.notify({title: 'Copied!', description: 'QRIS string copied to clipboard', icon: 'success'})"
                                class="absolute top-2 right-2 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                title="Copy to clipboard"
                            >
                                <x-heroicon-o-clipboard class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button wire:click="downloadImage" color="success">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                            Download QR Image
                        </x-filament::button>

                        <x-filament::button 
                            type="button"
                            color="gray"
                            onclick="navigator.clipboard.writeText('{{ $dynamicQris }}'); 
                                     window.$wireui?.notify ? window.$wireui.notify({title: 'Copied!', description: 'QRIS string copied to clipboard', icon: 'success'}) : alert('Copied to clipboard!')"
                        >
                            <x-heroicon-o-clipboard-document class="w-5 h-5 mr-2" />
                            Copy QRIS String
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::card>
        @endif

        {{-- Generated QRIS History Table --}}
        <x-filament::card>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-heroicon-o-clock class="w-5 h-5 mr-2 text-primary-500" />
                    Generated QRIS History
                </h3>
                {{ $this->table }}
            </div>
        </x-filament::card>

        {{-- Info Card --}}
        <x-filament::card>
            <div class="space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-heroicon-o-information-circle class="w-5 h-5 mr-2 text-blue-500" />
                    How to Use
                </h4>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>Select a saved QRIS from the dropdown or paste your static QRIS string</li>
                    <li>Enter the transaction amount in Rupiah</li>
                    <li>Optionally add a fee (fixed amount or percentage)</li>
                    <li>Click "Generate Dynamic QRIS" to create the QR code</li>
                    <li>Download the QR image or copy the QRIS string for payment processing</li>
                    <li>All generated QRIS will be saved in the history table below</li>
                </ul>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
