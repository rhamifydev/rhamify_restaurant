<?php

namespace App\Services;

use App\Models\Setting;
use Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService {

    function getSettings() {
        if (! Schema::hasTable('settings')) {
            return [];
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

}
