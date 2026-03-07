<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;
use Obelaw\Basketin\Cart\Tests\App\Carts\TestCart;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('Get Item Final Total', function () {
    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

    $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

    $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

    $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
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

        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(10.00, 'shipping');

        expect($totals->getAdditions()->has('shipping'))->toBeTrue();
    });
})->group('totals');

describe('Cart Base Manage', function () {
    test('apply manageTotals', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,

        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();

        expect($totals->getGrandTotal())->toEqual(499);
    });

    test('manageTotals discount appears in discounts resource', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();

        expect($totals->getDiscounts()->has('test'))->toBeTrue();
        expect($totals->getDiscounts()->get('test'))->toEqual([100]);
        expect($totals->getDiscountTotal())->toEqual(100);
    });

    test('manageTotals with multiple products', function () {
        $product1 = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $product2 = Product::create([
            'name' => 'PlayStation',
            'sku' => 54321,
            'price' => 499,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product1, 1);
        $cart->quote()->addQuote($product2, 2);

        $totals = $cart->totals();

        expect($totals->getSubTotal())->toEqual(1597);
        expect($totals->getDiscountTotal())->toEqual(100);
        expect($totals->getGrandTotal())->toEqual(1497);
    });

    test('manageTotals combined with manual additions', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->applyAddition(25, 'shipping');

        // 599 + 25 (addition) - 100 (manageTotals discount) = 524
        expect($totals->getGrandTotal())->toEqual(524);
    });

    test('manageTotals combined with global discount', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        $totals = $cart->totals();
        $totals->setGlobalDiscountTotal(50);

        // 599 - 100 (manageTotals) - 50 (global) = 449
        expect($totals->getDiscountTotal())->toEqual(150);
        expect($totals->getGrandTotal())->toEqual(449);
    });

    test('manageTotals with special price product', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
            'special_price' => 499,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 2);

        $totals = $cart->totals();

        // subtotal: 499 * 2 = 998
        // item discount: (599-499) * 2 = 200
        // manageTotals discount: 100
        // total discount: 200 + 100 = 300
        // grand total: 998 - 100 = 898 (item discounts already subtracted from price)
        expect($totals->getSubTotal())->toEqual(998);
        expect($totals->getItemDiscountTotal())->toEqual(200);
        expect($totals->getDiscountTotal())->toEqual(300);
        expect($totals->getGrandTotal())->toEqual(698);
    });

    test('manageTotals on empty cart', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        $totals = $cart->totals();

        expect($totals->getSubTotal())->toEqual(0);
        expect($totals->getDiscountTotal())->toEqual(100);
        expect($totals->getGrandTotal())->toEqual(-100);
    });

    test('make returns TestCart instance', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        expect($cart)->toBeInstanceOf(TestCart::class);
    });

    test('make with cart type', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD', 'order');

        expect($cart->getType())->toEqual('order');
        expect($cart->getUlid())->toEqual('01HF7V7N1MG9SDFPQYWXDNHR9Q');
        expect($cart->getCurrency())->toEqual('USD');
    });

    test('make auto-generates ulid', function () {
        $cart = TestCart::make();

        expect($cart->getUlid())->not->toBeEmpty();
        expect($cart->getCurrency())->toEqual('USD');
    });

    test('checkout clears cart', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        expect($cart->checkoutIt())->toBeTrue();
        expect($cart->getCart()->status)->toEqual('checkout');
    });

    test('fields work with TestCart', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        $cart->fields()->set('coupon_code', 'SAVE100');

        expect($cart->fields()->get('coupon_code'))->toEqual('SAVE100');
        expect($cart->fields()->has('coupon_code'))->toBeTrue();
    });

    test('quote operations with TestCart', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);
        $cart->quote()->increaseQuote($product, 2);

        expect($cart->getCountItems())->toEqual(3);
        expect($cart->getCountProducts())->toEqual(1);

        $cart->quote()->decreaseQuote($product, 1);
        expect($cart->getCountItems())->toEqual(2);

        $cart->quote()->removeQuote($product);
        expect($cart->getCountProducts())->toEqual(0);
    });

    test('config override with TestCart', function () {
        $cart = CartManagement::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->config(new \Obelaw\Basketin\Cart\Settings\Config([
            'limit_quote' => 10,
        ]));

        expect($cart->getConfig()->get('limit_quote'))->toEqual(10);
    });

    test('manageConfig is called and modifies config', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        expect($cart->getConfig()->get('custom_config'))->toEqual('test_value');
        expect($cart->getConfig()->get('limit_quote'))->toEqual(20);
    });

    test('manageConfig can override default config', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        expect($cart->getConfig()->get('limit_quote'))->toEqual(20);
    });

    test('manageConfig adds custom config values', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        expect($cart->getConfig()->get('custom_config'))->toBeString();
        expect($cart->getConfig()->all())->toHaveKey('custom_config');
    });

    test('manageConfig works with empty cart', function () {
        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

        expect($cart->getCountProducts())->toEqual(0);
        expect($cart->getConfig()->get('custom_config'))->toEqual('test_value');
    });

    test('manageConfig works with cart that has quotes', function () {
        $product = Product::create([
            'name' => 'xBox',
            'sku' => 12345,
            'price' => 599,
        ]);

        $cart = TestCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
        $cart->quote()->addQuote($product, 1);

        expect($cart->getCountProducts())->toEqual(1);
        expect($cart->getConfig()->get('custom_config'))->toEqual('test_value');
        expect($cart->getConfig()->get('limit_quote'))->toEqual(20);
    });
})->group('cart-class');
