# Basketin Cart

Laravel cart library for e-commerce. Provides carts, quotes (line items), totals, coupons, custom fields, and order preparation with a clean API.

[![Packagist](https://img.shields.io/static/v1?label=Packagist&message=basketin/cart&color=blue&logo=packagist&logoColor=white)](https://packagist.org/packages/basketin/cart)
[![Version](https://poser.pugx.org/basketin/cart/v)](https://packagist.org/packages/basketin/cart)

![Basketin Cart Cover](./cover.svg)

## Requirements

- PHP 8.1 or 8.2
- Laravel 10 or 11
- Filament 3.2+

## Installation

Install the package:

```bash
composer require basketin/cart
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag=basketin-cart-config
```

Run the migrations. You have two options:

- Enable auto-migrations by setting `basketin.cart.setup.auto_migrate` to `true` in `config/basketin/cart.php`.
- Or run the migrations explicitly:

```bash
php artisan migrate --path=vendor/basketin/cart/database/migrations
```

## Quick start

Initialize a cart (creates one if it doesn’t exist) or open an existing cart by ULID and optional type:

```php
use Obelaw\Basketin\Cart\Facades\CartManagement;

$cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD', 'ORDER');
// or open an existing cart
$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'ORDER');
```

Common getters:

```php
$cart->getUlid();        // string ULID
$cart->getCurrency();    // string currency code
$cart->getType();        // string|null cart type
$cart->getCountProducts(); // number of distinct products
$cart->getCountItems();    // total item quantity
```

Session key used per cart type: `${cartType}_basketin_cart_ulid`.

## Working with quotes (line items)

Your purchasable model must implement `Obelaw\Basketin\Cart\Contracts\IQuote` and use the traits below to participate in the cart totals.

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Obelaw\Basketin\Cart\Contracts\IQuote;
use Obelaw\Basketin\Cart\Traits\HasQuote;
use Obelaw\Basketin\Cart\Traits\HasTotal;

class Product extends Model implements IQuote
{
    use HasFactory, HasQuote, HasTotal;

    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->price;
    }

    public function getSpecialPriceAttribute(): ?float
    {
        return null; // or return a discounted price
    }
}
```

Add / increase / decrease / remove quotes:

```php
use App\Models\Product;
use Obelaw\Basketin\Cart\Facades\CartManagement;

$product = Product::first();
$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q');

$cart->quote()->addQuote($product, 1);
$cart->quote()->increaseQuote($product, 5);
$cart->quote()->decreaseQuote($product, 2); // returns false when item is removed
$cart->quote()->removeQuote($product);

$exists = $cart->quote()->hasQuote($product);
$quotes = $cart->quote()->getQuotes();
```

Per-cart limits and errors:

- Limit per quote is controlled by `limit_quote` (default: 5).
- Adding or increasing beyond the limit throws `QuoteQuantityLimitException`.
- Operating on a non-existing quote throws `QuoteNotFoundException`.

## Totals and discounts

```php
$totals = $cart->totals();

$subTotal = $totals->getSubTotal();
$discountTotal = $totals->getDiscountTotal(); // coupon + global discount (capped at subtotal)
$grandTotal = $totals->getGrandTotal();

// Optional global discount
$grandAfterGlobal = $totals->setGlobalDiscountTotal(500.00)
    ->getGrandTotal();
```

### Coupons

Provide a model that implements `Obelaw\Basketin\Cart\Contracts\ICoupon`:

```php
use Illuminate\Database\Eloquent\Model;
use Obelaw\Basketin\Cart\Contracts\ICoupon;

class Coupon extends Model implements ICoupon
{
    protected $fillable = [
        'coupon_name', 'coupon_code', 'discount_type', 'discount_value', 'start_at', 'ends_at',
    ];

    public function discountType(): string { return $this->discount_type; }
    public function discountValue(): int { return $this->discount_value; }
}
```

Attach a coupon to the cart and compute totals:

```php
use Obelaw\Basketin\Cart\Calculate\CouponCalculate;

$coupon = Coupon::first();
$cart->coupon($coupon);

$discount = (new CouponCalculate($coupon))
    ->setSubTotal($cart->totals()->getSubTotal())
    ->getSubTotal();
```

Supported types: `fixed` (`CouponCalculate::FIXED`) and `percent` (`CouponCalculate::PERCENT`).

## Custom fields

Key-value fields attached to a cart:

```php
$cart->fields()->set('shipping_method', 'express');
$cart->fields()->get('shipping_method');
$cart->fields()->has('shipping_method');
$cart->fields()->remove('shipping_method');
```

## Orders lifecycle

Prepare and associate an order, then checkout:

```php
$order = $cart->preparingOrder(); // ensures a cart order and sets fields.order_reference

// Associate with your domain order model
// $yourOrder->cartOrder()->save($order);
$cart->syncOrder($yourOrder);

$cart->checkoutIt('ORDER'); // marks cart as checkout and clears session for that type
```

## Events

- `BasketinCreateCartEvent` — fired when a cart is initialized.
- `BasketinAddedQuoteEvent` — after adding a quote.
- `BasketinIncreaseQuoteEvent` — after increasing a quote quantity.
- `BasketinDecreaseQuoteEvent` — after decreasing a quote quantity.
- `BasketinRemoveQuoteEvent` — after removing a quote.

## Configuration

`config/basketin/cart.php` (after publishing):

- `setup.auto_migrate` (bool): auto-load migrations when running in console. Default: `false`.
- `limit_quote` (int): max quantity per single quote. Default: `5`.

You can also override behavior at runtime via the settings object:

```php
use Obelaw\Basketin\Cart\Settings\Config;

$cart->config(new Config([
    'limit_quote' => 15,
]));
```

## Testing

```bash
composer test
```

## Contributing

Issues and PRs are welcome. Please include tests when contributing behavior changes.

## License

This package is open-sourced software licensed under the MIT license.
