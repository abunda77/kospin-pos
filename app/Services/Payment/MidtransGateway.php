<?php

namespace App\Services\Payment;

class MidtransGateway implements PaymentGatewayInterface
{
    protected $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        // Inisialisasi Midtrans SDK
    }
    
    public function createTransaction(array $params)
    {
        // Implementasi Midtrans untuk membuat transaksi
        // Return payment URL, token, dll
    }
    
    public function getTransactionStatus($transactionId)
    {
        // Implementasi cek status transaksi Midtrans
    }
    
    public function cancelTransaction($transactionId)
    {
        // Implementasi pembatalan transaksi Midtrans
    }
    
    public function notificationHandler(array $payload)
    {
        // Handle callback/webhook dari Midtrans
    }
}