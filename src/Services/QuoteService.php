<?php

namespace Obelaw\Basketin\Cart\Services;

use Obelaw\Basketin\Cart\Contracts\IQuote;
use Obelaw\Basketin\Cart\Events\BasketinAddedQuoteEvent;
use Obelaw\Basketin\Cart\Events\BasketinDecreaseQuoteEvent;
use Obelaw\Basketin\Cart\Events\BasketinIncreaseQuoteEvent;
use Obelaw\Basketin\Cart\Events\BasketinRemoveQuoteEvent;
use Obelaw\Basketin\Cart\Exceptions\QuoteNotFoundException;
use Obelaw\Basketin\Cart\Exceptions\QuoteQuantityLimitException;
use Obelaw\Basketin\Cart\Models\Cart;
use Obelaw\Basketin\Cart\Settings\Config;


class QuoteService
{
    /**
     * QuoteService constructor.
     */
    public function __construct(
        private CartService $cartService,
        private Cart $cart,
        private Config $config
    ) {}

    /**
     * Add a quote to the cart.
     */
    public function addQuote(IQuote $item, int $quantity = 1): self
    {
        if ($item->quote()->where('cart_id', $this->cart->id)->first()) {
            $this->increaseQuote($item, $quantity);
            return $this;
        }

        if ($quantity > $this->config->get('limit_quote')) {
            throw new QuoteQuantityLimitException;
        }

        $item->quote()->create([
            'cart_id' => $this->cart->id,
            'quantity' => $quantity,
        ]);
        BasketinAddedQuoteEvent::dispatch($this->cartService->getUlid(), $item, $quantity);
        return $this;
    }

    /**
     * Increase the quantity of a quote.
     */
    public function increaseQuote(IQuote $item, int $quantity = 1): self
    {
        $existing = $item->quote()->where('cart_id', $this->cart->id)->first();
        if (!$existing) {
            throw new QuoteNotFoundException;
        }
        if (($existing->quantity + $quantity) > $this->config->get('limit_quote')) {
            throw new QuoteQuantityLimitException;
        }
        $existing->increment('quantity', $quantity);
        BasketinIncreaseQuoteEvent::dispatch($this->cartService->getUlid(), $item, $quantity);
        return $this;
    }

    /**
     * Decrease the quantity of a quote.
     */
    public function decreaseQuote(IQuote $item, int $quantity = 1): bool|self
    {
        $_item = $item->quote()->where('cart_id', $this->cart->id)->first();
        if (!$_item) {
            throw new QuoteNotFoundException;
        }
        if ($_item->quantity <= $quantity) {
            $_item->delete();
            return false;
        }
        $_item->decrement('quantity', $quantity);
        BasketinDecreaseQuoteEvent::dispatch($this->cartService->getUlid(), $item, $quantity);
        return $this;
    }

    /**
     * Check if a quote exists for the item.
     */
    public function hasQuote(IQuote $item): bool
    {
        return $item->quote()->where('cart_id', $this->cart->id)->exists();
    }

    /**
     * Remove a quote from the cart.
     */
    public function removeQuote(IQuote $item): self
    {
        $_item = $item->quote()->where('cart_id', $this->cart->id)->first();
        if (!$_item) {
            throw new QuoteNotFoundException;
        }
        $_item->delete();
        BasketinRemoveQuoteEvent::dispatch($this->cartService->getUlid(), $item);
        return $this;
    }

    /**
     * Get all quotes for the cart.
     */
    public function getQuotes()
    {
        return $this->cart->quotes;
    }

    /**
     * Get a single quote for an item.
     */
    public function getQuote(IQuote $item)
    {
        return $item->quote()->where('cart_id', $this->cart->id)->first();
    }
}
