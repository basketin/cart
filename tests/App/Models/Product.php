<?php

namespace Obelaw\Basketin\Cart\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Obelaw\Basketin\Cart\Contracts\IQuote;
use Obelaw\Basketin\Cart\Traits\HasQuote;
use Obelaw\Basketin\Cart\Traits\HasTotal;

class Product extends Model implements IQuote
{
    use HasQuote;
    use HasTotal;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'special_price',
    ];

    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->getAttributes()['price'];
    }

    public function getSpecialPriceAttribute(): float|null
    {
        return ($this->getAttributes()['special_price']) ? (float) $this->getAttributes()['special_price'] : null;
    }
}
