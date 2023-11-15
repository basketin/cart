<?php

namespace Storephp\Cart\Exceptions;

use Exception;

class CartNotFoundException extends Exception
{
    public function __construct($message = 'Cart Not Found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
