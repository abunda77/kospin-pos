<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Anggota;
use App\Models\VoucherDiskon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Gunakan URL redirect dari konfigurasi route
        Config::$overrideNotifUrl = route('api.midtrans.notification');
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

        // Jika menggunakan Midtrans, cek status transaksi
        if ($order->paymentMethod && $order->paymentMethod->gateway === 'midtrans' && $order->transaction_id) {
            try {
                $status = Transaction::status($order->transaction_id);

                // Update status dan payment details jika ada perubahan
                if ((is_object($status) && isset($status->transaction_status)) ||
                    (is_array($status) && isset($status['transaction_status']))) {

                    $transactionStatus = is_object($status) ? $status->transaction_status : $status['transaction_status'];

                    $order->update([
                        'status' => $this->mapMidtransStatus($transactionStatus),
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

        // Jika payment method menggunakan Midtrans
        if ($paymentMethod->gateway === 'midtrans') {
            try {
                // Set konfigurasi Midtrans
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                // Siapkan item details untuk Midtrans
                $items = [];
                foreach ($cart as $id => $item) {
                    $items[] = [
                        'id' => $id,
                        'price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'name' => $item['name'],
                    ];
                }

                // Tambahkan item diskon jika ada
                if ($discount > 0) {
                    $items[] = [
                        'id' => 'DISCOUNT',
                        'price' => -$discount,
                        'quantity' => 1,
                        'name' => 'Discount',
                    ];
                }

                // Buat order_id untuk Midtrans menggunakan no_order
                $midtransOrderId = $order->no_order;

                // Siapkan parameter transaksi
                $transactionParams = [
                    'transaction_details' => [
                        'order_id' => $midtransOrderId,
                        'gross_amount' => $total,
                    ],
                    'customer_details' => [
                        'first_name' => $order->name,
                        'phone' => $order->whatsapp,
                        'billing_address' => [
                            'address' => $order->address,
                        ],
                    ],
                    'item_details' => $items,
                    'callbacks' => [
                        'finish' => route('payment.finish'),
                        'unfinish' => route('payment.unfinish'),
                        'error' => route('payment.error')
                    ]
                ];

                // Update transaction_id di order
                $order->update([
                    'transaction_id' => $midtransOrderId
                ]);

                // Pastikan payment_type tidak null dengan default bank_transfer
                $paymentType = $request->input('payment_type', 'bank_transfer');

                // Log payment type untuk debugging
                Log::info('Processing payment with type: ' . $paymentType, [
                    'payment_type_from_form' => $request->payment_type,
                    'payment_type_used' => $paymentType
                ]);

                // Proses berdasarkan jenis pembayaran
                switch ($paymentType) {
                    case 'credit_card':
                        // Proses pembayaran kartu kredit dengan 3DS
                        $cardToken = $this->getCardToken($request);

                        if (!$cardToken) {
                            return redirect()->back()->with('error', 'Gagal mendapatkan token kartu kredit');
                        }

                        $transactionParams['credit_card'] = [
                            'token_id' => $cardToken,
                            'authentication' => true,
                        ];

                        $chargeResponse = \Midtrans\CoreApi::charge($transactionParams);

                        if (isset($chargeResponse->redirect_url)) {
                            // Simpan response data
                            $order->update([
                                'payment_details' => json_encode($chargeResponse),
                                'payment_url' => $chargeResponse->redirect_url
                            ]);

                            // Redirect ke halaman 3DS
                            return redirect($chargeResponse->redirect_url);
                        }
                        break;

                    case 'bank_transfer':
                        // Tambahkan detail bank transfer
                        $transactionParams['payment_type'] = 'bank_transfer';
                        $transactionParams['bank_transfer'] = [
                            'bank' => $request->bank ?? 'bca', // Default ke BCA jika tidak diisi
                        ];

                        $chargeResponse = \Midtrans\CoreApi::charge($transactionParams);

                        // Log response
                        Log::info('Midtrans bank transfer response', [
                            'response' => $chargeResponse
                        ]);

                        // Simpan response data
                        $order->update([
                            'payment_details' => json_encode($chargeResponse),
                            'payment_url' => route('thank-you', $order->id)
                        ]);

                        // Redirect ke halaman thank you
                        return redirect()->route('thank-you', $order->id);

                    case 'gopay':
                        // Tambahkan detail gopay
                        $transactionParams['payment_type'] = 'gopay';

                        $chargeResponse = \Midtrans\CoreApi::charge($transactionParams);

                        // Log response
                        Log::info('Midtrans GoPay response', [
                            'response' => $chargeResponse
                        ]);

                        // Simpan response data
                        $order->update([
                            'payment_details' => json_encode($chargeResponse),
                            'payment_url' => $chargeResponse->actions[0]->url ?? null
                        ]);

                        // Redirect ke QR Code GoPay
                        if (isset($chargeResponse->actions[0]->url)) {
                            return redirect($chargeResponse->actions[0]->url);
                        }

                        return redirect()->route('thank-you', $order->id);

                    default:
                        // Default ke bank transfer BCA jika payment_type tidak dikenali
                        Log::warning('Unrecognized payment type: ' . $paymentType . ', defaulting to bank_transfer BCA');

                        $transactionParams['payment_type'] = 'bank_transfer';
                        $transactionParams['bank_transfer'] = [
                            'bank' => 'bca',
                        ];

                        $chargeResponse = \Midtrans\CoreApi::charge($transactionParams);

                        // Simpan response data
                        $order->update([
                            'payment_details' => json_encode($chargeResponse),
                            'payment_url' => isset($chargeResponse->va_numbers[0]->bank) ? route('thank-you', $order->id) : null
                        ]);

                        // Redirect ke halaman thank you
                        return redirect()->route('thank-you', $order->id);
                }
            } catch (\Exception $e) {
                // Log error
                Log::error('Midtrans payment error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'order_id' => $order->id
                ]);

                // Redirect kembali dengan pesan error
                return redirect()->back()->with('error', 'Gagal memulai pembayaran dengan Midtrans. Silakan coba lagi atau pilih metode pembayaran lain. Error: ' . $e->getMessage());
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
        try {
            $cardNumber = str_replace(' ', '', $request->card_number);

            // Gunakan Midtrans API untuk mendapatkan token
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
        try {
            $notificationBody = json_decode($request->getContent(), true);
            $transactionStatus = $notificationBody['transaction_status'];
            $midtransOrderId = $notificationBody['order_id'];
            $fraudStatus = $notificationBody['fraud_status'] ?? null;

            $order = Order::where('no_order', $midtransOrderId)->first();

            if (!$order) {
                Log::error('Midtrans notification: Order not found with no_order: ' . $midtransOrderId);
                return response()->json(['status' => 'error', 'message' => 'Order not found']);
            }

            // Update status order berdasarkan status transaksi
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $order->status = 'completed';
                }
            } else if ($transactionStatus == 'settlement') {
                $order->status = 'processing';
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $order->status = 'failed';
            } else if ($transactionStatus == 'pending') {
                $order->status = 'pending';
            }

            $order->payment_details = json_encode($notificationBody);
            $order->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
        Log::error('Midtrans Notification Error: ' . $e->getMessage());
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

        // Check transaction status from Midtrans
        try {
            // Gunakan ID yang sama (no_order) untuk mengecek status
            $status = Transaction::status($midtransOrderId);

            // Update order status and payment details
            $order->update([
                'status' => $this->mapMidtransStatus($status->transaction_status ?? 'pending'),
                'payment_details' => json_encode($status)
            ]);

            // Redirect to thank you page menggunakan UUID
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

            if (!$order->transaction_id) {
                return redirect()->back()->with('error', 'ID Transaksi tidak ditemukan');
            }

            $status = Transaction::status($order->transaction_id);

            // Update status order
            $order->payment_details = json_encode($status);

            if ((is_object($status) && ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture')) ||
                (is_array($status) && isset($status['transaction_status']) && ($status['transaction_status'] == 'settlement' || $status['transaction_status'] == 'capture'))) {
                $order->status = 'processing';
            } else if (is_object($status) && $status->transaction_status == 'pending') {
                $order->status = 'pending';
            } else if (is_object($status) && in_array($status->transaction_status, ['cancel', 'deny', 'expire'])) {
                $order->status = 'failed';
            } else if (is_array($status) && isset($status['transaction_status'])) {
                if ($status['transaction_status'] == 'pending') {
                    $order->status = 'pending';
                } else if (in_array($status['transaction_status'], ['cancel', 'deny', 'expire'])) {
                    $order->status = 'failed';
                }
            }

            $order->save();

            return redirect()->route('thank-you', $order->id)->with('status', 'Status pembayaran berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Check Transaction Status Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memeriksa status transaksi: ' . $e->getMessage());
        }
    }
}


