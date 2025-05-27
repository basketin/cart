<?php

namespace Obelaw\Basketin\Cart;

class Cart
{
    private static $cart = null;

    /**
     * Get the value of cart
     */
    public static function getCart()
    {
        return static::$cart;
    }

    /**
     * Set the value of cart
     *
     * @return  self
     */
    public static function setCart($cart)
    {
        static::$cart = $cart;
    }
}
