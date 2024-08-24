<?php

namespace Basketin\Component\Cart\Services;

use Illuminate\Support\Str;
use Basketin\Component\Cart\Contracts\ICoupon;
use Basketin\Component\Cart\Exceptions\CartNotFoundException;
use Basketin\Component\Cart\Repositories\CartRepository;
use Basketin\Component\Cart\Services\FieldService;

class CartService
{
    const SESSION_KEY = 'basketin_cart_ulid';

    private $coupon = null;
    private $currentCart = null;

    public function __construct(
        private CartRepository $cartRepository,
    ) {}

    public function initCart($ulid = null, $currency = 'USD')
    {
        $ulid = $ulid ?? session(self::SESSION_KEY, null) ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency);
        }

        $this->currentCart = $cart;

        session([self::SESSION_KEY => $this->getUlid()]);

        return $this;
    }

    public function openCart($ulid = null)
    {
        $ulid = $ulid ?? session(self::SESSION_KEY, null);

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException();
        }

        $this->currentCart = $cart;

        return $this;
    }

    public function getUlid()
    {
        return $this->currentCart->ulid;
    }

    public function getCurrency()
    {
        return $this->currentCart->currency;
    }

    public function getCart()
    {
        return $this->currentCart;
    }

    public function getCountProducts()
    {
        return $this->currentCart->quotes()->count();
    }

    public function getCountItems()
    {
        return $this->currentCart->quotes()->sum('quantity');
    }

    public function quote()
    {
        return new QuoteService($this->currentCart);
    }

    public function fields()
    {
        return new FieldService($this->currentCart);
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
        return new TotalService($this->currentCart->quotes, $this->coupon);
    }

    public function preparingOrder()
    {
        if (!$order = $this->currentCart->order()->first())
            $order = $this->currentCart->order()->create();

        $this->fields()->set('order_reference', $order->reference);

        return $order;
    }

    public function syncOrder($order)
    {
        $preparingOrder = $this->preparingOrder();
        $order->cartOrder()->save($preparingOrder);
    }

    public function checkoutIt()
    {
        $updated = $this->getCart()->update([
            'status' => 'checkout'
        ]);

        if ($updated) {
            session()->forget(self::SESSION_KEY);
            return true;
        }
    }
}
