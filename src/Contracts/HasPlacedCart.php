<?php

namespace Obelaw\Basketin\Cart\Contracts;

use Obelaw\Basketin\Cart\Services\CartManager;

interface HasPlacedCart
{
    public function placedCart(CartManager $cart): void;
}
