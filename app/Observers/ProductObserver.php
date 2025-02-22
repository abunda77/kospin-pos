<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    public function saved(Product $product)
    {
        if ($product->image && Storage::exists($product->image)) {
            $path = Storage::path($product->image);
            ImageOptimizer::optimize($path);
        }
    }
}
