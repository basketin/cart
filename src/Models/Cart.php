<?php

namespace Obelaw\Basketin\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Obelaw\Basketin\Cart\Traits\HasConnection;

class Cart extends Model
{
    use HasConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'basketin_carts';

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

    public function fields()
    {
        return $this->hasMany(Field::class, 'cart_id', 'id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'cart_id', 'id');
    }
}
