<?php

namespace App\Providers;

use App\Services\PaymentGatewaySettingService;
use Illuminate\Support\ServiceProvider;

class PaymentGatewaySettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewaySettingService::class, function(){
            return new PaymentGatewaySettingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && in_array($_SERVER['argv'][1] ?? null, ['package:discover'])) {
            return;
        }

        $paymentGatewaySetting = $this->app->make(PaymentGatewaySettingService::class);
        $paymentGatewaySetting->setGlobalSettings();
    }
}
