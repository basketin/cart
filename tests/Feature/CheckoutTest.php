<?php

use Obelaw\Basketin\Cart\Base\CartBase;
use Obelaw\Basketin\Cart\Contracts\HasCheckoutHandler;
use Obelaw\Basketin\Cart\Contracts\HasPlacedCart;
use Obelaw\Basketin\Cart\Exceptions\InvalidCheckoutHandlerException;
use Obelaw\Basketin\Cart\Facades\Cart;
use Obelaw\Basketin\Cart\Models\Cart as CartModel;
use Obelaw\Basketin\Cart\Services\CartManager;

class TestHandlerCart extends CartBase implements HasCheckoutHandler
{
    public static bool $called = false;
    public static ?CartManager $receivedCart = null;

    public function handle(CartManager $cart): void
    {
        static::$called = true;
        static::$receivedCart = $cart;
    }
}

class TestPlacedCartCart extends CartBase implements HasPlacedCart
{
    public static bool $called = false;
    public static ?CartManager $receivedCart = null;

    public function placedCart(CartManager $cart): void
    {
        static::$called = true;
        static::$receivedCart = $cart;
    }
}

class TestOrderHandlerAction
{
    public static bool $handled = false;
    public static ?CartManager $passedCart = null;
    public static ?CartModel $passedCartModel = null;

    public function handle(CartManager $cart, CartModel $cartModel): void
    {
        static::$handled = true;
        static::$passedCart = $cart;
        static::$passedCartModel = $cartModel;
    }

    public static function reset(): void
    {
        static::$handled = false;
        static::$passedCart = null;
        static::$passedCartModel = null;
    }
}

class TestPlacedCartAction
{
    public static bool $called = false;
    public static ?CartManager $passedCart = null;
    public static ?CartModel $passedCartModel = null;

    public function placedCart(CartManager $cart, CartModel $cartModel): void
    {
        static::$called = true;
        static::$passedCart = $cart;
        static::$passedCartModel = $cartModel;
    }

    public static function reset(): void
    {
        static::$called = false;
        static::$passedCart = null;
        static::$passedCartModel = null;
    }
}

class TestBothMethodsAction
{
    public static ?string $calledMethod = null;

    public function placedCart(CartManager $cart, CartModel $cartModel): void
    {
        static::$calledMethod = 'placedCart';
    }

    public function handle(CartManager $cart, CartModel $cartModel): void
    {
        static::$calledMethod = 'handle';
    }

    public static function reset(): void
    {
        static::$calledMethod = null;
    }
}

class TestInvalidHandlerAction
{
    // No placedCart, handle, or __invoke
}

class TestCartWithConfiguredHandler extends CartBase
{
    protected ?string $checkoutHandler = TestOrderHandlerAction::class;
}

class TestCartWithPlacedCartConfiguredHandler extends CartBase
{
    protected ?string $checkoutHandler = TestPlacedCartAction::class;
}

class TestOrderService
{
    public static bool $handled = false;
    public static ?CartManager $passedCart = null;
    public static ?CartModel $passedCartModel = null;

    public function createOrder(CartManager $cart, CartModel $cartModel): void
    {
        static::$handled = true;
        static::$passedCart = $cart;
        static::$passedCartModel = $cartModel;
    }

    public static function reset(): void
    {
        static::$handled = false;
        static::$passedCart = null;
        static::$passedCartModel = null;
    }
}

class TestInvokableAction
{
    public static bool $invoked = false;

    public function __invoke(CartManager $cart): void
    {
        static::$invoked = true;
    }
}

beforeEach(function () {
    TestHandlerCart::$called = false;
    TestHandlerCart::$receivedCart = null;
    TestPlacedCartCart::$called = false;
    TestPlacedCartCart::$receivedCart = null;
    TestOrderHandlerAction::reset();
    TestPlacedCartAction::reset();
    TestBothMethodsAction::reset();
    TestOrderService::reset();
    TestInvokableAction::$invoked = false;
    config(['basketin.cart.checkout_handler' => null]);
});

