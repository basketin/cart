<?php

namespace Obelaw\Basketin\Cart\Services;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Obelaw\Basketin\Cart\Cart;
use Obelaw\Basketin\Cart\Contracts\HasCheckoutHandler;
use Obelaw\Basketin\Cart\Contracts\HasPlacedCart;
use Obelaw\Basketin\Cart\Exceptions\CartNotFoundException;
use Obelaw\Basketin\Cart\Exceptions\InvalidCheckoutHandlerException;
use Obelaw\Basketin\Cart\Models\Cart as CartModel;
use Obelaw\Basketin\Cart\Repositories\CartRepository;
use Obelaw\Basketin\Cart\Settings\Config;

class CartManager
{
    use Macroable;

    public const SESSION_KEY = 'basketin_cart_ulid';

    private $currentCart = null;

    private Config $config;

    /**
     * CartManager constructor.
     */
    public function __construct(
        private CartRepository $cartRepository,
    ) {
        $this->config = new Config;
    }

    /**
     * Make cart instance.
     *
     * When $createIfNotExists is true, this behaves like the old initCart method.
     * When false, this behaves like the old openCart method.
     */
    public static function make(?string $ulid = null, string $currency = 'USD', ?string $cartType = null, bool $createIfNotExists = true): self
    {
        if (static::class === self::class && function_exists('app')) {
            $service = app('basketin.cart.cartservice');

            if ($service instanceof self) {
                return $service->buildCart($ulid, $currency, $cartType, $createIfNotExists);
            }
        }

        $service = new static(new CartRepository);

        return $service->buildCart($ulid, $currency, $cartType, $createIfNotExists);
    }

    protected function buildCart(?string $ulid = null, string $currency = 'USD', ?string $cartType = null, bool $createIfNotExists = true): self
    {
        $sessionKey = $cartType.'_'.self::SESSION_KEY;

        if ($createIfNotExists) {
            $ulid = $ulid ?? session($sessionKey, null) ?? (string) Str::ulid();
            if (! $cart = $this->cartRepository->getCartByUlid($ulid)) {
                $cart = $this->cartRepository->createNewCart($ulid, $currency, $cartType);
            }

            $this->currentCart = $cart;
            session([$sessionKey => $this->getUlid()]);
            Cart::setCart($this);

            return $this;
        }

        $ulid = $ulid ?? session($sessionKey, null);
        if (! $cart = $this->cartRepository->getCartByUlid($ulid)) {
            throw new CartNotFoundException;
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
    public function quote(): QuoteManager
    {
        return new QuoteManager($this, $this->currentCart, $this->config);
    }

    /**
     * Get totals service.
     */
    public function totals(): TotalManager
    {
        return new TotalManager($this, $this->currentCart->refresh()->quotes);
    }

    /**
     * Checkout cart.
     */
    public function checkoutIt(?string $cartType = null, callable|array|string|object|null $action = null): bool
    {
        $updated = $this->getCart()->update([
            'status' => 'checkout',
        ]);
        if ($updated) {
            session()->forget($cartType.'_'.self::SESSION_KEY);

            $actionToCall = $action;

            if (! $actionToCall) {
                if ($this instanceof HasPlacedCart || method_exists($this, 'placedCart')) {
                    $actionToCall = [$this, 'placedCart'];
                } elseif ($this instanceof HasCheckoutHandler || method_exists($this, 'handle')) {
                    $actionToCall = [$this, 'handle'];
                }
            }

            if (! $actionToCall && function_exists('config')) {
                $actionToCall = config('basketin.cart.checkout_handler');
            }

            if ($actionToCall) {
                $this->callCheckoutAction($actionToCall);
            }

            return true;
        }

        return false;
    }

    /**
     * Call checkout action from class or callable.
     */
    private function callCheckoutAction(callable|array|string|object $action): mixed
    {
        if ($action instanceof Closure) {
            return $action($this, $this->getCart());
        }

        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;
            $instance = is_string($class) ? (function_exists('app') ? app($class) : new $class) : $class;
            $action = [$instance, $method];
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$class, $method] = explode('@', $action, 2);
            $instance = function_exists('app') ? app($class) : new $class;
            $action = [$instance, $method];
        } else {
            if (is_string($action) && ! class_exists($action) && (! function_exists('app') || ! app()->has($action))) {
                throw new InvalidCheckoutHandlerException(
                    sprintf('Checkout handler [%s] not found.', $action)
                );
            }

            $instance = is_string($action) ? (function_exists('app') ? app($action) : new $action) : $action;

            if (is_object($instance)) {
                if (method_exists($instance, 'placedCart')) {
                    $action = [$instance, 'placedCart'];
                } elseif (method_exists($instance, 'handle')) {
                    $action = [$instance, 'handle'];
                } elseif (is_callable($instance)) {
                    $action = $instance;
                } else {
                    throw new InvalidCheckoutHandlerException(
                        sprintf('Checkout handler class [%s] must implement "placedCart", "handle", or be invokable.', get_class($instance))
                    );
                }
            }
        }

        if (function_exists('app')) {
            return app()->call($action, [
                'cart' => $this,
                'cartModel' => $this->getCart(),
                self::class => $this,
                static::class => $this,
                CartModel::class => $this->getCart(),
            ]);
        }

        if (is_callable($action)) {
            return $action($this, $this->getCart());
        }

        return null;
    }
}
