<?php

namespace Obelaw\Basketin\Cart\Contracts;

use Obelaw\Basketin\Cart\Services\TotalManager;

interface HasManageTotals
{
    public function manageTotals(TotalManager $totals): void;
}
