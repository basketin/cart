<?php

namespace Basketin\Component\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Order extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'basketin_cart_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'cart_id',
    ];

    public static function boot()
    {
        parent::boot();


        static::created(function ($order) {
            // $year = date('Y');

            $order->reference = Str::padLeft($order->id, 9, '0');
            $order->save();
        });
    }

    /**
     * Get the parent orderable model.
     */
    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }
}
