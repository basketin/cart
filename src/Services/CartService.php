<?php

namespace Storephp\Cart\Services;

use Illuminate\Support\Str;
use Storephp\Cart\Exceptions\CartNotFoundException;
use Storephp\Cart\Models\Cart;
use Storephp\Cart\Repositories\CartRepository;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository,
        private Cart $cart
    ) {
    }

    public function initCart($ulid = null, $currency = 'USD')
    {
        $ulid = $ulid ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency);
        }

        $this->cart = $cart;

        return $this;

        // return new QuoteService($cart);
    }

    public function openCart($ulid)
    {
        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException();
        }

        $this->cart = $cart;

        return $this;

        // return new QuoteService($cart);
    }

    public function getUlid()
    {
        return $this->cart->ulid;
    }

    public function getCurrency()
    {
        return $this->cart->currency;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function quote()
    {
        return new QuoteService($this->cart);
    }

    public function totals()
    {
        return new TotalService($this->cart->quotes);
    }
}
