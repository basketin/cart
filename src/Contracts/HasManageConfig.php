<?php

namespace Obelaw\Basketin\Cart\Contracts;

use Obelaw\Basketin\Cart\Settings\Config;

interface HasManageConfig
{
    public function manageConfig(Config $config): void;
}
