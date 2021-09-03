# ❤️ - Manage your users' wishes in a Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/laravel-wishlist.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-wishlist)

⚠️ Minor releases of this package may cause breaking changes as it has no stable release yet.

## What problem does this package solve?

This package provides solutions to common problems when managing user preferences AKA a wishlist.

It allows for seamless transitions between storage drivers depending on a user's authentication state.

## Installation

You can install the package via composer:

```bash
composer require dive-be/laravel-wishlist
```

You can publish the config file and migrations with:
```bash
php artisan wishlist:install
```

This is the contents of the published config file:

```php
return [
    'cookie' => [

        /**
         * You may choose to scope the cookies to a particular subdomain. 
         * Especially useful when serving multiple apps.
         * The default (no scoping) will suffice for most apps, though.
         */
        'domain' => env('WISHLIST_COOKIE_DOMAIN', env('SESSION_DOMAIN')),

        /**
         * Time-to-Live in minutes. Default 43200 (1 month).
         */
        'max_age' => env('WISHLIST_COOKIE_MAX_AGE', 43_200),

        /**
         * You may customize the name of the cookie. Completely optional.
         */
        'name' => env('WISHLIST_COOKIE_NAME', 'wishlist'),
    ],

    /**
     * Supported drivers:
     * - "array" (only available during the current request lifecycle)
     * - "cookie" (persists the user's wishes as a serialized string inside a cookie)
     * - "eloquent" (persists the users' wishes to the `wishes` table)
     * - "upgrade" (uses the cookie driver if a user is not authenticated, otherwise uses the eloquent driver)
     */
    'driver' => env('WISHLIST_DRIVER', Dive\Wishlist\WishlistManager::ELOQUENT),

    'eloquent' => [

        /**
         * You may choose to provide a context for the saved wishes in the database.
         * Particularly useful when serving multiple apps. The default will suffice for most apps, though.
         */
        'scope' => 'default',

        /**
         * The user model of your application. For most apps, the default will suffice.
         * However, if you wish, you may customize that here. This is used for the Wish model's
         * user relationship so you can display the owner of the wish in e.g. Laravel Nova.
         */
        'user' => config('auth.providers.users.model'),
    ],
];
```

## Usage

### Configuration

First things first, you should take a look at the published configuration file and adjust the values to your needs.
If you need additional information about the available options, read on.

### Drivers

#### Array ⏳

This driver is meant for use in testing. Its contents are ephemeral and should _not_ be used in production.

