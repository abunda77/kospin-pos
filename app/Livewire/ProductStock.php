<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductStock extends Component
{
    public $product;
    
    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.product-stock', [
            'stock' => $this->product->fresh()->stock
        ]);
    }
}
