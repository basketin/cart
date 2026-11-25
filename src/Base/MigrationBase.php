<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Ium\Bases\MigrationBase as StackMigrationBase;

abstract class MigrationBase extends StackMigrationBase
{
    /**
     * Table postfix.
     */
    protected ?string $module = 'basketin_';
}
