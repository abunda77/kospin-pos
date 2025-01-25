<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;

class CartItems extends Component
{
    public $cart = [];
    public $total = 0;
    public $paymentMethods;

    public function mount()
    {
        $this->updateCart();
        // Ambil payment methods saat komponen dimuat
        $this->paymentMethods = PaymentMethod::where('is_cash', true)->get();
    }

    private function updateCart()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $this->total = 0;
        foreach($this->cart as $item) {
            $this->total += $item['unit_price'] * $item['quantity'];
        }
    }

    public function incrementQuantity($productId)
    {
        if(isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');
        }
    }

    public function decrementQuantity($productId)
    {
        if(isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');
        }
    }

    public function removeItem($productId)
    {
        if(isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');

            $this->js("
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Produk berhasil dihapus dari keranjang!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");
        }
    }

    public function render()
    {
        return view('livewire.cart-items');
    }
}
