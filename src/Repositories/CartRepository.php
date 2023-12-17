<?php

namespace Basketin\Component\Cart\Repositories;

use Basketin\Component\Cart\Models\Cart;

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
