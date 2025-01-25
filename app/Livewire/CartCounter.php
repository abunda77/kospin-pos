<?php

namespace App\Livewire;

use Livewire\Component;

class CartCounter extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        $this->cartCount = count(session('cart', []));
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
