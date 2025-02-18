<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $category = null;
    
    protected $queryString = ['search'];

    public function mount($category = null)
    {
        $this->category = $category;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->with('category');

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        $products = $query->paginate(12);

        return view('livewire.product-search', [
            'products' => $products
        ]);
    }

    public function resetSearch()
    {
        $this->reset('search');
        $this->resetPage();
    }
}