<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Anggota;
use App\Models\VoucherDiskon;
use Illuminate\Http\Request;

class CheckoutUserController extends Controller
{
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethod::where('is_cash', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil metode pembayaran',
            'data' => $paymentMethods
        ]);
    }

    public function checkMember($nik)
    {
        $member = Anggota::where('nik', $nik)->first();

        return response()->json([
            'success' => true,
            'message' => $member ? 'Member ditemukan' : 'Member tidak ditemukan',
            'data' => [
                'exists' => !is_null($member),
                'member' => $member
            ]
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'address' => 'required|string',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.unit_price' => 'required|numeric|min:0',
            'voucher_id' => 'nullable|exists:voucher_diskons,id'
        ]);

        // Hitung subtotal
        $subtotal = collect($request->cart)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        // Hitung diskon jika ada voucher
        $discount = 0;
        $voucherModel = null;
        if ($request->voucher_id) {
            $voucherModel = VoucherDiskon::find($request->voucher_id);
            if ($voucherModel && $voucherModel->isValid()) {
                $discount = $voucherModel->calculateDiscount($subtotal);
            }
        }

        // Hitung total setelah diskon
        $total = $subtotal - $discount;

        // Buat order dengan transaction untuk mencegah duplicate no_order
        $order = \DB::transaction(function () use ($request, $subtotal, $discount, $total, $voucherModel) {
            // Generate sequential no_order with database locking
            $noOrder = Order::generateNextOrderNumber();
            
            $order = Order::create([
                'no_order' => $noOrder,
                'payment_method_id' => $request->payment_method_id,
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'address' => $request->address,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'total_price' => $total,
                'voucher_id' => $request->voucher_id,
                'status' => 'pending'
            ]);

            // Simpan order products
            foreach ($request->cart as $item) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);
            }

            // Kurangi stok voucher jika digunakan
            if ($voucherModel) {
                $voucherModel->decrement('stok_voucher');
            }
            
            return $order;
        });

        // Load relasi yang diperlukan
        $order->load(['orderProducts.product', 'paymentMethod']);

        // Add bank account details for Transfer payment
        $bankDetails = null;
        if ($order->paymentMethod && $order->paymentMethod->name === "Transfer") {
            $bankDetails = [
                'bank' => 'BCA',
                'account_number' => '0889333288',
                'account_name' => 'KOPERASI SINARA ARTHA'
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'data' => [
                'order' => $order,
                'bank_details' => $bankDetails
            ]
        ]);

    }

    public function getOrderDetail($orderId)
    {
        try {
            $order = Order::with(['orderProducts.product', 'paymentMethod'])
                ->where('id', $orderId)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Add bank account details for Transfer payment
            $bankDetails = null;
            if ($order->paymentMethod && $order->paymentMethod->name === "Transfer") {
                $bankDetails = [
                    'bank' => 'BCA',
                    'account_number' => '0889333288',
                    'account_name' => 'KOPERASI SINARA ARTHA'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Sukses mengambil detail order',
                'data' => [
                    'order' => $order,
                    'bank_details' => $bankDetails
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
