<?php

namespace Obelaw\Basketin\Cart\Traits;

use Obelaw\Basketin\Cart\Models\Quote;

trait HasQuote
{
    public function quote()
    {
        return $this->morphOne(Quote::class, 'item');
    }
}
