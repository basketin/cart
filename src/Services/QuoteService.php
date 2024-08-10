<?php

namespace Basketin\Component\Cart\Services;

use Basketin\Component\Cart\Contracts\IQuote;
use Basketin\Component\Cart\Exceptions\QuoteNotFoundException;
use Basketin\Component\Cart\Exceptions\QuoteQuantityLimitException;
use Basketin\Component\Cart\Models\Cart;
use Basketin\Component\Cart\Settings\QuoteQuantityLimit;

class QuoteService
{
    public function __construct(
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

        return $this;
    }

    public function getQuotes()
    {
        return $this->cart->quotes;
    }
}
