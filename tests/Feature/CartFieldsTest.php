<?php

use Obelaw\Basketin\Cart\Facades\CartManagement;

test('Set Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    expect($cart->fields()->all())->toMatchArray([
        'key' => 'value',
    ]);
});

test('Update Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    $cart->fields()->set('key', 'value 1');

    expect($cart->fields()->all())->toMatchArray([
        'key' => 'value 1',
    ]);
});

test('Get Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    expect($cart->fields()->get('key'))->toEqual('value');
});

test('Has Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    expect($cart->fields()->has('key'))->toEqual(true);
});

test('Not Has Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    expect($cart->fields()->has('key1'))->toEqual(false);
});


test('Remove Field', function () {
    $cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->fields()->set('key', 'value');

    expect($cart->fields()->remove('key'))->toEqual(true);
});
