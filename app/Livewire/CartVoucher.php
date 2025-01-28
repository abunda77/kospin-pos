<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VoucherDiskon;

class CartVoucher extends Component
{
    public $kode_voucher;
    public $message;
    public $discount = 0;
    public $voucher = null;

    public function applyVoucher()
    {
        $this->reset(['message', 'discount']);

        $voucher = VoucherDiskon::where('kode_voucher', $this->kode_voucher)->first();

        if (!$voucher) {
            $this->message = 'Kode voucher tidak tersedia';
            return;
        }

        if (!$voucher->isValid()) {
            $this->message = 'Voucher sudah expired atau stok habis';
            return;
        }

        $cart = session()->get('cart', []);
        $totalAmount = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $this->discount = $voucher->calculateDiscount($totalAmount);
        $this->voucher = $voucher;
        session()->put('voucher', [
            'id' => $voucher->id,
            'kode_voucher' => $voucher->kode_voucher,
            'discount' => $this->discount
        ]);

        $this->message = 'Voucher berhasil digunakan';
        $this->dispatch('voucherApplied', $this->discount);
    }

    public function removeVoucher()
    {
        $this->reset(['message', 'discount', 'kode_voucher', 'voucher']);
        session()->forget('voucher');
        $this->dispatch('voucherRemoved');
    }

    public function render()
    {
        return view('livewire.cart-voucher');
    }
}
