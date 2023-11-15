<?php

namespace Storephp\Cart\Services;

use Illuminate\Support\Str;
use Storephp\Cart\Exceptions\CartNotFoundException;
use Storephp\Cart\Repositories\CartRepository;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
    }

    public function initCart($ulid = null, $currency = 'USD')
    {
        $ulid = $ulid ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency);
        }

        return new QuoteService($cart);
    }

    public function openCart($ulid)
    {
        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException();
        }

        return new QuoteService($cart);
    }
}
