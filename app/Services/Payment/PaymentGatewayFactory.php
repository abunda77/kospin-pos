<?php

namespace App\Services\Payment;

use Exception;

class PaymentGatewayFactory
{
    public function make(string $gateway): PaymentGatewayInterface
    {
        switch (strtolower($gateway)) {
            case 'midtrans':
                $config = config('services.midtrans');
                return new MidtransGateway($config);
            case 'xendit':
                $config = config('services.xendit');
                return new XenditGateway($config);
            // Tambahkan gateway lain sesuai kebutuhan
            default:
                throw new \InvalidArgumentException("Unsupported payment gateway [{$gateway}]");
        }
    }
}
