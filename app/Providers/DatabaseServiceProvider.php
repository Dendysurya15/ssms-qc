<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Database\Factories\PerumahanFactory;
use Database\Factories\LingkunganFactory;
use Database\Factories\LandscapeFactory;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PerumahanFactory::class, function ($app) {
            return PerumahanFactory::new();
        });
        $this->app->singleton(LandscapeFactory::class, function ($app) {
            return LandscapeFactory::new();
        });
        $this->app->singleton(LandscapeFactory::class, function ($app) {
            return LandscapeFactory::new();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
