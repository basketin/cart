<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Settings\Config;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

describe('QuoteQuantityLimit', function () {
    it('Increase Quote', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->config(new Config([
            'limit_quote' => 15
        ]));
        $cart->quote()->addQuote($product, 1);
        $cart->quote()->increaseQuote($product, 5);

        expect($cart->quote()->getQuotes()->toArray()[0]['quantity'])->toEqual(6);
    });
});
