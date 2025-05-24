<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Register cache driver based on environment
        if ($this->app->environment('production')) {
            // Use Redis in production if available
            if (extension_loaded('redis')) {
                $this->app->singleton('cache', function ($app) {
                    return Cache::driver('redis');
                });
            }
        }
    }
}
