<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Anggota;
use App\Models\VoucherDiskon;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class CheckoutController extends Controller
{
    protected $paymentGatewayFactory;

    public function __construct(PaymentGatewayFactory $paymentGatewayFactory)
    {
        $this->paymentGatewayFactory = $paymentGatewayFactory;
    }

    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        // Cek jika keranjang kosong, redirect ke halaman cart
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda kosong');
        }

        // Ambil semua metode pembayaran yang aktif
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

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

        return view('checkout', compact('cart', 'paymentMethods', 'subtotal', 'total'));
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

        // Kosongkan keranjang saat pengguna sampai di halaman thank-you
        session()->forget(['cart', 'voucher']);

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

        // Add bank account details for Transfer payment
        $bankDetails = null;
        if ($order->paymentMethod && $order->paymentMethod->name === "Transfer") {
            $bankDetails = [
                'bank' => 'BCA',
                'account_number' => '0889333288',
                'account_name' => 'KOPERASI SINARA ARTHA'
            ];
        }

        // Jika menggunakan gateway, cek status transaksi
        if ($order->paymentMethod && $order->paymentMethod->gateway && $order->transaction_id) {
            try {
                $gateway = $this->paymentGatewayFactory->make($order->paymentMethod->gateway);
                $status = $gateway->getTransactionStatus($order->transaction_id);

                if ((is_object($status) && isset($status->transaction_status)) ||
                    (is_array($status) && isset($status['transaction_status']))) {

                    $transactionStatus = is_object($status) ? $status->transaction_status : $status['transaction_status'];

                    $order->update([
                        'status' => $this->mapMidtransStatus($transactionStatus), // This mapping could be gateway-specific
                        'payment_details' => json_encode($status)
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error fetching transaction status: ' . $e->getMessage());
            }
        }

        return view('thank-you', compact('order', 'bankDetails'));
    }

    /**
     * Memetakan status transaksi Midtrans ke status order
     */
    private function mapMidtransStatus($transactionStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                return 'completed';
            case 'pending':
                return 'pending';
            case 'deny':
            case 'cancel':
            case 'expire':
                return 'failed';
            default:
                return 'pending';
        }
    }

    public function generatePdf(Order $order)
    {
        $pdf = PDF::loadView('pdf.order', compact('order'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Invoice-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function processPayment(Request $request)
    {
        $isMember = $request->input('is_member') === '1';

        $baseRules = [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'is_member' => 'required|in:0,1',
        ];

        if ($isMember) {
            $memberRules = [
                'member_id' => 'required|exists:anggotas,id',
                'whatsapp' => 'required|string|max:20',
                'address' => 'required|string',
            ];
            $rules = array_merge($baseRules, $memberRules);
        } else {
            $nonMemberRules = [
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'address' => 'required|string',
            ];
            $rules = array_merge($baseRules, $nonMemberRules);
        }

        $request->validate($rules);

        // Log untuk debugging
        Log::info('Payment request received', [
            'payment_method_id' => $request->payment_method_id,
            'is_member' => $isMember,
            'all_data' => $request->all()
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

        // Ambil payment method
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Siapkan data order
        $orderData = [
            'payment_method_id' => $request->payment_method_id,
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'total_price' => $total,
            'voucher_id' => $voucher ? $voucher['id'] : null,
            'status' => 'pending'
        ];

        if ($isMember) {
            $member = Anggota::find($request->member_id);
            $orderData['name'] = $member->nama_lengkap ?? $member->nama; // Fallback ke 'nama'
            $orderData['whatsapp'] = $request->whatsapp;
            $orderData['address'] = $request->address;
            $orderData['anggota_id'] = $member->id; // Mengasumsikan ada kolom anggota_id
        } else {
            $orderData['name'] = $request->name;
            $orderData['whatsapp'] = $request->whatsapp;
            $orderData['address'] = $request->address;
        }

        // Buat order dengan status pending
        $order = Order::create($orderData);

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

        // Jika payment method menggunakan gateway
        if ($paymentMethod->gateway) {
            try {
                $gateway = $this->paymentGatewayFactory->make($paymentMethod->gateway);

                // Siapkan item details
                $items = [];
                foreach ($cart as $id => $item) {
                    $items[] = [
                        'id' => $id,
                        'price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'name' => $item['name'],
                    ];
                }

                if ($discount > 0) {
                    $items[] = [
                        'id' => 'DISCOUNT',
                        'price' => -$discount,
                        'quantity' => 1,
                        'name' => 'Discount',
                    ];
                }

                $gatewayOrderId = $order->no_order;

                $customerDetails = [
                    'first_name' => $order->name,
                    'phone' => $order->whatsapp,
                    'billing_address' => [
                        'address' => $order->address,
                    ],
                ];

                $transactionParams = [
                    'transaction_details' => [
                        'order_id' => $gatewayOrderId,
                        'gross_amount' => $total,
                    ],
                    'customer_details' => $customerDetails,
                    'item_details' => $items,
                    'callbacks' => [
                        'finish' => route('payment.finish'),
                        'unfinish' => route('payment.unfinish'),
                        'error' => route('payment.error')
                    ]
                ];

                $order->update(['transaction_id' => $gatewayOrderId]);

                // Logika spesifik per gateway untuk payment type, bisa di-refactor lebih lanjut
                $paymentType = $request->input('payment_type', 'bank_transfer');
                switch ($paymentMethod->gateway) {
                    case 'midtrans':
                        switch ($paymentType) {
                            case 'credit_card':
                                $cardToken = $this->getCardToken($request);
                                if (!$cardToken) {
                                    return redirect()->back()->with('error', 'Gagal mendapatkan token kartu kredit');
                                }
                                $transactionParams['payment_type'] = 'credit_card';
                                $transactionParams['credit_card'] = [
                                    'token_id' => $cardToken,
                                    'authentication' => true,
                                ];
                                break;
                            case 'bank_transfer':
                                $transactionParams['payment_type'] = 'bank_transfer';
                                $transactionParams['bank_transfer'] = ['bank' => $request->bank ?? 'bca'];
                                break;
                            case 'gopay':
                                $transactionParams['payment_type'] = 'gopay';
                                break;
                            default:
                                Log::warning('Unrecognized payment type for Midtrans: ' . $paymentType . ', defaulting to bank_transfer BCA');
                                $transactionParams['payment_type'] = 'bank_transfer';
                                $transactionParams['bank_transfer'] = ['bank' => 'bca'];
                                break;
                        }
                        break;
                    // Tambahkan case untuk gateway lain jika ada logika payment_type yang berbeda
                }

                $chargeResponse = $gateway->createTransaction($transactionParams);

                $paymentUrl = null;
                if (!empty($chargeResponse->redirect_url)) { // Untuk 3DS atau halaman pembayaran
                    $paymentUrl = $chargeResponse->redirect_url;
                } elseif ($paymentType === 'gopay' && !empty($chargeResponse->actions[0]->url)) { // Untuk GoPay QR
                    $paymentUrl = $chargeResponse->actions[0]->url;
                } else {
                    $paymentUrl = route('thank-you', $order->id);
                }

                $order->update([
                    'payment_details' => json_encode($chargeResponse),
                    'payment_url' => $paymentUrl
                ]);

                // Redirect jika ada URL pembayaran eksternal
                if ($paymentUrl && $paymentUrl !== route('thank-you', $order->id)) {
                    return redirect($paymentUrl);
                }

                return redirect()->route('thank-you', $order->id);

            } catch (\Exception $e) {
                Log::error('Payment gateway error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'order_id' => $order->id
                ]);
                return redirect()->back()->with('error', 'Gagal memulai pembayaran. Silakan coba lagi atau pilih metode pembayaran lain. Error: ' . $e->getMessage());
            }
        } else {
            // Untuk metode pembayaran non-gateway (misalnya transfer manual)
            Log::info('Non-gateway payment method used', [
                'payment_method' => $paymentMethod->name,
                'order_id' => $order->id
            ]);

            // Redirect ke halaman thank you
            return redirect()->route('thank-you', $order->id);
        }

        // Fallback redirect jika tidak ada return sebelumnya
        return redirect()->route('thank-you', $order->id);
    }

    /**
     * Mendapatkan token kartu dari Midtrans
     */
    private function getCardToken(Request $request)
    {
        // TODO: This Midtrans-specific logic should be moved to the MidtransGateway class.
        // It would require passing necessary request data to the gateway method.
        try {
            // Temporary initialization. A better approach is needed.
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');

            $cardNumber = str_replace(' ', '', $request->card_number);

            // Use Midtrans API to get token
            $response = \Midtrans\CoreApi::cardToken(
                $cardNumber,
                $request->card_exp_month,
                $request->card_exp_year,
                $request->card_cvv
            );

            return $response->token_id;
        } catch (\Exception $e) {
            Log::error('Midtrans Card Token Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle callback notification dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        // TODO: The route should specify the gateway, e.g. /api/notification/{gateway}
        // For now, assuming 'midtrans'
        $gateway = $this->paymentGatewayFactory->make('midtrans');

        try {
            $notification = $gateway->notificationHandler($request->all());

            $order = Order::where('no_order', $notification->order_id)->first();

            if (!$order) {
                Log::error('Gateway notification: Order not found with no_order: ' . $notification->order_id);
                return response()->json(['status' => 'error', 'message' => 'Order not found']);
            }

            // Update order status based on transaction status
            // This mapping logic could be moved to the gateway class
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $order->status = 'completed';
                } else {
                    $order->status = 'challenge';
                }
            } else if ($transactionStatus == 'settlement') {
                $order->status = 'processing';
            } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $order->status = 'failed';
            } else if ($transactionStatus == 'pending') {
                $order->status = 'pending';
            }

            $order->payment_details = json_encode($notification);
            $order->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
        Log::error('Gateway Notification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment finish redirect from Midtrans
     */
    public function finishPayment(Request $request)
    {
        $midtransOrderId = $request->order_id;

        // Cari order berdasarkan no_order, karena itu yang kita kirim ke Midtrans
        $order = Order::where('no_order', $midtransOrderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan');
        }

        // Kosongkan keranjang setelah pembayaran dikonfirmasi selesai
        session()->forget(['cart', 'voucher']);

        // Check transaction status from Gateway
        try {
            if ($order->paymentMethod && $order->paymentMethod->gateway) {
                $gateway = $this->paymentGatewayFactory->make($order->paymentMethod->gateway);
                $status = $gateway->getTransactionStatus($midtransOrderId);

                // Update order status and payment details
                $order->update([
                    'status' => $this->mapMidtransStatus($status->transaction_status ?? 'pending'),
                    'payment_details' => json_encode($status)
                ]);
            }

            // Redirect to thank you page
            return redirect()->route('thank-you', $order->id)
                ->with('status', 'Pembayaran berhasil diproses');
        } catch (\Exception $e) {
            Log::error('Finish Payment Error: ' . $e->getMessage());
        }

        return redirect()->route('thank-you', $order->id);
    }

    /**
     * Handle payment unfinish redirect from Midtrans
     */
    public function unfinishPayment(Request $request)
    {
        $midtransOrderId = $request->order_id;

        $order = Order::where('no_order', $midtransOrderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan');
        }

        return redirect()->route('thank-you', $order->id)
            ->with('warning', 'Pembayaran belum selesai. Silakan selesaikan pembayaran Anda.');
    }

    /**
     * Handle payment error redirect from Midtrans
     */
    public function errorPayment(Request $request)
    {
        $midtransOrderId = $request->order_id;

        $order = Order::where('no_order', $midtransOrderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan');
        }

        return redirect()->route('thank-you', $order->id)
            ->with('error', 'Terjadi kesalahan dalam pembayaran. Silakan coba lagi atau hubungi kami.');
    }

    /**
     * Cek status transaksi di Midtrans
     */
    public function checkTransactionStatus($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if (!$order->transaction_id || !$order->paymentMethod || !$order->paymentMethod->gateway) {
                return redirect()->back()->with('error', 'ID Transaksi atau Gateway Pembayaran tidak ditemukan.');
            }

            $gateway = $this->paymentGatewayFactory->make($order->paymentMethod->gateway);
            $status = $gateway->getTransactionStatus($order->transaction_id);

            // Update order status
            $order->payment_details = json_encode($status);
            $order->status = $this->mapMidtransStatus($status->transaction_status ?? 'pending');
            $order->save();

            return redirect()->route('thank-you', $order->id)->with('status', 'Status pembayaran berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Check Transaction Status Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memeriksa status transaksi: ' . $e->getMessage());
        }
    }
}


