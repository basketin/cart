<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Basketin\Cart\Contracts\HasManageConfig;
use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Services\CartService;
use Obelaw\Basketin\Cart\Services\TotalService;
use Obelaw\Basketin\Cart\Settings\Config;

class CartBase extends CartService
{
    public function getConfig(): Config
    {
        $config = parent::getConfig();

        if ($this instanceof HasManageConfig) {
            $this->manageConfig($config);
        }

        return $config;
    }

    public function totals(): TotalService
    {
        $totals = parent::totals();

        if ($this instanceof HasManageTotals) {
            $this->manageTotals($totals);
        }

        return $totals;
    }
}
