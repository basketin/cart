<?php

namespace Obelaw\Basketin\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Obelaw\Basketin\Cart\Traits\HasConnection;

class Field extends Model
{
    use HasConnection;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'basketin_cart_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'field_key',
        'field_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'field_value' => 'json',
    ];
}
