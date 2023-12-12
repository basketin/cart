<?php

namespace Storephp\Cart\Services;

use Illuminate\Support\Str;
use Storephp\Cart\Contracts\ICoupon;
use Storephp\Cart\Exceptions\CartNotFoundException;
use Storephp\Cart\Models\Cart;
use Storephp\Cart\Repositories\CartRepository;
use Storephp\Cart\Services\FieldService;

class CartService
{
    private $coupon = null;

    public function __construct(
        private CartRepository $cartRepository,
        private Cart $cart
    ) {
        $this->fields = $cart->fields;
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

    public function getCountProducts()
    {
        return $this->cart->quotes()->count();
    }

    public function getCountItems()
    {
        return $this->cart->quotes()->sum('quantity');
    }

    public function quote()
    {
        return new QuoteService($this->cart);
    }

    public function fields()
    {
        return new FieldService($this->cart);
    }

    public function coupon(ICoupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function couponInfo()
    {
        return $this->coupon;
    }

    public function totals()
    {
        return new TotalService($this->cart->quotes, $this->coupon);
    }
}
