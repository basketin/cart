<?php

namespace Obelaw\Basketin\Cart\Tests\App\Carts;

use Obelaw\Basketin\Cart\Base\CartBase;
use Obelaw\Basketin\Cart\Contracts\HasManageConfig;
use Obelaw\Basketin\Cart\Contracts\HasManageTotals;
use Obelaw\Basketin\Cart\Services\TotalManager;
use Obelaw\Basketin\Cart\Settings\Config;

class TestCart extends CartBase implements HasManageConfig, HasManageTotals
{
    public function manageTotals(TotalManager $totals): void
    {
        $totals->applyDiscount(100, 'test');
    }

    public function manageConfig(Config $config): void
    {
        $config->set('custom_config', 'test_value');
        $config->set('limit_quote', 20);
    }
}
