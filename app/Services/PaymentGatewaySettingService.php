<?php

namespace App\Services;

use App\Models\PaymentGatewaySetting;
use Cache;
use Illuminate\Support\Facades\Schema;

class PaymentGatewaySettingService {

    function getSettings() {
        if (! Schema::hasTable('payment_gateway_settings')) {
            return [];
        }

        return Cache::rememberForever('gatewaySettings', function(){
            return PaymentGatewaySetting::pluck('value', 'key')->toArray(); // ['key' => 'value']
        });
    }

    function setGlobalSettings() : void {
        $settings = $this->getSettings();
        config()->set('gatewaySettings', $settings);
    }

    function clearCachedSettings() : void {
        Cache::forget('gatewaySettings');
    }

}
