<?php

namespace Obelaw\Basketin\Cart\Tests\App\Carts;

use Obelaw\Basketin\Cart\Base\CartBase;
use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Services\TotalService;

class TestCart extends CartBase implements HasManageTotals
{
    public function manageTotals(TotalService $totals): void
    {
        $totals->applyDiscount(100, 'test');
    }
}
