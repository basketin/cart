<?php

use Obelaw\Basketin\Cart\Base\MigrationBase;
use Illuminate\Support\Facades\Schema;

return new class extends MigrationBase
{
    public function up(): void
    {
        Schema::rename('basketin_carts', $this->prefix . 'carts');
        Schema::rename('basketin_cart_quotes', $this->prefix . 'cart_quotes');
    }

    public function down(): void
    {
        Schema::rename($this->prefix . 'cart_quotes', 'basketin_cart_quotes');
        Schema::rename($this->prefix . 'carts', 'basketin_carts');
    }
};
