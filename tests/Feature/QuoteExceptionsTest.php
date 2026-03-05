<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Exceptions\QuoteNotFoundException;
use Obelaw\Basketin\Cart\Exceptions\QuoteQuantityLimitException;
use Obelaw\Basketin\Cart\Settings\Config;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Add Quote Exceed Limit', function () {
    $product = Product::create([
        'name' => 'PlayStation',
        'sku' => 54321,
        'price' => 499,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->config(new Config([
        'limit_quote' => 2,
    ]));

    $cart->quote()->addQuote($product, 3);
})->throws(QuoteQuantityLimitException::class);

test('Increase Quote Not Found', function () {
    $product = Product::create([
        'name' => 'Switch',
        'sku' => 11111,
        'price' => 299,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->increaseQuote($product, 1);
})->throws(QuoteNotFoundException::class);

test('Increase Quote Exceed Limit', function () {
    $product = Product::create([
        'name' => 'Keyboard',
        'sku' => 22222,
        'price' => 99,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->config(new Config([
        'limit_quote' => 5,
    ]));

    $cart->quote()->addQuote($product, 3);

    $cart->quote()->increaseQuote($product, 3);
})->throws(QuoteQuantityLimitException::class);

test('Decrease Quote Not Found', function () {
    $product = Product::create([
        'name' => 'Mouse',
        'sku' => 33333,
        'price' => 49,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->decreaseQuote($product, 1);
})->throws(QuoteNotFoundException::class);

test('Remove Quote Not Found', function () {
    $product = Product::create([
        'name' => 'Headset',
        'sku' => 44444,
        'price' => 79,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->removeQuote($product);
})->throws(QuoteNotFoundException::class);
