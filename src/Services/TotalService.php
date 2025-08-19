<?php

namespace Obelaw\Basketin\Cart\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Obelaw\Basketin\Cart\Calculate\CouponCalculate;
use Obelaw\Basketin\Cart\Traits\HasTotal;


class TotalService
{
    /**
     * @var float
     */
    private float $itemFinalTotal = 0;

    /**
     * @var float
     */
    private float $itemDiscountTotal = 0;

    /**
     * @var float|null
     */
    private ?float $globalDiscountTotal = null;

    /**
     * @var mixed|null
     */
    private $coupon = null;

    /**
     * TotalService constructor.
     * @param Collection $quotes
     * @param mixed|null $coupon
     * @throws Exception
     */
    public function __construct(
        private Collection $quotes,
        $coupon = null,
    ) {
        $this->coupon = $coupon;
        if ($quotes->isNotEmpty()) {
            if (!in_array(
                HasTotal::class,
                array_keys((new \ReflectionClass($quotes->first()->item_type))->getTraits())
            )) {
                throw new Exception('Quote item type must use the HasTotal trait: Obelaw\Basketin\Cart\Traits\HasTotal');
            }
            foreach ($quotes as $quote) {
                $this->itemFinalTotal += $quote->quantity * $quote->item->final_price;
                $this->itemDiscountTotal += $quote->quantity * $quote->item->discount_price;
            }
        }
    }

    /**
     * Get the global discount total.
     */
    public function getGlobalDiscountTotal(): float
    {
        return (float) $this->globalDiscountTotal;
    }

    /**
     * Set the global discount total.
     */
    public function setGlobalDiscountTotal(float $globalDiscountTotal): self
    {
        $this->globalDiscountTotal = $globalDiscountTotal;
        return $this;
    }

    /**
     * Get the item final total.
     */
    public function getItemFinalTotal(): float
    {
        return (float) $this->itemFinalTotal;
    }

    /**
     * Get the item discount total.
     */
    public function getItemDiscountTotal(): float
    {
        return (float) $this->itemDiscountTotal;
    }

    /**
     * Get the subtotal.
     */
    public function getSubTotal(): float
    {
        return (float) $this->getItemFinalTotal();
    }

    /**
     * Get the coupon object.
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Get the total discount (coupon + global), never exceeding subtotal.
     */
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
        if ($deductions >= $this->getSubTotal()) {
            return $this->getSubTotal();
        }
        return (float) $deductions;
    }

    /**
     * Get the grand total (subtotal - discounts).
     */
    public function getGrandTotal(): float
    {
        return (float) $this->getSubTotal() - $this->getDiscountTotal();
    }
}
