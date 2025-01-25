<?php

namespace App\Livewire;

use Livewire\Component;

class AddToCart extends Component
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
                // Sesuaikan dengan struktur yang digunakan di CartController
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

            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Produk berhasil ditambahkan ke keranjang!',
                        type: 'success'
                    }
                }));
            ");
        } catch (\Exception $e) {
            $this->js("
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Gagal menambahkan produk ke keranjang.',
                        type: 'error'
                    }
                }));
            ");
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
