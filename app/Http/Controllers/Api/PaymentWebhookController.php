<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentMethod;

class PaymentWebhookController extends Controller
{
    public function handleWebhook(Request $request, $gateway)
    {
        // Validasi signature webhook jika diperlukan

        $factory = app('payment.factory');

        try {
            $gatewayInstance = $factory->make($gateway);
            $result = $gatewayInstance->notificationHandler($request->all());

            // Update status order berdasarkan notifikasi
            $order = Order::where('transaction_id', $result['transaction_id'])->first();

            if ($order) {
                $order->update([
                    'status' => $this->mapPaymentStatus($result['transaction_status']),
                    'payment_details' => $result
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function mapPaymentStatus($gatewayStatus)
    {
        // Map status dari payment gateway ke status aplikasi
        $statusMap = [
            'settlement' => 'processing',
            'capture' => 'processing',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired',
            // Tambahkan mapping lainnya
        ];

        return $statusMap[$gatewayStatus] ?? 'pending';
    }
}