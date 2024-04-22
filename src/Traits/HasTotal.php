<?php

namespace Basketin\Component\Cart\Traits;

trait HasTotal
{
    abstract public function getOriginalPriceAttribute(): float;

    abstract public function getSpecialPriceAttribute(): float|null;

    public function getDiscountPriceAttribute(): float
    {
        if (!$this->special_price) {
            return (float) 0;
        }

        return (float) $this->original_price - $this->special_price;
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->special_price ?? $this->original_price);
    }
}
