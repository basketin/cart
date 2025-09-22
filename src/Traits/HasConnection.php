<?php

namespace Obelaw\Basketin\Cart\Traits;

trait HasConnection
{
    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return config('basketin.cart.connection', $this->connection ?? null);
    }
}
