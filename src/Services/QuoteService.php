<?php

namespace Storephp\Cart\Services;

use Storephp\Cart\Contracts\IQuote;
use Storephp\Cart\Exceptions\QuoteNotFoundException;
use Storephp\Cart\Exceptions\QuoteQuantityLimitException;
use Storephp\Cart\Models\Cart;
use Storephp\Cart\Settings\QuoteQuantityLimit;

class QuoteService
{
    public function __construct(
        private Cart $cart
    ) {
    }

    public function addQuote(IQuote $item, $quantity = 1)
    {
        if ($item->quote()->first()) {
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
        if (!$_item = $item->quote()->first()) {
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
        if (!$_item = $item->quote()->first()) {
            throw new QuoteNotFoundException;
        }

        $_item->decrement('quantity', $quantity);

        return $this;
    }

    public function hasQuote(IQuote $item)
    {
        return $item->quote()->exists();
    }

    public function removeQuote(IQuote $item)
    {
        if (!$_item = $item->quote()->first()) {
            throw new QuoteNotFoundException;
        }

        $_item->delete();

        return $this;
    }

    // public function getCart()
    // {
    //     return $this->cart;
    // }

    public function getQuotes()
    {
        return $this->cart->quotes;
    }

    // public function getTotals()
    // {
    //     return new TotalService($this->cart->quotes);
    // }
}
