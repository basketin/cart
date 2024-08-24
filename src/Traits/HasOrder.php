<?php

namespace Basketin\Component\Cart\Traits;

use Basketin\Component\Cart\Models\Order;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasOrder
{
    /**
     * Get the order.
     */
    public function cartOrder(): MorphOne
    {
        return $this->morphOne(Order::class, 'orderable');
    }
}
