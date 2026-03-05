<?php

namespace Obelaw\Basketin\Cart\Contracts;

use Obelaw\Basketin\Cart\Services\TotalService;

interface HasManageTotals
{
    public function manageTotals(TotalService $totals): void;
}
