<?php

namespace Storephp\Cart\Repositories;

use Storephp\Cart\Models\Cart;

class CartRepository
{
    public function createNewCart($ulid = null, $currency = 'USD')
    {
        return Cart::create([
            'ulid' => $ulid,
            'currency' => $currency,
        ]);
    }

    public function getCartByUlid($ulid)
    {
        return Cart::whereUlid($ulid)->first();
    }
}
