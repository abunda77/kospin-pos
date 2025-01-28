<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Anggota;
use App\Models\VoucherDiskon; // tambahkan model VoucherDiskon
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $paymentMethods = PaymentMethod::where('is_cash', true)->get();

        // Hitung subtotal
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        // Hitung diskon jika ada voucher
        $discount = 0;
        if (session()->has('voucher')) {
            $discount = session('voucher')['discount'];
        }

        // Hitung total setelah diskon
        $total = $subtotal - $discount;

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        return view('checkout', compact('cart', 'paymentMethods', 'subtotal', 'total', 'paymentMethod'));
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
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'address' => 'required|string'
        ]);

        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher');

        // Hitung subtotal
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        // Hitung diskon jika ada voucher
        $discount = 0;
        if ($voucher) {
            $discount = $voucher['discount'];
        }

        // Hitung total setelah diskon
        $total = $subtotal - $discount;

        // Buat order
        $order = Order::create([
            'payment_method_id' => $request->payment_method_id,
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'total_price' => $total,
            'voucher_id' => $voucher ? $voucher['id'] : null,
            'status' => 'pending'
        ]);

        // Simpan order products
        foreach ($cart as $id => $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ]);
        }

        // Kurangi stok voucher jika digunakan
        if ($voucher) {
            $voucherModel = VoucherDiskon::find($voucher['id']);
            if ($voucherModel) {
                $voucherModel->decrement('stok_voucher');
            }
        }

        // Bersihkan session
        session()->forget(['cart', 'voucher']);

        return redirect()->route('thank-you', $order->id);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher');

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $discount = 0;
        if ($voucher) {
            $voucherModel = VoucherDiskon::find($voucher['id']);
            if ($voucherModel && $voucherModel->isValid()) {
                $discount = $voucherModel->calculateDiscount($subtotal);
                $voucherModel->useVoucher();
            }
        }

        $total = $subtotal - $discount;

        $order = Order::create([
            // tidak perlu otorisasi user id
            'total_amount' => $total,
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'voucher_id' => $voucher['id'] ?? null,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        foreach ($cart as $productId => $item) {
            $order->items()->create([
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price']
            ]);
        }

        // Clear cart and voucher from session
        session()->forget(['cart', 'voucher']);

        return redirect()->route('thank-you', ['order' => $order->id]);
    }

    public function thankYou($orderId)
    {
        $order = Order::with(['orderProducts.product', 'paymentMethod'])
            ->findOrFail($orderId);

        // Hitung ulang subtotal dari order products
        $subtotal = $order->orderProducts->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        // Update order jika subtotal belum benar
        if ($order->subtotal_amount != $subtotal) {
            $order->update([
                'subtotal_amount' => $subtotal,
                'total_amount' => $subtotal - ($order->discount_amount ?? 0)
            ]);
        }

        return view('thank-you', compact('order'));
    }

    public function generatePdf(Order $order)
    {
        $pdf = PDF::loadView('pdf.order', compact('order'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Invoice-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }
}
