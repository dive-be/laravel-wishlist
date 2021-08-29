# Manage your users' wishes in a Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/laravel-wishlist.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-wishlist)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

⚠️ Minor releases of this package may cause breaking changes as it has no stable release yet.

## What problem does this package solve?

Optionally describe why someone would want to use this package.

## Installation

You can install the package via composer:

```bash
composer require dive-be/laravel-wishlist
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Dive\Wishlist\WishlistServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Dive\Wishlist\WishlistServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-wishlist = new Dive\Wishlist();
echo $laravel-wishlist->echoPhrase('Hello, Dive!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email oss@dive.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
