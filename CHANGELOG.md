# Changelog

All notable changes to `laravel-wishlist` will be documented in this file.

## 1.4.0 - 2023-03-14

### Added

- PHP 8.3 support
- Laravel 11 support

### Removed

- PHP 8.2 support
- Laravel 10 support

## 1.3.0 - 2023-04-27

### Added

- PHP 8.2 support
- Laravel 10 support

### Removed

- PHP 8.1 support
- Laravel 9 support

## 1.2.2 - 2023-04-27

### Fixed

- `wishable` relation now ignores any active global scopes

## 1.2.1 - 2022-09-19

### Fixed

- Allow models with integer keys

## 1.2.0 - 2022-02-11

### Added

- PHP 8.1 support
- Laravel 9 support

### Removed

- PHP 8 support
- Laravel 8 support

## 1.1.0 - 2021-12-12

### Added

- The possibility to provide your own implementation of the `Wish` model while using the `Eloquent` driver
- A `WishlistTouched` event will now be dispatched after performing "dirty" operations.

## 1.0.0 - 2021-09-12

- initial release
