<?php

namespace Storephp\Cart\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Storephp\Cart\Contracts\IQuote;
use Storephp\Cart\Traits\HasQuote;
use Storephp\Cart\Traits\HasTotal;

class Product extends Model implements IQuote
{
    use HasQuote;
    use HasTotal;

    protected $fillable = [
        'name',
        'sku',
        'price',
    ];

    public function getPriceAttribute(): float
    {
        return (float) $this->getAttributes()['price'];
    }

    public function getSpecialPriceAttribute(): float|null
    {
        return null;
    }
}
