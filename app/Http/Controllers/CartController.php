<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Tidak perlu lagi mengambil payment methods karena sudah dihapus dari view
        return view('cart');
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

