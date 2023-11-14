<?php

namespace Storephp\Cart\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Storephp\Cart\Traits\HasTotal;

class TotalService
{
    private $subTotal = 0;
    private $discountTotal = 0;
    private $grandTotal = 0;

    private $globalDiscountTotal = null;

    public function __construct(
        private Collection $quotes
    ) {
        if (!in_array(
            HasTotal::class,
            array_keys((new \ReflectionClass($quotes->first()->item_type))->getTraits())
        )) {
            throw new Exception('You must use `Storephp\Cart\Traits\HasTotal` Trait');
        }

        foreach ($quotes as $quote) {
            $this->subTotal += $quote->quantity * $quote->item->price;
            $this->discountTotal += $quote->quantity * $quote->item->discount_price;
            $this->grandTotal += $quote->quantity * $quote->item->final_price;
        }
    }

    public function getGlobalDiscountTotal()
    {
        return (float) $this->globalDiscountTotal;
    }

    public function setGlobalDiscountTotal(float $globalDiscountTotal)
    {
        $this->globalDiscountTotal = $globalDiscountTotal;

        return $this;
    }

    public function getSubTotal(): float
    {
        return (float) $this->subTotal;
    }

    public function getDiscountTotal(): float
    {
        return (float) $this->discountTotal;
    }

    public function getGrandTotal(): float
    {
        if ($globalDiscountTotal = $this->getGlobalDiscountTotal()) {
            return (float) $this->grandTotal - $globalDiscountTotal;
        }

        return (float) $this->grandTotal;
    }
}
