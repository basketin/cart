<?php

namespace Storephp\Cart\Providers;

use Illuminate\Support\ServiceProvider;
use Storephp\Cart\Services\CartService;

class StorePHPCartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('storephp.cart.cartservice', CartService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cart.php',
            'storephp.cart'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/cart.php' => config_path('storephp/cart.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
