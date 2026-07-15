<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function(){
            return new SettingsService();
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

        $settingsService = $this->app->make(SettingsService::class);
        $settingsService->setGlobalSettings();
    }
}
