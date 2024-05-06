<?php

namespace Basketin\Component\Cart\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Basketin\Component\Cart\Contracts\IQuote;
use Basketin\Component\Cart\Traits\HasQuote;
use Basketin\Component\Cart\Traits\HasTotal;

class Product extends Model implements IQuote
{
    use HasQuote;
    use HasTotal;

    protected $fillable = [
        'name',
        'sku',
        'price',
    ];

    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->getAttributes()['price'];
    }

    public function getSpecialPriceAttribute(): float|null
    {
        return null;
    }
}
