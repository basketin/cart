<?php

namespace Storephp\Cart\Traits;

use Storephp\Cart\Models\Quote;

trait HasQuote
{
    public function quote()
    {
        return $this->morphOne(Quote::class, 'item');
    }
}
