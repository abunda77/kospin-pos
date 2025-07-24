<?php

namespace App\Livewire;

use Livewire\Component;

class AddToCartMobile extends Component
{
    public $product;
    public $quantity = 1;
    public $isLoading = false;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        $this->isLoading = true;

        try {
            $cart = session()->get('cart', []);

            if(isset($cart[$this->product->id])) {
                $cart[$this->product->id]['quantity'] += $this->quantity;
            } else {
                $cart[$this->product->id] = [
                    'name' => $this->product->name,
                    'quantity' => $this->quantity,
                    'unit_price' => $this->product->price,
                    'image' => $this->product->image_url
                ];
            }

            session()->put('cart', $cart);

            // Dispatch event untuk update cart counter
            $this->dispatch('cartUpdated');

            // Mobile-optimized toast notification
            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Produk ditambahkan!',
                        type: 'success',
                        duration: 2000
                    }
                }));
            ");
        } catch (\Exception $e) {
            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Gagal menambahkan produk!',
                        type: 'error',
                        duration: 2000
                    }
                }));
            ");
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart-mobile');
    }
}
