<?php

use Storephp\Cart\Exceptions\CartNotFoundException;
use Storephp\Cart\Facades\CartManagement;
use Storephp\Cart\Tests\App\Models\Product;

test('Get Ulid', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getUlid())->toEqual('01HF7V7N1MG9SDFPQYWXDNHR9Q');
});

test('Get Currency', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getCurrency())->toEqual('USD');
});

test('Get Cart', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Open Cart', function () {
    CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Open Cart - CartNotFound', function () {
    CartManagement::openCart('02HF7V7N1MG9SDFPQYWXDNHR9Q');
})->throws(CartNotFoundException::class, 'Cart Not Found');


test('Count Products', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);

    expect($cart->getCountProducts())->toEqual(1);
});

test('Count Items', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);

    expect($cart->getCountItems())->toEqual(3);
});