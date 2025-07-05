<?php

namespace App\Services\Payment;

use Illuminate\Support\ServiceProvider;
use App\Models\PaymentMethod;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            return new PaymentGatewayFactory();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
