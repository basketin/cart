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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
