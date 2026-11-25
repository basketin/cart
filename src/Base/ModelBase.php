<?php

namespace Obelaw\Basketin\Cart\Base;

use Obelaw\Ium\Bases\ModelBase as StackBaseModel;

class ModelBase extends StackBaseModel
{
    /**
     * Optional module name for table prefixing.
     *
     * @var string|null $module
     */
    protected ?string $module = 'basketin_';
}
