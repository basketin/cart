<?php

namespace Obelaw\Basketin\Cart\Contracts;

use Obelaw\Basketin\Cart\Services\CartManager;

interface HasCheckoutHandler
{
    public function handle(CartManager $cart): void;
}
