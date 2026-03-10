<?php

use Obelaw\Basketin\Cart\Exceptions\CartNotFoundException;
use Obelaw\Basketin\Cart\Facades\Cart;
use Obelaw\Basketin\Cart\Services\CartManager;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Get Ulid', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getUlid())->toEqual('01HF7V7N1MG9SDFPQYWXDNHR9Q');
});

test('Get Currency', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getCurrency())->toEqual('USD');
});

test('Get Type', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD', 'order');

    expect($cart->getType())->toEqual('order');
});

test('Get Cart', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Check Session Cart', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getUlid())->toEqual(session('_'.CartManager::SESSION_KEY));
});

test('Open Cart', function () {
    Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Open Cart By Session', function () {
    Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart = Cart::make(null, 'USD', null, false);

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Init And Open Cart By Session', function () {
    Cart::make();

    $cart = Cart::make(null, 'USD', null, false);

    expect($cart->getUlid())->toEqual(session('_'.CartManager::SESSION_KEY));
});

test('Open Cart - CartNotFound', function () {
    Cart::make('02HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD', null, false);
})->throws(CartNotFoundException::class, 'Cart Not Found');

test('Count Products', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);

    expect($cart->getCountProducts())->toEqual(1);
});

test('Count Items', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);

    expect($cart->getCountItems())->toEqual(3);
});

test('Show Cart', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    expect([
        'subtotal' => $totals->getSubTotal(),
        'discounttotal' => $totals->getDiscountTotal(),
        'grandtotal' => $totals->getGrandTotal(),
    ])->toMatchArray([
        'subtotal' => 599,
        'discounttotal' => 0,
        'grandtotal' => 599,
    ]);
});

test('Cart Checkout', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);

    expect($cart->checkoutIt())->toBeTrue();
});
