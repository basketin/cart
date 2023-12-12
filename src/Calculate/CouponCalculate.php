<?php

namespace Storephp\Cart\Calculate;

use Storephp\Cart\Contracts\ICoupon;

class CouponCalculate
{
    const FIXED = 'fixed';
    const PERCENT = 'percent';

    private $subTotal;

    public function __construct(public ICoupon $coupon)
    {
    }

    public function setSubTotal($amount)
    {
        if ($this->coupon->discountType() === static::FIXED) {
            $this->subTotal = $amount - $this->coupon->discountValue();
        }

        if ($this->coupon->discountType() === static::PERCENT) {
            $this->subTotal = $amount * $this->coupon->discountValue() / 100;
        }

        return $this;
    }

    public function getSubTotal()
    {
        return $this->subTotal;
    }
}
