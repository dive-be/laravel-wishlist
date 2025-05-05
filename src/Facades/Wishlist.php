<?php declare(strict_types=1);

namespace Dive\Wishlist\Facades;

use Closure;
use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\EloquentWishlist;
use Dive\Wishlist\InMemoryWishlist;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use Dive\Wishlist\WishlistManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Wish add(Wishable $wishable)
 * @method static WishCollection all()
 * @method static int count()
 * @method static WishlistManager extend(string $driver, Closure $callback)
 * @method static EloquentWishlist forUser(Authenticatable $user)
 * @method static Wish|null find(string|Wishable $id)
 * @method static bool has(Wishable $wishable)
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 * @method static int purge()
 * @method static bool remove(string|Wish|Wishable $id)
 */
final class Wishlist extends Facade
{
    public static function fake(): InMemoryWishlist
    {
        self::swap($fake = InMemoryWishlist::make());

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'wishlist';
    }
}
