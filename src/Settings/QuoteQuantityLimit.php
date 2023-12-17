<?php

namespace Basketin\Component\Cart\Settings;

class QuoteQuantityLimit
{
    public static $limit = null;

    public static function setLimit(int $limit)
    {
        static::$limit = $limit;
    }

    public static function getLimit()
    {
        if ($limit = static::$limit) {
            return $limit;
        }

        return config('basketin.cart.limit_quote', 5);
    }
}
