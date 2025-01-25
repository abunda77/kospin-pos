<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach($cart as $id => $item) {
            if (!isset($item['unit_price'])) {
                // If unit_price is missing, remove the item from cart
                unset($cart[$id]);
                continue;
            }
            $total += $item['unit_price'] * $item['quantity'];
        }

        // Update the cart session after cleaning invalid items
        session()->put('cart', $cart);

        $paymentMethods = PaymentMethod::where('is_cash', true)->get();

        return view('cart', compact('cart', 'total', 'paymentMethods'));
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => 1,
                'unit_price' => $product->price,
                'image' => $product->image_url
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function delete($productId)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
    }
}
