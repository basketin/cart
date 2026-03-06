<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Services\CartService;
use Obelaw\Basketin\Cart\Services\TotalService;

class CartBase extends CartService
{
    public function totals(): TotalService
    {
        $totals = parent::totals();

        if ($this instanceof HasManageTotals) {
            $this->manageTotals($totals);
        }

        return $totals;
    }
}
