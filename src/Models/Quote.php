<?php

namespace Obelaw\Basketin\Cart\Models;

use Obelaw\Basketin\Cart\Base\ModelBase;

class Quote extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_quotes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'quantity',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'item',
    ];

    public function item()
    {
        return $this->morphTo();
    }
}
