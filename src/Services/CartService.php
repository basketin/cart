<?php

namespace Basketin\Component\Cart\Services;

use Basketin\Component\Cart\Cart;
use Basketin\Component\Cart\Contracts\ICoupon;
use Basketin\Component\Cart\Events\BasketinCreateCartEvent;
use Basketin\Component\Cart\Exceptions\CartNotFoundException;
use Basketin\Component\Cart\Repositories\CartRepository;
use Basketin\Component\Cart\Services\FieldService;
use Illuminate\Support\Str;

class CartService
{
    const SESSION_KEY = 'basketin_cart_ulid';

    private $coupon = null;
    private $currentCart = null;

    public function __construct(
        private CartRepository $cartRepository,
    ) {}

    public function initCart($ulid = null, $currency = 'USD', $cartType = null)
    {
        $ulid = $ulid ?? session($cartType . '_' . self::SESSION_KEY, null) ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency, $cartType);
        }

        $this->currentCart = $cart;

        session([$cartType . '_' . self::SESSION_KEY => $this->getUlid()]);

        Cart::setCart($this);

        BasketinCreateCartEvent::dispatch($this);

        return $this;
    }

    public function openCart($ulid = null, $cartType = null)
    {
        $ulid = $ulid ?? session($cartType . '_' . self::SESSION_KEY, null);

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException();
        }

        $this->currentCart = $cart;

        Cart::setCart($this);

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

    public function getType()
    {
        return $this->currentCart->cart_type;
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
        return new QuoteService($this, $this->currentCart);
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

    public function checkoutIt($cartType = null)
    {
        $updated = $this->getCart()->update([
            'status' => 'checkout'
        ]);

        if ($updated) {
            session()->forget($cartType . '_' . self::SESSION_KEY);
            return true;
        }
    }
}
