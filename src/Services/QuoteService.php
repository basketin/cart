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
use Obelaw\Basketin\Cart\Settings\QuoteQuantityLimit;

class QuoteService
{
    public function __construct(
        private CartService $cartService,
        private Cart $cart
    ) {}

    public function addQuote(IQuote $item, $quantity = 1)
    {
        if ($item->quote()->where('cart_id', $this->cart->id)->first()) {
            $this->increaseQuote($item, $quantity);
            return $this;
        }

        $item->quote()->create([
            'cart_id' => $this->cart->id,
            'quantity' => $quantity,
        ]);

        BasketinAddedQuoteEvent::dispatch($this->cartService->getUlid(), $item, $quantity);

        return $this;
    }

    public function increaseQuote(IQuote $item, $quantity = 1)
    {
        if (!$_item = $item->quote()->where('cart_id', $this->cart->id)->first()) {
            throw new QuoteNotFoundException;
        }

        if ($_item->quantity >= QuoteQuantityLimit::getLimit()) {
            throw new QuoteQuantityLimitException;
        }

        $_item->increment('quantity', $quantity);

        BasketinIncreaseQuoteEvent::dispatch($this->cartService->getUlid(), $item, $quantity);

        return $this;
    }

    public function decreaseQuote(IQuote $item, $quantity = 1)
    {
        if (!$_item = $item->quote()->where('cart_id', $this->cart->id)->first()) {
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

    public function hasQuote(IQuote $item)
    {
        return $item->quote()->where('cart_id', $this->cart->id)->exists();
    }

    public function removeQuote(IQuote $item)
    {
        if (!$_item = $item->quote()->where('cart_id', $this->cart->id)->first()) {
            throw new QuoteNotFoundException;
        }

        $_item->delete();

        BasketinRemoveQuoteEvent::dispatch($this->cartService->getUlid(), $item);

        return $this;
    }

    public function getQuotes()
    {
        return $this->cart->quotes;
    }
}
