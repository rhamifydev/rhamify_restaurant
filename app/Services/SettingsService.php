<?php

namespace App\Services;

use App\Models\Setting;
use Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class SettingsService {

    function getSettings() {
        try {
            if (! Schema::hasTable('settings')) {
                return [];
            }
        } catch (QueryException) {
            return [];
        }

        if (! $this->cacheTableIsReady()) {
            return Setting::pluck('value', 'key')->toArray(); // ['key' => 'value']
        }

        return Cache::rememberForever('settings', function(){
            return Setting::pluck('value', 'key')->toArray(); // ['key' => 'value']
        });
    }

    function setGlobalSettings() : void {
        $settings = $this->getSettings();
        config()->set('settings', $settings);
    }

    function clearCachedSettings() : void {
        Cache::forget('settings');
    }

    private function cacheTableIsReady(): bool
    {
        return config('cache.default') !== 'database' || Schema::hasTable(config('cache.stores.database.table', 'cache'));
    }

}
