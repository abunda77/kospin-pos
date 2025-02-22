<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizer;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Actions\Action;

class CompressImages extends Page
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.compress-images';

    public $selectedImages = [];
    public $processing = false;
    public $currentImage = null;
    public $progress = 0;
    public $totalImages = 0;

    public function getProductImagesProperty()
    {
        return Product::whereNotNull('image')
            ->get()
            ->map(function ($product) {
                $path = Storage::path($product->image);
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'path' => $product->image,
                    'size' => file_exists($path) ? round(filesize($path) / 1024, 2) : 0,
                    'url' => Storage::url($product->image),
                ];
            })
            ->filter(fn($image) => $image['size'] > 0);
    }

    public function compressSelected(): void
    {
        if (empty($this->selectedImages)) {
            Notification::make()
                ->warning()
                ->title('Pilih gambar terlebih dahulu')
                ->send();
            return;
        }

        try {
            $this->processing = true;
            $this->progress = 0;
            $this->totalImages = count($this->selectedImages);

            foreach ($this->selectedImages as $index => $imageId) {
                $product = Product::find($imageId);
                if (!$product || !$product->image) continue;

                $this->currentImage = $product->name;
                $path = Storage::path($product->image);

                if (file_exists($path)) {
                    ImageOptimizer::optimize($path);
                }

                $this->progress = ($index + 1) / $this->totalImages * 100;
                $this->dispatch('progressUpdated', progress: $this->progress);
            }

            $this->processing = false;
            $this->selectedImages = [];
            
            Notification::make()
                ->success()
                ->title('Kompresi gambar selesai')
                ->send();

        } catch (\Exception $e) {
            $this->processing = false;
            
            Notification::make()
                ->danger()
                ->title('Terjadi kesalahan saat mengkompresi gambar')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compress')
                ->label('Compress Selected')
                ->action('compressSelected')
                ->disabled(fn () => empty($this->selectedImages))
        ];
    }
}
