<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Add Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    expect($cart->quote()->getQuotes()->toArray()[0]['item'])->toMatchArray([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);
});

test('Increase Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->increaseQuote($product, 4);

    expect($cart->quote()->getQuotes()->toArray()[0]['quantity'])->toEqual(5);
});

test('Decrease Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->increaseQuote($product, 4);
    $cart->quote()->decreaseQuote($product, 3);

    expect($cart->quote()->getQuotes()->toArray()[0]['quantity'])->toEqual(2);
});

test('Decrease Quote To Be Remove', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->decreaseQuote($product, 1);

    expect($cart->quote()->getQuotes())->toBeEmpty();
});

test('Has Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    expect($cart->quote()->hasQuote($product))->toEqual(true);
});

test('Not Has Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->quote()->hasQuote($product))->toEqual(false);
});

test('Remove Quote', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->removeQuote($product);

    expect($cart->quote()->getQuotes())->toBeEmpty();
});
