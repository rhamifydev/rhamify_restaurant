<?php

namespace App\Services;

use App\Models\PaymentGatewaySetting;
use Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class PaymentGatewaySettingService {

    function getSettings() {
        try {
            if (! Schema::hasTable('payment_gateway_settings')) {
                return [];
            }
        } catch (QueryException) {
            return [];
        }

        if (! $this->cacheTableIsReady()) {
            return PaymentGatewaySetting::pluck('value', 'key')->toArray(); // ['key' => 'value']
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

    private function cacheTableIsReady(): bool
    {
        return config('cache.default') !== 'database' || Schema::hasTable(config('cache.stores.database.table', 'cache'));
    }

}
