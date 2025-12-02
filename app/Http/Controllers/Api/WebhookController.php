<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Get the server key from config
        $serverKey = config('services.midtrans.server_key');

        // 2. Generate our own signature
        // The signature key is a hash of order_id, status_code, gross_amount, and server_key
        $mySignatureKey = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        // 3. Compare the signature key sent by Midtrans with our own
        if ($payload['signature_key'] !== $mySignatureKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature.',
            ], Response::HTTP_FORBIDDEN);
        }

        // 4. Find the order by order_id
        // 4. Find the order by order_id
        // Coba cari dengan exact match terlebih dahulu (untuk format 'ORDER-123...')
        $order = Order::where('no_order', $payload['order_id'])->first();

        // Jika tidak ketemu, coba logic lama (split by hyphen) untuk backward compatibility
        // atau jika formatnya adalah '{no_order}-{timestamp}'
        if (!$order) {
            $orderIdParts = explode('-', $payload['order_id']);
            if (count($orderIdParts) > 1) {
                $noOrder = $orderIdParts[0];
                $order = Order::where('no_order', $noOrder)->first();
            }
        }

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        // 5. Check transaction status and update order status accordingly
        $transactionStatus = $payload['transaction_status'];

        if ($transactionStatus === 'settlement' && $order->status !== 'completed') {
            $order->status = 'processing'; // or 'paid' or 'completed', depending on your flow
            $order->save();
        } else if ($transactionStatus === 'capture' && $payload['fraud_status'] === 'accept') {
            $order->status = 'processing';
            $order->save();
        } else if ($transactionStatus === 'expire') {
            $order->status = 'expired';
            $order->save();
        } else if ($transactionStatus === 'cancel' || $transactionStatus === 'deny') {
            $order->status = 'cancelled';
            $order->save();
        }

        // Return response sesuai format Midtrans notification
        return response()->json([
            'transaction_time' => $payload['transaction_time'] ?? now()->format('Y-m-d H:i:s'),
            'transaction_status' => $payload['transaction_status'] ?? 'unknown',
            'transaction_id' => $payload['transaction_id'] ?? null,
            'status_message' => 'midtrans payment notification',
            'status_code' => $payload['status_code'] ?? '200',
            'signature_key' => $payload['signature_key'] ?? null,
            'settlement_time' => $payload['settlement_time'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
            'order_id' => $payload['order_id'] ?? null,
            'merchant_id' => $payload['merchant_id'] ?? null,
            'gross_amount' => $payload['gross_amount'] ?? $order->total_amount,
            'fraud_status' => $payload['fraud_status'] ?? null,
            'currency' => $payload['currency'] ?? 'IDR'
        ]);
    }
}

