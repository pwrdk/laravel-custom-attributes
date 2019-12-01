<?php

namespace PWRDK\CustomAttributes;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class CustomAttributesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/customattributes.php' => config_path('customattributes.php')
        ], 'config');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
