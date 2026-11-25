<?php

namespace Obelaw\Basketin\Cart\Tests\App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Obelaw\Basketin\Cart\Events\CartActionUpdated;

class BasketinCartTestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        Event::listen(CartActionUpdated::class, function ($event) {
            dd($event->eventId);
        });
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
