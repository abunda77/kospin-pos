<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizer;

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
                // Karena image sudah tersimpan dengan path 'public/products/filename.jpg'
                // dan Storage::path() sudah menambahkan storage/app/ di depannya
                $path = Storage::path($product->image);
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'path' => $product->image,
                    'size' => file_exists($path) ? round(filesize($path) / 1024, 2) : 0, // Size in KB
                    'url' => Storage::url($product->image), // Ini akan menghasilkan /storage/public/products/filename.jpg
                ];
            })
            ->filter(fn($image) => $image['size'] > 0);
    }

    public function compressSelected()
    {
        if (empty($this->selectedImages)) {
            $this->notify('warning', 'Pilih gambar terlebih dahulu');
            return;
        }

        $this->processing = true;
        $this->progress = 0;
        $this->totalImages = count($this->selectedImages);

        foreach ($this->selectedImages as $index => $imageId) {
            $product = Product::find($imageId);
            if (!$product || !$product->image) continue;

            $this->currentImage = $product->name;
            $path = Storage::path($product->image); // Ini akan memberikan path lengkap ke file

            if (file_exists($path)) {
                ImageOptimizer::optimize($path);
            }

            $this->progress = ($index + 1) / $this->totalImages * 100;
            $this->dispatch('progressUpdated', progress: $this->progress);
        }

        $this->processing = false;
        $this->selectedImages = [];
        $this->notify('success', 'Kompresi gambar selesai');
    }
}
