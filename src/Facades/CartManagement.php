<?php

namespace Obelaw\Basketin\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class CartManagement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'basketin.cart.cartservice';
    }
}
