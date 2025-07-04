<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function createTransaction(array $params);
    public function getTransactionStatus($transactionId);
    public function cancelTransaction($transactionId);
    public function notificationHandler(array $payload);
}