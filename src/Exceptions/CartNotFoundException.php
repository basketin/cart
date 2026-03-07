<?php

namespace Obelaw\Basketin\Cart\Exceptions;

use Exception;
use Throwable;

class CartNotFoundException extends Exception
{
    public function __construct($message = 'Cart Not Found', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
