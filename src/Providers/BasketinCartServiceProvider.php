<?php

namespace Basketin\Component\Cart\Providers;

use Illuminate\Support\ServiceProvider;
use Basketin\Component\Cart\Services\CartService;

class BasketinCartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('basketin.cart.cartservice', CartService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cart.php',
            'basketin.cart'
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
                __DIR__ . '/../../config/cart.php' => config_path('basketin/cart.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
