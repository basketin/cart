<?php

namespace Obelaw\Basketin\Cart\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Obelaw\Basketin\Cart\Calculate\CouponCalculate;
use Obelaw\Basketin\Cart\Traits\HasTotal;

class TotalService
{
    private $itemFinalTotal = 0;
    private $itemDiscountTotal = 0;
    private $globalDiscountTotal = null;

    public function __construct(
        private Collection $quotes,
        private $coupon = null,
    ) {
        if ($quotes->isNotEmpty()) {
            if (!in_array(
                HasTotal::class,
                array_keys((new \ReflectionClass($quotes->first()->item_type))->getTraits())
            )) {
                throw new Exception('You must use `Obelaw\Basketin\Cart\Traits\HasTotal` Trait');
            }

            foreach ($quotes as $quote) {
                $this->itemFinalTotal += $quote->quantity * $quote->item->final_price;
                $this->itemDiscountTotal += $quote->quantity * $quote->item->discount_price;
            }
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

    public function getItemFinalTotal(): float
    {
        return (float) $this->itemFinalTotal;
    }

    public function getItemDiscountTotal(): float
    {
        return (float) $this->itemDiscountTotal;
    }

    public function getSubTotal()
    {
        return (float) $this->getItemFinalTotal();
    }

    public function getDiscountTotal(): float
    {
        $deductions = 0;

        if ($this->coupon) {
            $deductions += (float) (new CouponCalculate($this->coupon))
                ->setSubTotal($this->getSubTotal())
                ->getSubTotal();
        }

        if ($globalDiscountTotal = $this->getGlobalDiscountTotal()) {
            $deductions += $globalDiscountTotal;
        }

        if ($deductions >= $this->getSubTotal())
            return $this->getSubTotal();

        return (float) $deductions;
    }

    public function getGrandTotal(): float
    {
        return (float) $this->getSubTotal() - $this->getDiscountTotal();
    }
}
