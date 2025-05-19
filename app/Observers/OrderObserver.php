<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->sendWebhook($order);
    }

    /**
     * Send webhook notification
     */
    private function sendWebhook(Order $order): void
    {
        $webhookUrl = env('WEBHOOK_URL');
        
        if (!$webhookUrl) {
            return;
        }

        // Load related models
        $order->load(['paymentMethod', 'user']);

        $orderData = $order->toArray();
        
        // Replace IDs with names
        if (isset($orderData['payment_method_id'])) {
            $orderData['payment_method_name'] = $order->paymentMethod?->name;
            unset($orderData['payment_method_id']);
        }
        
        if (isset($orderData['user_id'])) {
            $orderData['user_name'] = $order->user?->name;
            unset($orderData['user_id']);
        }

        $data = [
            'event' => 'order.created',
            'order' => $orderData,
            'timestamp' => now()->toISOString(),
        ];

        try {
            Http::post($webhookUrl, $data);
        } catch (\Exception $e) {
            // Log the error if needed
            // \Log::error('Webhook failed: ' . $e->getMessage());
        }
    }
}
