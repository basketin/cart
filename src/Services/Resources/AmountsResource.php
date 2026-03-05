<?php

namespace Obelaw\Basketin\Cart\Services\Resources;

class AmountsResource
{
    private array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function sum(): float
    {
        $total = 0;
        foreach ($this->items as $value) {
            if (is_array($value)) {
                $total += array_sum($value);
            } else {
                $total += $value;
            }
        }
        return (float) $total;
    }

    public function get(string $key)
    {
        return $this->items[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }
}
