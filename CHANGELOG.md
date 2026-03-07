# Changelog

All notable changes to this package will be documented in this file.

## [Unreleased]

### Added

- Migration `2023_12_18_000001_create_basketin_carts_tables.php` - Creates three tables:
  - `basketin_carts`: Main cart table with ulid, customer morphs, currency, and status
  - `basketin_cart_quotes`: Line items table with cart_id, item morphs, and quantity
  - `basketin_cart_fields`: Key-value custom fields table for carts

- Migration `2024_08_19_142245_create_basketin_cart_orders_table.php` - Creates the `basketin_cart_orders` table for linking carts to orders. This table stores the cart-order relationship with fields for cart_id (foreign key), reference, and polymorphic relation for orderable.

- Migration `2025_02_15_133051_add_type_to_basketin_carts_table.php` - Adds `cart_type` column to `basketin_carts` table for categorizing carts by type.
