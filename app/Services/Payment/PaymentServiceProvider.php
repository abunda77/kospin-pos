<?php

namespace App\Services\Payment;

use Illuminate\Support\ServiceProvider;
use App\Models\PaymentMethod;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('payment.factory', function ($app) {
            return new PaymentGatewayFactory();
        });
    }
}