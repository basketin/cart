<?php

namespace Obelaw\Basketin\Cart\Contracts;

interface ICoupon
{
    public function discountType(): String;

    public function discountValue(): Int;
}
