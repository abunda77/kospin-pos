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
        // Ekstrak ID order dari format '{no_order}-{timestamp}'
        $orderIdParts = explode('-', $payload['order_id']);
        $noOrder = $orderIdParts[0];

        $order = Order::where('no_order', $noOrder)->first();

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

        return response()->json(['status' => 'success']);
    }
}

