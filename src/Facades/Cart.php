<?php

namespace Obelaw\Basketin\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'basketin.cart.cartservice';
    }
}
