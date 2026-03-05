<?php

namespace Obelaw\Basketin\Cart\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\Macroable;
use Obelaw\Basketin\Cart\Traits\HasTotal;
use Obelaw\Basketin\Cart\Services\Resources\AmountsResource;

class TotalService
{
    use Macroable;

    /**
     * @var float
     */
    private float $itemFinalTotal = 0;

    /**
     * @var float
     */
    private float $itemDiscountTotal = 0;
    /**
     * @var float|null
     */

    private ?float $globalDiscountTotal = null;

    private array $additions = [];

    private array $discounts = [];

    /**
     * TotalService constructor.
     * @param Collection $quotes
     * @throws Exception
     */
    public function __construct(
        private Collection $quotes,
    ) {
        if ($quotes->isNotEmpty()) {
            if (!in_array(
                HasTotal::class,
                array_keys((new \ReflectionClass($quotes->first()->item_type))->getTraits())
            )) {
                throw new Exception('Quote item type must use the HasTotal trait: Obelaw\Basketin\Cart\Traits\HasTotal');
            }
            foreach ($quotes as $quote) {
                $this->itemFinalTotal += $quote->quantity * $quote->item->final_price;
                $this->itemDiscountTotal += $quote->quantity * $quote->item->discount_price;
            }
        }
    }

    /**
     * Get the global discount total.
     */
    public function getGlobalDiscountTotal(): float
    {
        return (float) $this->globalDiscountTotal;
    }

    /**
     * Set the global discount total.
     */
    public function setGlobalDiscountTotal(float $globalDiscountTotal): self
    {
        $this->globalDiscountTotal = $globalDiscountTotal;
        return $this;
    }

    /**
     * Get the item final total.
     */
    public function getItemFinalTotal(): float
    {
        return (float) $this->itemFinalTotal;
    }

    /**
     * Get the item discount total.
     */
    public function getItemDiscountTotal(): float
    {
        return (float) $this->itemDiscountTotal;
    }

    /**
     * Get the subtotal.
     */
    public function getSubTotal(): float
    {
        return (float) $this->getItemFinalTotal();
    }

    /**
     * Append an addition amount. If a key is provided, multiple amounts
     * can be stored under the same key.
     */
    public function applyAddition(float $amount, ?string $key = null): self
    {
        if (is_null($key)) {
            $this->additions[] = $amount;
            return $this;
        }
        if (!isset($this->additions[$key])) {
            $this->additions[$key] = [];
        }
        if (!is_array($this->additions[$key])) {
            $this->additions[$key] = [$this->additions[$key]];
        }
        $this->additions[$key][] = $amount;
        return $this;
    }

    /**
     * Append a discount amount. If a key is provided, multiple amounts
     * can be stored under the same key.
     */
    public function applyDiscount(float $amount, ?string $key = null): self
    {
        if (is_null($key)) {
            $this->discounts[] = $amount;
            return $this;
        }
        if (!isset($this->discounts[$key])) {
            $this->discounts[$key] = [];
        }
        if (!is_array($this->discounts[$key])) {
            $this->discounts[$key] = [$this->discounts[$key]];
        }
        $this->discounts[$key][] = $amount;
        return $this;
    }

    public function getAdditions(): AmountsResource
    {
        return new AmountsResource($this->additions);
    }

    public function getDiscounts(): AmountsResource
    {
        return new AmountsResource($this->discounts);
    }

    /**
     * Get the total discount (global + item), never exceeding subtotal.
     */
    public function getDiscountTotal(): float
    {
        $totalDiscount = $this->getDiscounts()->sum() + $this->getGlobalDiscountTotal() + $this->getItemDiscountTotal();
        return (float) $totalDiscount;
    }

    /**
     * Get the grand total (subtotal - discounts).
     */
    public function getGrandTotal(): float
    {
        return (float) ($this->getSubTotal() + $this->getAdditions()->sum()) - $this->getDiscountTotal();
    }
}
