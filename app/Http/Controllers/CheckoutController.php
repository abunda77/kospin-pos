<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Anggota;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach($cart as $item) {
            $total += $item['unit_price'] * $item['quantity'];
        }

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        return view('checkout', compact('cart', 'total', 'paymentMethod'));
    }

    public function checkMember($nik)
    {
        $member = Anggota::where('nik', $nik)->first();

        return response()->json([
            'exists' => !is_null($member),
            'member' => $member
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'whatsapp' => 'required',
            'address' => 'required',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        // Hitung total dari cart
        $cart = session()->get('cart', []);
        $total_price = 0;

        foreach($cart as $item) {
            $total_price += $item['unit_price'] * $item['quantity'];
        }

        if ($request->is_member) {
            if ($request->member_id) {
                // Existing member
                $member = Anggota::find($request->member_id);
            } else {
                // New member
                $member = Anggota::create([
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->name,
                    'total_pembelian' => 0
                ]);
            }

            // Buat order untuk member
            $order = Order::create([
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'address' => $request->address,
                'payment_method_id' => $request->payment_method_id,
                'anggota_id' => $member->id,
                'total_price' => $total_price,
                'status' => 'pending'
            ]);
        } else {
            // Buat order untuk non-member
            $order = Order::create([
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'address' => $request->address,
                'payment_method_id' => $request->payment_method_id,
                'total_price' => $total_price,
                'status' => 'pending'
            ]);
        }

        // Simpan order products
        foreach($cart as $id => $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ]);
        }

        // Kosongkan cart
        session()->forget('cart');

        return redirect('/')->with('success', 'Pesanan berhasil diproses!');
    }
}
