<?php

namespace Storephp\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class CartManagement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'storephp.cart.cartservice';
    }
}
