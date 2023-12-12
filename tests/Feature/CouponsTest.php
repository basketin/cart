<?php

use Storephp\Cart\Calculate\CouponCalculate;
use Storephp\Cart\Facades\CartManagement;
use Storephp\Cart\Tests\App\Models\Coupon;
use Storephp\Cart\Tests\App\Models\Product;

test('Cart With Fixed Coupon', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $coupon = Coupon::create([
        'coupon_name' => 'xCode',
        'coupon_code' => 'xcode',
        'discount_type' => CouponCalculate::FIXED,
        'discount_value' => 49,
    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $cart->coupon($coupon);

    $totals = $cart->totals();

    expect($totals->getSubTotal())->toEqual(550);
});


test('Cart With Percent Coupon', function () {
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

    expect($totals->getSubTotal())->toEqual(299.5);
});

test('Coupon Info', function () {
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

    $coupon = $cart->couponInfo();

    expect($coupon)->toMatchArray([
        'coupon_name' => 'xCode',
        'coupon_code' => 'xcode',
        'discount_type' => CouponCalculate::PERCENT,
        'discount_value' => 50,
    ]);
});

test('Cart Without Coupon', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    expect($totals->getSubTotal())->toEqual(599);
});
