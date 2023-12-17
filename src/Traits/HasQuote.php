<?php

namespace Basketin\Component\Cart\Traits;

use Basketin\Component\Cart\Models\Quote;

trait HasQuote
{
    public function quote()
    {
        return $this->morphOne(Quote::class, 'item');
    }
}
