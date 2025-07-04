<?php

namespace App\Services\Payment;

use Exception;

class PaymentGatewayFactory
{
    public function make($gateway, $config = [])
    {
        switch ($gateway) {
            case 'midtrans':
                return new MidtransGateway($config);
            case 'xendit':
                return new XenditGateway($config);
            // Tambahkan gateway lain sesuai kebutuhan
            default:
                throw new Exception("Payment gateway tidak didukung: {$gateway}");
        }
    }
}