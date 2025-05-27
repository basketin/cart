<?php

use Obelaw\Basketin\Cart\Calculate\CouponCalculate;
use Obelaw\Basketin\Cart\Exceptions\CartNotFoundException;
use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Services\CartService;
use Obelaw\Basketin\Cart\Tests\App\Models\Coupon;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Get Ulid', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getUlid())->toEqual('01HF7V7N1MG9SDFPQYWXDNHR9Q');
});

test('Get Currency', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getCurrency())->toEqual('USD');
});

test('Get Type', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD', 'order');

    expect($cart->getType())->toEqual('order');
});

test('Get Cart', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Check Session Cart', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    expect($cart->getUlid())->toEqual(session('_' . CartService::SESSION_KEY));
});

test('Open Cart', function () {
    CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q');

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Open Cart By Session', function () {
    CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart = CartManagement::openCart();

    expect($cart->getCart()->toArray())->toMatchArray([
        'ulid' => '01HF7V7N1MG9SDFPQYWXDNHR9Q',
        'currency' => 'USD',
    ]);
});

test('Init And Open Cart By Session', function () {
    CartManagement::initCart();

    $cart = CartManagement::openCart();

    expect($cart->getUlid())->toEqual(session('_' . CartService::SESSION_KEY));
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

test('Show Cart', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

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

test('Show Cart With Discount', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $coupon = Coupon::create([
        'coupon_name' => 'xCode',
        'coupon_code' => 'xcode',
        'discount_type' => CouponCalculate::PERCENT,
        'discount_value' => 50,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);

    $cart->coupon($coupon);

    $totals = $cart->totals();

    expect([
        'subtotal' => $totals->getSubTotal(),
        'discounttotal' => $totals->getDiscountTotal(),
        'grandtotal' => $totals->getGrandTotal(),
    ])->toMatchArray([
        'subtotal' => 599,
        'discounttotal' => 299.5,
        'grandtotal' => 299.5,
    ]);
});

test('Cart Checkout', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);
    $cart->quote()->addQuote($product, 1);

    expect($cart->checkoutIt())->toBeTrue();
});
