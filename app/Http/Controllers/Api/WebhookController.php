<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans webhook received', ['payload' => $payload]);

        // 1. Get the server key from config
        $serverKey = config('services.midtrans.server_key');

        // 2. Generate our own signature
        // The signature key is a hash of order_id, status_code, gross_amount, and server_key
        $mySignatureKey = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        // 3. Compare the signature key sent by Midtrans with our own
        if ($payload['signature_key'] !== $mySignatureKey) {
            Log::warning('Midtrans webhook: Invalid signature', [
                'received' => $payload['signature_key'],
                'expected' => $mySignatureKey,
                'order_id' => $payload['order_id'],
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature.',
            ], Response::HTTP_FORBIDDEN);
        }

        // 4. Find the order by order_id — coba beberapa strategi lookup
        $order = null;
        $orderId = $payload['order_id'];

        // Strategi 1: exact match on no_order (format: 000001)
        $order = Order::where('no_order', $orderId)->first();

        // Strategi 2: cari by UUID id (jika order_id dikirim sebagai UUID)
        if (!$order && \Illuminate\Support\Str::isUuid($orderId)) {
            $order = Order::find($orderId);
        }

        // Strategi 3: split by hyphen untuk backward compatibility (000001-123456)
        if (!$order) {
            $orderIdParts = explode('-', $orderId);
            if (count($orderIdParts) > 1) {
                $noOrder = $orderIdParts[0];
                $order = Order::where('no_order', $noOrder)->first();
            }
        }

        if (!$order) {
            Log::warning('Midtrans webhook: Order not found', [
                'order_id_from_midtrans' => $orderId,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        Log::info('Midtrans webhook: Order found', [
            'order_uuid' => $order->id,
            'no_order' => $order->no_order,
            'current_status' => $order->status,
        ]);

        // 5. Check transaction status and update order status accordingly
        $transactionStatus = $payload['transaction_status'];

        $newStatus = null;
        if ($transactionStatus === 'settlement' && $order->status !== 'completed') {
            $newStatus = 'completed'; // settlement = dana sudah masuk ke merchant, transaksi final
        } else if ($transactionStatus === 'capture' && $payload['fraud_status'] === 'accept') {
            $newStatus = 'completed'; // capture + fraud accept = pembayaran sukses
        } else if ($transactionStatus === 'expire') {
            $newStatus = 'expired';
        } else if ($transactionStatus === 'cancel' || $transactionStatus === 'deny') {
            $newStatus = 'cancelled';
        }

        if ($newStatus) {
            $order->status = $newStatus;
            $order->save();
            Log::info('Midtrans webhook: Order status updated', [
                'order_id' => $order->id,
                'no_order' => $order->no_order,
                'old_status_from_db' => $order->getOriginal('status'),
                'new_status' => $newStatus,
                'transaction_status' => $transactionStatus,
            ]);
        } else {
            Log::info('Midtrans webhook: No status change needed', [
                'order_id' => $order->id,
                'transaction_status' => $transactionStatus,
            ]);
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

