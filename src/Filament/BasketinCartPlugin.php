<?php

namespace Basketin\Component\Cart\Filament;

use Basketin\Component\Cart\Filament\Resources\CartResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class BasketinCartPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'basketin-cart';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                CartResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
