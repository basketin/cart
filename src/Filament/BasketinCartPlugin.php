<?php

namespace Obelaw\Basketin\Cart\Filament;

use Obelaw\Basketin\Cart\Filament\Resources\CartResource;
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
