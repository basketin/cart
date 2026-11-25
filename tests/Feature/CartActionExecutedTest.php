<?php

use Illuminate\Support\Facades\Event;
use Obelaw\Basketin\Cart\Events\CartActionUpdated;
use Obelaw\Basketin\Cart\Facades\Cart;
use Obelaw\Basketin\Cart\Tests\App\Models\Product;

test('add_quote dispatches CartActionUpdated event', function () {
    Event::fake();

    $product = Product::create([
        'name' => 'xBox',
        'sku' => 12345,
        'price' => 599,
    ]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $cart->quote()->addQuote($product, 1);

    Event::assertDispatched(CartActionUpdated::class, function ($event) {
        dump($event);
        return $event->action === 'add_quote'
            && $event->payload['item_type'] === 'Obelaw\Basketin\Cart\Tests\App\Models\Product'
            && $event->payload['quantity'] === 1;
    });
});
