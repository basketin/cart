<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Repositories\CartRepository;
use Obelaw\Basketin\Cart\Services\CartService;
use Obelaw\Basketin\Cart\Services\TotalService;

class CartBase extends CartService
{
    public static function make(?string $ulid = null, string $currency = 'USD', ?string $cartType = null): static
    {
        $cart = new static(new CartRepository());
        $cart->initCart($ulid, $currency, $cartType);

        return $cart;
    }

    public function totals(): TotalService
    {
        $totals = parent::totals();

        if ($this instanceof HasManageTotals) {
            $this->manageTotals($totals);
        }

        return $totals;
    }
}
