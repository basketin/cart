<p align="center"><a href="#" target="_blank"><img src="./cover.svg"/></a></p>

<p align="center">
  <a href="https://packagist.org/packages/basketin/cart" target="_blank"><img src="https://img.shields.io/static/v1?label=Packagist&message=basketin/cart&color=blue&logo=packagist&logoColor=white" alt="Source"></a>
  <a href="https://packagist.org/packages/basketin/cart" target="_blank"><img src="https://poser.pugx.org/basketin/cart/v" alt="Packagist Version"></a>
</p>

# Basketin Cart

Cart module for eCommerce system based on Laravel.

## Documentation

### Installation

Install via composer.

```bash
composer require basketin/cart
```

You need to migrate the package tables.

```bash
php artisan migrate --path=/vendor/basketin/cart/database/migrations
```

If you need to auth migrate without set path you can set `true` to `basketin.cart.setup.auto_migrate` at config.

### Publish config

```bash
php artisan vendor:publish --tag=basketin-cart-config
```

### How to use

#### Create New Cart

```php
<?php

use Basketin\Component\Cart\Facades\CartManagement;

$cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q', 'USD'); // <- ULID
```

You can open the cart if it exists or create a new cart if not exist.

#### Open Exist Cart

```php
<?php

use Basketin\Component\Cart\Facades\CartManagement;

$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
```

#### Get Ulid

```php
$cart->getUlid();
```

#### Get Currency

```php
$cart->getCurrency();
```

#### Get Count Products

```php
$cart->getCountProducts();
```

#### Get Count items

```php
$cart->getCountItems();
```

Open the existing cart only

#### Add QuoteYou need to prepare a `Product` model to use like this.

```php
// Product model
<?php

...
use Basketin\Component\Cart\Contracts\IQuote;
use Basketin\Component\Cart\Traits\HasQuote;
use Basketin\Component\Cart\Traits\HasTotal;

class Product extends Model implements IQuote
{
    use HasFactory;
    use HasQuote;
    use HasTotal;

    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->price;
    }

    public function getSpecialPriceAttribute(): float|null
    {
        return null;
    }
}
```

#### Increase Quote

```php
<?php

use App\Models\Product;
use Basketin\Component\Cart\Facades\CartManagement;

$product = Product::first();

$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->quote()->increaseQuote($product, 5);
```

#### Decrease Quote

```php
<?php

use App\Models\Product;
use Basketin\Component\Cart\Facades\CartManagement;

$product = Product::first();

$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->quote()->decreaseQuote($product, 2);
```

#### Has Quote

```php
<?php

use App\Models\Product;
use Basketin\Component\Cart\Facades\CartManagement;

$product = Product::first();

$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->quote()->hasQuote($product);
```

#### Remove Quote

```php
<?php

use App\Models\Product;
use Basketin\Component\Cart\Facades\CartManagement;

$product = Product::first();

$cart = CartManagement::openCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->quote()->removeQuote($product);
```

#### Get Cart

```php
<?php

use Basketin\Component\Cart\Facades\CartManagement;

$cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->getCart();
```

#### Get Quotes

```php
<?php

use Basketin\Component\Cart\Facades\CartManagement;

$cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$cart->quote()->getQuotes();
```

#### Get Totals

```php
<?php

use Basketin\Component\Cart\Facades\CartManagement;

$cart = CartManagement::initCart('01HF7V7N1MG9SDFPQYWXDNHR9Q'); // <- ULID
$totals = $cart->totals();
$totals->getSubTotal();
$totals->getDiscountTotal();
$totals->getGrandTotal();
```

If you need to add a global discount to the cart you can use it.

```php
$totals->setGlobalDiscountTotal(500.00)
    ->getGrandTotal();
```

### Coupon

#### Coupon model

You need to prepare a coupon model to inject into cart services

```php
use Illuminate\Database\Eloquent\Model;
use Basketin\Component\Cart\Contracts\ICoupon;

class Coupon extends Model implements ICoupon
{
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'discount_type',
        'discount_value',
        'start_at',
        'ends_at',
    ];

    public function discountType(): String
    {
        return $this->discount_type;
    }

    public function discountValue(): Int
    {
        return $this->discount_value;
    }
}
```

> The discount type: `fixed` = `CouponCalculate::FIXED` | `percent` = `CouponCalculate::PERCENT`

To apply coupon code on cart:-

```php
$coupon = Coupon::first();
$cart->coupon($coupon);
```

### Fields

You can create fields that contain a key and values for each shopping cart.

#### Set Field

```php
return $cart->fields()->set('key', 'value');
```

#### Get Field

```php
return $cart->fields()->get('key');
```

#### Remove

```php
return $cart->fields()->remove('key');
```

#### Has Field

```php
return $cart->fields()->has('key');
```

## Contributing

Thank you for considering contributing to this package! Be one of the Store team.

## License

This package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
