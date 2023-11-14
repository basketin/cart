<?php

namespace Storephp\Cart\Services;

use Illuminate\Support\Str;
use Storephp\Cart\Repositories\CartRepository;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
    }

    public function InitCart($ulid = null, $currency = 'USD')
    {
        $ulid = $ulid ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency);
        }

        return new QuoteService($cart);
    }
}
