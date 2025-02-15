<?php

namespace Basketin\Component\Cart\Repositories;

use Basketin\Component\Cart\Models\Cart;

class CartRepository
{
    public function createNewCart($ulid = null, $currency = 'USD', $cartType = null)
    {
        return Cart::create([
            'ulid' => $ulid,
            'cart_type' => $cartType,
            'currency' => $currency,
        ]);
    }

    public function getCartByUlid($ulid)
    {
        return Cart::whereUlid($ulid)->first();
    }
}
