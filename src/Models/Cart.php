<?php

namespace Storephp\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'storephp_carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ulid',
        'currency',
        'status',
    ];
}
