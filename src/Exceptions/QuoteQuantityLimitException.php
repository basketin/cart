<?php

namespace Obelaw\Basketin\Cart\Exceptions;

use Exception;

class QuoteQuantityLimitException extends Exception
{
    public function __construct($message = 'You exceed the existing limit and the quantity cannot be increased', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
