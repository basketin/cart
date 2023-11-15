<?php

namespace Storephp\Cart\Exceptions;

use Exception;

class QuoteNotFoundException extends Exception
{
    public function __construct($message = 'Quote Not Found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
