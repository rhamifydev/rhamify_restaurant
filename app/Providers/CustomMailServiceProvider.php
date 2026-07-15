<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CustomMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $mailSetting = Cache::rememberForever('mail_settings', function(){
            $key = [
                'mail_driver',
                'mail_encryption',
                'mail_from_address',
                'mail_host',
                'mail_password',
                'mail_port',
                'mail_receive_address',
                'mail_username'
            ];
            
            return Setting::whereIn('key', $key)->pluck('value', 'key')->toArray();
        });

        if($mailSetting) {
            Config::set('mail.mailers.smtp.host', $mailSetting['mail_host'] ?? config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port', $mailSetting['mail_port'] ?? config('mail.mailers.smtp.port'));
            Config::set('mail.mailers.smtp.encryption', $mailSetting['mail_encryption'] ?? config('mail.mailers.smtp.encryption'));
            Config::set('mail.mailers.smtp.username', $mailSetting['mail_username'] ?? config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $mailSetting['mail_password'] ?? config('mail.mailers.smtp.password'));
            Config::set('mail.from.address', $mailSetting['mail_from_address'] ?? config('mail.from.address'));
        }
    }
}