See [Testing section](#testing) below for additional information regarding unit tests.

#### Cookie 🍪

The user's wishlist will be persisted client-side as a stringified JSON. 
You should make use of [Laravel's cookie encryption](https://laravel.com/docs/8.x/requests#retrieving-cookies-from-requests) (enabled by default) or any user will be able to crash your application (there is no validation) when the cookie values are tampered with. 
The internal structure of your code base will be leaked partially as well if you do not make use of [relation morph maps](https://laravel.com/docs/8.x/eloquent-relationships#custom-polymorphic-types).

> Note: make sure the name of the cookie you wish to use (pun intended) is not excluded for encryption in the `\App\Http\Middleware\EncryptCookies::$excluded` array.

#### Eloquent 🧑‍🎨

The user's wishlist will be persisted server-side in the `wishes` table.
You can only use this driver inside routes that are protected by the `auth` middleware as it requires an instance of an authenticated user.

> Note: do not forget to publish and run the migrations if you opt to use this driver.

#### Upgrade 🚀

__> This <__ is the driver that makes this package shine.

- When the user is browsing your app unauthenticated, the [Cookie driver](#cookie) will be used under the hood to persist the wishlist.
- When the user is authenticated, the [Eloquent driver](#eloquent) will be used.

🤌 Best of all? You don't have to change anything in your code. 

But hold on, there is more! When the user logs in, the wishes can be carried over to the database automatically! 🙀
See [Migrating wishes section](#migrating-wishes-) below for additional information.

### Preparing Wishable models

Next, you must make the Eloquent models that can be wished for `Wishable` i.e. implement the contract and use the `CanBeWished` trait.

An example:

```php
use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Concerns\CanBeWished;

class Product extends Model implements Wishable
{
    use CanBeWished;
}
```

### Preparing User model (optional)

For convenience, this package also provides an `InteractsWithWishlist` trait which you can use in your `User` model to interact with the wishlist.

```php
use Dive\Wishlist\Models\Concerns\InteractsWithWishlist;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use InteractsWithWishlist;
}
```

You can now do the following:

```php
$user = auth()->user();

$user->wish(Product::first()); // adds the product to the wishlist
$user->unwish(Product::first()); // removes the product from the wishlist
$user->wishes(); // retrieves all of the user's wishes
```

### Resolving the wishlist

This package provides every possible way to resolve a `Wishlist` instance out of the IoC container. We've got you covered!

#### Facade

```php
use Dive\Wishlist\Facades\Wishlist;

Wishlist::all();
```

or using the alias (particularly helpful in Blade views)

```php
use Wishlist;

Wishlist::all();
```

#### Helper

```php
wishlist()->all();
```

#### Dependency injection

```php
use Dive\Wishlist\Contracts\Wishlist;

public function index(Wishlist $wishlist)
{
    return view('wishlist.index')->with('wishes', $wishlist->all());
}
```

#### Service Location

```php
app('wishlist')->all();
```

## Capabilities 💪

You can refer to the [Wishlist contract](src/Contracts/Wishlist.php) for an exhaustive list.

### Adding a wish

```php
$wish = Wishlist::add($product);
```

You will receive an instance of [`Dive\Wishlist\Wish`](src/Wish.php).

### Retrieving all wishes

```php
$wishes = Wishlist::all();
```

You will receive an instance of [`Dive\Wishlist\WishCollection<Wish>`](src/WishCollection.php).
Refer to the class definition for all convenience methods.

### Eager loading wish relations

When there is only a single type of `Wishable` (and you know for sure there won't be any other), you may omit the morph types entirely:

```php
$wishes = Wishlist::all()->load(['productable', 'variant']); // ✅ Collection only contains Wishables of type "product"
```

In other cases, you must provide a type-relation map:

```php
$wishes = Wishlist::all()->load([
    Product::class => ['productable', 'variant'],
    Sample::class  => 'purveyor',
]); // ✅ Collection contains multiple wishables
```

A `LogicException` will be thrown in case of ambiguity:    

```php
$wishes = Wishlist::all()->load(['productable', 'variant']); // 🚫 Collection contains 2 types of Wishable: Product & Sample
```

### Retrieving total count

```php
$count = Wishlist::count();
```

### Existence checks

```php
$isWished = Wishlist::has($product); 
```

### Emptiness checks

```php
Wishlist::isEmpty();
Wishlist::isNotEmpty();
```

### Purging

```php
$amountOfWishesRemoved = Wishlist::purge();
```

### Wish removal 

You can remove a wish either by its `id`

```php
public function destroy(int $id)
{
    Wishlist::remove($id);
}
```

or by its underlying `Wishable`

```php
public function destroy(Product $product)
{
    Wishlist::remove($product);
}
```

## Migrating wishes 🚚

While using the [upgrade driver](#upgrade-), you might want to carry over the user's cookie wishlist to the database.
This is not enabled by default, but you have 2 options to opt into this behavior:

### Event listener

Listen to the `Login` event in `EventServiceProvider`:

```php
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            \Dive\Wishlist\Listeners\MigrateWishes::class,
        ],
    ];
}
```

### Middleware

Add the `MigrateWishes` middleware to your application's middleware stack:

```php
class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Dive\Wishlist\Middleware\MigrateWishes::class, // 👈
            // omitted for brevity
        ],
    ];
}
```

> Note: make sure to place the middleware **after** `EncryptCookies`

## Route model binding

__TODO__

## Retrieving a particular user's wishlist 👱🏻‍♂️

You might want to modify a user's wishlist in e.g. an Artisan command where no auth context is available.
You may call the `forUser` method with a `User` instance to retrieve a wishlist scoped to that user:

```php
class ClearWishlistCommand extends Command
{
    public function handle()
    {
        $user = User::first();

        Wishlist::forUser($user)->purge();
    }
```

> Note: this is only available for the [Eloquent driver](#eloquent-)

## Extending the wishlist 👣

If the default drivers do not fulfill your needs, you may extend the `WishlistManager` with your own custom drivers:

```php
Wishlist::extend('redis', function () {
    return new RedisWishlist();
});
```

## Testing 🔎

This package offers a fake implementation of the `Wishlist` contract so you can make assertions in your unit tests and make sure you ship that bug-free code 💪.

```php
use App\Http\Livewire\HeartButton;
use Database\Factories\ProductFactory;
use Dive\Wishlist\Facades\Wishlist;
use function Pest\Livewire\livewire;

test('A wish is added when the visitor clicks on the heart icon', function () {
    Wishlist::fake();

    livewire(HeartButton::class)->call('wish', ProductFactory::new()->create()->getKey());

    expect(Wishlist::count())->toBe(1);
});
```

## Testing (package)

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
