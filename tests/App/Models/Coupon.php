<?php

namespace Basketin\Component\Cart\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Basketin\Component\Cart\Contracts\ICoupon;

class Coupon extends Model implements ICoupon
{
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'discount_type',
        'discount_value',
        'start_at',
        'ends_at',
    ];

    public function discountType(): String
    {
        return $this->discount_type;
    }

    public function discountValue(): Int
    {
        return $this->discount_value;
    }
}
