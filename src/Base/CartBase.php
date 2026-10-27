<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Basketin\Cart\Contracts\HasManageConfig;
use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Services\CartManager;
use Obelaw\Basketin\Cart\Services\TotalManager;
use Obelaw\Basketin\Cart\Settings\Config;

class CartBase extends CartManager
{
    protected ?string $checkoutHandler = null;

    public function getConfig(): Config
    {
        $config = parent::getConfig();

        if ($this instanceof HasManageConfig) {
            $this->manageConfig($config);
        }

        return $config;
    }

    public function totals(): TotalManager
    {
        $totals = parent::totals();

        if ($this instanceof HasManageTotals) {
            $this->manageTotals($totals);
        }

        return $totals;
    }

    public function checkoutIt(?string $cartType = null, callable|array|string|object|null $action = null): bool
    {
        $action = $action ?? $this->checkoutHandler;

        return parent::checkoutIt($cartType, $action);
    }
}
