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
