<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use Midtrans\Notification;

class MidtransGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->init();
    }

    protected function init()
    {
        Config::$serverKey = $this->config['server_key'];
        Config::$isProduction = (bool) $this->config['is_production'];
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(array $params)
    {
        try {
            return CoreApi::charge($params);
        } catch (\Exception $e) {
            Log::error('Midtrans payment error: ' . $e->getMessage(), [
                'exception' => $e,
                'params' => $params
            ]);
            throw $e;
        }
    }

    public function getTransactionStatus($transactionId)
    {
        try {
            return Transaction::status($transactionId);
        } catch (\Exception $e) {
            Log::error('Error fetching transaction status from Midtrans: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            throw $e;
        }
    }

    public function cancelTransaction($transactionId)
    {
        try {
            return Transaction::cancel($transactionId);
        } catch (\Exception $e) {
            Log::error('Error cancelling transaction on Midtrans: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            throw $e;
        }
    }

    public function notificationHandler(array $payload)
    {
        return new Notification();
    }
}
