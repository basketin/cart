<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Get Item Final Total', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    expect($totals->getItemFinalTotal())->toEqual(599);
});

test('Get Discount Total', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    expect($totals->getDiscountTotal())->toEqual(0);
});

test('Get Grand Total', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    expect($totals->getGrandTotal())->toEqual(599);
});

test('Set Global Discount Total', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,

    ]);

    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    $totals = $cart->totals();

    $totals->setGlobalDiscountTotal(500.00);

    expect($totals->getGrandTotal())->toEqual(99);
});

describe('Additions & Discounts', function () {
    test('Add Addition', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(10.00, 'shipping');

        expect($totals->getGrandTotal())->toEqual(609);
    });

    test('Add Discount', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyDiscount(10.00, 'coupon');

        expect($totals->getGrandTotal())->toEqual(589);
    });

    test('Callback Function', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();

        function callback($totals)
        {
            $totals->applyAddition(10.00, 'shipping');
            $totals->applyDiscount(10.00, 'coupon');
        }

        callback($totals);

        expect($totals->getGrandTotal())->toEqual(599);
    });

    test('Check Discount Total', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyDiscount(10.00, 'coupon');

        expect($totals->getDiscountTotal())->toEqual(10.00);
    });

    test('Multiple Additions Same Key', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(5.00, 'shipping');
        $totals->applyAddition(10.00, 'shipping');

        expect($totals->getAdditions()->get('shipping'))->toEqual([5.00, 10.00]);
        expect($totals->getAdditions()->sum())->toEqual(15.00);
        expect($totals->getGrandTotal())->toEqual(614);
    });

    test('Multiple Discounts Same Key', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyDiscount(5.00, 'coupon');
        $totals->applyDiscount(2.50, 'coupon');

        expect($totals->getDiscounts()->get('coupon'))->toEqual([5.00, 2.50]);
        expect($totals->getDiscounts()->sum())->toEqual(7.50);
        expect($totals->getGrandTotal())->toEqual(591.5);
    });

    test('Unkeyed Additions Append and Sum', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(20.00);
        $totals->applyAddition(5.00);

        expect($totals->getAdditions()->sum())->toEqual(25.00);
        expect($totals->getGrandTotal())->toEqual(624);
    });

    test('Resource Has Key', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(10.00, 'shipping');

        expect($totals->getAdditions()->has('shipping'))->toBeTrue();
    });
})->group('totals');
