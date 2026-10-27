<?php

return [
    'connection' => 'mysql',

    'setup' => [
        'auto_migrate' => false,
    ],

    'limit_quote' => 5,

    /**
     * Default Checkout Handler
     *
     * Class name or callable to execute on cart checkout.
     * Basketin resolves methods by convention: `placedCart`, `handle`, `__invoke`.
     */
    'checkout_handler' => null,
];
