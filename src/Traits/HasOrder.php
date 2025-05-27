<?php

namespace Obelaw\Basketin\Cart\Traits;

use Obelaw\Basketin\Cart\Models\Order;
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
