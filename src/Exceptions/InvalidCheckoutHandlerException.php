<?php

namespace Obelaw\Basketin\Cart\Exceptions;

use Exception;
use Throwable;

class InvalidCheckoutHandlerException extends Exception
{
    public function __construct($message = 'Invalid Checkout Handler: Expected method "placedCart", "handle", or invokable class', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
