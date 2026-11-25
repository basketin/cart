<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Ium\Bases\MigrationBase as StackMigrationBase;

abstract class MigrationBase extends StackMigrationBase
{
    /**
     * Table postfix.
     *
     * @var string|null $module
     */
    protected ?string $module = 'basketin_';
}