test('HasCheckoutHandler interface calls handle method from cart class on checkoutIt', function () {
    $cart = TestHandlerCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt())->toBeTrue();
    expect(TestHandlerCart::$called)->toBeTrue();
    expect(TestHandlerCart::$receivedCart)->toBeInstanceOf(TestHandlerCart::class);
    expect($cart->getCart()->status)->toEqual('checkout');
});

test('HasPlacedCart interface calls placedCart method from cart class on checkoutIt', function () {
    $cart = TestPlacedCartCart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt())->toBeTrue();
    expect(TestPlacedCartCart::$called)->toBeTrue();
    expect(TestPlacedCartCart::$receivedCart)->toBeInstanceOf(TestPlacedCartCart::class);
    expect($cart->getCart()->status)->toEqual('checkout');
});

test('checkoutIt accepts handler class directly to call handle method', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: TestOrderHandlerAction::class))->toBeTrue();
    expect(TestOrderHandlerAction::$handled)->toBeTrue();
    expect(TestOrderHandlerAction::$passedCart)->toBeInstanceOf(CartManager::class);
    expect(TestOrderHandlerAction::$passedCartModel)->toBeInstanceOf(CartModel::class);
});

test('checkoutIt accepts handler class directly to call placedCart method', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: TestPlacedCartAction::class))->toBeTrue();
    expect(TestPlacedCartAction::$called)->toBeTrue();
    expect(TestPlacedCartAction::$passedCart)->toBeInstanceOf(CartManager::class);
    expect(TestPlacedCartAction::$passedCartModel)->toBeInstanceOf(CartModel::class);
});

test('checkoutIt prefers placedCart over handle when class implements both', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: TestBothMethodsAction::class))->toBeTrue();
    expect(TestBothMethodsAction::$calledMethod)->toEqual('placedCart');
});

test('checkoutIt accepts array callable to call method from class', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: [TestOrderService::class, 'createOrder']))->toBeTrue();
    expect(TestOrderService::$handled)->toBeTrue();
    expect(TestOrderService::$passedCart)->toBeInstanceOf(CartManager::class);
    expect(TestOrderService::$passedCartModel)->toBeInstanceOf(CartModel::class);
});

test('checkoutIt accepts invokable class', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: TestInvokableAction::class))->toBeTrue();
    expect(TestInvokableAction::$invoked)->toBeTrue();
});

test('checkoutIt accepts class@method string syntax', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt(action: TestOrderService::class.'@createOrder'))->toBeTrue();
    expect(TestOrderService::$handled)->toBeTrue();
});

test('checkoutIt accepts callable closure', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');
    $closureCalled = false;

    expect($cart->checkoutIt(action: function ($c) use (&$closureCalled) {
        $closureCalled = true;
    }))->toBeTrue();

    expect($closureCalled)->toBeTrue();
});

test('CartBase $checkoutHandler property delegates checkout to handler class on checkoutIt', function () {
    $cart = TestCartWithConfiguredHandler::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt())->toBeTrue();
    expect(TestOrderHandlerAction::$handled)->toBeTrue();
});

test('CartBase $checkoutHandler property delegates to placedCart handler class on checkoutIt', function () {
    $cart = TestCartWithPlacedCartConfiguredHandler::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt())->toBeTrue();
    expect(TestPlacedCartAction::$called)->toBeTrue();
});

test('global config checkout_handler is called as fallback when no cart handler set on checkoutIt', function () {
    config(['basketin.cart.checkout_handler' => TestPlacedCartAction::class]);

    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    expect($cart->checkoutIt())->toBeTrue();
    expect(TestPlacedCartAction::$called)->toBeTrue();
});

test('checkoutIt throws InvalidCheckoutHandlerException when class has neither placedCart, handle, nor __invoke', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->checkoutIt(action: TestInvalidHandlerAction::class);
})->throws(InvalidCheckoutHandlerException::class);

test('checkoutIt throws InvalidCheckoutHandlerException when class string does not exist', function () {
    $cart = Cart::make('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD');

    $cart->checkoutIt(action: 'NonExistentActionClass');
})->throws(InvalidCheckoutHandlerException::class);
