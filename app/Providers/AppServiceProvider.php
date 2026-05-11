<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Avoid fatal errors during app bootstrap when PDO (or DB) is unavailable
        try {
            if (extension_loaded('pdo')) {
                Schema::defaultStringLength(191);
            }
        } catch (\Throwable $e) {
            // Ignore: this allows the app to boot for static pages or maintenance
        }
    }
}
