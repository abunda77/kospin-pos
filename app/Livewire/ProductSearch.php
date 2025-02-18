<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    public $category = null;

    public function mount($category = null)
    {
        $this->category = $category;
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->with('category');

        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        $products = $query->paginate(12);

        return view('livewire.product-search', [
            'products' => $products
        ]);
    }
}
