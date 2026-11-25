<?php

namespace Obelaw\Basketin\Cart\Models;

use Obelaw\Basketin\Cart\Base\ModelBase;

class Cart extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ulid',
        'cart_type',
        'currency',
        'status',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'quotes',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'cart_id', 'id');
    }
}
