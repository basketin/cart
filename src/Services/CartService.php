<?php

namespace Obelaw\Basketin\Cart\Services;

use Illuminate\Support\Str;
use Obelaw\Basketin\Cart\Cart;
use Obelaw\Basketin\Cart\Contracts\ICoupon;
use Obelaw\Basketin\Cart\Events\BasketinCreateCartEvent;
use Obelaw\Basketin\Cart\Exceptions\CartNotFoundException;
use Obelaw\Basketin\Cart\Repositories\CartRepository;
use Obelaw\Basketin\Cart\Services\FieldService;
use Obelaw\Basketin\Cart\Settings\Config;


class CartService
{
    public const SESSION_KEY = 'basketin_cart_ulid';

    private ?ICoupon $coupon = null;
    private $currentCart = null;
    private Config $config;

    /**
     * CartService constructor.
     */
    public function __construct(
        private CartRepository $cartRepository,
    ) {
        $this->config = new Config();
    }

    /**
     * Initialize a cart, create if not exists.
     */
    public function initCart(?string $ulid = null, string $currency = 'USD', ?string $cartType = null): self
    {
        $ulid = $ulid ?? session($cartType . '_' . self::SESSION_KEY, null) ?? (string) Str::ulid();

        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            $cart = $this->cartRepository->createNewCart($ulid, $currency, $cartType);
        }

        $this->currentCart = $cart;
        session([$cartType . '_' . self::SESSION_KEY => $this->getUlid()]);
        Cart::setCart($this);
        BasketinCreateCartEvent::dispatch($this->getUlid());
        return $this;
    }

    /**
     * Open an existing cart.
     */
    public function openCart(?string $ulid = null, ?string $cartType = null): self
    {
        $ulid = $ulid ?? session($cartType . '_' . self::SESSION_KEY, null);
        if (!$cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException();
        }
        $this->currentCart = $cart;
        Cart::setCart($this);
        return $this;
    }

    /**
     * Set config object.
     */
    public function config(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get config object.
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get cart ULID.
     */
    public function getUlid(): string
    {
        return $this->currentCart->ulid;
    }

    /**
     * Get cart currency.
     */
    public function getCurrency(): string
    {
        return $this->currentCart->currency;
    }

    /**
     * Get cart type.
     */
    public function getType(): ?string
    {
        return $this->currentCart->cart_type;
    }

    /**
     * Get cart model.
     */
    public function getCart()
    {
        return $this->currentCart;
    }

    /**
     * Get count of products in cart.
     */
    public function getCountProducts(): int
    {
        return $this->currentCart->quotes()->count();
    }

    /**
     * Get count of items in cart.
     */
    public function getCountItems(): int
    {
        return $this->currentCart->quotes()->sum('quantity');
    }

    /**
     * Get quote service.
     */
    public function quote(): QuoteService
    {
        return new QuoteService($this, $this->currentCart, $this->config);
    }

    /**
     * Get field service.
     */
    public function fields(): FieldService
    {
        return new FieldService($this->currentCart);
    }

    /**
     * Set coupon for cart.
     */
    public function coupon(ICoupon $coupon): self
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     * Get coupon info.
     */
    public function couponInfo(): ?ICoupon
    {
        return $this->coupon;
    }

    /**
     * Get totals service.
     */
    public function totals(): TotalService
    {
        return new TotalService($this->currentCart->quotes, $this->coupon);
    }

    /**
     * Prepare order for cart.
     */
    public function preparingOrder()
    {
        $order = $this->currentCart->order()->first();
        if (!$order) {
            $order = $this->currentCart->order()->create();
        }
        $this->fields()->set('order_reference', $order->reference);
        return $order;
    }

    /**
     * Sync order with cart.
     */
    public function syncOrder($order): void
    {
        $preparingOrder = $this->preparingOrder();
        $order->cartOrder()->save($preparingOrder);
    }

    /**
     * Checkout cart.
     */
    public function checkoutIt(?string $cartType = null): bool
    {
        $updated = $this->getCart()->update([
            'status' => 'checkout'
        ]);
        if ($updated) {
            session()->forget($cartType . '_' . self::SESSION_KEY);
            return true;
        }
        return false;
    }
}
