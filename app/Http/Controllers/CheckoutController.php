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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function generateQris(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1'
            ]);

            $amount = $request->input('amount');

            // Get active QRIS static code
            $qrisStatic = \App\Models\QrisStatic::where('is_active', true)->first();

            if (!$qrisStatic) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active QRIS configuration found. Please contact administrator.'
                ], 400);
            }

            // Parse merchant name from static QRIS
            $merchantName = $this->parseMerchantNameFromQris($qrisStatic->qris_string);

            // Generate dynamic QRIS
            $dynamicQris = $this->generateDynamicQrisString(
                $qrisStatic->qris_string,
                $amount,
                'Rupiah',
                '0'
            );

            // Generate QR code image
            $qrImagePath = $this->generateQrCodeImage($dynamicQris);

            // Save to database
            $qrisDynamic = \App\Models\QrisDynamic::create([
                'qris_static_id' => $qrisStatic->id,
                'merchant_name' => $merchantName,
                'qris_string' => $dynamicQris,
                'amount' => $amount,
                'fee_type' => 'Rupiah',
                'fee_value' => 0,
                'qr_image_path' => $qrImagePath,
                'created_by' => Auth::check() ? Auth::id() : null,
            ]);

            return response()->json([
                'success' => true,
                'qris_dynamic_id' => $qrisDynamic->id,
                'qr_image_url' => asset('storage/' . $qrImagePath),
                'amount_formatted' => number_format($amount, 0, ',', '.'),
                'merchant_name' => $merchantName
            ]);

        } catch (\Exception $e) {
            Log::error('QRIS Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QRIS: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function parseMerchantNameFromQris(string $qrisData): string
    {
        $tag = '59';
        $tagIndex = strpos($qrisData, $tag);

        if ($tagIndex === false) {
            return 'Merchant';
        }

        try {
            $lengthIndex = $tagIndex + strlen($tag);
            $lengthStr = substr($qrisData, $lengthIndex, 2);
            $length = intval($lengthStr);

            if ($length <= 0) {
                return 'Merchant';
            }

            $valueIndex = $lengthIndex + 2;
            $merchantName = substr($qrisData, $valueIndex, $length);

            return trim($merchantName) ?: 'Merchant';
        } catch (\Exception $e) {
            return 'Merchant';
        }
    }

    protected function generateDynamicQrisString(
        string $staticQris,
        string $amount,
        string $feeType,
        string $feeValue
    ): string {
        if (strlen($staticQris) < 4) {
            throw new \Exception('Invalid static QRIS data.');
        }

        // Remove CRC (last 4 characters)
        $qrisWithoutCrc = substr($staticQris, 0, -4);

        // Change from static (01) to dynamic (12)
        $step1 = str_replace('010211', '010212', $qrisWithoutCrc);

        // Split by merchant country code
        $parts = explode('5802ID', $step1);

        if (count($parts) !== 2) {
            throw new \Exception("QRIS data is not in the expected format (missing '5802ID').");
        }

        // Build amount tag
        $amountStr = strval(intval($amount));
        $amountTag = '54'.str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT).$amountStr;

        // Build fee tag if applicable
        $feeTag = '';
        if ($feeValue && floatval($feeValue) > 0) {
            if ($feeType === 'Rupiah') {
                $feeValueStr = strval(intval($feeValue));
                $feeTag = '55020256'.str_pad(strlen($feeValueStr), 2, '0', STR_PAD_LEFT).$feeValueStr;
            } else {
                $feeTag = '55020357'.str_pad(strlen($feeValue), 2, '0', STR_PAD_LEFT).$feeValue;
            }
        }

        // Reconstruct payload
        $payload = $parts[0].$amountTag.$feeTag.'5802ID'.$parts[1];

        // Calculate and append CRC
        $finalCrc = $this->calculateCrc16($payload);

        return $payload.$finalCrc;
    }

    protected function calculateCrc16(string $str): string
    {
        $crc = 0xFFFF;
        $strlen = strlen($str);

        for ($c = 0; $c < $strlen; $c++) {
            $crc ^= ord($str[$c]) << 8;
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
            }
        }

        $hex = strtoupper(dechex($crc & 0xFFFF));

        return str_pad($hex, 4, '0', STR_PAD_LEFT);
    }

    protected function generateQrCodeImage(string $qrisString): string
    {
        try {
            $builder = new \Endroid\QrCode\Builder\Builder(
                writer: new \Endroid\QrCode\Writer\PngWriter,
                writerOptions: [],
                validateResult: false,
                data: $qrisString,
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                size: 400,
                margin: 10,
            );

            $result = $builder->build();

            $filename = 'qris-checkout/qris-'.now()->format('YmdHis').'-'.uniqid().'.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $result->getString());

            return $filename;
        } catch (\Exception $e) {
            Log::error('QR Code Generation Error: ' . $e->getMessage());
            throw new \Exception('Failed to generate QR code image');
        }
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
        $order = Order::with(['orderProducts.product', 'paymentMethod', 'qrisDynamic'])
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
            'status' => 'pending',
            'qris_dynamic_id' => $request->input('qris_dynamic_id') ?: null // Store QRIS dynamic ID if provided, otherwise null
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
                
                // Cek jika ini adalah pembayaran GoPay yang sudah digenerate di frontend
                if ($paymentType === 'gopay' && $request->input('gopay_transaction_id') && $request->input('gopay_order_id')) {
                    // Gunakan Order ID yang sudah digenerate sebelumnya
                    $order->update(['no_order' => $request->input('gopay_order_id')]);
                    
                    // Ambil status transaksi dari Midtrans untuk mendapatkan detail pembayaran (QR Code dll)
                    $status = $gateway->getTransactionStatus($request->input('gopay_transaction_id'));
                    $chargeResponse = $status; // Gunakan status sebagai response
                    
                    Log::info('Using existing GoPay transaction', [
                        'transaction_id' => $request->input('gopay_transaction_id'),
                        'order_id' => $request->input('gopay_order_id')
                    ]);
                } else {
                    // Buat transaksi baru jika belum ada
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
                }

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

            // Return response sesuai format Midtrans notification
            return response()->json([
                'transaction_time' => $notification->transaction_time ?? now()->format('Y-m-d H:i:s'),
                'transaction_status' => $notification->transaction_status ?? 'unknown',
                'transaction_id' => $notification->transaction_id ?? null,
                'status_message' => 'midtrans payment notification',
                'status_code' => $notification->status_code ?? '200',
                'signature_key' => $notification->signature_key ?? null,
                'settlement_time' => $notification->settlement_time ?? null,
                'payment_type' => $notification->payment_type ?? null,
                'order_id' => $notification->order_id ?? null,
                'merchant_id' => $notification->merchant_id ?? null,
                'gross_amount' => $notification->gross_amount ?? $order->total_amount,
                'fraud_status' => $notification->fraud_status ?? null,
                'currency' => $notification->currency ?? 'IDR'
            ]);
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
            $order->status = $this->mapMidtransStatus($status->transaction_status ?? 'completed');
            $order->save();

            return redirect()->route('thank-you', $order->id)->with('status', 'Status pembayaran berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Check Transaction Status Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memeriksa status transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Generate GoPay QR Code via Midtrans
     */
    public function generateGopayQr(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'order_id' => 'required|string'
            ]);

            // Initialize Midtrans Config
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = (bool) config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $amount = $request->input('amount');
            $orderId = $request->input('order_id');

            // Prepare transaction parameters for GoPay
            $params = [
                'payment_type' => 'gopay',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount,
                ],
                'gopay' => [
                    'enable_callback' => true,
                    'callback_url' => route('checkout')
                ]
            ];

            // Charge transaction via Midtrans Core API
            $response = CoreApi::charge($params);

            Log::info('GoPay QR Response:', ['response' => $response]);

            // Extract QR code URL and deeplink from actions
            $qrCodeUrl = null;
            $deeplinkUrl = null;

            if (isset($response->actions) && is_array($response->actions)) {
                foreach ($response->actions as $action) {
                    if ($action->name === 'generate-qr-code') {
                        $qrCodeUrl = $action->url;
                    } elseif ($action->name === 'deeplink-redirect') {
                        $deeplinkUrl = $action->url;
                    }
                }
            }

            if (!$qrCodeUrl) {
                throw new \Exception('QR Code URL not found in response');
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $response->transaction_id,
                'qr_code_url' => $qrCodeUrl,
                'deeplink_url' => $deeplinkUrl,
                'amount_formatted' => number_format($amount, 0, ',', '.'),
                'status' => $response->transaction_status
            ]);

        } catch (\Exception $e) {
            Log::error('GoPay QR Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate GoPay QR: ' . $e->getMessage()
            ], 500);
        }
    }
}
