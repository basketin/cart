# Changelog

All notable changes to `basketin/cart` will be documented in this file.

## [2.2.0] - 2026-11-26

### Added
- Migrated `Cart` and `Quote` models and migrations onto Obelawium `ModelBase` and `MigrationBase` for transparent Database-per-Tenant connection resolution.
- Added `CartActionExecuted` event and feature test coverage.

### Changed
- Updated PHP requirement to `^8.2`, `obelaw/ium` to `^1.0`, `"minimum-stability": "stable"`, and `"prefer-stable": true`.
