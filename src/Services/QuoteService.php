<?php

namespace Storephp\Cart\Services;

use Storephp\Cart\Contracts\IQuote;
use Storephp\Cart\Models\Cart;

class QuoteService
{
    public function __construct(
        private Cart $cart
    ) {
    }

    public function addQuote(IQuote $item, $quantity = 1)
    {
        if ($_item = $item->quote()->first()) {
            $_item->quantity += $quantity;
            $_item->save();
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
        if ($_item = $item->quote()->first()) {
            $_item->increment('quantity', $quantity);
            return $this;
        }
    }

    public function decreaseQuote(IQuote $item, $quantity = 1)
    {
        if ($_item = $item->quote()->first()) {
            $_item->decrement('quantity', $quantity);
            return $this;
        }
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getQuotes()
    {
        return $this->cart->quotes;
    }
}
